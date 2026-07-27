<x-error-page
    code="401"
    title="Perlu Masuk Terlebih Dahulu"
    message="Sesi Anda sudah berakhir atau Anda belum masuk. Silakan masuk untuk melanjutkan."
    icon-bg="bg-blue-100 dark:bg-blue-900/30"
    icon-color="text-blue-600 dark:text-blue-400"
>
    <x-slot:icon>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
    </x-slot:icon>
</x-error-page>
