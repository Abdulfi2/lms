@php
    $certTitle = \App\Models\Setting::get('certificate_title') ?: 'SERTIFIKAT KELULUSAN';
    $certGivenTo = \App\Models\Setting::get('certificate_subtitle_given_to') ?: 'Diberikan kepada';
    $certForCourse = \App\Models\Setting::get('certificate_subtitle_for_course') ?: 'Atas kelulusannya pada kelas';
    $certSignatureName = \App\Models\Setting::get('certificate_signature_name') ?: 'Dr. Sunarto Zulkifli S.TP, MM';
    $certSignatureTitle = \App\Models\Setting::get('certificate_signature_title') ?: 'Direktur';

    $logoValue = \App\Models\Setting::get('certificate_logo');
    $logoPath = $logoValue
        ? \Illuminate\Support\Facades\Storage::disk('public')->path($logoValue)
        : public_path('images/logo-zakatsukses.png');

    $backgroundValue = \App\Models\Setting::get('certificate_background');
    $backgroundPath = $backgroundValue
        ? \Illuminate\Support\Facades\Storage::disk('public')->path($backgroundValue)
        : public_path('images/background.png');

    $signatureImageValue = \App\Models\Setting::get('certificate_signature_image');
    $signatureImagePath = $signatureImageValue
        ? \Illuminate\Support\Facades\Storage::disk('public')->path($signatureImageValue)
        : null;
@endphp
<style>
    @page {
        size: A4 landscape;
        margin: 0;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
    }

    .certificate {
        width: 1123px;
        height: 100%;
        position: relative;
        background: url('{{ $backgroundPath }}') no-repeat center;
        background-size: 100%;
    }

    .description {
        width: 1123px;
        height: 794px;
        position: relative;
        background: url('{{ $backgroundPath }}') no-repeat center;
        background-size: 100% 100%;
    }

    .content {
        padding: 56px;
        text-align: center;
    }

    .logo {
        position: absolute;
        top: 40px;
        left: 60px;
    }

    .logo img {
        height: 60px;
    }

    .title {
        font-size: 48px;
        font-weight: bold;
        margin-top: 40px;
    }

    .subtitle {
        margin-top: 20px;
        font-size: 16px;
        color: #555;
    }

    .name {
        font-size: 42px;
        font-weight: bold;
        color: #059669;
        margin: 25px 0;
    }

    .course {
        font-size: 24px;
        color: #1d4ed8;
        margin-top: 10px;
    }

    .qr-container {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .qr {
        position: inline-flex;
        justify-content: center;
        text-align: center;
    }

    .qr img {
        width: 120px;
    }

    .footer-container {
        border-top: 1px solid gray;
        margin-left: 80px;
        margin-right: 80px;
        margin-top: 30px;
    }

    .footer-left {
        position: absolute;
        bottom: 100px;
        left: 100px;
        text-align: left;
    }

    .footer-right {
        position: absolute;
        bottom: 100px;
        right: 100px;
        text-align: right;
    }

    .signature-image img {
        height: 50px;
        margin-bottom: 6px;
    }
</style>

<body>
    <div class="certificate">

        <div class="logo">
            <img src="{{ $logoPath }}">
        </div>

        <div class="content">
            <div class="title">{{ $certTitle }}</div>

            <div class="subtitle">{{ $certGivenTo }}</div>

            <div class="name">{{ $user->name }}</div>

            <div class="subtitle">{{ $certForCourse }}</div>

            <div class="course">{{ $course->title }}</div>
        </div>

        <div class="qr-container">
            <div class="qr">
                <img style="background: transparent;" src="data:image/png;base64,{{ $qr_code }}">
                <div style="font-size:10px;">Scan untuk verifikasi</div>
                <div style="font-size:9px;">{{ $verification_code }}</div>
            </div>
        </div>

        <div class="footer-container">
            <div class="footer-left">
                <div style="font-size:12px;">Diterbitkan pada</div>
                <div><b>{{ $issued_at }}</b></div>
                @if (!empty($expires_at))
                    <div style="font-size:10px; margin-top:2px;">Berlaku hingga {{ $expires_at }}</div>
                @endif
            </div>

            <div class="footer-right">
                @if ($signatureImagePath)
                    <div class="signature-image"><img src="{{ $signatureImagePath }}"></div>
                @endif
                <div style="font-size:12px;">{{ $certSignatureTitle }}</div>
                <div><b>{{ $certSignatureName }}</b></div>
            </div>
        </div>

    </div>
</body>

</html>
