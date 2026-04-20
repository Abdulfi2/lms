<x-client-layout>
    <div class="max-w-md m-auto">
        <div class="w-full sm:max-w-md m-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <div x-data="verifyForm()">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Verifikasi Alamat Email</h2>
                <p class="text-sm text-gray-600 text-center mb-6">
                    Sebelum melanjutkan, silakan verifikasi alamat email Anda dengan mengecek link yang telah
                    dikirimkan.
                    Jika belum menerima email, klik tombol di bawah untuk mengirim ulang.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 text-sm text-green-600">
                        Link verifikasi baru telah dikirim ke alamat email Anda.
                    </div>
                @endif

                <div class="flex flex-col space-y-4">
                    <form method="POST" action="{{ route('verification.send') }}" @submit.prevent="resend">
                        @csrf
                        <button type="submit" :disabled="loading"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50">
                            <span x-show="!loading">Kirim Ulang Email Verifikasi</span>
                            <span x-show="loading">Mengirim...</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function verifyForm() {
            return {
                loading: false,
                async resend(event) {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('verification.send') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            }
                        });
                        if (response.ok) {
                            alert('Link verifikasi baru telah dikirim ke email Anda.');
                        } else {
                            alert('Gagal mengirim ulang, silakan coba lagi.');
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan.');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-client-layout>
