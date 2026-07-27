@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User Baru')
@section('page-subtitle', 'Isi data user baru')

@section('content')
    <div x-data="userForm()" class="max-w-4xl mx-auto">
        <form @submit.prevent="submitForm" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 space-y-6">
                <!-- Tab navigation -->
                <div class="border-b dark:border-gray-700">
                    <nav class="flex space-x-4">
                        <button type="button" @click="activeTab = 'account'"
                            :class="{ 'border-primary text-primary': activeTab === 'account' }"
                            class="py-2 px-1 border-b-2 font-medium text-sm transition">Akun</button>
                        <button type="button" @click="activeTab = 'profile'"
                            :class="{ 'border-primary text-primary': activeTab === 'profile' }"
                            class="py-2 px-1 border-b-2 font-medium text-sm transition">Profil</button>
                        <button type="button" @click="activeTab = 'role'"
                            :class="{ 'border-primary text-primary': activeTab === 'role' }"
                            class="py-2 px-1 border-b-2 font-medium text-sm transition">Role & Status</button>
                    </nav>
                </div>

                <!-- Tab Akun -->
                <div x-show="activeTab === 'account'" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap <span
                                    class="text-red-500">*</span></label>
                            <input type="text" x-model="form.name"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary">
                            <p x-show="errors.name" class="mt-1 text-xs text-red-600" x-text="errors.name"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email <span
                                    class="text-red-500">*</span></label>
                            <input type="email" x-model="form.email"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary">
                            <p x-show="errors.email" class="mt-1 text-xs text-red-600" x-text="errors.email"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password <span
                                    class="text-red-500">*</span></label>
                            <input type="password" x-model="form.password"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary">
                            <p x-show="errors.password" class="mt-1 text-xs text-red-600" x-text="errors.password"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password
                                <span class="text-red-500">*</span></label>
                            <input type="password" x-model="form.password_confirmation"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                </div>

                <!-- Tab Profil -->
                <div x-show="activeTab === 'profile'" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Depan</label>
                            <input type="text" x-model="form.first_name"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Belakang</label>
                            <input type="text" x-model="form.last_name"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nickname</label>
                            <input type="text" x-model="form.nickname"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Telepon</label>
                            <input type="text" x-model="form.phone"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin</label>
                            <select x-model="form.gender"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">Pilih</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Lahir</label>
                            <input type="date" x-model="form.birth_date"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <p x-show="errors.birth_date" class="mt-1 text-xs text-red-600" x-text="errors.birth_date"></p>
                        </div>
                    </div>
                </div>

                <!-- Tab Role & Status -->
                <div x-show="activeTab === 'role'" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role <span
                                    class="text-red-500">*</span></label>
                            <select x-model="form.role"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">Pilih Role</option>
                                @foreach ($roles as $roleOption)
                                    <option value="{{ $roleOption->name }}">{{ ucfirst(str_replace('_', ' ', $roleOption->name)) }}</option>
                                @endforeach
                            </select>
                            <p x-show="errors.role" class="mt-1 text-xs text-red-600" x-text="errors.role"></p>
                        </div>
                        <div class="flex items-center space-x-4 mt-6">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="form.is_active"
                                    class="rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" x-model="form.email_verified"
                                    class="rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Verifikasi Email</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-3">
                <a href="{{ route('admin.users.index') }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">Batal</a>
                <button type="submit" :disabled="loading"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition disabled:opacity-50">
                    <span x-show="!loading">Simpan</span>
                    <span x-show="loading">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function userForm() {
                return {
                    activeTab: 'account',
                    form: {
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        first_name: '',
                        last_name: '',
                        nickname: '',
                        phone: '',
                        gender: '',
                        birth_date: '',
                        role: '',
                        is_active: true,
                        email_verified: false,
                    },
                    errors: {},
                    loading: false,

                    submitForm() {
                        this.loading = true;
                        this.errors = {};

                        fetch('{{ route('admin.users.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(this.form)
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    window.location.href = '{{ route('admin.users.index') }}';
                                } else {
                                    if (data.errors) this.errors = data.errors;
                                    else window.toast.error(data.message);
                                    this.loading = false;
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                window.toast.error('Terjadi kesalahan');
                                this.loading = false;
                            });
                    }
                }
            }
        </script>
    @endpush
@endsection
