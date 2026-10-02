@extends('layouts.app')

@section('title', __('Profile'))
@section('page-title', __('Profile'))
@section('page-subtitle', __('Kelola informasi akun dan keamanan Anda.'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Profile Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6" x-data="avatarCropper('{{ $user->avatar_url }}')">
        <div class="flex items-center gap-4">
            <div class="relative flex-shrink-0">
                <img :src="currentAvatarUrl" class="w-20 h-20 rounded-full object-cover ring-2 ring-primary/20">
                <button type="button" @click="$refs.fileInput.click()"
                    class="absolute bottom-0 right-0 w-7 h-7 bg-primary text-white rounded-full flex items-center justify-center shadow hover:bg-secondary transition"
                    title="{{ __('Ganti foto profil') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <input type="file" x-ref="fileInput" accept="image/png,image/jpeg,image/webp" class="hidden" @change="onFileSelected">
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-primary/10 text-primary">
                        {{ ucfirst($user->role_name) }}
                    </span>
                    <span class="text-xs text-gray-400">
                        {{ __('Bergabung') }} {{ $user->created_at->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Crop Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/60" @click="closeModal()"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6" @click.stop>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">{{ __('Ganti Foto Profil') }}</h3>

                <div class="rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-900" style="height: 320px;">
                    <img x-ref="cropperImage" class="block max-w-full">
                </div>

                <p x-show="errorMessage" x-text="errorMessage" class="mt-2 text-xs text-red-500"></p>

                <div class="mt-4 flex justify-between items-center">
                    <button type="button" @click="$refs.fileInput.click()" class="text-sm text-primary hover:underline">
                        {{ __('Pilih foto lain') }}
                    </button>
                    <div class="flex gap-2">
                        <button type="button" @click="closeModal()"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="button" @click="save()" :disabled="saving"
                            class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-secondary disabled:opacity-50 transition">
                            <span x-show="!saving">{{ __('Simpan') }}</span>
                            <span x-show="saving">{{ __('Menyimpan...') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-red-100 dark:border-red-900/30">
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
<script>
    function avatarCropper(initialUrl) {
        return {
            currentAvatarUrl: initialUrl,
            showModal: false,
            saving: false,
            errorMessage: '',
            cropper: null,

            onFileSelected(event) {
                const file = event.target.files[0];
                if (!file) return;

                this.errorMessage = '';
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.showModal = true;
                    this.$nextTick(() => {
                        this.$refs.cropperImage.src = e.target.result;

                        if (this.cropper) {
                            this.cropper.destroy();
                        }

                        this.cropper = new Cropper(this.$refs.cropperImage, {
                            aspectRatio: 1,
                            viewMode: 1,
                            background: false,
                            autoCropArea: 1,
                        });
                    });
                };
                reader.readAsDataURL(file);
            },

            closeModal() {
                this.showModal = false;
                this.errorMessage = '';
                if (this.cropper) {
                    this.cropper.destroy();
                    this.cropper = null;
                }
                this.$refs.fileInput.value = '';
            },

            save() {
                if (!this.cropper) return;

                this.saving = true;
                this.errorMessage = '';

                this.cropper.getCroppedCanvas({ width: 400, height: 400 }).toBlob((blob) => {
                    const formData = new FormData();
                    formData.append('avatar', blob, 'avatar.jpg');

                    fetch('{{ route('profile.avatar.update') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    })
                        .then(async (response) => {
                            const data = await response.json();
                            if (!response.ok) {
                                throw new Error(data.message || 'Gagal mengunggah foto.');
                            }
                            return data;
                        })
                        .then((data) => {
                            this.currentAvatarUrl = data.avatar_url;
                            this.closeModal();
                            if (window.toast) {
                                window.toast.success('Foto profil berhasil diubah.');
                            }
                        })
                        .catch((error) => {
                            this.errorMessage = error.message || 'Gagal mengunggah foto. Silakan coba lagi.';
                        })
                        .finally(() => {
                            this.saving = false;
                        });
                }, 'image/jpeg', 0.9);
            },
        };
    }
</script>
@endpush
