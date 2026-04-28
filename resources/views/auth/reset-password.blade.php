@extends('layouts.client')

@section('content')
    <div x-data="resetPasswordForm()" x-init="init()"
        class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <!-- Header dengan icon -->
        <div class="bg-gradient-to-r from-primary to-secondary px-6 py-8 text-center">
            <div class="mx-auto w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-3">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                    </path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Buat Password Baru</h1>
            <p class="text-white/80 text-sm mt-1">Masukkan password baru untuk akun Anda</p>
        </div>

        <div class="p-6 md:p-8">
            <!-- Error Message dari Server (Session Flash) -->
            <div x-show="sessionError" x-text="sessionError"
                class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-xl text-sm"></div>

            <!-- Success Message -->
            <div x-show="sessionSuccess" x-text="sessionSuccess"
                class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-xl text-sm">
            </div>

            <!-- Warning Message (Token Expired) -->
            <div x-show="tokenExpired" class="mb-4 p-4 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-yellow-800 dark:text-yellow-400 font-medium">Link Reset Password Kadaluarsa</span>
                </div>
                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                    Link reset password sudah kadaluarsa atau tidak valid. Silakan request ulang.
                </p>
                <a href="{{ route('password.request') }}"
                    class="inline-block mt-3 text-sm text-yellow-800 dark:text-yellow-400 font-medium hover:underline">
                    ↻ Request Ulang Reset Password
                </a>
            </div>

            <!-- Form Reset Password (hanya tampil jika token valid) -->
            <form x-show="!tokenExpired" @submit.prevent="submitForm" class="space-y-5">
                @csrf
                <input type="hidden" name="token" x-model="form.token">
                <input type="hidden" name="email" x-model="form.email">

                <!-- Email (Readonly) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Email</label>
                    <div class="relative">
                        <input type="email" x-model="form.email" readonly
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white bg-gray-100 dark:bg-gray-800 cursor-not-allowed pl-10"
                            :class="{ 'border-red-500': errors.email }">
                        <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <p x-show="errors.email" class="mt-1 text-xs text-red-600" x-text="errors.email"></p>
                </div>

                <!-- Password Strength Indicator -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password Baru <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" x-model="form.password"
                            @input="checkPasswordStrength" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition pl-10 pr-10"
                            :class="{ 'border-red-500': errors.password }" placeholder="Masukkan password baru">
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

                    <!-- Password Strength Bar -->
                    <div class="mt-2">
                        <div class="flex items-center space-x-1">
                            <div class="h-1 flex-1 rounded-full"
                                :class="strength >= 1 ? (strength >= 4 ? 'bg-green-500' : (strength >= 2 ? 'bg-yellow-500' :
                                    'bg-red-500')) : 'bg-gray-200'">
                            </div>
                            <div class="h-1 flex-1 rounded-full"
                                :class="strength >= 2 ? (strength >= 4 ? 'bg-green-500' : (strength >= 2 ? 'bg-yellow-500' :
                                    'bg-red-500')) : 'bg-gray-200'">
                            </div>
                            <div class="h-1 flex-1 rounded-full"
                                :class="strength >= 3 ? (strength >= 4 ? 'bg-green-500' : 'bg-yellow-500') : 'bg-gray-200'">
                            </div>
                            <div class="h-1 flex-1 rounded-full" :class="strength >= 4 ? 'bg-green-500' : 'bg-gray-200'">
                            </div>
                        </div>
                        <p class="text-xs mt-1"
                            :class="strength >= 4 ? 'text-green-600' : (strength >= 2 ? 'text-yellow-600' : 'text-gray-500')"
                            x-text="strengthText"></p>
                    </div>

                    <p x-show="errors.password" class="mt-1 text-xs text-red-600" x-text="errors.password"></p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Password
                        Baru <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input :type="showConfirmPassword ? 'text' : 'password'" x-model="form.password_confirmation"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition pl-10 pr-10"
                            :class="{ 'border-red-500': errors.password_confirmation }"
                            placeholder="Konfirmasi password baru">
                        <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                        <button type="button" @click="toggleConfirmPassword"
                            class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                            <span x-text="showConfirmPassword ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                    <p x-show="errors.password_confirmation" class="mt-1 text-xs text-red-600"
                        x-text="errors.password_confirmation"></p>
                </div>

                <!-- Password Requirements -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 space-y-1">
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Password harus mengandung:</p>
                    <div class="flex items-center text-xs" :class="hasMinLength ? 'text-green-600' : 'text-gray-500'">
                        <svg class="w-3 h-3 mr-1" :class="hasMinLength ? 'text-green-500' : 'text-gray-400'"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Minimal 8 karakter</span>
                    </div>
                    <div class="flex items-center text-xs" :class="hasUppercase ? 'text-green-600' : 'text-gray-500'">
                        <svg class="w-3 h-3 mr-1" :class="hasUppercase ? 'text-green-500' : 'text-gray-400'"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Minimal 1 huruf besar</span>
                    </div>
                    <div class="flex items-center text-xs" :class="hasLowercase ? 'text-green-600' : 'text-gray-500'">
                        <svg class="w-3 h-3 mr-1" :class="hasLowercase ? 'text-green-500' : 'text-gray-400'"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Minimal 1 huruf kecil</span>
                    </div>
                    <div class="flex items-center text-xs" :class="hasNumber ? 'text-green-600' : 'text-gray-500'">
                        <svg class="w-3 h-3 mr-1" :class="hasNumber ? 'text-green-500' : 'text-gray-400'"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Minimal 1 angka</span>
                    </div>
                    <div class="flex items-center text-xs" :class="hasSpecialChar ? 'text-green-600' : 'text-gray-500'">
                        <svg class="w-3 h-3 mr-1" :class="hasSpecialChar ? 'text-green-500' : 'text-gray-400'"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Minimal 1 simbol (@$!%*?&)</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" :disabled="loading"
                    class="w-full py-3 px-4 bg-gradient-to-r from-primary to-secondary hover:from-secondary hover:to-primary text-white font-semibold rounded-xl transition duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed shadow-md mt-4">
                    <span x-show="!loading">Reset Password</span>
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

            <!-- Link Back to Login -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-primary hover:text-secondary inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke halaman login
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function resetPasswordForm() {
                return {
                    form: {
                        token: '{{ $token ?? '' }}',
                        email: '{{ $email ?? '' }}',
                        password: '',
                        password_confirmation: ''
                    },
                    errors: {},
                    loading: false,
                    showPassword: false,
                    showConfirmPassword: false,
                    tokenExpired: false,
                    sessionError: '',
                    sessionSuccess: '',

                    // Password strength properties
                    strength: 0,
                    strengthText: '',
                    hasMinLength: false,
                    hasUppercase: false,
                    hasLowercase: false,
                    hasNumber: false,
                    hasSpecialChar: false,

                    init() {
                        // Check if token is empty (expired/invalid)
                        if (!this.form.token || !this.form.email) {
                            this.tokenExpired = true;
                        }

                        // Get session messages
                        @if (session('error'))
                            this.sessionError = '{{ session('error') }}';
                            this.tokenExpired = true;
                        @endif

                        @if (session('status'))
                            this.sessionSuccess = '{{ session('status') }}';
                        @endif
                    },

                    togglePassword() {
                        this.showPassword = !this.showPassword;
                    },
                    toggleConfirmPassword() {
                        this.showConfirmPassword = !this.showConfirmPassword;
                    },

                    checkPasswordStrength() {
                        const password = this.form.password;

                        this.hasMinLength = password.length >= 8;
                        this.hasUppercase = /[A-Z]/.test(password);
                        this.hasLowercase = /[a-z]/.test(password);
                        this.hasNumber = /[0-9]/.test(password);
                        this.hasSpecialChar = /[@$!%*?&]/.test(password);

                        // Calculate strength (0-4)
                        let count = 0;
                        if (this.hasMinLength) count++;
                        if (this.hasUppercase) count++;
                        if (this.hasLowercase) count++;
                        if (this.hasNumber) count++;
                        if (this.hasSpecialChar) count++;

                        this.strength = count;

                        if (count >= 4) {
                            this.strengthText = 'Kuat';
                        } else if (count >= 2) {
                            this.strengthText = 'Sedang';
                        } else if (count > 0) {
                            this.strengthText = 'Lemah';
                        } else {
                            this.strengthText = '';
                        }
                    },

                    async submitForm() {
                        this.loading = true;
                        this.errors = {};

                        // Check password match
                        if (this.form.password !== this.form.password_confirmation) {
                            this.errors.password_confirmation = 'Konfirmasi password tidak cocok.';
                            this.loading = false;
                            return;
                        }

                        // Check password strength
                        if (this.strength < 4) {
                            this.errors.password = 'Password terlalu lemah. Gunakan kombinasi yang lebih kuat.';
                            this.loading = false;
                            return;
                        }

                        try {
                            const response = await fetch('{{ route('password.update') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    token: this.form.token,
                                    email: this.form.email,
                                    password: this.form.password,
                                    password_confirmation: this.form.password_confirmation
                                })
                            });

                            const data = await response.json();

                            if (response.ok) {
                                window.toast.success('Password berhasil direset! Silakan login.');
                                setTimeout(() => {
                                    window.location.href = data.redirect || '{{ route('login') }}';
                                }, 1500);
                            } else {
                                if (data.is_expired) {
                                    this.tokenExpired = true;
                                    window.toast.error(data.message);
                                } else if (data.errors) {
                                    this.errors = data.errors;
                                    window.toast.error('Periksa kembali input Anda.');
                                } else {
                                    window.toast.error(data.message || 'Terjadi kesalahan, silakan coba lagi.');
                                }
                                this.loading = false;
                            }
                        } catch (error) {
                            console.error(error);
                            window.toast.error('Terjadi kesalahan, silakan coba lagi.');
                            this.loading = false;
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
