@extends('layouts.app')

@section('title', 'Manajemen Review')
@section('page-title', 'Review')

@section('content')
    <div x-data="reviewManager()" x-init="init()" class="space-y-6">
        <div class="flex gap-2">
            <button @click="filter('pending')" class="px-4 py-2 rounded-lg border">Pending</button>
            <button @click="filter('approved')" class="px-4 py-2 rounded-lg border">Approved</button>
            <button @click="filter('all')" class="px-4 py-2 rounded-lg border">Semua</button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">...</thead>
                <tbody>
                    @foreach ($reviews as $review)
                        <tr>
                            <td class="px-6 py-4">{{ $review->user->name }}</td>
                            <td class="px-6 py-4">{{ $review->course->title }}</td>
                            <td class="px-6 py-4">{{ $review->rating }} ★</td>
                            <td class="px-6 py-4">{{ Str::limit($review->comment, 50) }}</td>
                            <td class="px-6 py-4">
                                @if (!$review->is_approved)
                                    <button @click="approve({{ $review->id }})" class="text-green-600">Approve</button>
                                @else
                                    <span class="text-gray-400">Approved</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button @click="deleteReview({{ $review->id }})" class="text-red-600">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            function reviewManager() {
                return {
                    init() {},
                    filter(status) {
                        window.location.href = '?status=' + status;
                    },
                    approve(id) {
                        fetch(`/admin/reviews/${id}/approve`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
