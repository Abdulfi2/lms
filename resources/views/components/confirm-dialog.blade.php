{{-- resources/views/components/confirm-dialog.blade.php --}}
{{-- Pengganti window.confirm() bawaan browser yang tampilannya kaku/tidak konsisten dengan tema
     aplikasi. Dipakai lewat: const ok = await window.confirmDialog('Pesan...', { title, confirmText,
     cancelText, danger: true }); — mengembalikan Promise<boolean>. --}}
<div x-data="confirmDialogHandler()" x-init="init()" x-show="visible" x-cloak
    class="fixed inset-0 z-[60] flex items-center justify-center p-4"
    @keydown.escape.window="cancel()">
    <div class="absolute inset-0 bg-black/50" x-show="visible" x-transition.opacity.duration.200ms @click="cancel()"></div>

    <div x-show="visible" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-sm w-full p-6">
        <div class="flex items-start space-x-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                :class="danger ? 'bg-red-100 dark:bg-red-900/40 text-red-600' : 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-600'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-800 dark:text-white" x-text="title"></h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="message"></p>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" @click="cancel()"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                x-text="cancelText"></button>
            <button type="button" @click="confirm()"
                class="px-4 py-2 rounded-lg text-sm font-medium text-white transition"
                :class="danger ? 'bg-red-600 hover:bg-red-700' : 'bg-primary hover:bg-secondary'"
                x-text="confirmText"></button>
        </div>
    </div>
</div>

<script>
    function confirmDialogHandler() {
        return {
            visible: false,
            title: 'Konfirmasi',
            message: '',
            confirmText: 'OK',
            cancelText: 'Batal',
            danger: false,
            _resolve: null,
            init() {
                window.__confirmDialogInstance = this;
            },
            show(message, options = {}) {
                this.message = message;
                this.title = options.title || 'Konfirmasi';
                this.confirmText = options.confirmText || 'OK';
                this.cancelText = options.cancelText || 'Batal';
                this.danger = options.danger || false;
                this.visible = true;
                return new Promise((resolve) => {
                    this._resolve = resolve;
                });
            },
            confirm() {
                this.visible = false;
                if (this._resolve) this._resolve(true);
            },
            cancel() {
                this.visible = false;
                if (this._resolve) this._resolve(false);
            },
        };
    }

    window.confirmDialog = (message, options) => {
        if (!window.__confirmDialogInstance) {
            // Fallback kalau komponen belum sempat ter-init (seharusnya tidak pernah terjadi
            // karena komponen ini dimuat di layout utama).
            return Promise.resolve(confirm(message));
        }
        return window.__confirmDialogInstance.show(message, options);
    };
</script>
