<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Http\Requests\MediaUploadRequest;
use App\Models\MediaFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    use LogsActivity;

    public function index()
    {
        return MediaFile::latest()->paginate(24);
    }

    public function store(MediaUploadRequest $request)
    {
        $file = $request->file('file');
        $collection = $request->string('collection', 'general')->toString();
        $name = Str::uuid().'.'.$file->extension();
        $path = $file->storeAs("media/{$collection}", $name, 'public');

        $media = MediaFile::create([
            'user_id' => $request->user()->id,
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'collection' => $collection,
        ]);
        $this->logActivity('media.uploaded', $media);

        return response()->json($media, 201);
    }

    public function destroy(MediaFile $mediaFile)
    {
        Storage::disk($mediaFile->disk)->delete($mediaFile->path);
        $this->logActivity('media.deleted', $mediaFile, ['path' => $mediaFile->path]);
        $mediaFile->delete();

        return response()->noContent();
    }
}
