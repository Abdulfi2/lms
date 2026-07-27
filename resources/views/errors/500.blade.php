<x-error-page
    code="500"
    title="Terjadi Kesalahan pada Server"
    message="Maaf, ada yang tidak berjalan semestinya di sisi kami. Tim kami sudah diberi tahu dan sedang menanganinya."
    icon-bg="bg-red-100 dark:bg-red-900/30"
    icon-color="text-red-600 dark:text-red-400"
>
    <x-slot:icon>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    </x-slot:icon>

    <button type="button" onclick="location.reload()"
        class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-100 dark:hover:bg-gray-800 transition text-sm">
        Coba Lagi
    </button>
</x-error-page>
