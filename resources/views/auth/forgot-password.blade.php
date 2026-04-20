<x-client-layout>
    <div class="max-w-md m-auto">
        <div class="w-full sm:max-w-md m-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <div x-data="forgotForm()">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Lupa Kata Sandi?</h2>
                <p class="text-sm text-gray-600 text-center mb-6">Masukkan email Anda, kami akan mengirimkan link reset
                    password.</p>

                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" @submit.prevent="submitForm">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" x-model="form.email" required autofocus
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 @error('email') border-red-500 @enderror">
                        <template x-if="errors.email">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.email"></p>
                        </template>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" :disabled="loading"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50">
                        <span x-show="!loading">Kirim Link Reset</span>
                        <span x-show="loading">Mengirim...</span>
                    </button>

                    <div class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="text-sm text-primary hover:text-secondary">Kembali ke
                            halaman login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                            // Tampilkan pesan sukses
                            alert('Link reset password telah dikirim ke email Anda.');
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
