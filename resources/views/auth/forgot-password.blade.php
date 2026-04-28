@extends('layouts.client')

@section('content')
    <div x-data="forgotForm()" class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 md:p-8">
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 7.5a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM12 10.5v4.5m-6 4.5h12a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25v9A2.25 2.25 0 006 18.75z">
                    </path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Lupa Kata Sandi?</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Masukkan email Anda, kami akan mengirimkan link reset
                password.</p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-xl">
                {{ session('status') }}
            </div>
        @endif

        <form @submit.prevent="submitForm">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Email</label>
                <div class="relative">
                    <input type="email" x-model="form.email" required autofocus
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition pl-10"
                        :class="{ 'border-red-500': errors.email }" placeholder="nama@example.com">
                    <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <p x-show="errors.email" class="mt-1 text-xs text-red-600" x-text="errors.email"></p>
            </div>

            <button type="submit" :disabled="loading"
                class="w-full py-3 px-4 bg-gradient-to-r from-primary to-secondary hover:from-secondary hover:to-primary text-white font-semibold rounded-xl transition duration-200 disabled:opacity-50 shadow-md">
                <span x-show="!loading">Kirim Link Reset</span>
                <span x-show="loading" class="flex items-center justify-center">
                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Mengirim...
                </span>
            </button>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-primary hover:text-secondary inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke halaman login
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function forgotForm() {
                return {
                    form: {
                        email: ''
                    },
                    errors: {},
                    loading: false,
                    async submitForm() {
                        this.loading = true;
                        this.errors = {};

                        try {
                            const response = await fetch('{{ route('password.email') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify(this.form)
                            });

                            const data = await response.json();

                            if (response.ok) {
                                window.toast.success('Link reset password telah dikirim ke email Anda.');
                                this.form.email = '';
                            } else {
                                if (data.errors) this.errors = data.errors;
                                else window.toast.error(data.message);
                            }
                            this.loading = false;
                        } catch (error) {
                            window.toast.error('Terjadi kesalahan, silakan coba lagi.');
                            this.loading = false;
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
