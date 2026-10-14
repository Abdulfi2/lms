<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\GoogleService;
use App\Support\FooterSettings;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public const GROUPS = [
        'general' => 'Umum',
        'email' => 'Email',
        'payment' => 'Payment Gateway',
        'homepage' => 'Homepage',
        'footer' => 'Footer',
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
            'hints' => $activeGroup === 'footer' ? FooterSettings::hints() : [],
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

        if ($group === 'footer') {
            $this->validateFooter($request);
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

    /**
     * Form pengaturan generik tidak punya validasi per field, padahal isi footer tampil di semua
     * halaman publik dan masuk ke atribut href. Tolak URL berbahaya sebelum disimpan.
     */
    private function validateFooter(Request $request): void
    {
        $linkRule = function (string $attribute, $value, $fail) {
            $problems = FooterSettings::invalidLines($value);

            if ($problems) {
                $fail('Tautan tidak valid: ' . implode('; ', $problems) . '.');
            }
        };

        $rules = [
            'footer_description' => 'nullable|string|max:500',
            'footer_about_title' => 'nullable|string|max:50',
            'footer_help_title' => 'nullable|string|max:50',
            'footer_about_links' => ['nullable', 'string', 'max:2000', $linkRule],
            'footer_help_links' => ['nullable', 'string', 'max:2000', $linkRule],
            'footer_phone' => 'nullable|string|max:30',
            'footer_address' => 'nullable|string|max:300',
            'footer_copyright' => 'nullable|string|max:255',
        ];

        foreach (array_keys(FooterSettings::SOCIALS) as $key) {
            $rules[$key] = ['nullable', 'string', 'max:255', function (string $attribute, $value, $fail) {
                if (filled($value) && !FooterSettings::isSocialUrl($value)) {
                    $fail('Harus berupa alamat lengkap yang diawali http:// atau https://.');
                }
            }];
        }

        $request->validate($rules, [], [
            'footer_about_links' => 'Tautan Kolom 1',
            'footer_help_links' => 'Tautan Kolom 2',
            'footer_facebook' => 'Tautan Facebook',
            'footer_twitter' => 'Tautan X',
            'footer_instagram' => 'Tautan Instagram',
            'footer_linkedin' => 'Tautan LinkedIn',
            'footer_youtube' => 'Tautan YouTube',
        ]);
    }
}
