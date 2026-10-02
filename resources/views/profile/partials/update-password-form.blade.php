<section>
    <header class="flex items-start gap-3 mb-6">
        <span class="flex-shrink-0 w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </span>
        <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                {{ __('Ubah Password') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Gunakan password yang panjang dan acak agar akun tetap aman.') }}
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Password Saat Ini') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
            @error('current_password', 'updatePassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Password Baru') }}</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
            @error('password', 'updatePassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Konfirmasi Password Baru') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
            @error('password_confirmation', 'updatePassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-secondary transition">
                {{ __('Simpan') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 dark:text-green-400"
                >{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
