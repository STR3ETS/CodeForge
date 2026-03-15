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
</head>

<body class="bg-[#F7F4F3] min-h-screen grid grid-cols-1 lg:grid-cols-2">
    {{-- Left panel --}}
    <div class="hidden lg:block h-full bg-[#5B2333] relative overflow-hidden">
        <img src="/assets/stacked-waves-haikei.png" class="w-full h-full absolute inset-0 z-[1] object-cover opacity-30" alt="">
        <div class="absolute inset-0 z-0 bg-gradient-to-b from-[#5B2333]/70 via-[#5B2333]/75 to-[#5B2333]/90"></div>

        <div class="relative z-10 h-full flex flex-col justify-center items-center text-white px-10">
            <div class="grid gap-3 max-w-md w-full">
                <div class="flex gap-3 items-start rounded-2xl bg-white/10 border border-white/10 p-4 backdrop-blur-sm">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-fire text-sm"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-sm">Bouw je streak op</p>
                        <p class="text-xs text-white/70 mt-1 leading-relaxed">
                            Doe elke dag een challenge en blijf in de flow.<br>Je streak groeit vanzelf.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 items-start rounded-2xl bg-white/10 border border-white/10 p-4 backdrop-blur-sm">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-calendar-check text-sm"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-sm">Elke dag een nieuwe uitdaging</p>
                        <p class="text-xs text-white/70 mt-1 leading-relaxed">
                            Korte opdrachten die je écht afmaakt.<br>Kies makkelijk, normaal of moeilijk.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 items-start rounded-2xl bg-white/10 border border-white/10 p-4 backdrop-blur-sm">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-medal text-sm"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-sm">Verdien XP & badges</p>
                        <p class="text-xs text-white/70 mt-1 leading-relaxed">
                            Elke voltooide challenge geeft XP.<br>Unlock badges en stijg op het leaderboard.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="min-h-screen flex justify-center items-center px-6 py-10 relative">
        {{-- Terug naar website --}}
        <a href="{{ route('home') }}" class="absolute top-6 left-6 inline-flex items-center gap-2 text-sm font-semibold text-[#564D4A]/40 hover:text-[#564D4A] transition">
            <i class="fa-solid fa-arrow-left text-[11px]"></i> Terug naar website
        </a>

        {{ $slot }}
    </div>
</body>
</html>