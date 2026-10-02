<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\GoogleService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public const GROUPS = [
        'general' => 'Umum',
        'email' => 'Email',
        'payment' => 'Payment Gateway',
        'homepage' => 'Homepage',
    ];

    public function index(Request $request, GoogleService $google)
    {
        $activeGroup = $request->get('group', 'general');

        if (!array_key_exists($activeGroup, self::GROUPS)) {
            $activeGroup = 'general';
        }

        $settings = Setting::where('group', $activeGroup)->orderBy('id')->get();

        return view('admin.settings.index', [
            'settings' => $settings,
            'groups' => self::GROUPS,
            'activeGroup' => $activeGroup,
            'googleMailerActive' => config('mail.default') === 'gmail_api',
            'googleAuthorized' => $google->isAuthorized(),
            'googleEmail' => $google->authorizedEmail(),
        ]);
    }

    public function update(Request $request)
    {
        $group = $request->input('group');

        if (!array_key_exists($group, self::GROUPS)) {
            abort(404);
        }

        $keys = Setting::where('group', $group)->pluck('type', 'key');

        foreach ($keys as $key => $type) {
            if ($type === 'boolean') {
                Setting::set($key, $request->has($key) ? '1' : '0');
            } else {
                Setting::set($key, $request->input($key));
            }
        }

        return redirect()->route('admin.settings.index', ['group' => $group])
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
