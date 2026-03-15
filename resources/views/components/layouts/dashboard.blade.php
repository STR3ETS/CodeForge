@props([
    'title' => 'Dashboard',
    'active' => null,
    'chatBadge' => 3,
])

@php
    $activeKey = $active ?? (request()->routeIs('dashboard') ? 'dashboard' : 'dashboard');

    $pendingCount = auth()->check()
        ? \App\Models\Friendship::where('friend_id', auth()->id())->where('status', 'pending')->count()
        : 0;

    $sidebarUser = auth()->user();
    $sidebarIsFree = $sidebarUser && ($sidebarUser->plan ?? 'free') !== 'pro';
    $sidebarDone = $sidebarUser ? (int) ($sidebarUser->daily_challenges_done ?? 0) : 0;
    $sidebarLimit = 5;

    $nav = [
        ['key' => 'dashboard', 'label' => 'Dashboard',  'icon' => 'fa-solid fa-grid-2', 'href' => route('dashboard')],
        ['key' => 'leaderboard', 'label' => 'Scorebord', 'icon' => 'fa-solid fa-medal', 'href' => route('leaderboard')],
        ['key' => 'daily', 'label' => 'Uitdagingen', 'icon' => 'fa-solid fa-bullseye-arrow', 'href' => route('dashboard.daily')],
        ['key' => 'iqtest',     'label' => 'IQ Test',      'icon' => 'fa-solid fa-brain', 'href' => route('games.iqtest'), 'pro' => true],
        ['key' => 'shop',        'label' => 'Shop',         'icon' => 'fa-solid fa-bag-shopping', 'href' => route('shop')],
        ['key' => 'friends',     'label' => 'Vrienden',    'icon' => 'fa-solid fa-user-group', 'href' => route('friends.index'), 'badge' => $pendingCount > 0 ? $pendingCount : null],
        ['key' => 'profile',     'label' => 'Mijn Profiel', 'icon' => 'fa-solid fa-user', 'href' => route('profile')],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css">
    <link rel="preload" href="{{ asset('fontawesome/css/all.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}"></noscript>
    <script defer src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-gradient { background: linear-gradient(180deg, #5B2333 0%, #7a3349 100%); }

        /* Page transitions — uses margin instead of transform to avoid breaking fixed modals */
        #page-content {
            opacity: 0;
            margin-top: 16px;
            transition: opacity .35s cubic-bezier(.16,1,.3,1), margin-top .35s cubic-bezier(.16,1,.3,1);
        }
        #page-content.visible {
            opacity: 1;
            margin-top: 0;
        }
        #page-content.leaving {
            opacity: 0;
            margin-top: -10px;
            transition-duration: .18s;
            transition-timing-function: ease-in;
        }
    </style>

    <script>
        /* bfcache: force reload so game state is fresh */
        window.addEventListener('pageshow', function(e) {
            if (e.persisted) window.location.reload();
        });

        /* Fade out on internal link click */
        document.addEventListener('click', function(e) {
            var link = e.target.closest('a[href]');
            if (!link) return;
            var href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript') ||
                href.startsWith('mailto') || href.startsWith('tel') ||
                link.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey) return;
            try {
                var url = new URL(href, location.origin);
                if (url.origin !== location.origin) return;
                if (url.pathname === location.pathname && url.search === location.search) return;
            } catch(_) { return; }
            e.preventDefault();
            var el = document.getElementById('page-content');
            if (el) {
                el.classList.remove('visible');
                el.classList.add('leaving');
            }
            setTimeout(function() { window.location.href = href; }, 180);
        });
    </script>
</head>

