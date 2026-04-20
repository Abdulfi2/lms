<x-client-layout>
    <div class="max-w-md m-auto">
        <div class="w-full sm:max-w-md m-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">

            <div x-data="confirmForm()">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Konfirmasi Kata Sandi</h2>
                <p class="text-sm text-gray-600 text-center mb-6">Silakan konfirmasi kata sandi Anda sebelum melanjutkan.
                </p>

                <form method="POST" action="{{ route('password.confirm') }}" @submit.prevent="submitForm">
                    @csrf

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                                x-model="form.password" required autofocus
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 @error('password') border-red-500 @enderror">
                            <button type="button" @click="togglePassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                <span x-text="showPassword ? '🙈' : '👁️'"></span>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" :disabled="loading"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50">
                        <span x-show="!loading">Konfirmasi</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmForm() {
            return {
                form: {
                    password: ''
                },
                loading: false,
                showPassword: false,
                togglePassword() {
                    this.showPassword = !this.showPassword;
                },
                async submitForm() {
                    this.loading = true;
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
                            alert(data.message || 'Password salah.');
                            this.loading = false;
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan.');
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-client-layout>
