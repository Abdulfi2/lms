@extends('layouts.app')

@section('title', 'Manajemen Review')
@section('page-title', 'Review')
@section('page-subtitle', 'Moderasi ulasan kursus dari siswa')

@section('content')
    <div x-data="reviewManager()" x-init="init()" class="space-y-6">
        <div class="flex gap-2">
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}"
                class="px-4 py-2 rounded-lg border {{ request('status') === 'pending' ? 'bg-primary text-white' : 'dark:border-gray-600' }}">Pending</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}"
                class="px-4 py-2 rounded-lg border {{ request('status') === 'approved' ? 'bg-primary text-white' : 'dark:border-gray-600' }}">Approved</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'all']) }}"
                class="px-4 py-2 rounded-lg border {{ !request()->filled('status') || request('status') === 'all' ? 'bg-primary text-white' : 'dark:border-gray-600' }}">Semua</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Komentar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($reviews as $review)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $review->user->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm">{{ $review->course->title ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm">{{ $review->rating }} &#9733;</td>
                                <td class="px-6 py-4 text-sm">{{ Str::limit($review->comment, 50) }}</td>
                                <td class="px-6 py-4">
                                    @if (!$review->is_approved)
                                        <button @click="approve({{ $review->id }})" class="text-green-600 hover:text-green-800 text-sm">Approve</button>
                                    @else
                                        <span class="text-gray-400 text-sm">Approved</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="deleteReview({{ $review->id }})" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Tidak ada review.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function reviewManager() {
                return {
                    init() {},
                    approve(id) {
                        fetch(`/admin/reviews/${id}/approve`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json()).then(data => {
                                if (data.success) location.reload();
                            });
                    },
                    deleteReview(id) {
                        if (confirm('Hapus review ini?')) {
                            fetch(`/admin/reviews/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json()).then(data => {
                                    if (data.success) location.reload();
                                });
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
