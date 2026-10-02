<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleService;
use Illuminate\Http\Request;

class GoogleMailController extends Controller
{
    public function __construct(protected GoogleService $google)
    {
    }

    public function redirect()
    {
        return $this->google->redirectToGoogle();
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('admin.settings.index', ['group' => 'email'])
                ->with('error', 'Otorisasi Google dibatalkan atau ditolak.');
        }

        try {
            $email = $this->google->handleGoogleCallback($request->get('code'));
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index', ['group' => 'email'])
                ->with('error', 'Gagal menghubungkan akun Google: ' . $e->getMessage());
        }

        return redirect()->route('admin.settings.index', ['group' => 'email'])
            ->with('success', "Akun Google ({$email}) berhasil dihubungkan untuk mengirim email.");
    }

    public function disconnect()
    {
        $this->google->disconnect();

        return redirect()->route('admin.settings.index', ['group' => 'email'])
            ->with('success', 'Koneksi akun Google telah diputuskan.');
    }
}
