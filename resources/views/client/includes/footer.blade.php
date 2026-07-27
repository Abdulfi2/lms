<footer class="bg-white dark:bg-gray-800 border-t dark:border-gray-700 py-12 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <div
                        class="w-8 h-8 bg-gradient-to-r from-primary to-secondary rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">L</span>
                    </div>
                    <span
                        class="font-bold text-xl bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                        {{ config('app.name', 'LMS') }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Platform pembelajaran online terbaik untuk masa depan yang lebih cerah.
                </p>
            </div>

            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Tentang</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('about') }}" class="text-gray-600 dark:text-gray-400 hover:text-primary transition">Tentang
                            Kami</a></li>
                    <li><a href="#"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary transition">Karir</a></li>
                    <li><a href="{{ route('articles.index') }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary transition">Blog</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Bantuan</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('faq') }}" class="text-gray-600 dark:text-gray-400 hover:text-primary transition">FAQ</a>
                    </li>
                    <li><a href="{{ route('contact.create') }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary transition">Kontak</a></li>
                    <li><a href="{{ route('privacy-policy') }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-primary transition">Kebijakan Privasi</a>
                    </li>
                    <li><a href="{{ route('terms') }}" class="text-gray-600 dark:text-gray-400 hover:text-primary transition">Syarat
                            & Ketentuan</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Hubungi Kami</h4>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li><i class="fas fa-envelope mr-2 w-4"></i> {{ \App\Models\Setting::get('contact_email', 'support@lms.com') }}</li>
                    <li><i class="fas fa-phone mr-2 w-4"></i> +62 21 12345678</li>
                </ul>
                <div class="flex space-x-4 mt-4">
                    <a href="#"
                        class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-500 hover:text-primary hover:bg-primary/10 transition">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#"
                        class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-500 hover:text-primary hover:bg-primary/10 transition">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#"
                        class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-500 hover:text-primary hover:bg-primary/10 transition">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#"
                        class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-500 hover:text-primary hover:bg-primary/10 transition">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t dark:border-gray-700 mt-8 pt-8 text-center text-sm text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'LMS') }}. All rights reserved.
        </div>
    </div>
</footer>
