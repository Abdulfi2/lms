@extends('layouts.app')

@section('title', 'Manajemen Kursus')
@section('page-title', 'Kursus')
@section('page-subtitle', 'Kelola semua kursus')

@section('content')
    <div x-data="courseManager()" x-init="init()" class="space-y-6">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div class="flex flex-wrap gap-2">
                <input type="text" x-model="filters.search" @input.debounce.300="fetchCourses()" placeholder="Cari kursus..."
                    class="px-4 py-2 rounded-lg border dark:bg-gray-700">
                <select x-model="filters.status" @change="fetchCourses()"
                    class="px-4 py-2 rounded-lg border dark:bg-gray-700">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="pending">Pending</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>
                <select x-model="filters.level" @change="fetchCourses()"
                    class="px-4 py-2 rounded-lg border dark:bg-gray-700">
                    <option value="">Semua Level</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                    <option value="all_levels">All Levels</option>
                </select>
                <select x-model="filters.instructor_id" @change="fetchCourses()"
                    class="px-4 py-2 rounded-lg border dark:bg-gray-700">
                    <option value="">Semua Instruktur</option>
                    @foreach ($instructors as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                    @endforeach
                </select>
                <button @click="fetchCourses()" class="px-4 py-2 bg-primary text-white rounded-lg">Filter</button>
            </div>
            <a href="{{ route('admin.courses.create') }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">+ Kursus Baru</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Instruktur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Siswa</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="course in courses" :key="course.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <img :src="course.thumbnail ? '/storage/' + course.thumbnail :
                                            'https://placehold.co/60x40/3B82F6/white?text=Course'"
                                            class="w-16 h-10 object-cover rounded">
                                        <div>
                                            <div class="font-medium" x-text="course.title"></div>
                                            <div class="text-xs text-gray-500" x-text="course.slug"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4" x-text="course.instructor?.name ?? '-'"></td>
                                <td class="px-6 py-4">
                                    <span x-show="course.sale_price && course.sale_price < course.price"
                                        class="line-through text-gray-400 text-sm"
                                        x-text="formatPrice(course.price)"></span>
                                    <span class="font-semibold" x-text="course.formatted_price"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full"
                                        :class="{
                                            'bg-blue-100 text-blue-800': course.level === 'beginner',
                                            'bg-yellow-100 text-yellow-800': course.level === 'intermediate',
                                            'bg-red-100 text-red-800': course.level === 'advanced',
                                            'bg-purple-100 text-purple-800': course.level === 'all_levels'
                                        }"
                                        x-text="course.level"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <button @click="course.status === 'pending' ? null : toggleStatus(course.id, course.status)"
                                        class="px-2 py-1 text-xs rounded-full"
                                        :class="{
                                            'bg-gray-100 text-gray-800': course.status === 'draft',
                                            'bg-yellow-100 text-yellow-800 cursor-default': course.status === 'pending',
                                            'bg-green-100 text-green-800': course.status === 'published',
                                            'bg-red-100 text-red-800': course.status === 'archived'
                                        }"
                                        x-text="course.status"></button>
                                </td>
                                <td class="px-6 py-4 text-sm" x-text="course.total_students"></td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <template x-if="course.status === 'pending'">
                                        <span class="inline-flex space-x-2">
                                            <button @click="approveCourse(course.id)" class="text-green-600 hover:text-green-800" title="Setujui">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button @click="rejectCourse(course.id)" class="text-red-600 hover:text-red-800" title="Tolak">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                    <a :href="'/admin/courses/' + course.id" class="text-gray-600 hover:text-gray-800" title="Lihat Detail">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a :href="'/admin/courses/' + course.id + '/sections'"
                                        class="text-green-600 hover:text-green-800" title="Kelola Sections">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                    </a>
                                    <a :href="'/admin/courses/' + course.id + '/edit'"
                                        class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button @click="confirmDelete(course.id, course.title)"
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
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary">
                                </div>
                            </td>
                        </tr>
                        <tr x-show="!loading && courses.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">Tidak ada data kursus.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t flex justify-between items-center">
                <div class="text-sm text-gray-500">Menampilkan <span x-text="from"></span> - <span
                        x-text="to"></span> dari <span x-text="total"></span></div>
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
            function courseManager() {
                return {
                    courses: [],
                    filters: {
                        search: '',
                        status: '',
                        level: '',
                        instructor_id: ''
                    },
                    loading: false,
                    currentPage: 1,
                    lastPage: 1,
                    total: 0,
                    from: 0,
                    to: 0,
                    init() {
                        const params = new URLSearchParams(window.location.search);
                        this.filters.search = params.get('search') || '';
                        this.filters.status = params.get('status') || '';
                        this.filters.level = params.get('level') || '';
                        this.filters.instructor_id = params.get('instructor_id') || '';

                        this.fetchCourses();
                    },
                    approveCourse(id) {
                        if (!confirm('Setujui dan publikasikan kursus ini?')) return;
                        fetch(`/admin/courses/${id}/approve`, {
                                method: 'PATCH',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) { window.toast.success(data.message); this.fetchCourses(); }
                                else window.toast.error(data.message);
                            });
                    },
                    rejectCourse(id) {
                        const reason = prompt('Alasan penolakan (akan dikirim ke instruktur):');
                        if (!reason) return;
                        fetch(`/admin/courses/${id}/reject`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ reason })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) { window.toast.success(data.message); this.fetchCourses(); }
                                else window.toast.error(data.message);
                            });
                    },
                    fetchCourses() {
                        this.loading = true;
                        const params = new URLSearchParams({
                            ...this.filters,
                            page: this.currentPage
                        });
                        fetch(`/admin/courses?${params.toString()}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.courses = data.data;
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
                            this.fetchCourses();
                        }
                    },
                    nextPage() {
                        if (this.currentPage < this.lastPage) {
                            this.currentPage++;
                            this.fetchCourses();
                        }
                    },
                    toggleStatus(id, currentStatus) {
                        fetch(`/admin/courses/${id}/toggle-status`, {
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
                                    this.fetchCourses();
                                } else window.toast.error(data.message);
                            });
                    },
                    confirmDelete(id, title) {
                        if (confirm(`Yakin ingin menghapus kursus "${title}"?`)) {
                            fetch(`/admin/courses/${id}`, {
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
                                        this.fetchCourses();
                                    } else window.toast.error(data.message);
                                });
                        }
                    },
                    formatPrice(price) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(price);
                    }
                }
            }
        </script>
    @endpush
@endsection
