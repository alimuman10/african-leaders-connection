<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use LogsActivity;

    public function index()
    {
        return Setting::latest()->paginate(30);
    }

    public function store(Request $request)
    {
        $setting = Setting::create($this->validated($request));
        $this->logActivity('setting.created', $setting);

        return $setting;
    }

    public function show(Setting $setting)
    {
        return $setting;
    }

    public function update(Request $request, Setting $setting)
    {
        $setting->update($this->validated($request));
        $this->logActivity('setting.updated', $setting);

        return $setting->fresh();
    }

    public function destroy(Setting $setting)
    {
        $this->logActivity('setting.deleted', $setting, ['key' => $setting->key]);
        $setting->delete();

        return response()->noContent();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:160'],
            'value' => ['nullable'],
            'group' => ['nullable', 'string', 'max:80'],
        ]);

        $data['group'] = $data['group'] ?? 'general';

        return $data;
    }
}
