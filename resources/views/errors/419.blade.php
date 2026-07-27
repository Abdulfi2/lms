<x-error-page
    code="419"
    title="Sesi Halaman Berakhir"
    message="Halaman ini sudah terlalu lama terbuka sehingga sesinya kedaluwarsa. Silakan kembali dan coba lagi."
    icon-bg="bg-orange-100 dark:bg-orange-900/30"
    icon-color="text-orange-600 dark:text-orange-400"
>
    <x-slot:icon>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </x-slot:icon>

    <button type="button" onclick="history.back()"
        class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-100 dark:hover:bg-gray-800 transition text-sm">
        Kembali &amp; Coba Lagi
    </button>
</x-error-page>
