@extends('layouts.app')

@section('title', 'Manajemen Role')
@section('page-title', 'Role')
@section('page-subtitle', 'Kelola role dan permission pengguna')

@section('content')
    <div x-data="roleManager()" x-init="init()" class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="relative">
                <input type="text" x-model="filters.search" @input.debounce.300="fetchRoles()" placeholder="Cari role..."
                    class="w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <a href="{{ route('admin.roles.create') }}"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Role
            </a>
        </div>

        <!-- Roles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="role in roles" :key="role.id">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                    :class="{
                                        'bg-red-100 text-red-600': role.name === 'admin',
                                        'bg-blue-100 text-blue-600': role.name === 'instructor',
                                        'bg-green-100 text-green-600': role.name === 'student',
                                        'bg-gray-100 text-gray-600': !['admin', 'instructor', 'student'].includes(role
                                            .name)
                                    }">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 dark:text-white" x-text="role.name"></h3>
                                    <p class="text-xs text-gray-500" x-text="role.guard_name"></p>
                                </div>
                            </div>
                            <div class="flex space-x-1">
                                <a :href="'/admin/roles/' + role.id + '/edit'"
                                    class="p-1 text-yellow-600 hover:text-yellow-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </a>
                                <button @click="confirmDelete(role.id, role.name)"
                                    class="p-1 text-red-600 hover:text-red-800"
                                    x-show="role.name !== 'admin' && role.name !== 'instructor' && role.name !== 'student'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-400">Jumlah User</span>
                                <span class="font-semibold" x-text="role.users_count || 0"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full"
                                    :style="'width: ' + Math.min((role.users_count || 0) * 5, 100) + '%'"></div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-1 mt-3">
                            <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 rounded-full"
                                x-text="role.permissions_count + ' permissions'"></span>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="roles.length === 0 && !loading" class="col-span-full text-center py-12 text-gray-500">
                Tidak ada data role.
            </div>
        </div>

        <!-- Pagination -->
        <div x-show="lastPage > 1" class="flex justify-center space-x-2">
            <button @click="prevPage" :disabled="currentPage === 1"
                class="px-3 py-1 rounded border hover:bg-gray-100 disabled:opacity-50">← Sebelumnya</button>
            <span class="px-3 py-1" x-text="currentPage + ' / ' + lastPage"></span>
            <button @click="nextPage" :disabled="currentPage === lastPage"
                class="px-3 py-1 rounded border hover:bg-gray-100 disabled:opacity-50">Selanjutnya →</button>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="deleteModalOpen = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
                <div class="text-center">
                    <div class="mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium">Hapus Role</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus role <span x-text="deleteRoleName"
                            class="font-semibold"></span>?
                    </p>
                    <div class="mt-5 flex justify-center space-x-3">
                        <button @click="deleteModalOpen = false" class="px-4 py-2 border rounded-lg">Batal</button>
                        <button @click="deleteRole" class="px-4 py-2 bg-red-600 text-white rounded-lg">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function roleManager() {
                return {
                    roles: [],
                    filters: {
                        search: ''
                    },
                    loading: false,
                    currentPage: 1,
                    lastPage: 1,
                    deleteModalOpen: false,
                    deleteRoleId: null,
                    deleteRoleName: '',

                    init() {
                        this.fetchRoles();
                    },

                    fetchRoles() {
                        this.loading = true;
                        const params = new URLSearchParams({
                            search: this.filters.search,
                            page: this.currentPage
                        });

                        fetch(`/admin/roles?${params.toString()}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.roles = data.data;
                                this.currentPage = data.current_page;
                                this.lastPage = data.last_page;
                                this.loading = false;
                            })
                            .catch(() => this.loading = false);
                    },

                    prevPage() {
                        if (this.currentPage > 1) {
                            this.currentPage--;
                            this.fetchRoles();
                        }
                    },
                    nextPage() {
                        if (this.currentPage < this.lastPage) {
                            this.currentPage++;
                            this.fetchRoles();
                        }
                    },

                    confirmDelete(id, name) {
                        this.deleteRoleId = id;
                        this.deleteRoleName = name;
                        this.deleteModalOpen = true;
                    },

                    deleteRole() {
                        fetch(`/admin/roles/${this.deleteRoleId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    this.deleteModalOpen = false;
                                    this.fetchRoles();
                                } else {
                                    window.toast.error(data.message);
                                }
                            });
                    }
                }
            }
        </script>
    @endpush
@endsection
