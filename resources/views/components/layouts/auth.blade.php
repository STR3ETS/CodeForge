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
                        <p class="font-semibold text-sm">Build your streak</p>
                        <p class="text-xs text-white/70 mt-1 leading-relaxed">
                            Do one challenge every day and stay in the flow.<br>Your streak will grow naturally.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 items-start rounded-2xl bg-white/10 border border-white/10 p-4 backdrop-blur-sm">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-calendar-check text-sm"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-sm">A new challenge every day</p>
                        <p class="text-xs text-white/70 mt-1 leading-relaxed">
                            Short assignments that you actually complete.<br>Choose easy, normal or hard.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 items-start rounded-2xl bg-white/10 border border-white/10 p-4 backdrop-blur-sm">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-medal text-sm"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-sm">Earn XP & badges</p>
                        <p class="text-xs text-white/70 mt-1 leading-relaxed">
                            Each completed challenge gives you XP.<br>Unlock badges and climb the leaderboard.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="min-h-screen flex justify-center items-center px-6 py-10">
        {{ $slot }}
    </div>
</body>
</html>