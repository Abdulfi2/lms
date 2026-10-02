@extends('layouts.app')

@section('title', 'Tambah Tag')
@section('page-title', 'Tambah Tag Baru')

@section('content')
    <div x-data="tagForm()" class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form @submit.prevent="submitForm">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Tag <span
                            class="text-red-500">*</span></label>
                    <input type="text" x-model="form.name" required
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <p x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug (biarkan kosong untuk
                        auto-generate)</label>
                    <input type="text" x-model="form.slug"
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea x-model="form.description" rows="3"
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Warna (Hex)</label>
                    <input type="color" x-model="form.color" value="#3B82F6" class="mt-1 w-full h-10 rounded border">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" x-model="form.is_active" class="rounded border-gray-300">
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</label>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.tags.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
                <button type="submit" :disabled="loading"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary disabled:opacity-50">
                    <span x-show="!loading">Simpan</span>
                    <span x-show="loading">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function tagForm() {
                return {
                    form: {
                        name: '',
                        slug: '',
                        description: '',
                        color: '#3B82F6',
                        is_active: true
                    },
                    errors: {},
                    loading: false,
                    submitForm() {
                        this.loading = true;
                        this.errors = {};
                        fetch('{{ route('admin.tags.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(this.form)
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    setTimeout(() => {
                                        window.location.href = '{{ route('admin.tags.index') }}';
                                    }, 800);
                                } else {
                                    if (data.errors) this.errors = data.errors;
                                    else window.toast.error(data.message);
                                    this.loading = false;
                                }
                            }).catch(() => {
                                window.toast.error('Terjadi kesalahan');
                                this.loading = false;
                            });
                    }
                }
            }
        </script>
    @endpush
@endsection
