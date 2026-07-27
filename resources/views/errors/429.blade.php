<x-error-page
    code="429"
    title="Terlalu Banyak Permintaan"
    message="Anda mengirim permintaan terlalu cepat. Silakan tunggu sebentar lalu coba lagi."
    icon-bg="bg-amber-100 dark:bg-amber-900/30"
    icon-color="text-amber-600 dark:text-amber-500"
>
    <x-slot:icon>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
    </x-slot:icon>
</x-error-page>
