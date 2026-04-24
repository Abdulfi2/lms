@extends('layouts.app')

@section('title', 'Manajemen Tag')
@section('page-title', 'Tag')
@section('page-subtitle', 'Kelola tag untuk konten')

@section('content')
    <div x-data="tagManager()" x-init="init()" class="space-y-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex space-x-3">
                <input type="text" x-model="filters.search" @input.debounce.300="fetchTags()" placeholder="Cari tag..."
                    class="px-4 py-2 rounded-lg border dark:bg-gray-700">
                <button @click="fetchTags()" class="px-4 py-2 bg-primary text-white rounded-lg">Cari</button>
            </div>
            <a href="{{ route('admin.tags.create') }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">+ Tambah Tag</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Warna</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Penggunaan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="tag in tags" :key="tag.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full"
                                            :style="{ backgroundColor: tag.color || '#3B82F6' }"></div>
                                        <span x-text="tag.name"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm" x-text="tag.slug"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full"
                                        :style="{ backgroundColor: tag.color || '#3B82F6', color: '#fff' }"
                                        x-text="tag.color || '#3B82F6'"></span>
                                </td>
                                <td class="px-6 py-4 text-sm" x-text="tag.usage_count"></td>
                                <td class="px-6 py-4">
                                    <button @click="toggleStatus(tag.id, tag.is_active)"
                                        class="px-2 py-1 text-xs rounded-full"
                                        :class="tag.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                        <span x-text="tag.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a :href="'/admin/tags/' + tag.id + '/edit'" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button @click="confirmDelete(tag.id, tag.name)"
                                        class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="loading">
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                            </td>
                        </tr>
                        <tr x-show="!loading && tags.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Tidak ada data tag.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t dark:border-gray-700 flex justify-between items-center">
                <div class="text-sm text-gray-500">Menampilkan <span x-text="from"></span> - <span x-text="to"></span>
                    dari <span x-text="total"></span> data</div>
                <div class="flex space-x-2">
                    <button @click="prevPage" :disabled="currentPage === 1"
                        class="px-3 py-1 rounded border disabled:opacity-50">Sebelumnya</button>
                    <span x-text="currentPage + ' / ' + lastPage"></span>
                    <button @click="nextPage" :disabled="currentPage === lastPage"
                        class="px-3 py-1 rounded border disabled:opacity-50">Selanjutnya</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function tagManager() {
                return {
                    tags: [],
                    filters: {
                        search: ''
                    },
                    loading: false,
                    currentPage: 1,
                    lastPage: 1,
                    total: 0,
                    from: 0,
                    to: 0,
                    init() {
                        this.fetchTags();
                    },
                    fetchTags() {
                        this.loading = true;
                        const params = new URLSearchParams({
                            search: this.filters.search,
                            page: this.currentPage
                        });
                        fetch(`/admin/tags?${params.toString()}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.tags = data.data;
                                this.currentPage = data.current_page;
                                this.lastPage = data.last_page;
                                this.total = data.total;
                                this.from = data.from;
                                this.to = data.to;
                                this.loading = false;
                            }).catch(() => this.loading = false);
                    },
                    prevPage() {
                        if (this.currentPage > 1) {
                            this.currentPage--;
                            this.fetchTags();
                        }
                    },
                    nextPage() {
                        if (this.currentPage < this.lastPage) {
                            this.currentPage++;
                            this.fetchTags();
                        }
                    },
                    toggleStatus(id, currentStatus) {
                        fetch(`/admin/tags/${id}/toggle-status`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    const idx = this.tags.findIndex(t => t.id === id);
                                    if (idx !== -1) this.tags[idx].is_active = data.is_active;
                                } else window.toast.error(data.message);
                            });
                    },
                    confirmDelete(id, name) {
                        if (confirm(`Yakin ingin menghapus tag "${name}"?`)) {
                            fetch(`/admin/tags/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        window.toast.success(data.message);
                                        this.fetchTags();
                                    } else window.toast.error(data.message);
                                });
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
