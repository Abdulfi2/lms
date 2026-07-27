<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureInstructorApproved
{
    /**
     * Blokir akses ke area instruktur selama profil instruktur masih 'pending'/'rejected'.
     * Role lain (admin, student) tidak terpengaruh oleh middleware ini.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->hasRole('instructor')) {
            $status = $user->profile?->approval_status ?? 'approved';

            if ($status !== 'approved') {
                $message = $status === 'rejected'
                    ? 'Pengajuan akun instruktur Anda ditolak. Hubungi admin untuk informasi lebih lanjut.'
                    : 'Akun instruktur Anda masih menunggu persetujuan admin.';

                // Catatan: jangan redirect ke 'dashboard' — route itu akan melempar instruktur
                // kembali ke 'instructor.dashboard' dan memicu redirect loop lewat middleware ini.
                return redirect()->route('instructor.pending-approval')->with('error', $message);
            }
        }

        return $next($request);
    }
}
