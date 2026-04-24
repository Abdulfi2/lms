{{-- resources/views/profile/tokens.blade.php --}}

@extends('layouts.app')

@section('title', 'API Tokens')
@section('page-title', 'API Tokens')
@section('page-subtitle', 'Kelola token untuk akses API')

@section('content')
    <div x-data="tokenManager()" x-init="fetchTokens()" class="space-y-6">
        <!-- Create Token Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Buat Token Baru</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Token</label>
                    <input type="text" x-model="newToken.name" placeholder="Mobile App, Web App, dll"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expired (hari)</label>
                    <input type="number" x-model="newToken.expires_in_days" placeholder="30 (default)"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permissions
                    (Abilities)</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    <label class="flex items-center">
                        <input type="checkbox" x-model="newToken.abilities" value="courses:view" class="rounded">
                        <span class="ml-2 text-sm">View Courses</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" x-model="newToken.abilities" value="courses:enroll" class="rounded">
                        <span class="ml-2 text-sm">Enroll Courses</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" x-model="newToken.abilities" value="assignments:submit" class="rounded">
                        <span class="ml-2 text-sm">Submit Assignments</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" x-model="newToken.abilities" value="quizzes:attempt" class="rounded">
                        <span class="ml-2 text-sm">Take Quizzes</span>
                    </label>
                </div>
            </div>

            <button @click="createToken" :disabled="creating"
                class="mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                <span x-show="!creating">Buat Token</span>
                <span x-show="creating">Membuat...</span>
            </button>
        </div>

        <!-- New Token Display (shown right after creation) -->
        <div x-show="newlyCreatedToken"
            class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl p-4">
            <p class="text-green-800 dark:text-green-400 font-medium">Token berhasil dibuat!</p>
            <p class="text-sm text-green-700 dark:text-green-300 mt-1">Simpan token ini sekarang. Anda tidak akan bisa
                melihatnya lagi.</p>
            <div class="mt-2 p-2 bg-white dark:bg-gray-800 rounded border font-mono text-sm break-all">
                <span x-text="newlyCreatedToken"></span>
            </div>
            <button @click="copyToken" class="mt-2 text-sm text-primary hover:underline">📋 Copy Token</button>
        </div>

        <!-- Tokens List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Token Aktif</h3>
            </div>

            <div class="divide-y dark:divide-gray-700">
                <template x-for="token in tokens" :key="token.id">
                    <div class="px-6 py-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white" x-text="token.name"></p>
                            <p class="text-xs text-gray-500 mt-1">
                                <span>Last used: <span x-text="token.last_used_at || 'Never'"></span></span>
                                <span class="mx-2">•</span>
                                <span>Created: <span x-text="formatDate(token.created_at)"></span></span>
                                <span x-show="token.expires_at" class="ml-2">• Expires: <span
                                        x-text="formatDate(token.expires_at)"></span></span>
                            </p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                <template x-for="ability in token.abilities">
                                    <span class="px-1.5 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 rounded">
                                        <span x-text="ability"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                        <button @click="revokeToken(token.id)" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    </div>
                </template>
                <div x-show="tokens.length === 0" class="px-6 py-8 text-center text-gray-500">
                    Belum ada token. Buat token pertama Anda.
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function tokenManager() {
                return {
                    tokens: [],
                    newToken: {
                        name: '',
                        expires_in_days: 30,
                        abilities: ['courses:view', 'courses:enroll']
                    },
                    newlyCreatedToken: null,
                    creating: false,

                    fetchTokens() {
                        fetch('/api/tokens', {
                                headers: {
                                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) this.tokens = data.data;
                            });
                    },

                    createToken() {
                        this.creating = true;
                        fetch('/api/tokens', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(this.newToken)
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.newlyCreatedToken = data.token;
                                    this.newToken.name = '';
                                    this.fetchTokens();
                                    window.toast.success(data.message);
                                } else {
                                    window.toast.error(data.message);
                                }
                                this.creating = false;
                            });
                    },

                    revokeToken(id) {
                        if (confirm('Yakin ingin menghapus token ini?')) {
                            fetch(`/api/tokens/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        this.fetchTokens();
                                        window.toast.success(data.message);
                                    } else {
                                        window.toast.error(data.message);
                                    }
                                });
                        }
                    },

                    copyToken() {
                        navigator.clipboard.writeText(this.newlyCreatedToken);
                        window.toast.success('Token berhasil disalin!');
                    },

                    formatDate(date) {
                        if (!date) return '-';
                        return new Date(date).toLocaleDateString('id-ID');
                    }
                }
            }
        </script>
    @endpush
@endsection
