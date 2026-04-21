<?php

namespace App\Http\Controllers;

use App\Models\TokenDetails;
use App\Services\GoogleService;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Gmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MailController extends Controller
{
    protected $googleService;

    public function __construct(GoogleService $googleService)
    {
        $this->googleService = $googleService;
    }

    public function getAuthUrl()
    {
        return $this->googleService->redirectToGoogle();
    }

    public function handleCallback()
    {
        $this->googleService->handleGoogleCallback(request()->code);
        return redirect('/send-email')->with('success', 'Authenticated successfully');
    }

    public function sendEmail()
    {
        // $this->googleService->sendEmailCreateOrUpdateToken('faqih@zakatsukses.org', 'Create or Update Token Berhasil, terimakasih');
        $this->googleService->sendEmail('faqih@zakatsukses.org', 'Create or Update token', 'verification_token', ['name' => 'Faqih']);
        return redirect()->route('login');
    }
}