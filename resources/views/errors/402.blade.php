<x-error-page
    code="402"
    title="Pembayaran Diperlukan"
    message="Selesaikan pembayaran terlebih dahulu untuk mengakses konten ini."
    icon-bg="bg-yellow-100 dark:bg-yellow-900/30"
    icon-color="text-yellow-600 dark:text-yellow-500"
    :custom-message="$exception->getMessage() ?: null"
>
    <x-slot:icon>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
    </x-slot:icon>
</x-error-page>
