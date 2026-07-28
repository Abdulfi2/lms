@extends('layouts.app')

@section('title', 'Tugas Saya')
@section('page-title', 'Tugas')
@section('page-subtitle', 'Daftar tugas yang harus Anda kerjakan')

@section('content')
    <div x-data="{ activeTab: 'all' }" class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Total Tugas</p>
                <p class="text-2xl font-bold">{{ $totalAssignments }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Selesai</p>
                <p class="text-2xl font-bold text-green-600">{{ $completedAssignments }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Belum Dikerjakan</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $pendingAssignments }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Rata-rata Nilai</p>
                <p class="text-2xl font-bold text-primary">{{ round($averageScore ?? 0) }}%</p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="border-b dark:border-gray-700">
            <nav class="flex space-x-4">
                <button @click="activeTab = 'all'" :class="{ 'border-primary text-primary': activeTab == 'all' }"
                    class="py-2 px-1 border-b-2 font-medium text-sm">Semua</button>
                <button @click="activeTab = 'pending'" :class="{ 'border-primary text-primary': activeTab == 'pending' }"
                    class="py-2 px-1 border-b-2 font-medium text-sm">Belum Dikerjakan</button>
                <button @click="activeTab = 'submitted'" :class="{ 'border-primary text-primary': activeTab == 'submitted' }"
                    class="py-2 px-1 border-b-2 font-medium text-sm">Sudah Dikirim</button>
                <button @click="activeTab = 'graded'" :class="{ 'border-primary text-primary': activeTab == 'graded' }"
                    class="py-2 px-1 border-b-2 font-medium text-sm">Sudah Dinilai</button>
                <button @click="activeTab = 'overdue'" :class="{ 'border-primary text-primary': activeTab == 'overdue' }"
                    class="py-2 px-1 border-b-2 font-medium text-sm">Terlambat</button>
            </nav>
        </div>

        <!-- Assignment List -->
        <div class="space-y-4">
            @forelse($assignments as $assignment)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition"
                    x-show="activeTab === 'all' || 
                     (activeTab === 'pending' && !{{ $assignment->submissions->isNotEmpty() ? 'true' : 'false' }} && '{{ $assignment->due_date }}' > now())
||
                     (activeTab === 'submitted' && {{ $assignment->submissions->isNotEmpty() && $assignment->submissions->first()->status == 'submitted' ? 'true' : 'false' }}) ||
                     (activeTab === 'graded' && {{ $assignment->submissions->isNotEmpty() && $assignment->submissions->first()->status == 'graded' ? 'true' : 'false' }}) ||
                     (activeTab === 'overdue' && !{{ $assignment->submissions->isNotEmpty() ? 'true' : 'false' }} && '{{ $assignment->due_date }}' < now())">

                    <div class="p-5">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-800 dark:text-white text-lg">{{ $assignment->title }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $assignment->course->title }}</p>
                            </div>
                            <div class="text-right">
                                @php
                                    $submission = $assignment->submissions->first();
                                    $isOverdue = !$submission && $assignment->due_date !== null && $assignment->due_date < now();
                                    $status = $submission ? $submission->status : 'pending';
                                @endphp

                                @if ($status == 'graded')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Nilai:
                                        {{ $submission->score }}/{{ $assignment->max_score }}</span>
                                @elseif($status == 'submitted')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Menunggu
                                        Penilaian</span>
                                @elseif($isOverdue)
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Terlambat</span>
                                @else
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Belum
                                        Dikerjakan</span>
                                @endif

                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    @if ($assignment->due_date)
                                        Deadline: {{ $assignment->due_date->format('d M Y, H:i') }}
                                    @else
                                        Tidak ada deadline
                                    @endif
                                </p>
                            </div>
                        </div>

                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">
                            {{ Str::limit($assignment->description, 150) }}</p>

                        <div class="mt-4 flex justify-between items-center">
                            <div class="flex space-x-2">
                                @if ($status == 'graded')
                                    <span class="text-sm text-gray-500">Nilai:
                                        {{ $submission->score }}/{{ $assignment->max_score }}</span>
                                    @if ($submission->feedback)
                                        <span class="text-sm text-green-600">Ada feedback</span>
                                    @endif
                                @endif
                            </div>

                            <a href="{{ route('student.assignments.show', $assignment) }}"
                                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary text-sm">
                                @if ($status == 'graded')
                                    Lihat Hasil
                                @elseif($status == 'submitted')
                                    Lihat Pengiriman
                                @else
                                    Kerjakan Tugas
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="text-lg font-medium">Belum Ada Tugas</h3>
                    <p class="text-gray-500 mt-1">Tidak ada tugas yang perlu dikerjakan saat ini.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $assignments->links() }}
        </div>
    </div>
@endsection
