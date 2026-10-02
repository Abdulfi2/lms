{{-- resources/views/student/certificates/print.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Sertifikat - {{ $certificate->course->title }}</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon-32.png') }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            .certificate-card {
                box-shadow: none;
                border: 1px solid #e5e7eb;
                margin: 0;
                page-break-inside: avoid;
            }
        }

        .btn-hover:hover {
            transform: translateY(-2px);
            transition: all 0.2s ease;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="max-w-4xl mx-auto py-8 px-4">
        <!-- Kartu Sertifikat -->
        <div id="certificateArea" class="certificate-card bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header warna -->
            <div class="bg-gradient-to-r from-yellow-500 to-amber-600 h-3"></div>

            <div class="p-8 md:p-12 text-center">
                <!-- Ikon -->
                <div class="flex justify-center mb-6">
                    <div class="bg-yellow-100 rounded-full p-4 inline-flex">
                        <i class="fas fa-award text-5xl text-yellow-600"></i>
                    </div>
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-800 uppercase tracking-wide">Sertifikat
                    Kelulusan</h1>
                <p class="text-gray-500 mt-2">Diberikan kepada</p>
                <div class="my-6 py-3 border-y-2 border-dashed border-yellow-300">
                    <p class="text-3xl md:text-4xl font-bold text-gray-800 uppercase tracking-wide">
                        {{ $certificate->user->name }}</p>
                </div>
                <p class="text-gray-700">Telah menyelesaikan kursus</p>
                <h2 class="text-2xl md:text-3xl font-bold text-blue-600 mt-2">{{ $certificate->course->title }}</h2>
                <p class="text-gray-600 mt-4 max-w-lg mx-auto">dengan nilai memuaskan dan dinyatakan <span
                        class="font-semibold text-green-600">LULUS</span> sebagai bentuk apresiasi atas dedikasi dan
                    kerja keras.</p>

                <div class="mt-8 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-left">
                        <p class="text-sm text-gray-500">Diterbitkan pada</p>
                        <p class="font-semibold">{{ $certificate->issued_at->isoFormat('D MMMM Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Instruktur</p>
                        <p class="font-semibold">{{ $certificate->course->instructor->name ?? 'LMS Team' }}</p>
                        <div class="w-32 h-0.5 bg-gray-300 mt-1 mx-auto md:mx-0"></div>
                    </div>
                </div>

                <div class="mt-8 bg-gray-50 rounded-lg p-4 text-sm text-gray-600 text-left">
                    <p><i class="fas fa-hashtag mr-2 text-gray-400"></i> Nomor Sertifikat: <span
                            class="font-mono">{{ $certificate->certificate_number }}</span></p>
                    <p class="mt-1"><i class="fas fa-qrcode mr-2 text-gray-400"></i> Kode Verifikasi: <span
                            class="font-mono">{{ $certificate->verification_code }}</span></p>
                </div>
            </div>

            <div class="bg-gray-50 px-8 py-4 text-center text-xs text-gray-400 border-t">
                <i class="fas fa-globe mr-1"></i> Verifikasi keaslian di
                {{ url('/certificate/verify') }}/{{ $certificate->verification_code }}
            </div>
        </div>

        <!-- Tombol Aksi (tidak dicetak) -->
        <div class="no-print flex flex-wrap justify-center gap-4 mt-8">
            <button onclick="window.print()"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl shadow-md flex items-center gap-2 transition btn-hover">
                <i class="fas fa-print"></i> Cetak Sertifikat
            </button>
            <a href="{{ route('student.certificates.download', $certificate) }}"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-xl shadow-md flex items-center gap-2 btn-hover">
                <i class="fas fa-download"></i> Download PDF
            </a>
            <a href="{{ route('student.certificates.show', $certificate) }}"
                class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl shadow-md flex items-center gap-2 btn-hover">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <script>
        window.onbeforeprint = function() {
            document.body.style.margin = '0';
            document.body.style.padding = '0';
        };
        window.onafterprint = function() {
            document.body.style.margin = '';
            document.body.style.padding = '';
        };
    </script>
</body>

</html>
