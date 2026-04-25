<?php
// app/Http/Controllers/Student/CertificateController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('course')
            ->where('user_id', Auth::id())
            ->orderBy('issued_at', 'desc')
            ->paginate(12);

        return view('student.certificates.index', compact('certificates'));
    }

    public function show(Certificate $certificate)
    {
        if ($certificate->user_id !== Auth::id()) {
            abort(403);
        }

        return view('student.certificate-show', compact('certificate'));
    }

    public function verify($code)
    {
        $certificate = Certificate::where('verification_code', $code)->firstOrFail();
        return view('certificates.verify', compact('certificate'));
    }
}