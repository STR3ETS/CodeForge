<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    <link rel="preload" href="{{ asset('fontawesome/css/all.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}"></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .auth-float { animation: authFloat 6s ease-in-out infinite; }
        .auth-float-delay { animation: authFloat 6s ease-in-out 2s infinite; }
        .auth-float-delay-2 { animation: authFloat 6s ease-in-out 4s infinite; }
        @keyframes authFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
    </style>
</head>

<body class="bg-[#F7F4F3] min-h-screen grid grid-cols-1 lg:grid-cols-2">
    {{-- Left panel --}}
    <div class="hidden lg:flex h-full relative overflow-hidden" style="background: linear-gradient(to bottom, #5B2333 0%, #7a3349 50%, #5B2333 100%);">
        <img src="/assets/stacked-waves-haikei.png" class="w-full h-full absolute inset-0 z-[1] object-cover opacity-15 pointer-events-none" alt="">

        <div class="relative z-10 h-full w-full flex flex-col justify-between py-10 px-10">
            {{-- Logo top --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-sm">
                    <img src="/assets/logo-wit.png" class="max-h-5" alt="Logo">
                </div>
                <span class="font-black text-white text-lg tracking-tight">BrainForge.</span>
            </a>

            {{-- Center content --}}
            <div class="flex-1 flex flex-col justify-center items-center">
                <div class="max-w-sm w-full space-y-4">
                    {{-- Headline --}}
                    <div class="mb-8">
                        <h2 class="text-3xl font-black text-white tracking-tight leading-tight">
                            Train je brein,<br>
                            <span class="text-white/50">elke dag opnieuw.</span>
                        </h2>
                        <p class="mt-3 text-sm text-white/40 leading-relaxed">
                            13 dagelijkse puzzels en breinspellen. Bouw je streak op, verdien XP en daag vrienden uit.
                        </p>
                    </div>

                    {{-- Floating cards --}}
                    <div class="relative h-[280px]">
                        {{-- Game card --}}
                        <div class="absolute top-0 left-0 auth-float">
                            <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl p-4 flex items-center gap-3 w-56">
                                <div class="w-10 h-10 rounded-xl bg-[#D6E4F0]/20 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-font text-[#D6E4F0] text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-white">Woord Raden</p>
                                    <p class="text-[10px] text-white/40">Opgelost in 01:24</p>
                                </div>
                            </div>
                        </div>

                        {{-- Streak notification --}}
                        <div class="absolute top-16 right-0 auth-float-delay">
                            <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl px-4 py-3 flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-orange-400/20 flex items-center justify-center">
                                    <i class="fa-solid fa-fire text-orange-400 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-white">21 dagen streak!</p>
                                    <p class="text-[9px] text-white/30">Blijf spelen</p>
                                </div>
                            </div>
                        </div>

                        {{-- XP level up --}}
                        <div class="absolute top-36 left-6 auth-float-delay-2">
                            <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl px-4 py-3 flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-green-400/20 flex items-center justify-center">
                                    <i class="fa-solid fa-arrow-trend-up text-green-400 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-white">Level 12 bereikt!</p>
                                    <p class="text-[9px] text-white/30">+25 coins verdiend</p>
                                </div>
                            </div>
                        </div>

                        {{-- Leaderboard --}}
                        <div class="absolute bottom-4 right-2 auth-float">
                            <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl px-4 py-3 flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-yellow-400/20 flex items-center justify-center">
                                    <i class="fa-solid fa-trophy text-yellow-400 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-white">Nieuw #1 op scorebord</p>
                                    <p class="text-[9px] text-white/30">Vlaggen Quiz</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom stats --}}
            <div class="flex items-center gap-8 text-center">
                <div>
                    <p class="text-xl font-black text-white">13</p>
                    <p class="text-[10px] text-white/30 font-medium">Games</p>
                </div>
                <div>
                    <p class="text-xl font-black text-white">250+</p>
                    <p class="text-[10px] text-white/30 font-medium">Cosmetics</p>
                </div>
                <div>
                    <p class="text-xl font-black text-white">0,-</p>
                    <p class="text-[10px] text-white/30 font-medium">Om te starten</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="min-h-screen flex flex-col justify-center items-center px-6 py-10 relative">
        {{-- Mobile logo --}}
        <a href="{{ route('home') }}" class="lg:hidden flex items-center gap-2.5 mb-8">
            <div class="w-9 h-9 rounded-xl bg-[#5B2333] flex items-center justify-center">
                <img src="/assets/logo-wit.png" class="max-h-4" alt="Logo">
            </div>
            <span class="font-black text-[#564D4A] text-base tracking-tight">BrainForge.</span>
        </a>

        {{-- Back link (desktop) --}}
        <a href="{{ route('home') }}" class="hidden lg:inline-flex absolute top-6 right-6 items-center gap-2 text-xs font-semibold text-[#564D4A]/30 hover:text-[#564D4A] transition">
            <i class="fa-solid fa-arrow-left text-[10px]"></i> Terug naar website
        </a>

        {{ $slot }}

        {{-- Mobile back link --}}
        <a href="{{ route('home') }}" class="lg:hidden mt-6 text-xs font-semibold text-[#564D4A]/30 hover:text-[#564D4A] transition">
            <i class="fa-solid fa-arrow-left text-[10px] mr-1"></i> Terug naar website
        </a>
    </div>
</body>
</html>
