@props([
    'title' => 'Dashboard',
    'active' => null, // optioneel: 'dashboard' | 'tasks' | ...
    'chatBadge' => 3, // optioneel
])

@php
    $activeKey = $active ?? (request()->routeIs('dashboard') ? 'dashboard' : 'dashboard');

    $nav = [
        ['key' => 'dashboard', 'label' => 'Dashboard',  'icon' => 'fa-solid fa-grid-2', 'href' => route('dashboard')],
        ['key' => 'daily', 'label' => 'Daily Challenges', 'icon' => 'fa-solid fa-bullseye-arrow', 'href' => route('dashboard.daily')],
        ['key' => 'leaderboard', 'label' => 'Leaderboard', 'icon' => 'fa-solid fa-medal', 'href' => route('leaderboard')],
        ['key' => 'friends',     'label' => 'Friends',    'icon' => 'fa-solid fa-user-group', 'href' => route('dashboard')],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    <link rel="preload" href="{{ asset('fontawesome/css/all.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}"></noscript>
    <script defer src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F7F4F3] h-screen overflow-hidden">
    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <aside class="w-[270px] shrink-0 hidden lg:flex flex-col bg-white border-r border-[#564D4A]/10">
            {{-- Brand --}}
            <div class="px-6 pt-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#5B2333] flex items-center justify-center">
                        <img src="/assets/logo-wit.png" class="max-h-5" alt="Logo">
                    </div>
                    <div>
                        <p class="text-[#564D4A] font-black tracking-tight text-lg leading-none">
                            CodeForge<span class="text-[#5B2333]">.</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Menu --}}
            <div class="px-4 mt-8">
                <p class="px-2 text-[11px] uppercase font-bold tracking-wider text-[#564D4A]/35">
                    Menu
                </p>

                <nav class="mt-3 grid gap-1">
                    @foreach ($nav as $item)
                        @php
                            $isActive = $activeKey === $item['key'];
                        @endphp

                        <a href="{{ $item['href'] }}"
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl transition
                                  {{ $isActive ? 'bg-[#5B2333]/10 text-[#5B2333]' : 'text-[#564D4A] hover:bg-[#564D4A]/5' }}">
                            <span class="flex items-center gap-3">
                                <i class="{{ $item['icon'] }} text-[14px]"></i>
                                <span class="text-[13px] font-semibold">
                                    {{ $item['label'] }}
                                </span>
                            </span>

                            @if (!empty($item['badge']))
                                <span class="min-w-6 h-6 px-2 inline-flex items-center justify-center rounded-full bg-[#5B2333] text-white text-[11px] font-bold">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- Upgrade card --}}
            
            {{-- Bottom --}}
            <div class="mt-auto">
                <div class="px-4 mt-6">
                    <div class="rounded-2xl p-4 bg-gradient-to-tl from-[#5B2333]/20 to-[#5B2333]/10">
                        <p class="text-[1.5rem] font-black text-[#564D4A] leading-tight">Upgrade<br>your plan</p>
                        <p class="text-xs text-[#564D4A] mt-2 leading-[1.3] font-semibold">
                            You've played 0 out of 5 free challanges today. Upgrade your plan and unlock full potential!
                        </p>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-[11px] font-semibold text-[#564D4A]/60">
                                Upgrade from Free to Pro
                            </div>
                            <div class="text-[11px] font-bold text-[#5B2333]">
                                Unlimited
                            </div>
                        </div>

                        <a href="#"
                        class="mt-4 inline-flex items-center justify-center w-full rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 transition text-white text-xs font-semibold py-2.5">
                            See plans
                        </a>
                    </div>
                </div>
                <div class="px-4 border-[#5B2333]">
                    <hr class="border-[#564D4A]/10 mt-6 mb-3.5">
                </div>
                <div class="px-4 pb-6">
                    <div class="grid gap-1">
                        <a href="#"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[#564D4A] hover:bg-[#564D4A]/5 transition">
                            <i class="fa-solid fa-gear text-[14px]"></i>
                            <span class="text-[13px] font-semibold">Settings</span>
                        </a>
                        <a href="#"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[#564D4A] hover:bg-[#564D4A]/5 transition">
                            <i class="fa-regular fa-circle-question text-[14px]"></i>
                            <span class="text-[13px] font-semibold">Help & Support</span>
                        </a>
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
            <header class="sticky top-0 z-20 bg-[#F7F4F3]/80 backdrop-blur border-b border-[#564D4A]/10">
                <div class="max-w-6xl mx-auto px-6 h-16 flex items-center gap-4">
                    <div class="min-w-0">
                        <p class="text-[13px] font-extrabold text-[#564D4A] truncate">{{ $title }}</p>
                    </div>
                    <div class="flex-1"></div>
                    <div class="flex items-center gap-3">
                        <div class="mr-4">
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
                            <div class="flex items-center gap-3">
                                <p class="text-[11px] text-[#564D4A] font-medium italic opacity-40">{{ $xpFmt }} / {{ $nextFmt }} XP</p>
                                <div class="w-[200px] h-[5px] rounded-full bg-[#564D4A]/5 overflow-hidden">
                                    <div class="h-full bg-[#5B2333] rounded-full" style="width: {{ (int)$xpMeta['percent'] }}%"></div>
                                </div>
                                <p class="text-[11px] text-[#5B2333] font-semibold">Level {{ (int)$xpMeta['level'] }}</p>
                            </div>
                        </div>
                        @php
                            $u = auth()->user();
                            $avatarUrl = $u->profile_picture ? asset('storage/' . $u->profile_picture) : null;
                        @endphp
                        <div class="w-[34px] h-[34px] rounded-xl overflow-hidden border border-[#564D4A]/10 bg-white">
                            <img src="{{ $avatarUrl }}" class="w-full h-full object-cover" alt="Avatar">
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                            @csrf
                            <button class="inline-flex items-center gap-2 bg-white border border-[#564D4A]/10 hover:border-[#564D4A]/20 px-4 py-2 rounded-xl text-xs font-semibold text-[#564D4A] transition">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 min-h-0 overflow-y-auto">
                <div class="max-w-6xl mx-auto px-6 py-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    <script>
    (function () {
        const canvas = document.getElementById('mainConfettiCanvas');
        if (!canvas) return;

        // ✅ Zorg dat canvas altijd echt de MAIN area size heeft
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

        // ✅ Centrale confetti (met localStorage “ik heb confetti gehad” flag)
        window.fireMainConfetti = function ({ gameKey, date } = {}) {
            const key = `cf:${gameKey || 'game'}:${date || 'na'}`;

            try {
                // Als je hem maar 1x per dag per game wil:
                if (localStorage.getItem(key)) return;
                localStorage.setItem(key, '1'); // 👈 dit is wat jij mistte
            } catch (e) {}

            // Confetti lib nog niet geladen? dan niks doen
            if (typeof window.confetti !== 'function') return;

            // Canvas size nog 0? probeer 1x opnieuw
            resizeCanvas();
            if (!canvas.width || !canvas.height) return;

            const cannon = window.confetti.create(canvas, { useWorker: true, resize: true });

            // ✅ Vanaf links & rechts onder (binnen MAIN), wat rustiger vallend
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