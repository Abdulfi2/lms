<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="#" class="text-2xl font-bold text-primary">LMS</a>
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="#" class="text-gray-700 hover:text-primary transition">Beranda</a>
                <a href="#" class="text-gray-700 hover:text-primary transition">Kursus</a>
                <a href="#" class="text-gray-700 hover:text-primary transition">Kategori</a>
                <a href="#" class="text-gray-700 hover:text-primary transition">Jadwal</a>
                <a href="#" class="text-gray-700 hover:text-primary transition">Bantuan</a>
            </div>
            <div class="flex items-center space-x-4">
                @if (Auth::check())
                    <a href="{{ route('dashboard') }}"
                        class="bg-primary text-white px-6 py-2 rounded-lg font-medium hover:bg-secondary transition shadow-md">Dashboard</a>
                @else
                    <a href="{{ route('login') }}"
                        class="text-gray-700 hover:text-primary px-4 py-2 transition">Masuk</a>
                    <a href="{{ route('register') }}"
                        class="bg-primary text-white px-6 py-2 rounded-lg font-medium hover:bg-secondary transition shadow-md">Daftar</a>
                @endif
            </div>
        </div>
    </div>
</nav>
