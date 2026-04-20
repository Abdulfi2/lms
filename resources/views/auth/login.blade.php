<x-client-layout>
    <div class="max-w-md m-auto">
        <div class="w-full sm:max-w-md m-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <form method="POST" action="{{ route('login') }}" @submit.prevent="submitForm">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                    <input type="email" name="email" id="email" x-model="form.email" required autofocus
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 @error('email') border-red-500 @enderror">
                    <template x-if="errors.email">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.email"></p>
                    </template>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                            x-model="form.password" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 @error('password') border-red-500 @enderror">
                        <button type="button" @click="togglePassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                            <span x-text="showPassword ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                    <template x-if="errors.password">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.password"></p>
                    </template>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember"
                            class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-primary hover:text-secondary">Lupa
                            kata sandi?</a>
                    @endif
                </div>

                <button type="submit" :disabled="loading"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50">
                    <span x-show="!loading">Masuk</span>
                    <span x-show="loading">Memproses...</span>
                </button>

                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-primary hover:text-secondary">Daftar sekarang</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        function loginForm() {
            return {
                form: {
                    email: '',
                    password: '',
                },
                errors: {},
                loading: false,
                showPassword: false,
                init() {
                    // Optional: prefill email if you want
                },
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
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();

                        if (response.ok) {
                            window.location.href = data.redirect || '/dashboard';
                        } else {
                            if (data.errors) {
                                this.errors = data.errors;
                            } else if (data.message) {
                                // tampilkan toast atau alert
                                alert(data.message);
                            }
                            this.loading = false;
                        }
                    } catch (error) {
                        console.error(error);
                        alert('Terjadi kesalahan, silakan coba lagi.');
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-client-layout>
