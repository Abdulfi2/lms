<x-client-layout>
    <div class="max-w-md m-auto">
        <div class="w-full sm:max-w-md m-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <div x-data="resetForm()">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Reset Kata Sandi</h2>

                <form method="POST" action="{{ route('password.update') }}" @submit.prevent="submitForm">
                    @csrf
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" x-model="form.email" required
                            value="{{ request()->email }}" readonly
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru</label>
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
                            Kata Sandi Baru</label>
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
                        <span x-show="!loading">Reset Password</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function resetForm() {
            return {
                form: {
                    email: '{{ request()->email }}',
                    password: '',
                    password_confirmation: '',
                    token: '{{ request()->route('token') }}'
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
                        const response = await fetch('{{ route('password.update') }}', {
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
                            alert('Password berhasil direset. Silakan login.');
                            window.location.href = '{{ route('login') }}';
                        } else {
                            if (data.errors) this.errors = data.errors;
                            else alert(data.message);
                            this.loading = false;
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan, silakan coba lagi.');
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-client-layout>
