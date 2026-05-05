<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Http\Requests\ContactMessageRequest;
use App\Http\Resources\ContactMessageResource;
use App\Mail\NewContactMessageMail;
use App\Models\ContactMessage;
use App\Services\CloudflareTurnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        return ContactMessageResource::collection(
            ContactMessage::query()
                ->when($request->status, fn ($query, $status) => $query->where('status', $status))
                ->latest()
                ->paginate($request->integer('per_page', 15))
        );
    }

    public function store(ContactMessageRequest $request, CloudflareTurnstile $turnstile)
    {
        abort_unless($turnstile->verify($request->cf_turnstile_response, $request->ip()), 422, 'Human verification failed.');

        $message = ContactMessage::create([
            ...$request->safe()->except('cf_turnstile_response'),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        Mail::to(config('mail.from.address'))->queue(new NewContactMessageMail($message));

        return new ContactMessageResource($message);
    }

    public function show(ContactMessage $contactMessage)
    {
        return new ContactMessageResource($contactMessage);
    }

    public function update(Request $request, ContactMessage $contactMessage)
    {
        $contactMessage->update($request->validate(['status' => ['required', 'in:new,read,replied,archived']]));
        $this->logActivity('contact.updated', $contactMessage);

        return new ContactMessageResource($contactMessage);
    }

    public function reply(Request $request, ContactMessage $contactMessage)
    {
        $request->validate(['reply' => ['required', 'string', 'max:5000']]);
        $contactMessage->update(['status' => 'replied', 'replied_at' => now()]);
        $this->logActivity('contact.replied', $contactMessage);

        return new ContactMessageResource($contactMessage);
    }

    public function archive(ContactMessage $contactMessage)
    {
        $contactMessage->update(['status' => 'archived', 'archived_at' => now()]);
        $this->logActivity('contact.archived', $contactMessage);

        return new ContactMessageResource($contactMessage);
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $this->logActivity('contact.deleted', $contactMessage);
        $contactMessage->delete();

        return response()->noContent();
    }
}
