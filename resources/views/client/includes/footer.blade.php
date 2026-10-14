@php
    use App\Models\Setting;
    use App\Support\FooterSettings;

    // Isi footer diatur admin di Pengaturan > Footer. Kolom tautan yang kosong tidak ditampilkan.
    $columns = collect([
        ['title' => FooterSettings::value('footer_about_title'), 'links' => FooterSettings::parseLinks(FooterSettings::value('footer_about_links'))],
        ['title' => FooterSettings::value('footer_help_title'), 'links' => FooterSettings::parseLinks(FooterSettings::value('footer_help_links'))],
    ])->filter(fn ($column) => $column['links'])->values();

    $email = Setting::get('contact_email', 'support@lms.com');
    $phone = trim((string) FooterSettings::value('footer_phone'));
    $address = trim((string) FooterSettings::value('footer_address'));
    $socials = FooterSettings::socials();
    $description = trim((string) FooterSettings::value('footer_description'));

    // Nama kelas ditulis utuh (bukan disusun dari angka) supaya terdeteksi build Tailwind.
    $gridClass = [2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3', 4 => 'md:grid-cols-4'][2 + $columns->count()];
@endphp

<footer class="bg-white dark:bg-gray-800 border-t dark:border-gray-700 py-12 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 {{ $gridClass }} gap-8">
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-8 h-8 shadow rounded-lg flex items-center justify-center">
                        <img src="{{ asset('images/logo-zakatsukses.png') }}" alt="Logo ZakatSukses" class="w-6">
                    </div>
                    <span
                        class="font-bold text-xl bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent hidden sm:inline">
                        {{ config('app.name', 'ZS Academy') }}
                    </span>
                </div>
                @if ($description !== '')
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $description }}</p>
                @endif
            </div>

            @foreach ($columns as $column)
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4">{{ $column['title'] }}</h4>
                    <ul class="space-y-2 text-sm">
                        @foreach ($column['links'] as $link)
                            <li><a href="{{ $link['url'] }}"
                                    @if (preg_match('#^https?://#i', $link['url'])) target="_blank" rel="noopener noreferrer" @endif
                                    class="text-gray-600 dark:text-gray-400 hover:text-primary transition">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Hubungi Kami</h4>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    @if ($email)
                        <li><i class="fas fa-envelope mr-2 w-4"></i> {{ $email }}</li>
                    @endif
                    @if ($phone !== '')
                        <li><i class="fas fa-phone mr-2 w-4"></i> {{ $phone }}</li>
                    @endif
                    @if ($address !== '')
                        <li class="flex"><i class="fas fa-location-dot mr-2 w-4 mt-1"></i> <span>{!! nl2br(e($address)) !!}</span></li>
                    @endif
                </ul>
                @if ($socials)
                    <div class="flex space-x-4 mt-4">
                        @foreach ($socials as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}"
                                class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-500 hover:text-primary hover:bg-primary/10 transition">
                                @if ($social['icon'] === 'svg:x')
                                    <x-icons.x class="w-3.5 h-3.5" />
                                @else
                                    <i class="{{ $social['icon'] }}"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="border-t dark:border-gray-700 mt-8 pt-8 text-center text-sm text-gray-500 dark:text-gray-400">
            {{ FooterSettings::copyright() }}
        </div>
    </div>
</footer>
