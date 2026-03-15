<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <meta name="description" content="Train je brein met dagelijkse puzzels, woordspellen en meer. Bouw je streak op, verdien XP en daag je vrienden uit.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    <link rel="preload" href="{{ asset('fontawesome/css/all.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}"></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html { scroll-behavior: smooth; }
        .hero-gradient { background: linear-gradient(135deg, #5B2333 0%, #7a3349 50%, #5B2333 100%); }
        .float-animation { animation: float 6s ease-in-out infinite; }
        .float-animation-delay { animation: float 6s ease-in-out 2s infinite; }
        .float-animation-delay-2 { animation: float 6s ease-in-out 4s infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .game-card:hover .game-icon { transform: scale(1.1); }
        .step-line::after {
            content: '';
            position: absolute;
            top: 2.5rem;
            left: 50%;
            width: 2px;
            height: calc(100% - 2.5rem);
            background: linear-gradient(to bottom, #5B2333, transparent);
        }
    </style>
</head>

<body class="bg-[#F7F4F3] text-[#564D4A] font-[Instrument_Sans] antialiased overflow-x-hidden">

    {{-- Navigation --}}
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" x-data="{ scrolled: false }"
         @scroll.window="scrolled = (window.scrollY > 20)">
        <div :class="scrolled ? 'bg-white/90 backdrop-blur-lg shadow-sm' : 'bg-transparent'"
             class="transition-all duration-300">
            <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#5B2333] flex items-center justify-center">
                        <img src="/assets/logo-wit.png" class="max-h-4" alt="BrainForge logo">
                    </div>
                    <span class="font-black text-lg tracking-tight" :class="scrolled ? 'text-[#564D4A]' : 'text-white'">
                        Brain<span class="text-[#5B2333]" :class="scrolled ? '' : '!text-white/60'">Forge.</span>
                    </span>
                </div>

                <div class="hidden md:flex items-center gap-8">
                    <a href="#games" class="text-sm font-semibold transition" :class="scrolled ? 'text-[#564D4A]/70 hover:text-[#564D4A]' : 'text-white/70 hover:text-white'">Games</a>
                    <a href="#how-it-works" class="text-sm font-semibold transition" :class="scrolled ? 'text-[#564D4A]/70 hover:text-[#564D4A]' : 'text-white/70 hover:text-white'">Hoe het werkt</a>
                    <a href="#features" class="text-sm font-semibold transition" :class="scrolled ? 'text-[#564D4A]/70 hover:text-[#564D4A]' : 'text-white/70 hover:text-white'">Features</a>
                    <a href="#pricing" class="text-sm font-semibold transition" :class="scrolled ? 'text-[#564D4A]/70 hover:text-[#564D4A]' : 'text-white/70 hover:text-white'">Pricing</a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="text-sm font-semibold px-4 py-2 rounded-xl transition" :class="scrolled ? 'text-[#564D4A] hover:bg-[#564D4A]/8' : 'text-white hover:bg-white/10'">
                        Inloggen
                    </a>
                    <a href="{{ route('register') }}" class="text-sm font-bold px-5 py-2.5 rounded-xl transition"
                       :class="scrolled ? 'bg-[#5B2333] text-white hover:bg-[#5B2333]/90' : 'bg-white text-[#5B2333] hover:bg-white/90'">
                        Gratis starten
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="hero-gradient relative min-h-[100vh] flex items-center overflow-hidden">
        <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-20 pointer-events-none" alt="">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#F7F4F3]"></div>

        <div class="relative z-10 max-w-6xl mx-auto px-6 pt-32 pb-24 w-full">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                {{-- Left: text --}}
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/15 backdrop-blur-sm mb-6">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        <span class="text-xs font-semibold text-white/80">11 dagelijkse games beschikbaar</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-[1.1] tracking-tight">
                        Train je brein,<br>
                        <span class="text-white/60">elke dag opnieuw.</span>
                    </h1>

                    <p class="mt-6 text-lg text-white/70 leading-relaxed max-w-lg">
                        Dagelijkse puzzels, woordspellen en logische uitdagingen.
                        Bouw je streak op, verdien XP en daag je vrienden uit.
                    </p>

                    <div class="mt-10 flex flex-wrap items-center gap-4">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-[#5B2333] font-bold text-sm px-7 py-4 rounded-2xl hover:bg-white/90 transition shadow-lg shadow-black/10">
                            <i class="fa-solid fa-bolt"></i> Gratis beginnen
                        </a>
                        <a href="#games" class="inline-flex items-center gap-2 text-white/80 hover:text-white font-semibold text-sm px-5 py-4 rounded-2xl border border-white/20 hover:bg-white/10 transition">
                            <i class="fa-solid fa-gamepad"></i> Bekijk de games
                        </a>
                    </div>

                    <div class="mt-10 flex items-center gap-6 text-white/50 text-xs font-medium">
                        <span><i class="fa-solid fa-check mr-1.5 text-green-400"></i> 100% gratis te spelen</span>
                        <span><i class="fa-solid fa-check mr-1.5 text-green-400"></i> Geen installatie nodig</span>
                    </div>
                </div>

                {{-- Right: floating game cards --}}
                <div class="hidden lg:block relative h-[480px]">
                    <div class="absolute top-4 left-8 float-animation">
                        <div class="bg-white rounded-2xl shadow-xl shadow-black/5 p-5 w-56">
                            <div class="w-12 h-12 rounded-xl bg-[#D6E4F0] flex items-center justify-center mb-3">
                                <i class="fa-solid fa-font text-[#4a7fa5] text-xl"></i>
                            </div>
                            <p class="font-bold text-sm text-[#564D4A]">Woord Raden</p>
                            <p class="text-[11px] text-[#564D4A]/50 mt-1">Raad het woord in 6 pogingen</p>
                        </div>
                    </div>

                    <div class="absolute top-0 right-4 float-animation-delay">
                        <div class="bg-white rounded-2xl shadow-xl shadow-black/5 p-5 w-56">
                            <div class="w-12 h-12 rounded-xl bg-[#FBE2D8] flex items-center justify-center mb-3">
                                <i class="fa-solid fa-face-grin text-[#c0705a] text-xl"></i>
                            </div>
                            <p class="font-bold text-sm text-[#564D4A]">Vind de Emoji</p>
                            <p class="text-[11px] text-[#564D4A]/50 mt-1">Vind de juiste emoji combinatie</p>
                        </div>
                    </div>

                    <div class="absolute top-44 right-12 float-animation-delay-2">
                        <div class="bg-white rounded-2xl shadow-xl shadow-black/5 p-5 w-56">
                            <div class="w-12 h-12 rounded-xl bg-[#D9EAD3] flex items-center justify-center mb-3">
                                <i class="fa-solid fa-arrow-up-1-9 text-[#5a8a4e] text-xl"></i>
                            </div>
                            <p class="font-bold text-sm text-[#564D4A]">Reeks Raden</p>
                            <p class="text-[11px] text-[#564D4A]/50 mt-1">Vul de reeks zo snel mogelijk aan</p>
                        </div>
                    </div>

                    <div class="absolute bottom-16 left-4 float-animation-delay">
                        <div class="bg-white rounded-2xl shadow-xl shadow-black/5 p-5 w-56">
                            <div class="w-12 h-12 rounded-xl bg-[#FFF3CD] flex items-center justify-center mb-3">
                                <i class="fa-solid fa-flag text-[#9a7a20] text-xl"></i>
                            </div>
                            <p class="font-bold text-sm text-[#564D4A]">Vlaggen Quiz</p>
                            <p class="text-[11px] text-[#564D4A]/50 mt-1">Herken vlaggen van over de wereld</p>
                        </div>
                    </div>

                    <div class="absolute bottom-4 right-0 float-animation-delay-2">
                        <div class="bg-white rounded-2xl shadow-xl shadow-black/5 p-5 w-48">
                            <div class="w-12 h-12 rounded-xl bg-[#E8D5F0] flex items-center justify-center mb-3">
                                <i class="fa-solid fa-cube text-[#7a4fa0] text-xl"></i>
                            </div>
                            <p class="font-bold text-sm text-[#564D4A]">Blok Drop</p>
                            <p class="text-[11px] text-[#564D4A]/50 mt-1">Tetris-achtige puzzel</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Social proof bar --}}
    <section class="relative z-10 -mt-16">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-white rounded-2xl shadow-lg shadow-black/5 border border-[#564D4A]/5 px-8 py-6 flex flex-wrap items-center justify-center gap-8 sm:gap-14">
                <div class="text-center">
                    <p class="text-2xl font-black text-[#5B2333]">11</p>
                    <p class="text-[11px] font-semibold text-[#564D4A]/50 mt-0.5">Unieke games</p>
                </div>
                <div class="w-px h-10 bg-[#564D4A]/10 hidden sm:block"></div>
                <div class="text-center">
                    <p class="text-2xl font-black text-[#5B2333]">3</p>
                    <p class="text-[11px] font-semibold text-[#564D4A]/50 mt-0.5">Moeilijkheden</p>
                </div>
                <div class="w-px h-10 bg-[#564D4A]/10 hidden sm:block"></div>
                <div class="text-center">
                    <p class="text-2xl font-black text-[#5B2333]">24/7</p>
                    <p class="text-[11px] font-semibold text-[#564D4A]/50 mt-0.5">Beschikbaar</p>
                </div>
                <div class="w-px h-10 bg-[#564D4A]/10 hidden sm:block"></div>
                <div class="text-center">
                    <p class="text-2xl font-black text-[#5B2333]"><i class="fa-solid fa-infinity text-xl"></i></p>
                    <p class="text-[11px] font-semibold text-[#564D4A]/50 mt-0.5">Gratis speelbaar</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Games showcase --}}
    <section id="games" class="max-w-6xl mx-auto px-6 py-28">
        <div class="text-center mb-16">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-gamepad text-[10px]"></i> ONZE GAMES
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                13 games, elke dag nieuw
            </h2>
            <p class="mt-4 text-[#564D4A]/60 max-w-lg mx-auto leading-relaxed">
                Van woordpuzzels tot logische reeksen. Kies je moeilijkheid en daag jezelf uit met een nieuwe uitdaging, elke dag.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            {{-- Word Forge --}}
            <div class="game-card group bg-white rounded-2xl border border-[#564D4A]/6 p-6 hover:shadow-lg hover:shadow-black/5 hover:-translate-y-1 transition-all duration-300">
                <div class="game-icon w-14 h-14 rounded-2xl bg-[#D6E4F0] flex items-center justify-center mb-4 transition-transform duration-300">
                    <i class="fa-solid fa-font text-[#4a7fa5] text-2xl"></i>
                </div>
                <h3 class="font-bold text-lg text-[#564D4A]">Woord Raden</h3>
                <p class="text-sm text-[#564D4A]/50 mt-2 leading-relaxed">Raad het verborgen woord in maximaal 6 pogingen. Elke gok geeft je hints.</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-[#D6E4F0]/50 text-[#4a7fa5] text-[10px] font-bold">WOORDEN</span>
                    <span class="px-2 py-0.5 rounded-md bg-[#564D4A]/5 text-[#564D4A]/40 text-[10px] font-bold">LOGICA</span>
                </div>
            </div>

            {{-- Find the Emoji --}}
            <div class="game-card group bg-white rounded-2xl border border-[#564D4A]/6 p-6 hover:shadow-lg hover:shadow-black/5 hover:-translate-y-1 transition-all duration-300">
                <div class="game-icon w-14 h-14 rounded-2xl bg-[#FBE2D8] flex items-center justify-center mb-4 transition-transform duration-300">
                    <i class="fa-solid fa-face-grin text-[#c0705a] text-2xl"></i>
                </div>
                <h3 class="font-bold text-lg text-[#564D4A]">Vind de Emoji</h3>
                <p class="text-sm text-[#564D4A]/50 mt-2 leading-relaxed">Ontdek welke emoji's bij de beschrijving horen. Test je creativiteit.</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-[#FBE2D8]/50 text-[#c0705a] text-[10px] font-bold">EMOJI</span>
                    <span class="px-2 py-0.5 rounded-md bg-[#564D4A]/5 text-[#564D4A]/40 text-[10px] font-bold">CREATIVITEIT</span>
                </div>
            </div>

            {{-- Sequence Rush --}}
            <div class="game-card group bg-white rounded-2xl border border-[#564D4A]/6 p-6 hover:shadow-lg hover:shadow-black/5 hover:-translate-y-1 transition-all duration-300">
                <div class="game-icon w-14 h-14 rounded-2xl bg-[#D9EAD3] flex items-center justify-center mb-4 transition-transform duration-300">
                    <i class="fa-solid fa-arrow-up-1-9 text-[#5a8a4e] text-2xl"></i>
                </div>
                <h3 class="font-bold text-lg text-[#564D4A]">Reeks Raden</h3>
                <p class="text-sm text-[#564D4A]/50 mt-2 leading-relaxed">Ontdek het patroon en vul de reeks aan. Hoe sneller, hoe beter.</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-[#D9EAD3]/50 text-[#5a8a4e] text-[10px] font-bold">GETALLEN</span>
                    <span class="px-2 py-0.5 rounded-md bg-[#564D4A]/5 text-[#564D4A]/40 text-[10px] font-bold">SNELHEID</span>
                </div>
            </div>

            {{-- Flag Guess --}}
            <div class="game-card group bg-white rounded-2xl border border-[#564D4A]/6 p-6 hover:shadow-lg hover:shadow-black/5 hover:-translate-y-1 transition-all duration-300">
                <div class="game-icon w-14 h-14 rounded-2xl bg-[#FFF3CD] flex items-center justify-center mb-4 transition-transform duration-300">
                    <i class="fa-solid fa-flag text-[#9a7a20] text-2xl"></i>
                </div>
                <h3 class="font-bold text-lg text-[#564D4A]">Vlaggen Quiz</h3>
                <p class="text-sm text-[#564D4A]/50 mt-2 leading-relaxed">Herken vlaggen van landen over de hele wereld. Test je aardrijkskundekennis.</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-[#FFF3CD]/50 text-[#9a7a20] text-[10px] font-bold">VLAGGEN</span>
                    <span class="px-2 py-0.5 rounded-md bg-[#564D4A]/5 text-[#564D4A]/40 text-[10px] font-bold">KENNIS</span>
                </div>
            </div>

            {{-- Block Drop --}}
            <div class="game-card group bg-white rounded-2xl border border-[#564D4A]/6 p-6 hover:shadow-lg hover:shadow-black/5 hover:-translate-y-1 transition-all duration-300">
                <div class="game-icon w-14 h-14 rounded-2xl bg-[#E8D5F0] flex items-center justify-center mb-4 transition-transform duration-300">
                    <i class="fa-solid fa-cube text-[#7a4fa0] text-2xl"></i>
                </div>
                <h3 class="font-bold text-lg text-[#564D4A]">Blok Drop</h3>
                <p class="text-sm text-[#564D4A]/50 mt-2 leading-relaxed">Plaats blokken strategisch op het bord. Maak rijen vrij en scoor punten.</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-[#E8D5F0]/50 text-[#7a4fa0] text-[10px] font-bold">PUZZEL</span>
                    <span class="px-2 py-0.5 rounded-md bg-[#564D4A]/5 text-[#564D4A]/40 text-[10px] font-bold">STRATEGIE</span>
                </div>
            </div>

            {{-- Sudoku --}}
            <div class="game-card group bg-white rounded-2xl border border-[#564D4A]/6 p-6 hover:shadow-lg hover:shadow-black/5 hover:-translate-y-1 transition-all duration-300">
                <div class="game-icon w-14 h-14 rounded-2xl bg-[#D0EAE8] flex items-center justify-center mb-4 transition-transform duration-300">
                    <i class="fa-solid fa-table-cells text-[#3a8a85] text-2xl"></i>
                </div>
                <h3 class="font-bold text-lg text-[#564D4A]">Sudoku</h3>
                <p class="text-sm text-[#564D4A]/50 mt-2 leading-relaxed">De klassieke nummerpuzzel. Vul het 9x9 grid in zonder herhalingen.</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-[#D0EAE8]/50 text-[#3a8a85] text-[10px] font-bold">KLASSIEK</span>
                    <span class="px-2 py-0.5 rounded-md bg-[#564D4A]/5 text-[#564D4A]/40 text-[10px] font-bold">LOGICA</span>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-28">
            <div class="text-center mb-16">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                    <i class="fa-solid fa-route text-[10px]"></i> HOE HET WERKT
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                    In 3 stappen aan de slag
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-10">
                <div class="text-center group">
                    <div class="w-16 h-16 rounded-2xl bg-[#5B2333] flex items-center justify-center mx-auto mb-5 group-hover:scale-110 transition-transform">
                        <span class="text-white font-black text-xl">1</span>
                    </div>
                    <h3 class="font-bold text-lg text-[#564D4A] mb-2">Maak een account</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">
                        Gratis aanmelden in 30 seconden. Geen creditcard nodig, geen verplichtingen.
                    </p>
                </div>

                <div class="text-center group">
                    <div class="w-16 h-16 rounded-2xl bg-[#5B2333] flex items-center justify-center mx-auto mb-5 group-hover:scale-110 transition-transform">
                        <span class="text-white font-black text-xl">2</span>
                    </div>
                    <h3 class="font-bold text-lg text-[#564D4A] mb-2">Kies je uitdaging</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">
                        Elke dag 11 nieuwe games. Kies je moeilijkheidsgraad: makkelijk, normaal of moeilijk.
                    </p>
                </div>

                <div class="text-center group">
                    <div class="w-16 h-16 rounded-2xl bg-[#5B2333] flex items-center justify-center mx-auto mb-5 group-hover:scale-110 transition-transform">
                        <span class="text-white font-black text-xl">3</span>
                    </div>
                    <h3 class="font-bold text-lg text-[#564D4A] mb-2">Bouw je streak op</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">
                        Speel elke dag en bouw een onbreekbare streak. Verdien XP, unlock badges en klim het leaderboard op.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="max-w-6xl mx-auto px-6 py-28">
        <div class="text-center mb-16">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-sparkles text-[10px]"></i> FEATURES
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                Meer dan alleen puzzels
            </h2>
            <p class="mt-4 text-[#564D4A]/60 max-w-lg mx-auto leading-relaxed">
                BrainForge is gebouwd om je gemotiveerd te houden met alles wat een gamer nodig heeft.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-[#564D4A]/6 p-7 hover:shadow-md hover:shadow-black/3 transition-all">
                <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-fire text-orange-500"></i>
                </div>
                <h3 class="font-bold text-[#564D4A] mb-1.5">Dagelijkse Streaks</h3>
                <p class="text-sm text-[#564D4A]/50 leading-relaxed">Speel elke dag en bouw een streak op. Hoe langer je streak, hoe meer respect.</p>
            </div>

            <div class="bg-white rounded-2xl border border-[#564D4A]/6 p-7 hover:shadow-md hover:shadow-black/3 transition-all">
                <div class="w-11 h-11 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-star text-purple-500"></i>
                </div>
                <h3 class="font-bold text-[#564D4A] mb-1.5">XP & Levels</h3>
                <p class="text-sm text-[#564D4A]/50 leading-relaxed">Verdien XP voor elke voltooide uitdaging. Level up en laat zien hoe goed je bent.</p>
            </div>

            <div class="bg-white rounded-2xl border border-[#564D4A]/6 p-7 hover:shadow-md hover:shadow-black/3 transition-all">
                <div class="w-11 h-11 rounded-xl bg-yellow-100 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-trophy text-yellow-500"></i>
                </div>
                <h3 class="font-bold text-[#564D4A] mb-1.5">Leaderboard</h3>
                <p class="text-sm text-[#564D4A]/50 leading-relaxed">Vergelijk je scores met andere spelers. Strijdt om de top positie op het leaderboard.</p>
            </div>

            <div class="bg-white rounded-2xl border border-[#564D4A]/6 p-7 hover:shadow-md hover:shadow-black/3 transition-all">
                <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-user-group text-blue-500"></i>
                </div>
                <h3 class="font-bold text-[#564D4A] mb-1.5">Vrienden</h3>
                <p class="text-sm text-[#564D4A]/50 leading-relaxed">Voeg vrienden toe en bekijk hun voortgang. Daag ze uit en speel samen.</p>
            </div>

            <div class="bg-white rounded-2xl border border-[#564D4A]/6 p-7 hover:shadow-md hover:shadow-black/3 transition-all">
                <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-scroll text-green-500"></i>
                </div>
                <h3 class="font-bold text-[#564D4A] mb-1.5">Dagelijkse Quests</h3>
                <p class="text-sm text-[#564D4A]/50 leading-relaxed">Voltooi speciale dagelijkse opdrachten voor extra XP en beloningen.</p>
            </div>

            <div class="bg-white rounded-2xl border border-[#564D4A]/6 p-7 hover:shadow-md hover:shadow-black/3 transition-all">
                <div class="w-11 h-11 rounded-xl bg-rose-100 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-gauge-high text-rose-500"></i>
                </div>
                <h3 class="font-bold text-[#564D4A] mb-1.5">3 Moeilijkheden</h3>
                <p class="text-sm text-[#564D4A]/50 leading-relaxed">Kies makkelijk, normaal of moeilijk. Perfect voor beginners en ervaren puzzelaars.</p>
            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section id="pricing" class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-28">
            <div class="text-center mb-16">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                    <i class="fa-solid fa-tag text-[10px]"></i> PRICING
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                    Gratis spelen, of ga Pro
                </h2>
                <p class="mt-4 text-[#564D4A]/60 max-w-lg mx-auto leading-relaxed">
                    BrainForge is gratis te spelen. Wil je meer? Upgrade naar Pro voor onbeperkte games en extra features.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">
                {{-- Free --}}
                <div class="bg-[#F7F4F3] rounded-2xl border border-[#564D4A]/8 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-11 h-11 rounded-xl bg-[#564D4A]/8 flex items-center justify-center">
                            <i class="fa-solid fa-user text-[#564D4A]/50"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-[#564D4A]">Gratis</h3>
                            <p class="text-xs text-[#564D4A]/40 font-medium">Voor altijd gratis</p>
                        </div>
                    </div>

                    <p class="text-4xl font-black text-[#564D4A] mb-1">&euro;0 <span class="text-sm font-semibold text-[#564D4A]/30">/ maand</span></p>
                    <p class="text-xs text-[#564D4A]/40 font-medium mb-8">Geen creditcard nodig</p>

                    <ul class="grid gap-3 mb-8">
                        <li class="flex items-center gap-2.5 text-sm text-[#564D4A]/70">
                            <i class="fa-solid fa-check text-green-500 text-xs"></i> 5 games per dag
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-[#564D4A]/70">
                            <i class="fa-solid fa-check text-green-500 text-xs"></i> Alle 13 game types
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-[#564D4A]/70">
                            <i class="fa-solid fa-check text-green-500 text-xs"></i> Streaks & XP
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-[#564D4A]/70">
                            <i class="fa-solid fa-check text-green-500 text-xs"></i> Leaderboard
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-[#564D4A]/70">
                            <i class="fa-solid fa-check text-green-500 text-xs"></i> Vrienden toevoegen
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="block w-full text-center bg-[#564D4A]/10 hover:bg-[#564D4A]/15 transition text-[#564D4A] font-bold text-sm py-3.5 rounded-xl">
                        Gratis starten
                    </a>
                </div>

                {{-- Pro --}}
                <div class="bg-[#5B2333] rounded-2xl p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center">
                                <i class="fa-solid fa-crown text-yellow-300"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-white">Pro</h3>
                                <p class="text-xs text-white/50 font-medium">Alles onbeperkt</p>
                            </div>
                        </div>

                        <p class="text-4xl font-black text-white mb-1">&euro;4,99 <span class="text-sm font-semibold text-white/40">/ maand</span></p>
                        <p class="text-xs text-white/40 font-medium mb-8">Elk moment opzegbaar</p>

                        <ul class="grid gap-3 mb-8">
                            <li class="flex items-center gap-2.5 text-sm text-white/80">
                                <i class="fa-solid fa-check text-yellow-300 text-xs"></i> Onbeperkt games per dag
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-white/80">
                                <i class="fa-solid fa-check text-yellow-300 text-xs"></i> Alle 13 game types
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-white/80">
                                <i class="fa-solid fa-check text-yellow-300 text-xs"></i> GIF profielfoto & banner
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-white/80">
                                <i class="fa-solid fa-check text-yellow-300 text-xs"></i> Grotere uploads (5MB avatar, 8MB banner)
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-white/80">
                                <i class="fa-solid fa-check text-yellow-300 text-xs"></i> Pro badge op profiel
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-white/80">
                                <i class="fa-solid fa-check text-yellow-300 text-xs"></i> Epische & Legendarische cosmetics
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-white/80">
                                <i class="fa-solid fa-check text-yellow-300 text-xs"></i> Animated naamkleuren & custom badges
                            </li>
                        </ul>

                        <a href="{{ route('register') }}" class="block w-full text-center bg-white hover:bg-white/90 transition text-[#5B2333] font-bold text-sm py-3.5 rounded-xl">
                            Start met Pro
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="max-w-6xl mx-auto px-6 py-28">
        <div class="hero-gradient rounded-3xl p-12 sm:p-16 text-center relative overflow-hidden">
            <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-15 pointer-events-none" alt="">
            <div class="relative z-10">
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    Klaar om je brein te trainen?
                </h2>
                <p class="mt-4 text-white/60 max-w-md mx-auto leading-relaxed">
                    Sluit je aan bij BrainForge en begin vandaag nog met je eerste uitdaging. Het is gratis.
                </p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-[#5B2333] font-bold text-sm px-8 py-4 rounded-2xl hover:bg-white/90 transition shadow-lg shadow-black/10 mt-8">
                    <i class="fa-solid fa-bolt"></i> Maak gratis een account
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-[#564D4A]/8 bg-white">
        <div class="max-w-6xl mx-auto px-6 py-12">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#5B2333] flex items-center justify-center">
                        <img src="/assets/logo-wit.png" class="max-h-3.5" alt="BrainForge logo">
                    </div>
                    <span class="font-black text-base tracking-tight text-[#564D4A]">
                        Brain<span class="text-[#5B2333]">Forge.</span>
                    </span>
                </div>

                <div class="flex items-center gap-6 text-sm text-[#564D4A]/40 font-medium">
                    <a href="#games" class="hover:text-[#564D4A] transition">Games</a>
                    <a href="#features" class="hover:text-[#564D4A] transition">Features</a>
                    <a href="#pricing" class="hover:text-[#564D4A] transition">Pricing</a>
                    <a href="{{ route('login') }}" class="hover:text-[#564D4A] transition">Inloggen</a>
                </div>

                <p class="text-xs text-[#564D4A]/30 font-medium">
                    &copy; {{ date('Y') }} BrainForge. Alle rechten voorbehouden.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>
