<section>
    <div class="flex items-start gap-3">
        <span class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </span>
        <div class="flex-1">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                {{ __('Hapus Akun') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Setelah akun dihapus, seluruh data terkait akan dihapus permanen. Unduh data yang ingin Anda simpan sebelum melanjutkan.') }}
            </p>

            <button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition"
            >{{ __('Hapus Akun') }}</button>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                {{ __('Yakin ingin menghapus akun Anda?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Setelah akun dihapus, seluruh data terkait akan dihapus permanen. Masukkan password Anda untuk konfirmasi.') }}
            </p>

            <div class="mt-4">
                <label for="password" class="sr-only">{{ __('Password') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="{{ __('Password') }}"
                    class="w-full sm:w-3/4 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 transition"
                >
                @error('password', 'userDeletion') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    {{ __('Batal') }}
                </button>

                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">
                    {{ __('Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
