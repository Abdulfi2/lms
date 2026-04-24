@extends('layouts.app')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')
@section('page-subtitle', 'Ubah role dan permission-nya')

@section('content')
    <div x-data="roleForm()" x-init="init()" class="max-w-4xl mx-auto">
        <form @submit.prevent="submitForm" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Informasi Dasar</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Role <span
                                class="text-red-500">*</span></label>
                        <input type="text" x-model="form.name" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            placeholder="Contoh: editor, moderator">
                        <p class="text-xs text-gray-500 mt-1">Gunakan huruf kecil tanpa spasi (contoh: editor, support)</p>
                        <p x-show="errors.name" class="mt-1 text-xs text-red-600" x-text="errors.name"></p>
                    </div>
                </div>

                <!-- Permissions Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Permissions</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Pilih permission yang akan diberikan ke role
                        ini.</p>

                    @foreach ($permissionsByModule as $module => $perms)
                        <div class="border dark:border-gray-700 rounded-lg overflow-hidden mb-4">
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-2">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" x-model="modules['{{ $module }}']"
                                        @change="toggleModule('{{ $module }}')" class="rounded">
                                    <span class="font-medium text-gray-800 dark:text-white">{{ ucfirst($module) }}</span>
                                </label>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach ($perms as $perm)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" value="{{ $perm->name }}" x-model="form.permissions"
                                            class="rounded border-gray-300">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $perm->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-3">
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
                <button type="submit" :disabled="loading"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary disabled:opacity-50">
                    <span x-show="!loading">Simpan Role</span>
                    <span x-show="loading">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            const updateUrl = "{{ route('admin.roles.update', $role->id) }}";

            function roleForm() {
                return {
                    form: {
                        name: '{{ $role->name }}',
                        permissions: @json($rolePermissions)
                    },
                    modules: {},
                    errors: {},
                    loading: false,
                    roleId: {{ $role->id }},

                    init() {
                        // Initialize modules based on existing permissions
                        @foreach ($permissionsByModule as $module => $perms)
                            let allChecked = true;
                            @foreach ($perms as $perm)
                                if (!this.form.permissions.includes('{{ $perm->name }}')) {
                                    allChecked = false;
                                }
                            @endforeach
                            this.modules['{{ $module }}'] = allChecked;
                        @endforeach
                    },

                    toggleModule(module) {
                        const isChecked = this.modules[module];
                        const moduleLower = module.toLowerCase();

                        // Cari div module berdasarkan teks di dalam span header
                        let targetDiv = null;
                        const allDivs = document.querySelectorAll('.border');
                        for (let div of allDivs) {
                            const span = div.querySelector('.bg-gray-50 span');
                            if (span && span.innerText.toLowerCase().trim() === moduleLower) {
                                targetDiv = div;
                                break;
                            }
                        }

                        if (targetDiv) {
                            const checkboxes = targetDiv.querySelectorAll('.grid input[type="checkbox"]');
                            checkboxes.forEach(checkbox => {
                                const permissionValue = checkbox.value;
                                if (permissionValue) {
                                    checkbox.checked = isChecked;
                                    if (isChecked) {
                                        if (!this.form.permissions.includes(permissionValue)) {
                                            this.form.permissions.push(permissionValue);
                                        }
                                    } else {
                                        this.form.permissions = this.form.permissions.filter(p => p !==
                                        permissionValue);
                                    }
                                }
                            });
                        }
                    },

                    submitForm() {
                        this.loading = true;
                        this.errors = {};

                        fetch(updateUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-HTTP-Method-Override': 'PUT',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    name: this.form.name,
                                    permissions: this.form.permissions,
                                    _method: 'PUT'
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    window.location.href = '{{ route('admin.roles.index') }}';
                                } else {
                                    if (data.errors) this.errors = data.errors;
                                    else window.toast.error(data.message);
                                    this.loading = false;
                                }
                            })
                            .catch((error) => {
                                console.error('Error:', error);
                                window.toast.error('Terjadi kesalahan: ' + (error.message || 'Unknown error'));
                                this.loading = false;
                            });
                    }
                }
            }
        </script>
    @endpush
@endsection
