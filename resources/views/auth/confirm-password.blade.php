<x-client-layout>
    <div x-data="confirmForm()" class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 md:p-8">
        <div class="text-center mb-8">
            <div
                class="mx-auto w-16 h-16 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Konfirmasi Kata Sandi</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Silakan konfirmasi kata sandi Anda sebelum melanjutkan ke
                halaman yang aman.</p>
        </div>

        <form @submit.prevent="submitForm">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kata Sandi <span
                        class="text-red-500">*</span></label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" x-model="form.password" required autofocus
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition pl-10 pr-10"
                        :class="{ 'border-red-500': errors.password }" placeholder="Masukkan kata sandi Anda">
                    <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <button type="button" @click="togglePassword"
                        class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                        <span x-text="showPassword ? '🙈' : '👁️'"></span>
                    </button>
                </div>
                <p x-show="errors.password" class="mt-1 text-xs text-red-600" x-text="errors.password"></p>
            </div>

            <button type="submit" :disabled="loading"
                class="w-full py-3 px-4 bg-gradient-to-r from-primary to-secondary hover:from-secondary hover:to-primary text-white font-semibold rounded-xl transition duration-200 disabled:opacity-50 shadow-md">
                <span x-show="!loading">Konfirmasi</span>
                <span x-show="loading" class="flex items-center justify-center">
                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Memproses...
                </span>
            </button>
        </form>
    </div>

    <script>
        function confirmForm() {
            return {
                form: {
                    password: ''
                },
                errors: {},
                loading: false,
                showPassword: false,
                togglePassword() {
                    this.showPassword = !this.showPassword;
                },
                async submitForm() {
                    this.loading = true;
                    this.errors = {};

                    try {
                        const response = await fetch('{{ route('password.confirm') }}', {
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
                            window.location.href = data.redirect || '/dashboard';
                        } else {
                            if (data.errors) this.errors = data.errors;
                            else window.toast.error(data.message || 'Password salah.');
                            this.loading = false;
                        }
                    } catch (error) {
                        window.toast.error('Terjadi kesalahan, silakan coba lagi.');
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-client-layout>
