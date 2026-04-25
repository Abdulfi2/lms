<!DOCTYPE html>
<html>

<head>
    <title>Certificate of Completion</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: center;
            background: url('{{ public_path('images/certificate-bg.jpg') }}') no-repeat center;
            background-size: cover;
            padding: 50px;
        }

        .certificate {
            border: 20px solid #3B82F6;
            padding: 40px;
            background: rgba(255, 255, 255, 0.9);
        }

        h1 {
            font-size: 48px;
            color: #1D4ED8;
        }

        h2 {
            font-size: 32px;
        }

        .name {
            font-size: 36px;
            font-weight: bold;
            margin: 30px 0;
        }

        .course {
            font-size: 28px;
            margin: 20px 0;
        }

        .date {
            margin-top: 50px;
        }

        .signature {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 300px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <div class="certificate">
        <h1>CERTIFICATE OF COMPLETION</h1>
        <p>This certificate is proudly presented to</p>
        <div class="name">{{ $user->name }}</div>
        <p>for successfully completing the course</p>
        <div class="course">{{ $course->title }}</div>
        <div class="date">
            <p>Issued on: {{ $date->format('F d, Y') }}</p>
            <p>Certificate #: {{ $certificate_number }}</p>
        </div>
        <div class="signature">
            <p>Authorized Signature</p>
        </div>
    </div>
</body>

</html>
