<x-client-layout>
    <div class="max-w-4xl m-auto">
        <div class="w-full m-6 px-6 py-4">
            <div x-data="loginForm()" class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="grid md:grid-cols-2">
                    <!-- Left Side - Form -->
                    <div class="p-8 md:p-12">
                        <div class="mb-8">
                            <h2 class="text-3xl font-bold text-gray-900">Selamat Datang</h2>
                            <p class="text-gray-600 mt-2">Silakan masuk ke akun Anda</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" @submit.prevent="submitForm">
                            @csrf

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat
                                    Email</label>
                                <input type="email" name="email" id="email" x-model="form.email" required
                                    autofocus
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition @error('email') border-red-500 @enderror"
                                    placeholder="nama@example.com">
                            </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata
                                    Sandi</label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                                        x-model="form.password" required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition @error('password') border-red-500 @enderror"
                                        placeholder="••••••••">
                                    <button type="button" @click="togglePassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <span x-text="showPassword ? '🙈' : '👁️'" class="text-gray-400"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between mb-6">
                                <label class="flex items-center">
                                    <input type="checkbox" name="remember"
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                        class="text-sm text-primary hover:text-secondary">Lupa kata sandi?</a>
                                @endif
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" :disabled="loading"
                                class="w-full py-3 px-4 bg-primary hover:bg-secondary text-white font-semibold rounded-lg transition duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!loading">Masuk</span>
                                <span x-show="loading" class="flex items-center justify-center">
                                    <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>

                            <!-- Register Link -->
                            <div class="mt-6 text-center">
                                <p class="text-sm text-gray-600">
                                    Belum punya akun?
                                    <a href="{{ route('register') }}"
                                        class="text-primary hover:text-secondary font-medium">Daftar sekarang</a>
                                </p>
                            </div>
                        </form>
                    </div>

                    <!-- Right Side - Hero -->
                    <div class="hidden md:block bg-gradient-to-br from-primary to-secondary p-8 text-white">
                        <div class="h-full flex flex-col justify-center">
                            <div class="mb-6">
                                <svg class="w-16 h-16 text-white/80" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-4">Belajar Lebih Mudah</h3>
                            <p class="text-white/80 mb-6">
                                Akses ribuan kursus berkualitas dari instruktur terbaik. Tingkatkan skill Anda bersama
                                LMS.
                            </p>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>1000+ Kursus Premium</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Sertifikat Resmi Zakat Sukses</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
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
        </div>
    </div>

    <script>
        function loginForm() {
            return {
                form: {
                    email: '',
                    password: '',
                    remember: false
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
                        const response = await fetch('{{ route('login') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                email: this.form.email,
                                password: this.form.password,
                                remember: this.form.remember
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            // Login sukses
                            window.toast.success(data.message);
                            setTimeout(() => {
                                window.location.href = data.redirect || '/dashboard';
                            }, 1000);
                        } else {
                            // Cek apakah perlu verifikasi email
                            if (data.need_verification) {
                                window.toast.warning(data.message);
                                setTimeout(() => {
                                    window.location.href = data.redirect; // redirect ke verification.notice
                                }, 1500);
                            } else {
                                // Error biasa (password salah, dll)
                                if (data.message) {
                                    window.toast.error(data.message);
                                }
                                if (data.errors) {
                                    this.errors = data.errors;
                                }
                            }
                            this.loading = false;
                        }
                    } catch (error) {
                        console.error(error);
                        window.toast.error('Terjadi kesalahan jaringan. Silakan coba lagi.');
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-client-layout>
