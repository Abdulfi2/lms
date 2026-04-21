<x-client-layout>
    <div x-data="registerForm()" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="grid lg:grid-cols-2">
            <!-- Left Side - Form -->
            <div class="p-6 md:p-8 lg:p-10">
                <div class="mb-8">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Buat Akun Baru</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Mulai perjalanan belajar Anda</p>
                </div>

                <form @submit.prevent="submitForm" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" x-model="form.name" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition pl-10"
                                :class="{ 'border-red-500': errors.name }" placeholder="John Doe">
                            <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <p x-show="errors.name" class="mt-1 text-xs text-red-600" x-text="errors.name"></p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Email
                            <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="email" x-model="form.email" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition pl-10"
                                :class="{ 'border-red-500': errors.email }" placeholder="nama@example.com">
                            <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <p x-show="errors.email" class="mt-1 text-xs text-red-600" x-text="errors.email"></p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kata Sandi <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" x-model="form.password" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition pl-10 pr-10"
                                :class="{ 'border-red-500': errors.password }" placeholder="••••••••">
                            <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
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

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Kata
                            Sandi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'"
                                x-model="form.password_confirmation" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition pl-10 pr-10"
                                placeholder="••••••••">
                            <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <button type="button" @click="toggleConfirmPassword"
                                class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                                <span x-text="showConfirmPassword ? '🙈' : '👁️'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="flex items-start">
                        <input type="checkbox" x-model="form.terms" id="terms"
                            class="mt-1 rounded border-gray-300 text-primary focus:ring-primary">
                        <label for="terms" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            Saya setuju dengan <a href="#" class="text-primary hover:underline">Syarat &
                                Ketentuan</a> dan <a href="#" class="text-primary hover:underline">Kebijakan
                                Privasi</a>
                        </label>
                    </div>
                    <p x-show="errors.terms" class="text-xs text-red-600" x-text="errors.terms"></p>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="loading"
                        class="w-full py-3 px-4 bg-gradient-to-r from-primary to-secondary hover:from-secondary hover:to-primary text-white font-semibold rounded-xl transition duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed shadow-md">
                        <span x-show="!loading">Daftar Sekarang</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Memproses...
                        </span>
                    </button>

                    <!-- Login Link -->
                    <div class="text-center pt-2">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="text-primary hover:text-secondary font-medium">Masuk
                                disini</a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Right Side - Info -->
            <div class="hidden lg:block bg-gradient-to-br from-primary to-secondary p-8 text-white">
                <div class="h-full flex flex-col justify-center">
                    <div class="mb-8">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">Bergabung Sekarang!</h3>
                        <p class="text-white/80">Dapatkan akses ke ribuan kursus berkualitas</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-white/80" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>1000+ Kursus Premium</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-white/80" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Sertifikat Resmi Zakat Sukses</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-white/80" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Akses Seumur Hidup</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-white/80" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Belajar Fleksibel</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function registerForm() {
            return {
                form: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    terms: false
                },
                errors: {},
                loading: false,
                showPassword: false,
                showConfirmPassword: false,
                togglePassword() {
                    this.showPassword = !this.showPassword;
                },
                toggleConfirmPassword() {
                    this.showConfirmPassword = !this.showConfirmPassword;
                },
                async submitForm() {
                    this.loading = true;
                    this.errors = {};

                    if (!this.form.terms) {
                        this.errors.terms = 'Anda harus menyetujui syarat & ketentuan.';
                        this.loading = false;
                        return;
                    }

                    try {
                        const response = await fetch('{{ route('register') }}', {
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
                            window.toast.success('Pendaftaran berhasil! Silakan verifikasi email Anda.');
                            setTimeout(() => {
                                window.location.href = data.redirect || '/verify-email';
                            }, 1500);
                        } else {
                            if (data.errors) this.errors = data.errors;
                            else window.toast.error(data.message);
                            this.loading = false;
                        }
                    } catch (error) {
                        window.toast.error('Terjadi kesalahan. Silakan coba lagi.');
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-client-layout>