<body class="bg-[#F7F4F3] h-screen overflow-hidden" style="font-family: 'Instrument Sans', sans-serif;">
    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <aside class="w-[270px] shrink-0 hidden lg:flex flex-col bg-white border-r border-[#564D4A]/6">
            {{-- Brand --}}
            <div class="px-6 pt-7 pb-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-[#5B2333] flex items-center justify-center">
                        <img src="/assets/logo-wit.png" class="max-h-5" alt="BrainForge logo">
                    </div>
                    <span class="font-black text-lg tracking-tight text-[#564D4A]">
                        Brain<span class="text-[#5B2333]">Forge.</span>
                    </span>
                </a>
            </div>

            {{-- Menu --}}
            <div class="px-4 mt-6">
                <p class="px-3 text-[10px] uppercase font-bold tracking-widest text-[#564D4A]/30">
                    Menu
                </p>

                <nav class="mt-2.5 relative grid gap-0.5" id="sidebar-nav">
                    @foreach ($nav as $i => $item)
                        @php
                            $isActive = $activeKey === $item['key'];
                        @endphp

                        <a href="{{ $item['href'] }}"
                           data-nav-index="{{ $i }}"
                           class="sidebar-nav-link relative z-[1] flex items-center justify-between px-3 py-2.5 rounded-xl transition-colors duration-200
                                  {{ $isActive ? 'text-[#5B2333]' : 'text-[#564D4A]/70 hover:text-[#564D4A]' }}"
                           @if($isActive) data-active @endif>
                            <span class="flex items-center gap-3">
                                <i class="{{ $item['icon'] }} text-[13px]"></i>
                                <span class="text-[13px] font-semibold">
                                    {{ $item['label'] }}
                                </span>
                            </span>

                            @if (!empty($item['pro']) && $sidebarIsFree)
                                <span class="px-1.5 py-0.5 rounded-md bg-gradient-to-r from-amber-400 to-amber-500 text-white text-[9px] font-black uppercase tracking-wide">Pro</span>
                            @elseif (!empty($item['badge']))
                                <span class="min-w-5 h-5 px-1.5 inline-flex items-center justify-center rounded-full bg-[#5B2333] text-white text-[10px] font-bold">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    @endforeach

                    {{-- Sliding active indicator --}}
                    <div id="nav-indicator" class="absolute left-0 right-0 rounded-xl bg-[#5B2333]/10 transition-all duration-300 cubic-bezier(.16,1,.3,1) pointer-events-none" style="opacity:0;"></div>
                </nav>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var nav = document.getElementById('sidebar-nav');
                        var indicator = document.getElementById('nav-indicator');
                        if (!nav || !indicator) return;

                        function moveIndicator(link, animate) {
                            if (!link) { indicator.style.opacity = '0'; return; }
                            var navRect = nav.getBoundingClientRect();
                            var linkRect = link.getBoundingClientRect();
                            indicator.style.top = (linkRect.top - navRect.top) + 'px';
                            indicator.style.height = linkRect.height + 'px';
                            if (!animate) indicator.style.transition = 'none';
                            indicator.style.opacity = '1';
                            if (!animate) requestAnimationFrame(function() {
                                indicator.style.transition = '';
                            });
                        }

                        /* Set initial position without animation */
                        var active = nav.querySelector('[data-active]');
                        moveIndicator(active, false);

                        /* On link click, slide indicator + swap text color before navigating */
                        nav.addEventListener('click', function(e) {
                            var link = e.target.closest('.sidebar-nav-link');
                            if (!link) return;
                            moveIndicator(link, true);
                            nav.querySelectorAll('.sidebar-nav-link').forEach(function(l) {
                                l.style.color = l === link ? '#5B2333' : 'rgb(86 77 74 / 0.7)';
                            });
                        });
                    });
                </script>
            </div>

            {{-- Bottom --}}
            <div class="mt-auto">
                {{-- Upgrade card --}}
                @if($sidebarIsFree)
                <div class="px-4 mt-6">
                    <div class="sidebar-gradient rounded-2xl p-5 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                        <div class="absolute bottom-0 left-0 w-16 h-16 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                        <div class="relative">
                            <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center mb-3">
                                <i class="fa-solid fa-crown text-yellow-300"></i>
                            </div>
                            <p class="text-sm font-black text-white leading-tight">Upgrade naar Pro</p>
                            <p class="text-[11px] text-white/50 mt-1.5 leading-snug font-medium">
                                Onbeperkt games, GIF profielfoto's & Pro badge.
                            </p>

                            <div class="mt-3 flex items-center gap-2">
                                <div class="flex-1 h-[4px] rounded-full bg-white/10 overflow-hidden">
                                    <div class="h-full rounded-full bg-white/50" style="width: {{ min(100, ($sidebarDone / max(1, $sidebarLimit)) * 100) }}%"></div>
                                </div>
                                <span class="text-[10px] font-bold text-white/40">{{ $sidebarDone }}/{{ $sidebarLimit }}</span>
                            </div>

                            <a href="{{ route('pages.pricing') }}"
                               class="mt-4 flex items-center justify-center w-full rounded-xl bg-white hover:bg-white/90 transition text-[#5B2333] text-xs font-bold py-2.5">
                                Bekijk Pro
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <div class="px-4">
                    <hr class="border-[#564D4A]/6 my-4">
                </div>
                <div class="px-4 pb-6">
                    <div class="grid gap-0.5">
                        <a href="{{ route('home') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[#564D4A]/50 hover:text-[#564D4A] hover:bg-[#564D4A]/5 transition">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[13px]"></i>
                            <span class="text-[13px] font-semibold">Naar website</span>
                        </a>
                        @if(auth()->user()?->plan === 'pro')
                            <a href="{{ route('subscription.portal') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[#564D4A]/50 hover:text-[#564D4A] hover:bg-[#564D4A]/5 transition">
                                <i class="fa-solid fa-credit-card text-[13px]"></i>
                                <span class="text-[13px] font-semibold">Abonnement</span>
                            </a>
                        @endif
                        @if(auth()->user()?->is_admin)
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500/60 hover:text-red-500 hover:bg-red-50 transition">
                                <i class="fa-solid fa-shield-halved text-[13px]"></i>
                                <span class="text-[13px] font-semibold">Admin</span>
                            </a>
                        @endif
                        <a href="{{ route('settings') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ ($active ?? '') === 'settings' ? 'text-[#5B2333] bg-[#5B2333]/5 font-bold' : 'text-[#564D4A]/50 hover:text-[#564D4A] hover:bg-[#564D4A]/5' }}">
                            <i class="fa-solid fa-gear text-[13px]"></i>
                            <span class="text-[13px] font-semibold">Instellingen</span>
                        </a>
                        <a href="#"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[#564D4A]/50 hover:text-[#564D4A] hover:bg-[#564D4A]/5 transition">
                            <i class="fa-regular fa-circle-question text-[13px]"></i>
                            <span class="text-[13px] font-semibold">Help & Ondersteuning</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-[#564D4A]/50 hover:text-red-600 hover:bg-red-50 transition cursor-pointer">
                                <i class="fa-solid fa-right-from-bracket text-[13px]"></i>
                                <span class="text-[13px] font-semibold">Uitloggen</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- MAIN --}}
        <div class="flex-1 min-w-0 flex flex-col min-h-0 relative">
            <canvas
                id="mainConfettiCanvas"
                class="pointer-events-none absolute inset-0 w-full h-full z-[60]"
                aria-hidden="true"
            ></canvas>
            {{-- Topbar --}}
            <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-[#564D4A]/6">
                <div class="max-w-6xl mx-auto px-6 h-16 flex items-center gap-4">
                    {{-- Mobile logo --}}
                    <a href="{{ route('dashboard') }}" class="lg:hidden flex items-center gap-2 mr-2">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333] flex items-center justify-center">
                            <img src="/assets/logo-wit.png" class="max-h-3.5" alt="BrainForge logo">
                        </div>
                    </a>

                    <div class="min-w-0">
                        <p class="text-sm font-black text-[#564D4A] tracking-tight truncate">{{ $title }}</p>
                    </div>
                    <div class="flex-1"></div>
                    <div class="flex items-center gap-3">
                        {{-- XP bar --}}
                        <div class="hidden sm:flex">
                            @php
                                $u = auth()->user();
                                $xpMeta = $u?->levelMeta() ?? [
                                    'xp' => 0, 'level' => 1, 'nextXp' => 5000,
                                    'prevXp' => 0, 'inLevel' => 0, 'nextInLevel' => 5000,
                                    'percent' => 0
                                ];

                                $xpFmt = number_format((int)($xpMeta['inLevel'] ?? 0), 0, ',', '.');
                                $nextFmt = number_format((int)($xpMeta['nextInLevel'] ?? 0), 0, ',', '.');
                            @endphp
                            <div id="xp-bar-wrapper" class="relative flex items-center gap-3">
                                <div class="w-[140px] h-[5px] rounded-full bg-[#564D4A]/8 overflow-hidden">
                                    <div id="xp-bar-fill" class="h-full bg-[#5B2333] rounded-full transition-all duration-500" style="width: {{ (int)$xpMeta['percent'] }}%"></div>
                                </div>
                                <p id="xp-bar-text" class="w-[70px] text-[10px] text-[#564D4A]/40 font-semibold tabular-nums">{{ $xpFmt }}/{{ $nextFmt }}</p>
                                <span id="xp-bar-level" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#5B2333]/8 text-[#5B2333] text-[11px] font-bold">
                                    <i class="fa-solid fa-bolt text-[9px]"></i> Lvl {{ (int)$xpMeta['level'] }}
                                </span>
                            </div>
                            <style>
                                @keyframes xp-slide-in {
                                    0%   { opacity: 0; }
                                    10%  { opacity: 1; }
                                    60%  { opacity: 1; transform: translateX(0); }
                                    100% { opacity: 0; transform: translateX(20px); }
                                }
                                @keyframes xp-glow {
                                    0%, 100% { box-shadow: 0 0 0 0 rgba(91,35,51,0); }
                                    50%      { box-shadow: 0 0 8px 3px rgba(91,35,51,0.35); }
                                }
                                .xp-pop-badge {
                                    position: absolute;
                                    left: -54px;
                                    top: 4px;
                                    pointer-events: none;
                                    animation: xp-slide-in 1.6s ease-in-out forwards;
                                    white-space: nowrap;
                                }
                                .xp-bar-glow {
                                    animation: xp-glow 0.8s ease-in-out 2;
                                    border-radius: 9999px;
                                }
                            </style>
                        </div>

                        {{-- Coins --}}
                        <a href="{{ route('shop') }}" class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#F59E0B]/8 hover:bg-[#F59E0B]/15 text-[#B88B2A] text-[11px] font-bold transition">
                            <i class="fa-solid fa-coins text-[#F59E0B] text-[9px]"></i>
                            {{ number_format(auth()->user()->coins ?? 0, 0, ',', '.') }}
                        </a>

                        {{-- Avatar --}}
                        @php
                            $u = auth()->user();
                            $avatarUrl = $u->profile_picture ? asset('storage/' . $u->profile_picture) : null;
                        @endphp
                        <a href="{{ route('profile') }}" class="w-9 h-9 rounded-xl overflow-hidden border border-[#564D4A]/8 bg-white hover:border-[#5B2333]/30 transition">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" class="w-full h-full object-cover" alt="{{ $u->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/8 text-[#564D4A] font-bold text-xs">
                                    {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                                </div>
                            @endif
                        </a>

                    </div>
                </div>
            </header>

            <main class="flex-1 min-h-0 overflow-y-auto">
                <div id="page-content" class="max-w-6xl mx-auto px-6 py-8">
                    {{ $slot }}
                </div>
                <script>
                    /* Trigger fade-in immediately — runs as soon as #page-content is in the DOM */
                    requestAnimationFrame(function() {
                        requestAnimationFrame(function() {
                            document.getElementById('page-content').classList.add('visible');
                        });
                    });
                </script>
            </main>
        </div>
    </div>
    {{-- FLOATING CHAT --}}
    @auth
    <div x-data="chatWidget()" x-cloak class="fixed bottom-6 right-6 z-[80]" @keydown.escape.window="if(open) open = false">
        {{-- Chat FAB --}}
        <button @click="toggle()" class="w-14 h-14 rounded-full bg-[#5B2333] hover:bg-[#5B2333]/85 text-white shadow-lg shadow-[#5B2333]/25 flex items-center justify-center transition cursor-pointer relative">
            <i class="fa-solid fa-comment-dots text-lg" x-show="!open"></i>
            <i class="fa-solid fa-xmark text-lg" x-show="open" x-cloak></i>
            <span x-show="totalUnread > 0 && !open" x-cloak
                class="absolute -top-1 -right-1 min-w-5 h-5 px-1 flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold"
                x-text="totalUnread > 99 ? '99+' : totalUnread"></span>
        </button>

        {{-- Chat window --}}
        <div x-show="open" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="absolute bottom-[72px] right-0 w-[420px] h-[480px] bg-white rounded-2xl shadow-2xl shadow-black/15 border border-[#564D4A]/8 overflow-hidden flex flex-col">

            {{-- CONVERSATION LIST VIEW --}}
            <template x-if="!activeChat">
                <div class="flex flex-col h-full">
                    <div class="px-5 pt-5 pb-3 border-b border-[#564D4A]/6">
                        <h3 class="text-base font-black text-[#564D4A]">Berichten</h3>
                        <p class="text-[11px] font-semibold text-[#564D4A]/40 mt-0.5">Chat met je vrienden</p>
                    </div>

                    <div class="flex-1 overflow-y-auto">
                        {{-- Loading --}}
                        <div x-show="loadingConvos" class="flex items-center justify-center py-12">
                            <i class="fa-solid fa-spinner fa-spin text-[#5B2333] text-xl"></i>
                        </div>

                        {{-- Empty --}}
                        <div x-show="!loadingConvos && conversations.length === 0" class="px-5 py-12 text-center">
                            <div class="w-14 h-14 mx-auto rounded-2xl bg-[#F7F4F3] flex items-center justify-center mb-3">
                                <i class="fa-solid fa-user-group text-[#564D4A]/20 text-xl"></i>
                            </div>
                            <p class="text-sm font-bold text-[#564D4A]">Geen vrienden</p>
                            <p class="text-[11px] text-[#564D4A]/40 mt-1">Voeg vrienden toe om te chatten.</p>
                        </div>

                        {{-- Conversation rows --}}
                        <template x-for="c in conversations" :key="c.user.id">
                            <button @click="openChat(c.user)"
                                class="w-full flex items-center gap-3 px-5 py-3.5 hover:bg-[#F7F4F3] transition text-left cursor-pointer border-b border-[#564D4A]/4 last:border-b-0">
                                <div class="w-10 h-10 rounded-full overflow-hidden bg-[#F7F4F3] border border-[#564D4A]/6 shrink-0">
                                    <template x-if="c.user.profile_picture_url">
                                        <img :src="c.user.profile_picture_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!c.user.profile_picture_url">
                                        <div class="w-full h-full flex items-center justify-center text-[#564D4A]/30 font-bold text-xs"
                                            x-text="c.user.initials"></div>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-bold text-[#564D4A] truncate" x-text="c.user.name"></p>
                                        <span x-show="c.last_message" class="text-[10px] text-[#564D4A]/30 font-semibold shrink-0"
                                            x-text="c.last_message?.time"></span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2 mt-0.5">
                                        <p class="text-[11px] text-[#564D4A]/45 font-medium truncate"
                                            x-text="c.last_message ? ((c.last_message.is_me ? 'Jij: ' : '') + c.last_message.body) : 'Nog geen berichten'"></p>
                                        <span x-show="c.unread > 0"
                                            class="min-w-5 h-5 px-1 flex items-center justify-center rounded-full bg-[#5B2333] text-white text-[10px] font-bold shrink-0"
                                            x-text="c.unread"></span>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- CHAT VIEW --}}
            <template x-if="activeChat">
                <div class="flex flex-col h-full">
                    {{-- Chat header --}}
                    <div class="px-4 py-3 border-b border-[#564D4A]/6 flex items-center gap-3 shrink-0">
                        <button @click="closeChat()" class="w-8 h-8 rounded-lg bg-[#564D4A]/5 hover:bg-[#564D4A]/10 flex items-center justify-center transition cursor-pointer shrink-0">
                            <i class="fa-solid fa-arrow-left text-[#564D4A]/50 text-xs"></i>
                        </button>
                        <div class="w-8 h-8 rounded-full overflow-hidden bg-[#F7F4F3] border border-[#564D4A]/6 shrink-0">
                            <template x-if="activeChat.profile_picture_url">
                                <img :src="activeChat.profile_picture_url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!activeChat.profile_picture_url">
                                <div class="w-full h-full flex items-center justify-center text-[#564D4A]/30 font-bold text-[10px]"
                                    x-text="activeChat.initials"></div>
                            </template>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#564D4A] truncate" x-text="activeChat.name"></p>
                            <p class="text-[10px] font-semibold text-[#564D4A]/35" x-text="'Level ' + activeChat.level"></p>
                        </div>
                    </div>

                    {{-- Messages --}}
                    <div x-ref="msgContainer" class="flex-1 overflow-y-auto px-4 py-4 space-y-3" style="scroll-behavior: smooth;">
                        <div x-show="loadingMsgs" class="flex items-center justify-center py-8">
                            <i class="fa-solid fa-spinner fa-spin text-[#5B2333]"></i>
                        </div>

                        <div x-show="!loadingMsgs && messages.length === 0" class="text-center py-8">
                            <p class="text-[11px] text-[#564D4A]/30 font-semibold">Nog geen berichten. Zeg hoi! 👋</p>
                        </div>

                        <template x-for="msg in messages" :key="msg.id">
                            <div :class="msg.is_me ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="msg.is_me
                                    ? 'bg-[#5B2333] rounded-2xl rounded-br-sm max-w-[75%] px-4 py-2.5'
                                    : 'bg-[#F7F4F3] rounded-2xl rounded-bl-sm max-w-[75%] px-4 py-2.5'">
                                    <p class="text-[13px] leading-relaxed break-words [&_a]:underline [&_a]:hover:opacity-70"
                                        :class="msg.is_me ? 'text-white/90' : 'text-[#564D4A]'"
                                        x-html="linkify(msg.body)"></p>
                                    <p class="text-[9px] mt-1 font-semibold"
                                        :class="msg.is_me ? 'text-white/30 text-right' : 'text-[#564D4A]/25'"
                                        x-text="msg.time"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Input --}}
                    <div class="px-4 py-3 border-t border-[#564D4A]/6 shrink-0">
                        <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                            <input x-ref="chatInput" x-model="draft" type="text" maxlength="1000"
                                placeholder="Typ een bericht..."
                                class="flex-1 px-4 py-2.5 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-sm text-[#564D4A] placeholder-[#564D4A]/30 focus:border-[#5B2333] focus:outline-none transition">
                            <button type="submit" :disabled="!draft.trim() || sending"
                                class="w-10 h-10 rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 text-white flex items-center justify-center transition cursor-pointer disabled:opacity-40 shrink-0">
                                <i class="fa-solid fa-paper-plane text-xs" x-show="!sending"></i>
                                <i class="fa-solid fa-spinner fa-spin text-xs" x-show="sending" x-cloak></i>
                            </button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
    function linkify(text) {
        const escaped = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        return escaped.replace(/(https?:\/\/[^\s<]+)/g, '<a href="$1" class="underline hover:opacity-70 transition">$1</a>');
    }

    function chatWidget() {
        return {
            open: false,
            activeChat: null,
            conversations: [],
            messages: [],
            draft: '',
            sending: false,
            loadingConvos: false,
            loadingMsgs: false,
            totalUnread: 0,
            pollInterval: null,
            msgPollInterval: null,

            init() {
                // Poll unread count every 30s
                this.fetchUnread();
                this.pollInterval = setInterval(() => this.fetchUnread(), 30000);
            },

            destroy() {
                clearInterval(this.pollInterval);
                clearInterval(this.msgPollInterval);
            },

            async toggle() {
                this.open = !this.open;
                if (this.open && !this.activeChat) {
                    this.loadConversations();
                }
            },

            async fetchUnread() {
                try {
                    const r = await fetch('/chat/unread', { headers: { 'Accept': 'application/json' } });
                    const d = await r.json();
                    this.totalUnread = d.count || 0;
                } catch(e) {}
            },

            async loadConversations() {
                this.loadingConvos = true;
                try {
                    const r = await fetch('/chat/conversations', { headers: { 'Accept': 'application/json' } });
                    const d = await r.json();
                    if (d.ok) this.conversations = d.conversations;
                    this.totalUnread = d.total_unread || 0;
                } catch(e) {}
                this.loadingConvos = false;
            },

            async openChat(user) {
                this.activeChat = user;
                this.messages = [];
                this.draft = '';
                this.loadingMsgs = true;

                try {
                    const r = await fetch('/chat/messages/' + user.id, { headers: { 'Accept': 'application/json' } });
                    const d = await r.json();
                    if (d.ok) {
                        this.messages = d.messages;
                        this.activeChat = d.partner;
                    }
                } catch(e) {}
                this.loadingMsgs = false;

                this.$nextTick(() => {
                    this.scrollDown();
                    this.$refs.chatInput?.focus();
                });

                // Update unread
                this.fetchUnread();
                this.loadConversations();

                // Poll for new messages every 5s
                clearInterval(this.msgPollInterval);
                this.msgPollInterval = setInterval(() => this.pollMessages(), 5000);
            },

            closeChat() {
                this.activeChat = null;
                clearInterval(this.msgPollInterval);
                this.loadConversations();
            },

            async sendMessage() {
                if (!this.draft.trim() || this.sending) return;
                this.sending = true;
                const body = this.draft.trim();
                this.draft = '';

                try {
                    const r = await fetch('/chat/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ receiver_id: this.activeChat.id, body }),
                    });
                    const d = await r.json();
                    if (d.ok) {
                        this.messages.push(d.message);
                        this.$nextTick(() => this.scrollDown());
                    }
                } catch(e) {}
                this.sending = false;
                this.$refs.chatInput?.focus();
            },

            async pollMessages() {
                if (!this.activeChat) return;
                try {
                    const r = await fetch('/chat/messages/' + this.activeChat.id, { headers: { 'Accept': 'application/json' } });
                    const d = await r.json();
                    if (d.ok && d.messages.length !== this.messages.length) {
                        const wasAtBottom = this.isAtBottom();
                        this.messages = d.messages;
                        if (wasAtBottom) this.$nextTick(() => this.scrollDown());
                        this.fetchUnread();
                    }
                } catch(e) {}
            },

            scrollDown() {
                const c = this.$refs.msgContainer;
                if (c) c.scrollTop = c.scrollHeight;
            },

            isAtBottom() {
                const c = this.$refs.msgContainer;
                if (!c) return true;
                return c.scrollHeight - c.scrollTop - c.clientHeight < 60;
            },
        };
    }
    </script>
    @endauth

    {{-- UPDATE MODAL v1.1 --}}
    <div
        x-data="{
            show: false,
            init() {
                const key = 'cf:update:v1.1';
                if (!localStorage.getItem(key)) {
                    setTimeout(() => { this.show = true; }, 600);
                }
            },
            dismiss() {
                localStorage.setItem('cf:update:v1.1', '1');
                this.show = false;
            }
        }"
        x-init="init()"
        x-show="show"
        x-cloak
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @keydown.escape.window="dismiss()"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-[#564D4A]/60 backdrop-blur-md" @click="dismiss()"></div>

        {{-- Card --}}
        <div
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden"
        >
            {{-- Hero --}}
            <div class="relative h-36 bg-gradient-to-br from-[#5B2333] to-[#7a3349] flex items-center justify-center overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-4 left-6 w-20 h-20 rounded-full bg-white/20"></div>
                    <div class="absolute bottom-2 right-8 w-14 h-14 rounded-full bg-white/15"></div>
                    <div class="absolute top-8 right-20 w-8 h-8 rounded-full bg-white/10"></div>
                </div>
                <div class="relative flex flex-col items-center gap-2">
                    <div class="w-14 h-14 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center">
                        <i class="fa-solid fa-rocket text-white text-2xl"></i>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-white/40 text-[11px] font-bold tracking-wide line-through">CODEFORGE</span>
                        <i class="fa-solid fa-arrow-right text-white/30 text-[8px]"></i>
                        <span class="text-white/70 text-[11px] font-bold tracking-wide">BRAINFORGE</span>
                    </div>
                </div>

                <button @click="dismiss()"
                    class="cursor-pointer absolute top-3 right-3 w-8 h-8 rounded-xl bg-white/15 hover:bg-white/30 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-white text-xs"></i>
                </button>

                <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white text-[#5B2333] text-[10px] font-bold">
                    <i class="fa-solid fa-sparkles text-[9px]"></i> V1.1
                </span>
            </div>

            {{-- Content --}}
            <div class="p-6">
                <h2 class="text-xl font-black text-[#564D4A] leading-tight">Update V1.1 is live!</h2>
                <p class="mt-1.5 text-xs font-semibold text-[#564D4A]/50 leading-relaxed">
                    Een grote update met nieuwe features en verbeteringen.
                </p>

                <div class="mt-4 space-y-2">
                    <div class="flex items-start gap-3 p-2.5 rounded-xl bg-[#F7F4F3]">
                        <div class="w-8 h-8 rounded-lg bg-[#F59E0B]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-solid fa-bag-shopping text-[#B88B2A] text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[#564D4A]">Cosmetica Shop</p>
                            <p class="text-[11px] font-medium text-[#564D4A]/50 leading-snug">250+ items: borders, hoedjes, effecten, badges & naamkleuren. Dagelijks wisselend bundelpakket.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-2.5 rounded-xl bg-[#F7F4F3]">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-solid fa-crown text-[#5B2333] text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[#564D4A]">Pro Exclusives</p>
                            <p class="text-[11px] font-medium text-[#564D4A]/50 leading-snug">Epische & legendarische cosmetics, geanimeerde naamkleuren en custom badges alleen voor Pro.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-2.5 rounded-xl bg-[#F7F4F3]">
                        <div class="w-8 h-8 rounded-lg bg-[#3B82F6]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-solid fa-user-group text-[#3B82F6] text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[#564D4A]">Vriendensysteem</p>
                            <p class="text-[11px] font-medium text-[#564D4A]/50 leading-snug">Voeg vrienden toe, bekijk hun profielen en vergelijk scores.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <a href="{{ route('shop') }}" @click="dismiss()"
                       class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/90 text-white text-xs font-bold py-3 transition active:scale-[0.98]">
                        <i class="fa-solid fa-bag-shopping text-[11px]"></i>
                        Bekijk de Shop
                    </a>
                    <button @click="dismiss()"
                        class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-[#564D4A]/5 hover:bg-[#564D4A]/10 text-[#564D4A]/55 text-xs font-semibold px-5 py-3 transition">
                        Sluiten
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const canvas = document.getElementById('mainConfettiCanvas');
        if (!canvas) return;

        const resizeCanvas = () => {
            const dpr = window.devicePixelRatio || 1;
            const w = canvas.clientWidth || 0;
            const h = canvas.clientHeight || 0;
            if (!w || !h) return;

            canvas.width  = Math.round(w * dpr);
            canvas.height = Math.round(h * dpr);
        };

        resizeCanvas();

        try {
            const ro = new ResizeObserver(resizeCanvas);
            ro.observe(canvas);
            ro.observe(canvas.parentElement);
        } catch (e) {}

        window.addEventListener('resize', resizeCanvas);

        window.fireMainConfetti = function ({ gameKey, date } = {}) {
            const key = `cf:${gameKey || 'game'}:${date || 'na'}`;

            try {
                if (localStorage.getItem(key)) return;
                localStorage.setItem(key, '1');
            } catch (e) {}

            if (typeof window.confetti !== 'function') return;

            resizeCanvas();
            if (!canvas.width || !canvas.height) return;

            const cannon = window.confetti.create(canvas, { useWorker: true, resize: true });

            const end = Date.now() + 2400;

            (function frame() {
                cannon({
                    particleCount: 6,
                    angle: 60,
                    spread: 55,
                    startVelocity: 28,
                    gravity: 1.05,
                    ticks: 360,
                    origin: { x: 0.02, y: 1 },
                });

                cannon({
                    particleCount: 6,
                    angle: 120,
                    spread: 55,
                    startVelocity: 28,
                    gravity: 1.05,
                    ticks: 360,
                    origin: { x: 0.98, y: 1 },
                });

                if (Date.now() < end) requestAnimationFrame(frame);
            })();
        };
    })();
    </script>
</body>
</html>