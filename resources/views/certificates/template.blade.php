<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sertifikat Kelulusan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            background: white;
            margin: 0;
            padding: 20px;
        }

        .print-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .certificate-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .bg-gradient {
            background: linear-gradient(135deg, #eab308 0%, #d97706 100%);
            height: 12px;
        }

        .content {
            padding: 48px;
            text-align: center;
        }

        .icon-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
        }

        .icon-circle {
            background: #fef3c7;
            border-radius: 9999px;
            padding: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .icon-circle svg {
            width: 48px;
            height: 48px;
            color: #d97706;
        }

        .title {
            font-size: 36px;
            font-weight: 800;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 16px;
        }

        .recipient {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px 0;
            border-top: 2px dashed #fcd34d;
            border-bottom: 2px dashed #fcd34d;
            margin: 20px 0;
        }

        .course {
            font-size: 24px;
            font-weight: 700;
            color: #2563eb;
            margin: 12px 0 16px;
        }

        .description {
            font-size: 14px;
            color: #6b7280;
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.5;
        }

        .footer-info {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .info-left,
        .info-right {
            flex: 1;
        }

        .info-left {
            text-align: left;
        }

        .info-right {
            text-align: right;
        }

        .label {
            font-size: 12px;
            color: #6b7280;
        }

        .value {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        .signature-line {
            width: 120px;
            height: 1px;
            background: #d1d5db;
            margin-top: 6px;
            margin-left: auto;
        }

        .details {
            background: #f9fafb;
            border-radius: 8px;
            padding: 16px;
            margin-top: 32px;
            text-align: left;
            font-size: 12px;
            color: #6b7280;
        }

        .details p {
            margin-bottom: 4px;
        }

        .footer-note {
            background: #f9fafb;
            padding: 16px 24px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }

        @media (max-width: 640px) {
            .content {
                padding: 24px;
            }

            .title {
                font-size: 24px;
            }

            .recipient {
                font-size: 20px;
            }

            .course {
                font-size: 18px;
            }

            .footer-info {
                flex-direction: column;
                align-items: center;
                gap: 16px;
            }

            .info-left,
            .info-right {
                text-align: center;
            }

            .signature-line {
                margin-left: auto;
                margin-right: auto;
            }
        }
    </style>
</head>

<body>
    <div class="print-container">
        <div class="certificate-card">
            <div class="bg-gradient"></div>
            <div class="content">
                <div class="icon-wrapper">
                    <div class="icon-circle">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
                <h1 class="title">Sertifikat Kelulusan</h1>
                <p class="subtitle">Diberikan kepada</p>
                <div class="recipient">{{ $user->name }}</div>
                <p class="subtitle">Telah menyelesaikan kursus</p>
                <h2 class="course">{{ $course->title }}</h2>
                <p class="description">dengan nilai memuaskan dan dinyatakan <strong>LULUS</strong> sebagai bentuk
                    apresiasi atas dedikasi dan kerja keras.</p>

                <div class="footer-info">
                    <div class="info-left">
                        <p class="label">Diterbitkan pada</p>
                        <p class="value">{{ $issued_at ?? now()->format('d F Y') }}</p>
                    </div>
                    <div class="info-right">
                        <p class="label">Instruktur</p>
                        <p class="value">{{ $course->instructor->name ?? 'LMS Team' }}</p>
                        <div class="signature-line"></div>
                    </div>
                </div>

                <div class="details">
                    <p><span style="margin-right:8px;">📄</span> Nomor Sertifikat:
                        <strong>{{ $certificate_number ?? 'N/A' }}</strong>
                    </p>
                    <p><span style="margin-right:8px;">🔑</span> Kode Verifikasi:
                        <strong>{{ $verification_code ?? 'N/A' }}</strong>
                    </p>
                </div>
            </div>
            <div class="footer-note">
                🌐 Verifikasi keaslian di {{ url('/certificate/verify') }}/{{ $verification_code ?? '' }}
            </div>
        </div>
    </div>
</body>

</html>
