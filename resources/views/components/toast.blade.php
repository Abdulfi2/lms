{{-- resources/views/components/toast.blade.php --}}
@props(['position' => 'top-right'])

<div x-data="toastHandler()" x-init="init()" @toast.window="showToast($event.detail)" class="fixed z-50"
    :class="{
        'top-4 right-4': position === 'top-right',
        'top-4 left-4': position === 'top-left',
        'bottom-4 right-4': position === 'bottom-right',
        'bottom-4 left-4': position === 'bottom-left',
        'top-4 left-1/2 transform -translate-x-1/2': position === 'top-center',
        'bottom-4 left-1/2 transform -translate-x-1/2': position === 'bottom-center'
    }">

    <template x-for="(toast, index) in toasts" :key="index">
        <div x-show="visible[index]" x-transition.duration.300ms
            class="mb-3 min-w-[300px] max-w-md rounded-lg shadow-lg overflow-hidden"
            :class="{
                'bg-green-500': toast.type === 'success',
                'bg-red-500': toast.type === 'error',
                'bg-blue-500': toast.type === 'info',
                'bg-yellow-500': toast.type === 'warning',
                'bg-gray-800': toast.type === 'dark'
            }">

            <div class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <!-- Icon -->
                    <div x-show="toast.type === 'success'" class="text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div x-show="toast.type === 'error'" class="text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div x-show="toast.type === 'warning'" class="text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div x-show="toast.type === 'info'" class="text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>

                    <span class="text-white text-sm font-medium" x-text="toast.message"></span>
                </div>

                <button @click="removeToast(index)" class="text-white hover:text-gray-200 transition">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- Progress Bar -->
            <div x-show="toast.showProgress !== false" class="h-1 bg-white/30">
                <div class="h-full bg-white" x-init="() => {
                    const el = $el;
                    el.style.width = '100%';
                    const startTime = Date.now();
                    const duration = toast.duration || 3000;
                    const interval = setInterval(() => {
                        const elapsed = Date.now() - startTime;
                        const remaining = Math.max(0, duration - elapsed);
                        const percentage = (remaining / duration) * 100;
                        el.style.width = percentage + '%';
                        if (percentage <= 0) {
                            clearInterval(interval);
                            removeToast(index);
                        }
                    }, 16);
                }">
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    // ✅ Gunakan flag untuk mencegah multiple initialization
    let toastInitialized = false;
    let globalToastHandlers = [];

    function toastHandler() {
        return {
            toasts: [],
            visible: [],
            position: '{{ $position }}',
            init() {
                // Hanya register event listener sekali
                if (!toastInitialized) {
                    toastInitialized = true;

                    // Bersihkan listener lama jika ada
                    window.removeEventListener('toast', this.showToast);
                    window.addEventListener('toast', (event) => {
                        this.showToast(event.detail);
                    });
                }
            },
            showToast(toastData) {
                // Prevent duplicate toast dalam 100ms
                const now = Date.now();
                if (this.lastToastTime && (now - this.lastToastTime) < 100) {
                    return;
                }
                this.lastToastTime = now;

                const defaultToast = {
                    message: toastData.message || 'Notification',
                    type: toastData.type || 'info',
                    duration: toastData.duration || 3000,
                    showProgress: toastData.showProgress !== false,
                };

                const index = this.toasts.length;
                this.toasts.push(defaultToast);
                this.visible[index] = true;

                if (defaultToast.duration > 0) {
                    setTimeout(() => {
                        this.removeToast(index);
                    }, defaultToast.duration);
                }
            },
            removeToast(index) {
                if (!this.visible[index]) return;
                this.visible[index] = false;
                setTimeout(() => {
                    this.toasts.splice(index, 1);
                    this.visible.splice(index, 1);
                }, 300);
            }
        }
    }

    // ✅ Global helper dengan debounce untuk mencegah multiple calls
    let lastToastCall = 0;

    window.toast = {
        success: (message, duration = 3000) => {
            // Debounce: cegah multiple toast dalam 50ms
            const now = Date.now();
            if (now - lastToastCall < 50) return;
            lastToastCall = now;

            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    message,
                    type: 'success',
                    duration
                }
            }));
        },
        error: (message, duration = 3000) => {
            const now = Date.now();
            if (now - lastToastCall < 50) return;
            lastToastCall = now;

            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    message,
                    type: 'error',
                    duration
                }
            }));
        },
        warning: (message, duration = 3000) => {
            const now = Date.now();
            if (now - lastToastCall < 50) return;
            lastToastCall = now;

            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    message,
                    type: 'warning',
                    duration
                }
            }));
        },
        info: (message, duration = 3000) => {
            const now = Date.now();
            if (now - lastToastCall < 50) return;
            lastToastCall = now;

            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    message,
                    type: 'info',
                    duration
                }
            }));
        }
    };
</script>
