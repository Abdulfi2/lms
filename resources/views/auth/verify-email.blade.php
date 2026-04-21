<x-client-layout>
    <div x-data="verifyForm()" class="max-w-md mx-auto bg-white rounded-2xl shadow-xl p-8 md:p-12 text-center">
        <div class="mx-auto w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                </path>
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">Verifikasi Alamat Email</h2>

        <p class="text-gray-600 mb-4">
            Kami telah mengirimkan link verifikasi ke email:
            <strong class="text-primary">{{ $email ?? 'email Anda' }}</strong>
        </p>

        <p class="text-gray-500 text-sm mb-6">
            Jika Anda tidak menerima email, periksa folder spam atau klik tombol di bawah untuk mengirim ulang.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
                Link verifikasi baru telah dikirim ke alamat email Anda.
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-4 p-3 bg-yellow-100 text-yellow-700 rounded-lg">
                {{ session('warning') }}
            </div>
        @endif

        <div class="space-y-3">
            <form method="POST" action="{{ route('verification.send') }}" @submit.prevent="resend">
                @csrf
                <input type="hidden" name="email" value="{{ $email ?? '' }}">
                <button type="submit" :disabled="loading"
                    class="w-full py-3 px-4 bg-primary hover:bg-secondary text-white font-semibold rounded-lg transition duration-200 disabled:opacity-50">
                    <span x-show="!loading">Kirim Ulang Email Verifikasi</span>
                    <span x-show="loading">Mengirim...</span>
                </button>
            </form>

            <a href="{{ route('login') }}"
                class="block w-full py-3 px-4 border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-lg transition duration-200 text-center">
                Kembali ke Halaman Login
            </a>
        </div>
    </div>

    <script>
        function verifyForm() {
            return {
                loading: false,
                async resend() {
                    this.loading = true;
                    const email = document.querySelector('input[name="email"]')?.value || '';

                    try {
                        const response = await fetch('{{ route('verification.send') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                email: email
                            })
                        });

                        if (response.ok) {
                            window.toast.success('Link verifikasi baru telah dikirim ke email Anda.');
                        } else {
                            const data = await response.json();
                            window.toast.error(data.message || 'Gagal mengirim ulang, silakan coba lagi.');
                        }
                    } catch (error) {
                        window.toast.error('Terjadi kesalahan, silakan coba lagi.');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-client-layout>
