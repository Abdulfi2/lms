@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Kelola semua pengguna sistem')

@section('content')
    <div x-data="userManager()" x-init="init()" class="space-y-6">
        <!-- Header dengan tombol tambah -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <input type="text" x-model="filters.search" @input.debounce.300="fetchUsers()"
                        placeholder="Cari nama atau email..."
                        class="w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary">
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <select x-model="filters.role" @change="fetchUsers()"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2 px-3 focus:ring-2 focus:ring-primary">
                    <option value="">Semua Role</option>
                    @foreach ($roles as $roleOption)
                        <option value="{{ $roleOption->name }}">{{ ucfirst(str_replace('_', ' ', $roleOption->name)) }}</option>
                    @endforeach
                </select>

                <select x-model="filters.status" @change="fetchUsers()"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2 px-3 focus:ring-2 focus:ring-primary">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                    <option value="pending_approval">Menunggu Approval</option>
                </select>

                <button @click="fetchUsers()" class="p-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                </button>
            </div>

            <a href="{{ route('admin.users.create') }}"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah User
            </a>
        </div>

        <!-- Bulk Actions -->
        <div x-show="selectedIds.length > 0" x-cloak
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 flex items-center gap-3">
            <span class="text-sm" x-text="selectedIds.length + ' user dipilih'"></span>
            <button @click="confirmBulkDelete" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
                Hapus Terpilih
            </button>
        </div>

        <!-- Tabel User -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                <input type="checkbox" @click="toggleSelectAll" x-model="selectAll"
                                    class="rounded border-gray-300 text-primary focus:ring-primary">
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                User</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Role</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Email Verifikasi</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Bergabung</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="user in users" :key="user.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4">
                                    <input type="checkbox" x-model="selectedIds" :value="user.id"
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img :src="user.avatar_url" class="w-10 h-10 rounded-full object-cover mr-3">
                                        <div>
                                            <div class="font-medium text-gray-800 dark:text-white" x-text="user.name"></div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400" x-text="user.email"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <!-- Badge role dengan warna berbeda -->
                                    <span x-show="user.role_name === 'admin'"
                                        class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Admin</span>
                                    <span x-show="user.role_name === 'instructor'"
                                        class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Instructor</span>
                                    <span x-show="user.role_name === 'student'"
                                        class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Student</span>
                                    <span x-show="!['admin', 'instructor', 'student'].includes(user.role_name) && user.role_name"
                                        class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full capitalize"
                                        x-text="(user.role_name || '').replace('_', ' ')"></span>
                                    <!-- Fallback jika role_name tidak ada -->
                                    <span x-show="!user.role_name"
                                        class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">-</span>
                                    <div x-show="user.profile && user.profile.approval_status === 'pending'" class="mt-1">
                                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Menunggu Approval</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <button @click="toggleStatus(user.id, user.is_active)"
                                        class="px-2 py-1 text-xs rounded-full transition"
                                        :class="user.is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' :
                                            'bg-gray-100 text-gray-800 hover:bg-gray-200'">
                                        <span x-text="user.is_active ? 'Aktif' : 'Tidak Aktif'"></span>
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <span x-show="user.email_verified_at"
                                        class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Terverifikasi</span>
                                    <span x-show="!user.email_verified_at"
                                        class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Belum</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"
                                    x-text="formatDate(user.created_at)"></td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a :href="'/admin/users/' + user.id" class="text-blue-600 hover:text-blue-800"
                                        title="Lihat">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>
                                    <a :href="'/admin/users/' + user.id + '/edit'"
                                        class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <button @click="confirmDelete(user.id, user.name)"
                                        class="text-red-600 hover:text-red-800" title="Hapus">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="users.length === 0 && !loading">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada data user.
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

    <!-- Modal Konfirmasi Hapus Massal -->
    <div x-show="bulkDeleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="bulkDeleteModalOpen = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Hapus User Terpilih</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Yakin ingin menghapus <span x-text="selectedIds.length" class="font-semibold"></span> user
                        yang dipilih? Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="mt-5 flex justify-center space-x-3">
                        <button @click="bulkDeleteModalOpen = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300">Batal</button>
                        <button @click="bulkDelete"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="deleteModalOpen = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Hapus User</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Apakah Anda yakin ingin menghapus user <span x-text="deleteUserName"
                            class="font-semibold"></span>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="mt-5 flex justify-center space-x-3">
                        <button @click="deleteModalOpen = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300">Batal</button>
                        <button @click="deleteUser"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function userManager() {
                return {
                    users: [],
                    selectedIds: [],
                    selectAll: false,
                    filters: {
                        search: '',
                        role: '',
                        status: ''
                    },
                    loading: false,
                    currentPage: 1,
                    lastPage: 1,
                    total: 0,
                    from: 0,
                    to: 0,
                    deleteModalOpen: false,
                    deleteUserId: null,
                    deleteUserName: '',
                    bulkDeleteModalOpen: false,

                    init() {
                        // Baca filter awal dari query string URL (mis. link dari dashboard
                        // "Instruktur Menunggu Approval" yang membawa ?status=pending_approval)
                        const params = new URLSearchParams(window.location.search);
                        this.filters.search = params.get('search') || '';
                        this.filters.role = params.get('role') || '';
                        this.filters.status = params.get('status') || '';

                        this.fetchUsers();
                    },

                    fetchUsers() {
                        this.loading = true;
                        const params = new URLSearchParams({
                            search: this.filters.search,
                            role: this.filters.role,
                            status: this.filters.status,
                            page: this.currentPage
                        });

                        fetch(`/admin/users?${params.toString()}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.users = data.data;
                                this.currentPage = data.current_page;
                                this.lastPage = data.last_page;
                                this.total = data.total;
                                this.from = data.from;
                                this.to = data.to;
                                this.loading = false;
                                this.selectedIds = [];
                                this.selectAll = false;
                            })
                            .catch(err => {
                                console.error(err);
                                this.loading = false;
                            });
                    },

                    toggleSelectAll() {
                        if (this.selectAll) {
                            this.selectedIds = this.users.map(u => u.id);
                        } else {
                            this.selectedIds = [];
                        }
                    },

                    prevPage() {
                        if (this.currentPage > 1) {
                            this.currentPage--;
                            this.fetchUsers();
                        }
                    },

                    nextPage() {
                        if (this.currentPage < this.lastPage) {
                            this.currentPage++;
                            this.fetchUsers();
                        }
                    },

                    toggleStatus(userId, currentStatus) {
                        fetch(`/admin/users/${userId}/toggle-status`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({})
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    this.fetchUsers();
                                } else {
                                    window.toast.error(data.message);
                                }
                            })
                            .catch(() => window.toast.error('Gagal mengubah status'));
                    },

                    confirmDelete(id, name) {
                        this.deleteUserId = id;
                        this.deleteUserName = name;
                        this.deleteModalOpen = true;
                    },

                    deleteUser() {
                        fetch(`/admin/users/${this.deleteUserId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    this.deleteModalOpen = false;
                                    this.fetchUsers();
                                } else {
                                    window.toast.error(data.message);
                                }
                            })
                            .catch(() => window.toast.error('Gagal menghapus user'));
                    },

                    formatDate(date) {
                        if (!date) return '-';
                        return new Date(date).toLocaleDateString('id-ID');
                    },

                    confirmBulkDelete() {
                        if (this.selectedIds.length === 0) return;
                        this.bulkDeleteModalOpen = true;
                    },

                    bulkDelete() {
                        fetch('{{ route('admin.users.bulk-delete') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ ids: this.selectedIds })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    this.bulkDeleteModalOpen = false;
                                    this.fetchUsers();
                                } else {
                                    window.toast.error(data.message || 'Gagal menghapus user terpilih');
                                }
                            })
                            .catch(() => window.toast.error('Gagal menghapus user terpilih'));
                    }
                }
            }
        </script>
    @endpush
@endsection
