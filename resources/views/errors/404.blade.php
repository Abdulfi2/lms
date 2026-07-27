<x-error-page
    code="404"
    title="Halaman Tidak Ditemukan"
    message="Halaman yang Anda cari mungkin sudah dipindahkan, dihapus, atau alamatnya salah ketik."
    icon-bg="bg-indigo-100 dark:bg-indigo-900/30"
    icon-color="text-indigo-600 dark:text-indigo-400"
>
    <x-slot:icon>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16zM9.5 9.5a2.5 2.5 0 013.86-2.1M11 14h.01" />
        </svg>
    </x-slot:icon>

    <a href="{{ route('courses.index') }}"
        class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-100 dark:hover:bg-gray-800 transition text-sm">
        Jelajahi Kursus
    </a>
</x-error-page>
