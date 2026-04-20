<x-client-layout>
    <div class="max-w-md m-auto">
        <div class="w-full sm:max-w-md m-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <div x-data="registerForm()" x-init="init()">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Register</h2>

                <form method="POST" action="{{ route('register') }}" @submit.prevent="submitForm">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" id="name" x-model="form.name" required autofocus
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 @error('name') border-red-500 @enderror">
                        <template x-if="errors.name">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.name"></p>
                        </template>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                        <input type="email" name="email" id="email" x-model="form.email" required
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

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi
                            Kata
                            Sandi</label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation"
                                id="password_confirmation" x-model="form.password_confirmation" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <button type="button" @click="toggleConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                <span x-text="showConfirmPassword ? '🙈' : '👁️'"></span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" :disabled="loading"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50">
                        <span x-show="!loading">Daftar</span>
                        <span x-show="loading">Memproses...</span>
                    </button>

                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="text-primary hover:text-secondary">Masuk
                                disini</a>
                        </p>
                    </div>
                </form>
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
                            window.location.href = data.redirect || '/dashboard';
                        } else {
                            if (data.errors) this.errors = data.errors;
                            else alert(data.message);
                            this.loading = false;
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan, silakan coba lagi.');
                        console.log(error);
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-client-layout>
