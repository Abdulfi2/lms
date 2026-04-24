{{-- resources/views/admin/categories/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Manajemen Kategori')
@section('page-title', 'Kategori Kursus')
@section('page-subtitle', 'Kelola kategori untuk mengelompokkan kursus')

@section('content')
    <div x-data="categoryManager()" x-init="init()" class="space-y-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex space-x-3">
                <input type="text" x-model="filters.search" @input.debounce.300="fetchCategories()"
                    placeholder="Cari kategori..." class="px-4 py-2 rounded-lg border dark:bg-gray-700">
                <button @click="fetchCategories()" class="px-4 py-2 bg-primary text-white rounded-lg">Cari</button>
            </div>
            <a href="{{ route('admin.categories.create') }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">+ Tambah Kategori</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Parent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Urutan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="cat in categories" :key="cat.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <i x-show="cat.icon" :class="cat.icon"></i>
                                        <span x-text="cat.name"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm" x-text="cat.slug"></td>
                                <td class="px-6 py-4 text-sm" x-text="cat.parent_name || '-'"></td>
                                <td class="px-6 py-4 text-sm" x-text="cat.courses_count"></td>
                                <td class="px-6 py-4 text-sm" x-text="cat.order"></td>
                                <td class="px-6 py-4">
                                    <button @click="toggleStatus(cat.id, cat.is_active)"
                                        class="px-2 py-1 text-xs rounded-full"
                                        :class="cat.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                        <span x-text="cat.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a :href="'/admin/categories/' + cat.id + '/edit'"
                                        class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button @click="confirmDelete(cat.id, cat.name)"
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
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                            </td>
                        </tr>
                        <tr x-show="!loading && categories.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Tidak ada data kategori.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t dark:border-gray-700 flex justify-between items-center">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <span x-text="from"></span> - <span x-text="to"></span> dari <span
                        x-text="total"></span> data
                </div>
                <div class="flex space-x-2">
                    <button @click="prevPage" :disabled="currentPage === 1"
                        class="px-3 py-1 rounded border dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50">
                        Sebelumnya
                    </button>
                    <span class="px-3 py-1" x-text="currentPage + ' / ' + lastPage"></span>
                    <button @click="nextPage" :disabled="currentPage === lastPage"
                        class="px-3 py-1 rounded border dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function categoryManager() {
                return {
                    categories: [],
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
                        this.fetchCategories();
                    },

                    fetchCategories() {
                        this.loading = true;
                        const params = new URLSearchParams({
                            search: this.filters.search,
                            page: this.currentPage
                        });

                        fetch(`/admin/categories?${params.toString()}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.categories = data.data;
                                this.currentPage = data.current_page;
                                this.lastPage = data.last_page;
                                this.total = data.total;
                                this.from = data.from;
                                this.to = data.to;
                                this.loading = false;
                            })
                            .catch(err => {
                                console.error(err);
                                this.loading = false;
                            });
                    },

                    prevPage() {
                        if (this.currentPage > 1) {
                            this.currentPage--;
                            this.fetchCategories();
                        }
                    },

                    nextPage() {
                        if (this.currentPage < this.lastPage) {
                            this.currentPage++;
                            this.fetchCategories();
                        }
                    },

                    toggleStatus(id, currentStatus) {
                        fetch(`/admin/categories/${id}/toggle-status`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({})
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    // Update local data tanpa reload
                                    const index = this.categories.findIndex(c => c.id === id);
                                    if (index !== -1) {
                                        this.categories[index].is_active = data.is_active;
                                    }
                                } else {
                                    window.toast.error(data.message);
                                }
                            })
                            .catch(() => window.toast.error('Gagal mengubah status'));
                    },

                    confirmDelete(id, name) {
                        if (confirm(`Yakin ingin menghapus kategori "${name}"?`)) {
                            fetch(`/admin/categories/${id}`, {
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
                                        this.fetchCategories(); // refresh list
                                    } else {
                                        window.toast.error(data.message);
                                    }
                                });
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
