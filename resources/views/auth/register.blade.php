@extends('layouts.client')

@section('title', $role === 'instructor' ? 'Daftar sebagai Instruktur' : 'Daftar sebagai Student')

@section('content')
    <div x-data="registerForm()" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="grid lg:grid-cols-2">
            <!-- Left Side - Form -->
            <div class="p-6 md:p-8 lg:p-10">
                <div class="mb-8">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $role === 'instructor' ? 'Daftar sebagai Instruktur' : 'Daftar sebagai Student' }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        {{ $role === 'instructor' ? 'Bagikan pengetahuan Anda kepada ribuan siswa' : 'Mulai perjalanan belajar Anda' }}
                    </p>
                </div>

                <form @submit.prevent="submitForm" class="space-y-5">
                    @csrf

                    <!-- Hidden Field untuk Role -->
                    <input type="hidden" name="role" value="{{ $role }}">

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap <span
                                class="text-red-500">*</span></label>
                        <input type="text" x-model="form.name" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-primary focus:border-transparent">
                        <p x-show="errors.name" class="mt-1 text-xs text-red-600" x-text="errors.name"></p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" x-model="form.email" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-primary focus:border-transparent">
                        <p x-show="errors.email" class="mt-1 text-xs text-red-600" x-text="errors.email"></p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kata Sandi <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" x-model="form.password" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-primary focus:border-transparent pr-10">
                            <button type="button" @click="togglePassword" class="absolute right-3 top-3">
                                <span x-text="showPassword ? '🙈' : '👁️'"></span>
                            </button>
                        </div>
                        <p x-show="errors.password" class="mt-1 text-xs text-red-600" x-text="errors.password"></p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Kata Sandi
                            <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'" x-model="form.password_confirmation"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-primary focus:border-transparent pr-10">
                            <button type="button" @click="toggleConfirmPassword" class="absolute right-3 top-3">
                                <span x-text="showConfirmPassword ? '🙈' : '👁️'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start">
                        <input type="checkbox" x-model="form.terms" id="terms" class="mt-1 rounded">
                        <label for="terms" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            Saya setuju dengan <a href="#" class="text-primary hover:underline">Syarat & Ketentuan</a>
                        </label>
                    </div>
                    <p x-show="errors.terms" class="text-xs text-red-600" x-text="errors.terms"></p>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="loading"
                        class="w-full py-3 px-4 bg-gradient-to-r from-primary to-secondary hover:from-secondary hover:to-primary text-white font-semibold rounded-xl transition disabled:opacity-50">
                        <span
                            x-show="!loading">{{ $role === 'instructor' ? 'Daftar sebagai Instruktur' : 'Daftar Sekarang' }}</span>
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
                    @if ($role === 'instructor')
                        <div class="mb-8">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path
                                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">Jadi Instruktur</h3>
                            <p class="text-white/80">Bagikan ilmu dan dapatkan penghasilan tambahan</p>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center"><svg class="w-5 h-5 mr-3" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg><span>Dukungan penuh dari tim</span></div>
                            <div class="flex items-center"><svg class="w-5 h-5 mr-3" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg><span>Bagi hasil menguntungkan</span></div>
                            <div class="flex items-center"><svg class="w-5 h-5 mr-3" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg><span>Jangkau ribuan siswa</span></div>
                        </div>
                    @else
                        <div class="mb-8">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">Mulai Belajar</h3>
                            <p class="text-white/80">Akses ribuan kursus berkualitas</p>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center"><svg class="w-5 h-5 mr-3" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg><span>1000+ Kursus Premium</span></div>
                            <div class="flex items-center"><svg class="w-5 h-5 mr-3" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg><span>Sertifikat Resmi</span></div>
                            <div class="flex items-center"><svg class="w-5 h-5 mr-3" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg><span>Akses Seumur Hidup</span></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
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
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    name: this.form.name,
                                    email: this.form.email,
                                    password: this.form.password,
                                    password_confirmation: this.form.password_confirmation,
                                    role: '{{ $role }}'
                                })
                            });

                            const data = await response.json();

                            if (response.ok) {
                                window.toast.success(data.message || 'Pendaftaran berhasil!');
                                setTimeout(() => {
                                    window.location.href = data.redirect || (data.role === 'instructor' ?
                                        '/instructor/dashboard' : '/verify-email');
                                }, 1500);
                            } else {
                                if (data.errors) this.errors = data.errors;
                                else window.toast.error(data.message || 'Terjadi kesalahan');
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
    @endpush
@endsection
