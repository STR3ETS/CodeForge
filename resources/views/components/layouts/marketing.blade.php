<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? 'Train je brein met dagelijkse puzzels, woordspellen en meer. Bouw je streak op, verdien XP en daag je vrienden uit.' }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    <link rel="preload" href="{{ asset('fontawesome/css/all.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}"></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        html { scroll-behavior: smooth; }
        .hero-gradient { background: linear-gradient(135deg, #5B2333 0%, #7a3349 50%, #5B2333 100%); }

        /* Scroll animations */
        [data-animate] {
            opacity: 0;
            transform: translateY(32px);
            transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }
        [data-animate="fade-left"] { transform: translateX(-32px); }
        [data-animate="fade-right"] { transform: translateX(32px); }
        [data-animate="scale"] { transform: scale(0.95); }
        [data-animate].is-visible {
            opacity: 1;
            transform: translateY(0) translateX(0) scale(1);
        }
        [data-animate-delay="1"] { transition-delay: 0.1s; }
        [data-animate-delay="2"] { transition-delay: 0.2s; }
        [data-animate-delay="3"] { transition-delay: 0.3s; }
        [data-animate-delay="4"] { transition-delay: 0.4s; }
        [data-animate-delay="5"] { transition-delay: 0.5s; }
    </style>

    {{ $head ?? '' }}
</head>

<body class="bg-[#F7F4F3] text-[#564D4A] antialiased overflow-x-hidden" style="font-family: 'Instrument Sans', sans-serif;">

    {{-- Navigation --}}
    <nav class="fixed top-4 left-0 right-0 z-50 px-4"
         x-data="{ scrolled: false, mobileOpen: false }"
         @scroll.window="scrolled = (window.scrollY > 20)">
        <div
             class="transition-all duration-300">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between bg-white rounded-full">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#5B2333] flex items-center justify-center">
                        <img src="/assets/logo-wit.png" class="max-h-4" alt="BrainForge logo">
                    </div>
                    <span class="font-black text-lg tracking-tight transition-colors">
                        Brain<span class="transition-colors">Forge.</span>
                    </span>
                </a>

                {{-- Desktop links --}}
                <div class="hidden md:flex items-center gap-8">
                    @php
                        $links = [
                            ['url' => route('home'), 'route' => 'home', 'label' => 'Home'],
                            ['url' => route('pages.games'), 'route' => 'pages.games', 'label' => 'Games'],
                            ['url' => route('pages.how'), 'route' => 'pages.how', 'label' => 'Hoe het werkt'],
                            ['url' => route('pages.pricing'), 'route' => 'pages.pricing', 'label' => 'Pricing'],
                        ];
                    @endphp
                    @foreach($links as $link)
                        <a href="{{ $link['url'] }}"
                           class="text-sm font-semibold transition-colors {{ request()->routeIs($link['route']) ? 'text-[#5B2333]' : 'text-[#564D4A]/60 hover:text-[#564D4A]' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>

                {{-- Right side --}}
                <div class="flex items-center gap-3">
                    @auth
                        {{-- Logged-in user dropdown --}}
                        <div class="relative" x-data="{ userOpen: false }" @click.away="userOpen = false">
                            <button @click="userOpen = !userOpen" class="flex items-center gap-2.5 px-2 py-1.5 rounded-xl hover:bg-[#564D4A]/5 transition cursor-pointer">
                                @php
                                    $navUser = auth()->user();
                                    $navAvatar = $navUser->profile_picture ? asset('storage/' . $navUser->profile_picture) : null;
                                @endphp
                                <div class="w-8 h-8 rounded-lg overflow-hidden border border-[#564D4A]/8 bg-white shrink-0">
                                    @if($navAvatar)
                                        <img src="{{ $navAvatar }}" class="w-full h-full object-cover" alt="{{ $navUser->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/8 text-[#564D4A] font-bold text-xs">
                                            {{ strtoupper(mb_substr($navUser->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <span class="hidden sm:block text-sm font-semibold text-[#564D4A] max-w-[120px] truncate">{{ $navUser->name }}</span>
                                <i class="fa-solid fa-chevron-down text-[#564D4A]/30 text-[9px] transition-transform" :class="userOpen && 'rotate-180'"></i>
                            </button>

                            {{-- Dropdown --}}
                            <div x-show="userOpen" x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg shadow-black/8 border border-[#564D4A]/6 py-2 z-50">

                                {{-- User info --}}
                                <div class="px-4 py-2.5 border-b border-[#564D4A]/6">
                                    <p class="text-sm font-bold text-[#564D4A] truncate">{{ $navUser->name }}</p>
                                    <p class="text-[11px] text-[#564D4A]/40 truncate">{{ $navUser->email }}</p>
                                </div>

                                <div class="py-1">
                                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#564D4A]/70 hover:text-[#564D4A] hover:bg-[#564D4A]/5 transition">
                                        <i class="fa-solid fa-grid-2 text-[12px] w-4 text-center"></i> Dashboard
                                    </a>
                                    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#564D4A]/70 hover:text-[#564D4A] hover:bg-[#564D4A]/5 transition">
                                        <i class="fa-solid fa-user text-[12px] w-4 text-center"></i> Mijn Profiel
                                    </a>
                                    <a href="{{ route('leaderboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#564D4A]/70 hover:text-[#564D4A] hover:bg-[#564D4A]/5 transition">
                                        <i class="fa-solid fa-medal text-[12px] w-4 text-center"></i> Scorebord
                                    </a>
                                </div>

                                <div class="border-t border-[#564D4A]/6 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#564D4A]/50 hover:text-red-600 hover:bg-red-50 transition cursor-pointer">
                                            <i class="fa-solid fa-right-from-bracket text-[12px] w-4 text-center"></i> Uitloggen
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                           class="hidden sm:inline-flex text-sm font-semibold px-4 py-2 rounded-xl transition-colors text-[#564D4A]/60 hover:text-[#564D4A]">
                            Inloggen
                        </a>
                        <a href="{{ route('register') }}"
                           class="text-sm font-bold px-5 py-2.5 rounded-xl transition-all bg-[#5B2333] hover:bg-[#5B2333]/85 text-white">
                            Gratis starten
                        </a>
                    @endauth

                    {{-- Mobile menu toggle --}}
                    <button @click="mobileOpen = !mobileOpen" class="md:hidden w-9 h-9 flex items-center justify-center rounded-lg transition hover:bg-[#564D4A]/8">
                        <i class="fa-solid" :class="mobileOpen ? 'fa-xmark' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>

            {{-- Mobile menu --}}
            <div x-show="mobileOpen" x-cloak x-transition.opacity class="md:hidden bg-white border-t border-[#564D4A]/6 shadow-lg rounded-2xl mt-1">
                <div class="px-6 py-4 grid gap-1">
                    <a href="{{ route('home') }}" class="text-sm font-semibold px-3 py-2.5 rounded-lg transition {{ request()->routeIs('home') ? 'text-[#5B2333] bg-[#5B2333]/5' : 'text-[#564D4A]/70 hover:text-[#564D4A] hover:bg-[#564D4A]/5' }}">Home</a>
                    <a href="{{ route('pages.games') }}" class="text-sm font-semibold px-3 py-2.5 rounded-lg transition {{ request()->routeIs('pages.games') ? 'text-[#5B2333] bg-[#5B2333]/5' : 'text-[#564D4A]/70 hover:text-[#564D4A] hover:bg-[#564D4A]/5' }}">Games</a>
                    <a href="{{ route('pages.how') }}" class="text-sm font-semibold px-3 py-2.5 rounded-lg transition {{ request()->routeIs('pages.how') ? 'text-[#5B2333] bg-[#5B2333]/5' : 'text-[#564D4A]/70 hover:text-[#564D4A] hover:bg-[#564D4A]/5' }}">Hoe het werkt</a>
                    <a href="{{ route('pages.pricing') }}" class="text-sm font-semibold px-3 py-2.5 rounded-lg transition {{ request()->routeIs('pages.pricing') ? 'text-[#5B2333] bg-[#5B2333]/5' : 'text-[#564D4A]/70 hover:text-[#564D4A] hover:bg-[#564D4A]/5' }}">Pricing</a>
                    <hr class="my-2 border-[#564D4A]/8">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-[#5B2333] hover:bg-[#5B2333]/5 px-3 py-2.5 rounded-lg transition flex items-center gap-2">
                            <i class="fa-solid fa-grid-2 text-[11px]"></i> Dashboard
                        </a>
                        <a href="{{ route('profile') }}" class="text-sm font-semibold text-[#564D4A]/70 hover:text-[#564D4A] hover:bg-[#564D4A]/5 px-3 py-2.5 rounded-lg transition flex items-center gap-2">
                            <i class="fa-solid fa-user text-[11px]"></i> Mijn Profiel
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left text-sm font-semibold text-[#564D4A]/50 hover:text-red-600 hover:bg-red-50 px-3 py-2.5 rounded-lg transition flex items-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-right-from-bracket text-[11px]"></i> Uitloggen
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-[#564D4A]/70 hover:text-[#564D4A] hover:bg-[#564D4A]/5 px-3 py-2.5 rounded-lg transition">Inloggen</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Page content --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-[#564D4A]/8 bg-white">
        <div class="max-w-6xl mx-auto px-6 py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                {{-- Brand --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-9 h-9 rounded-xl bg-[#5B2333] flex items-center justify-center">
                            <img src="/assets/logo-wit.png" class="max-h-4" alt="BrainForge logo">
                        </div>
                        <span class="font-black text-lg tracking-tight text-[#564D4A]">
                            Brain<span class="text-[#5B2333]">Forge.</span>
                        </span>
                    </div>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed max-w-xs">
                        Train je brein met dagelijkse puzzels en uitdagingen. Gratis te spelen, elke dag nieuw.
                    </p>
                </div>

                {{-- Games --}}
                <div>
                    <p class="font-bold text-sm text-[#564D4A] mb-4">Games</p>
                    <ul class="grid gap-2.5">
                        <li><a href="{{ route('pages.games.word-forge') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Woord Raden</a></li>
                        <li><a href="{{ route('pages.games.find-the-emoji') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Vind de Emoji</a></li>
                        <li><a href="{{ route('pages.games.sequence-rush') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Reeks Raden</a></li>
                        <li><a href="{{ route('pages.games.flag-guess') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Vlaggen Quiz</a></li>
                        <li><a href="{{ route('pages.games.sudoku') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Sudoku</a></li>
                        <li><a href="{{ route('pages.games') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Alle games →</a></li>
                    </ul>
                </div>

                {{-- Platform --}}
                <div>
                    <p class="font-bold text-sm text-[#564D4A] mb-4">Platform</p>
                    <ul class="grid gap-2.5">
                        <li><a href="{{ route('pages.how') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Hoe het werkt</a></li>
                        <li><a href="{{ route('pages.pricing') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Pricing</a></li>
                        <li><a href="{{ route('pages.categorie.hersenkrakers') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Hersenkrakers</a></li>
                        <li><a href="{{ route('pages.categorie.geheugentraining') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Geheugentraining</a></li>
                        <li><a href="{{ route('register') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Account aanmaken</a></li>
                        <li><a href="{{ route('login') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Inloggen</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div>
                    <p class="font-bold text-sm text-[#564D4A] mb-4">Juridisch</p>
                    <ul class="grid gap-2.5">
                        <li><a href="{{ route('pages.terms') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Algemene voorwaarden</a></li>
                        <li><a href="{{ route('pages.privacy') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Privacybeleid</a></li>
                        <li><a href="{{ route('pages.cookies') }}" class="text-sm text-[#564D4A]/50 hover:text-[#564D4A] transition">Cookiebeleid</a></li>
                    </ul>
                </div>
            </div>

            <hr class="my-10 border-[#564D4A]/8">

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-[#564D4A]/30 font-medium">
                    &copy; {{ date('Y') }} BrainForge. Alle rechten voorbehouden.
                </p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

            document.querySelectorAll('[data-animate]').forEach(function(el) {
                observer.observe(el);
            });
        });
    </script>

</body>
</html>
