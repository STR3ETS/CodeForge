<x-layouts.marketing :navDark="true">
    <x-slot:title>BrainForge - Train je brein, elke dag opnieuw</x-slot:title>
    <x-slot:description>Train je brein met 11 dagelijkse puzzels en breinspellen. Bouw je streak op, verdien XP, daag vrienden uit en klim het leaderboard. Gratis te spelen!</x-slot:description>
    <x-slot:head>
        <style>
            .float-animation { animation: bfFloat 6s ease-in-out infinite; }
            .float-delay-1 { animation: bfFloat 6s ease-in-out 1.5s infinite; }
            .float-delay-2 { animation: bfFloat 6s ease-in-out 3s infinite; }
            .float-delay-3 { animation: bfFloat 6s ease-in-out 4.5s infinite; }
            @keyframes bfFloat {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-14px); }
            }
            .animate-badge-custom-gradient {
                background: linear-gradient(90deg, var(--badge-c1), var(--badge-c2), var(--badge-c3), var(--badge-c1));
                background-size: 200% 200%;
                animation: badge-gradient-shift 3s linear infinite;
            }
            @keyframes badge-gradient-shift {
                0% { background-position: 0% 50%; }
                100% { background-position: 200% 50%; }
            }
        </style>
    </x-slot:head>

    {{-- Hero --}}
    <section class="relative h-[800px] flex items-center overflow-hidden" style="background: linear-gradient(to bottom, #5B2333 0%, #5B2333 25%, #8a4a58 50%, #e2c8c4 75%, #F7F4F3 100%);">
        <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-15 pointer-events-none" alt="">

        <div class="relative z-10 max-w-6xl mx-auto px-6 pt-32 pb-32 w-full">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                {{-- Text --}}
                <div>
                    <div data-animate="fade-up" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/15 backdrop-blur-sm mb-7">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        <span class="text-xs font-semibold text-white/80">13 dagelijkse brain games</span>
                    </div>

                    <h1 data-animate="fade-up" data-animate-delay="1" class="text-4xl sm:text-5xl lg:text-[3.5rem] font-black text-white leading-[1.08] tracking-tight">
                        Train je brein,<br>
                        <span class="text-white/50">elke dag opnieuw.</span>
                    </h1>

                    <p data-animate="fade-up" data-animate-delay="2" class="mt-7 text-lg text-white/60 leading-relaxed max-w-lg">
                        Dagelijkse puzzels, woordspellen en logische uitdagingen.
                        Bouw je streak op, verdien XP en daag je vrienden uit.
                    </p>

                    <div data-animate="fade-up" data-animate-delay="3" class="mt-10 flex flex-wrap items-center gap-4">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2.5 bg-white text-[#5B2333] font-bold text-sm px-7 py-4 rounded-2xl hover:bg-white/90 transition shadow-lg shadow-black/10">
                            <i class="fa-solid fa-bolt"></i> Gratis beginnen
                        </a>
                        <a href="{{ route('pages.games') }}" class="inline-flex items-center gap-2.5 text-white/70 hover:text-white font-semibold text-sm px-6 py-4 rounded-2xl border border-white/15 hover:bg-white/10 transition">
                            <i class="fa-solid fa-gamepad"></i> Bekijk de games
                        </a>
                    </div>

                    <div class="mt-10 flex flex-wrap items-center gap-6 text-white/40 text-xs font-medium">
                        <span><i class="fa-solid fa-check mr-1.5 text-green-400/80"></i> 100% gratis te spelen</span>
                        <span><i class="fa-solid fa-check mr-1.5 text-green-400/80"></i> Geen installatie nodig</span>
                        <span><i class="fa-solid fa-check mr-1.5 text-green-400/80"></i> Elke dag nieuwe puzzels</span>
                    </div>
                </div>

                {{-- Floating game cards + social bubbles --}}
                <div class="hidden lg:block relative h-[500px]">
                    <div class="absolute top-10 right-4 float-delay-1">
                        <div class="bg-white rounded-2xl shadow-xl shadow-black/8 p-5 w-52">
                            <div class="w-11 h-11 rounded-xl bg-[#FBE2D8] flex items-center justify-center mb-3">
                                <i class="fa-solid fa-face-grin text-[#c0705a] text-lg"></i>
                            </div>
                            <p class="font-bold text-sm text-[#564D4A]">Vind de Emoji</p>
                            <p class="text-[10px] text-[#564D4A]/40 mt-1">Vind de juiste emoji combinatie</p>
                        </div>
                    </div>

                    {{-- Chat bubble 1: brag --}}
                    <div class="absolute top-24 left-16 float-delay-3 z-10">
                        <div class="bg-white/95 backdrop-blur-sm rounded-2xl rounded-bl-sm shadow-lg shadow-black/8 px-4 py-3 max-w-[200px]">
                            <div class="flex items-center gap-2 mb-1.5">
                                <div class="w-6 h-6 rounded-full bg-[#D6E4F0] flex items-center justify-center">
                                    <span class="text-[8px] font-bold text-[#4a7fa5]">LS</span>
                                </div>
                                <span class="text-[10px] font-bold text-[#564D4A]">Lisa</span>
                            </div>
                            <p class="text-[11px] text-[#564D4A]/80 leading-snug">Ik ben beter dan jij vandaag! 😏🔥</p>
                        </div>
                    </div>

                    <div class="absolute top-44 left-0 float-delay-2">
                        <div class="bg-white rounded-2xl shadow-xl shadow-black/8 p-5 w-52">
                            <div class="w-11 h-11 rounded-xl bg-[#FFF3CD] flex items-center justify-center mb-3">
                                <i class="fa-solid fa-flag text-[#9a7a20] text-lg"></i>
                            </div>
                            <p class="font-bold text-sm text-[#564D4A]">Vlaggen Quiz</p>
                            <p class="text-[10px] text-[#564D4A]/40 mt-1">Herken vlaggen wereldwijd</p>
                        </div>
                    </div>

                    {{-- Chat bubble 2: reply --}}
                    <div class="absolute top-[220px] right-2 float-delay-1 z-10">
                        <div class="bg-[#5B2333] rounded-2xl rounded-br-sm shadow-lg shadow-black/10 px-4 py-3 max-w-[210px]">
                            <div class="flex items-center gap-2 mb-1.5">
                                <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center">
                                    <span class="text-[8px] font-bold text-white">MK</span>
                                </div>
                                <span class="text-[10px] font-bold text-white/90">Mark</span>
                            </div>
                            <p class="text-[11px] text-white/80 leading-snug">Wacht maar, ik haal je in! 💪</p>
                        </div>
                    </div>

                    {{-- Streak notification --}}
                    <div class="absolute bottom-32 right-0 float-animation z-10">
                        <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg shadow-black/8 px-4 py-2.5 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center">
                                <i class="fa-solid fa-fire text-orange-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-[#564D4A]">Jasper heeft 21 dagen streak!</p>
                                <p class="text-[9px] text-[#564D4A]/40">Zojuist</p>
                            </div>
                        </div>
                    </div>

                    <div class="absolute bottom-8 left-16 float-delay-1">
                        <div class="bg-white rounded-2xl shadow-xl shadow-black/8 p-5 w-48">
                            <div class="w-11 h-11 rounded-xl bg-[#E8D5F0] flex items-center justify-center mb-3">
                                <i class="fa-solid fa-cube text-[#7a4fa0] text-lg"></i>
                            </div>
                            <p class="font-bold text-sm text-[#564D4A]">Blok Drop</p>
                            <p class="text-[10px] text-[#564D4A]/40 mt-1">Puzzel met blokken</p>
                        </div>
                    </div>

                    {{-- Leaderboard notification --}}
                    <div class="absolute bottom-2 left-0 float-delay-3 z-10">
                        <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg shadow-black/8 px-4 py-2.5 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center">
                                <i class="fa-solid fa-trophy text-yellow-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-[#564D4A]">Sophie is nu #1!</p>
                                <p class="text-[9px] text-[#564D4A]/40">Leaderboard update</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Pro banner --}}
    <section class="relative z-10 -mt-10">
        <div class="max-w-4xl mx-auto px-6">
            <div data-animate="fade-up" class="bg-[#5B2333] rounded-2xl shadow-lg shadow-black/10 px-8 py-6 flex flex-wrap items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-crown text-yellow-300"></i>
                    </div>
                    <div>
                        <p class="font-bold text-white text-sm">Upgrade naar Pro vanaf <span class="text-yellow-300">1,99/maand</span></p>
                        <p class="text-xs text-white/50 mt-0.5">Onbeperkt games, GIF profielfoto's, exclusieve cosmetics & Pro badge.</p>
                    </div>
                </div>
                <a href="{{ route('pages.pricing') }}" class="inline-flex items-center gap-2 bg-white text-[#5B2333] font-bold text-sm px-6 py-3 rounded-xl hover:bg-white/90 transition shrink-0">
                    Bekijk Pro <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Quick intro: what is BrainForge --}}
    <section class="max-w-6xl mx-auto px-6 py-28">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-5">
                    <i class="fa-solid fa-brain text-[10px]"></i> WAT IS BRAINFORGE?
                </span>
                <h2 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight leading-tight">
                    Jouw dagelijkse<br>brain workout
                </h2>
                <p class="mt-5 text-[#564D4A]/60 leading-relaxed text-lg">
                    BrainForge biedt elke dag 11 nieuwe puzzels en uitdagingen die je brein scherp houden. Van woordspellen tot logische reeksen — er is voor iedereen iets bij.
                </p>
                <div class="mt-8 grid gap-4">
                    <div data-animate="fade-up" class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-solid fa-fire text-orange-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-bold text-[#564D4A]">Bouw een streak op</p>
                            <p class="text-sm text-[#564D4A]/50 mt-1">Speel elke dag en zie je streak groeien. Mis een dag en je begint opnieuw.</p>
                        </div>
                    </div>
                    <div data-animate="fade-up" data-animate-delay="1" class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-solid fa-ranking-star text-purple-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-bold text-[#564D4A]">Strijdt om de top</p>
                            <p class="text-sm text-[#564D4A]/50 mt-1">Vergelijk je scores met anderen op het leaderboard en daag vrienden uit.</p>
                        </div>
                    </div>
                    <div data-animate="fade-up" data-animate-delay="2" class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-solid fa-bolt text-green-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-bold text-[#564D4A]">Snelle sessies</p>
                            <p class="text-sm text-[#564D4A]/50 mt-1">Elke game duurt 2-5 minuten. Perfect voor tussendoor of als dagelijkse routine.</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('register') }}" data-animate="fade-up" data-animate-delay="3" class="inline-flex items-center gap-2 mt-8 bg-[#5B2333] text-white font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-[#5B2333]/85 transition w-fit">
                    <i class="fa-solid fa-bolt text-xs"></i> Gratis beginnen
                </a>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div data-animate="fade-up" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-2xl bg-[#D6E4F0] flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-font text-[#4a7fa5] text-2xl"></i>
                    </div>
                    <p class="font-bold text-sm">Woord Raden</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="1" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-2xl bg-[#FBE2D8] flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-face-grin text-[#c0705a] text-2xl"></i>
                    </div>
                    <p class="font-bold text-sm">Vind de Emoji</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="2" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-2xl bg-[#D9EAD3] flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-arrow-up-1-9 text-[#5a8a4e] text-2xl"></i>
                    </div>
                    <p class="font-bold text-sm">Reeks Raden</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="3" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-2xl bg-[#FFF3CD] flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-flag text-[#9a7a20] text-2xl"></i>
                    </div>
                    <p class="font-bold text-sm">Vlaggen Quiz</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="4" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-2xl bg-[#E8D5F0] flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-cube text-[#7a4fa0] text-2xl"></i>
                    </div>
                    <p class="font-bold text-sm">Blok Drop</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="5" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-2xl bg-[#D0EAE8] flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-table-cells text-[#3a8a85] text-2xl"></i>
                    </div>
                    <p class="font-bold text-sm">Sudoku</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Dashboard preview --}}
    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-28">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-5">
                        <i class="fa-solid fa-desktop text-[10px]"></i> JE DASHBOARD
                    </span>
                    <h2 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight leading-tight">
                        Alles op één plek
                    </h2>
                    <p data-animate="fade-up" data-animate-delay="2" class="mt-5 text-[#564D4A]/60 leading-relaxed text-lg">
                        Je persoonlijke dashboard toont je dagelijkse games, streak, XP-voortgang en quests. Eén blik en je weet precies waar je staat.
                    </p>
                    <div class="mt-8 grid gap-3">
                        <div data-animate="fade-up" class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-green-500 text-sm"></i>
                            <span class="text-sm text-[#564D4A]/70">Dagelijkse games overzichtelijk per moeilijkheid</span>
                        </div>
                        <div data-animate="fade-up" data-animate-delay="1" class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-green-500 text-sm"></i>
                            <span class="text-sm text-[#564D4A]/70">Live streak-teller en XP-balk</span>
                        </div>
                        <div data-animate="fade-up" data-animate-delay="2" class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-green-500 text-sm"></i>
                            <span class="text-sm text-[#564D4A]/70">Quest-voortgang met beloningen</span>
                        </div>
                        <div data-animate="fade-up" data-animate-delay="3" class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-green-500 text-sm"></i>
                            <span class="text-sm text-[#564D4A]/70">Vriendenactiviteit en leaderboard</span>
                        </div>
                    </div>
                    <a href="{{ route('register') }}" data-animate="fade-up" data-animate-delay="4" class="inline-flex items-center gap-2 mt-8 bg-[#5B2333] text-white font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-[#5B2333]/85 transition w-fit">
                        Probeer het zelf <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>

                {{-- Dashboard screenshot placeholder --}}
                <div data-animate="fade-up" data-animate-delay="1" class="relative">
                    <img src="/assets/dashboard.png" class="rounded-3xl shadow-xl">
                    <div class="absolute -bottom-3 -right-3 bg-white rounded-xl border border-[#564D4A]/8 shadow-lg px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">
                            <i class="fa-solid fa-fire text-orange-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[#564D4A]">12 dagen streak</p>
                            <p class="text-[10px] text-[#564D4A]/40">Blijf spelen!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Game categories showcase --}}
    <section class="max-w-6xl mx-auto px-6 py-28">
        <div class="text-center mb-14">
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-layer-group text-[10px]"></i> 4 CATEGORIEEN
            </span>
            <h2 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">Voor elk type brein iets</h2>
            <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/50 max-w-lg mx-auto">Van snelle reflexen tot diepe logica — kies de categorie die bij jou past.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <a href="{{ route('pages.categorie.snelheid-reactie') }}" data-animate="fade-up" class="group relative bg-white rounded-2xl border border-[#564D4A]/6 p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-red-100 to-transparent rounded-bl-full opacity-60"></div>
                <div class="relative">
                    <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-bolt text-red-500 text-lg"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-1.5">Snelheid & Reactie</h3>
                    <p class="text-xs text-[#564D4A]/45 leading-relaxed">Test je reflexen en reageer razendsnel op visuele uitdagingen.</p>
                    <div class="flex flex-wrap gap-1.5 mt-4">
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-red-50 text-red-400">Reactietijd</span>
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-red-50 text-red-400">Kleuren Match</span>
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-red-50 text-red-400">Vind de Emoji</span>
                    </div>
                </div>
            </a>
            <a href="{{ route('pages.categorie.geheugentraining') }}" data-animate="fade-up" data-animate-delay="1" class="group relative bg-white rounded-2xl border border-[#564D4A]/6 p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-purple-100 to-transparent rounded-bl-full opacity-60"></div>
                <div class="relative">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-brain text-purple-500 text-lg"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-1.5">Geheugentraining</h3>
                    <p class="text-xs text-[#564D4A]/45 leading-relaxed">Train je werkgeheugen en onthoud patronen onder druk.</p>
                    <div class="flex flex-wrap gap-1.5 mt-4">
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-purple-50 text-purple-400">Geheugen Grid</span>
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-purple-50 text-purple-400">Reeks Raden</span>
                    </div>
                </div>
            </a>
            <a href="{{ route('pages.categorie.logica-strategie') }}" data-animate="fade-up" data-animate-delay="2" class="group relative bg-white rounded-2xl border border-[#564D4A]/6 p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-100 to-transparent rounded-bl-full opacity-60"></div>
                <div class="relative">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-chess text-blue-500 text-lg"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-1.5">Logica & Strategie</h3>
                    <p class="text-xs text-[#564D4A]/45 leading-relaxed">Denk vooruit, plan en los complexe puzzels op.</p>
                    <div class="flex flex-wrap gap-1.5 mt-4">
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-400">Sudoku</span>
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-400">Doolhof</span>
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-400">Blok Drop</span>
                    </div>
                </div>
            </a>
            <a href="{{ route('pages.categorie.hersenkrakers') }}" data-animate="fade-up" data-animate-delay="3" class="group relative bg-white rounded-2xl border border-[#564D4A]/6 p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-amber-100 to-transparent rounded-bl-full opacity-60"></div>
                <div class="relative">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-lightbulb text-amber-500 text-lg"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-1.5">Hersenkrakers</h3>
                    <p class="text-xs text-[#564D4A]/45 leading-relaxed">Woorden, vlaggen, aardrijkskunde en rekensommen.</p>
                    <div class="flex flex-wrap gap-1.5 mt-4">
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-400">Woord Raden</span>
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-400">Vlaggen Quiz</span>
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-400">Geo Gok</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('pages.games') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#5B2333] hover:text-[#7a3349] transition">
                Bekijk alle 13 games <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </section>

    {{-- Social & Competition --}}
    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-28">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                {{-- Mock leaderboard & social --}}
                <div data-animate="fade-up" class="order-2 lg:order-1">
                    <div class="bg-[#F7F4F3] rounded-2xl border border-[#564D4A]/6 overflow-hidden">
                        {{-- Leaderboard header --}}
                        <div class="px-6 py-4 border-b border-[#564D4A]/6 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-trophy text-yellow-500 text-sm"></i>
                                <span class="font-bold text-sm text-[#564D4A]">Leaderboard</span>
                            </div>
                            <div class="flex gap-1">
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-[#5B2333] text-white">Wereldwijd</span>
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-[#564D4A]/5 text-[#564D4A]/50">Vrienden</span>
                            </div>
                        </div>
                        {{-- Podium --}}
                        <div class="flex items-end justify-center gap-3 px-6 pt-6 pb-4">
                            <div class="text-center">
                                <div class="w-12 h-12 rounded-full bg-[#D9EAD3] flex items-center justify-center mx-auto mb-1.5 ring-2 ring-[#C0C0C0]">
                                    <span class="text-xs font-bold text-[#5a8a4e]">MK</span>
                                </div>
                                <p class="text-[10px] font-bold text-[#564D4A]">Mark</p>
                                <div class="w-16 bg-gradient-to-t from-[#C0C0C0]/30 to-[#C0C0C0]/10 rounded-t-lg mt-1.5 flex items-end justify-center" style="height: 48px">
                                    <span class="text-xs font-black text-[#564D4A]/40 mb-1">2</span>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="w-14 h-14 rounded-full bg-[#FBE2D8] flex items-center justify-center mx-auto mb-1.5 ring-2 ring-yellow-400">
                                    <span class="text-sm font-bold text-[#c0705a]">SR</span>
                                </div>
                                <p class="text-[10px] font-bold text-[#564D4A]">Sophie</p>
                                <div class="w-16 bg-gradient-to-t from-yellow-300/30 to-yellow-300/10 rounded-t-lg mt-1.5 flex items-end justify-center" style="height: 64px">
                                    <span class="text-xs font-black text-yellow-600/60 mb-1">1</span>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="w-12 h-12 rounded-full bg-[#D6E4F0] flex items-center justify-center mx-auto mb-1.5 ring-2 ring-amber-700/40">
                                    <span class="text-xs font-bold text-[#4a7fa5]">TH</span>
                                </div>
                                <p class="text-[10px] font-bold text-[#564D4A]">Thomas</p>
                                <div class="w-16 bg-gradient-to-t from-amber-700/20 to-amber-700/5 rounded-t-lg mt-1.5 flex items-end justify-center" style="height: 36px">
                                    <span class="text-xs font-black text-[#564D4A]/30 mb-1">3</span>
                                </div>
                            </div>
                        </div>
                        {{-- List --}}
                        <div class="px-6 pb-4 space-y-2">
                            <div class="flex items-center gap-3 bg-white rounded-xl px-4 py-2.5">
                                <span class="text-xs font-bold text-[#564D4A]/30 w-5">4</span>
                                <div class="w-8 h-8 rounded-full bg-[#E8D5F0] flex items-center justify-center">
                                    <span class="text-[9px] font-bold text-[#7a4fa0]">JB</span>
                                </div>
                                <span class="text-xs font-bold text-[#564D4A] flex-1">Jasper</span>
                                <span class="text-[10px] font-semibold text-[#564D4A]/40">Level 18</span>
                            </div>
                            <div class="flex items-center gap-3 bg-[#5B2333]/5 rounded-xl px-4 py-2.5 ring-1 ring-[#5B2333]/15">
                                <span class="text-xs font-bold text-[#5B2333] w-5">5</span>
                                <div class="w-8 h-8 rounded-full bg-[#5B2333]/10 flex items-center justify-center">
                                    <span class="text-[9px] font-bold text-[#5B2333]">JIJ</span>
                                </div>
                                <span class="text-xs font-bold text-[#5B2333] flex-1">Jij</span>
                                <span class="text-[10px] font-semibold text-[#5B2333]/50">Level 12</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="order-1 lg:order-2">
                    <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-5">
                        <i class="fa-solid fa-users text-[10px]"></i> SOCIAAL & COMPETITIEF
                    </span>
                    <h2 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight leading-tight">
                        Speel niet alleen,<br>speel tegen iedereen
                    </h2>
                    <p data-animate="fade-up" data-animate-delay="2" class="mt-5 text-[#564D4A]/60 leading-relaxed text-lg">
                        Voeg vrienden toe, vergelijk scores en vecht om de #1 positie op het leaderboard. Elke dag een nieuwe kans om de beste te zijn.
                    </p>
                    <div class="mt-8 grid gap-4">
                        <div data-animate="fade-up" class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-user-plus text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-bold text-[#564D4A]">Voeg vrienden toe</p>
                                <p class="text-sm text-[#564D4A]/50 mt-1">Zoek spelers, stuur een verzoek en bekijk elkaars profielen.</p>
                            </div>
                        </div>
                        <div data-animate="fade-up" data-animate-delay="1" class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-trophy text-yellow-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-bold text-[#564D4A]">Dagelijks leaderboard</p>
                                <p class="text-sm text-[#564D4A]/50 mt-1">Elke dag een nieuwe ranglijst. De snelste tijd, de minste zetten — alles telt.</p>
                            </div>
                        </div>
                        <div data-animate="fade-up" data-animate-delay="2" class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-pink-100 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-share-nodes text-pink-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-bold text-[#564D4A]">Deel je scores</p>
                                <p class="text-sm text-[#564D4A]/50 mt-1">Post je resultaten op je profiel en stuur ze naar vrienden.</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('register') }}" data-animate="fade-up" data-animate-delay="3" class="inline-flex items-center gap-2 mt-8 bg-[#5B2333] text-white font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-[#5B2333]/85 transition w-fit">
                        Maak een account <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Cosmetics / Personalisatie --}}
    <section class="max-w-6xl mx-auto px-6 py-28">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-5">
                    <i class="fa-solid fa-palette text-[10px]"></i> PERSONALISATIE
                </span>
                <h2 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight leading-tight">
                    Maak je profiel<br>helemaal van jou
                </h2>
                <p data-animate="fade-up" data-animate-delay="2" class="mt-5 text-[#564D4A]/60 leading-relaxed text-lg">
                    Verdien coins door te levelen en koop cosmetica in de shop. Kies uit 250+ items om je profiel uniek te maken.
                </p>
                <div class="mt-8 grid grid-cols-2 gap-3">
                    <div data-animate="fade-up" class="flex items-center gap-2.5 bg-[#F7F4F3] rounded-xl px-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-circle text-blue-400 text-xs"></i>
                        </div>
                        <span class="text-xs font-bold text-[#564D4A]">Avatar Borders</span>
                    </div>
                    <div data-animate="fade-up" data-animate-delay="1" class="flex items-center gap-2.5 bg-[#F7F4F3] rounded-xl px-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-hat-wizard text-amber-500 text-xs"></i>
                        </div>
                        <span class="text-xs font-bold text-[#564D4A]">Hoedjes</span>
                    </div>
                    <div data-animate="fade-up" data-animate-delay="2" class="flex items-center gap-2.5 bg-[#F7F4F3] rounded-xl px-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-pink-100 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-certificate text-pink-400 text-xs"></i>
                        </div>
                        <span class="text-xs font-bold text-[#564D4A]">Profiel Badges</span>
                    </div>
                    <div data-animate="fade-up" data-animate-delay="3" class="flex items-center gap-2.5 bg-[#F7F4F3] rounded-xl px-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-paint-brush text-green-500 text-xs"></i>
                        </div>
                        <span class="text-xs font-bold text-[#564D4A]">Naam Kleuren</span>
                    </div>
                    <div data-animate="fade-up" data-animate-delay="4" class="flex items-center gap-2.5 bg-[#F7F4F3] rounded-xl px-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-sparkles text-purple-400 text-xs"></i>
                        </div>
                        <span class="text-xs font-bold text-[#564D4A]">Effecten</span>
                    </div>
                    <div data-animate="fade-up" data-animate-delay="5" class="flex items-center gap-2.5 bg-[#F7F4F3] rounded-xl px-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-flag text-red-400 text-xs"></i>
                        </div>
                        <span class="text-xs font-bold text-[#564D4A]">Wereldvlaggen</span>
                    </div>
                </div>
                <a href="{{ route('register') }}" data-animate="fade-up" data-animate-delay="6" class="inline-flex items-center gap-2 mt-6 text-sm font-bold text-[#5B2333] hover:text-[#7a3349] transition">
                    Bekijk de shop <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

            {{-- Mock profile card --}}
            <div data-animate="fade-up" data-animate-delay="1" class="flex justify-center">
                <div class="bg-white rounded-2xl border border-[#564D4A]/6 shadow-xl shadow-black/5 w-full max-w-sm overflow-hidden">
                    {{-- Banner --}}
                    <div class="h-24 bg-gradient-to-r from-[#5B2333] via-[#8a4a58] to-[#5B2333] relative">
                        <div class="absolute inset-0 opacity-20">
                            <div class="absolute top-2 left-4 text-white/30 text-xl">&#10024;</div>
                            <div class="absolute top-6 right-8 text-white/20 text-sm">&#10024;</div>
                            <div class="absolute bottom-3 left-1/3 text-white/25 text-lg">&#10024;</div>
                        </div>
                    </div>
                    {{-- Avatar --}}
                    <div class="relative px-6 -mt-10">
                        <div class="w-20 h-20 rounded-full bg-[#D6E4F0] flex items-center justify-center border-4 border-white shadow-md ring-2 ring-cyan-400">
                            <span class="text-xl font-black text-[#4a7fa5]">SR</span>
                        </div>
                        <div class="absolute top-0 left-3">
                            <span class="text-xl">👑</span>
                        </div>
                    </div>
                    {{-- Info --}}
                    <div class="px-6 pt-3 pb-6">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold border animate-badge-custom-gradient" style="--badge-c1:#8B5CF6;--badge-c2:#EC4899;--badge-c3:#F59E0B; color:white; border-color:transparent;">
                                <span class="text-[10px]">&#9734;</span> Big Brain
                            </span>
                        </div>
                        <h3 class="text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-500 to-blue-500 mt-1.5">Sophie R.</h3>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#5B2333]/8 text-[#5B2333]">Level 27</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-yellow-300/20 text-yellow-600"><i class="fa-solid fa-crown text-[8px] mr-0.5"></i>PRO</span>
                        </div>
                        {{-- Stats --}}
                        <div class="grid grid-cols-3 gap-3 mt-5 pt-4 border-t border-[#564D4A]/6">
                            <div class="text-center">
                                <p class="text-lg font-black text-[#5B2333]">42</p>
                                <p class="text-[9px] text-[#564D4A]/40 font-medium">Streak</p>
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-black text-[#5B2333]">#1</p>
                                <p class="text-[9px] text-[#564D4A]/40 font-medium">Ranking</p>
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-black text-[#5B2333]">156</p>
                                <p class="text-[9px] text-[#564D4A]/40 font-medium">Vrienden</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- XP & Quests system --}}
    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-28">
            <div class="text-center mb-14">
                <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                    <i class="fa-solid fa-star text-[10px]"></i> PROGRESSIE
                </span>
                <h2 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">Level up, elke dag</h2>
                <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/50 max-w-lg mx-auto">Verdien XP met elke game, voltooi quests voor bonussen en unlock cosmetica naarmate je stijgt.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                {{-- XP & Leveling --}}
                <div data-animate="fade-up" class="bg-[#F7F4F3] rounded-2xl border border-[#564D4A]/6 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-[#5B2333] flex items-center justify-center">
                            <i class="fa-solid fa-arrow-trend-up text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-[#564D4A]">XP & Levels</p>
                            <p class="text-[10px] text-[#564D4A]/40">Stijg met elke game</p>
                        </div>
                    </div>
                    {{-- Fake XP bar --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-[#5B2333]">Level 12</span>
                            <span class="text-[#564D4A]/40">340 / 500 XP</span>
                        </div>
                        <div class="h-3 bg-[#564D4A]/8 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-[#5B2333] to-[#8a4a58] rounded-full" style="width: 68%"></div>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-[10px] font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">+50 XP per game</span>
                            <span class="text-[10px] font-semibold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">+25 coins per level</span>
                        </div>
                    </div>
                </div>

                {{-- Daily Quests --}}
                <div data-animate="fade-up" data-animate-delay="1" class="bg-[#F7F4F3] rounded-2xl border border-[#564D4A]/6 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center">
                            <i class="fa-solid fa-scroll text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-[#564D4A]">Dagelijkse Quests</p>
                            <p class="text-[10px] text-[#564D4A]/40">Bonus XP verdienen</p>
                        </div>
                    </div>
                    <div class="space-y-2.5">
                        <div class="bg-white rounded-xl px-4 py-3 flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-green-500 text-[10px]"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-[#564D4A] line-through opacity-50">Speel 3 spellen</p>
                            </div>
                            <span class="text-[10px] font-bold text-green-600">+75 XP</span>
                        </div>
                        <div class="bg-white rounded-xl px-4 py-3 flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-fire text-orange-400 text-[10px]"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-[#564D4A]">Houd je streak</p>
                                <div class="h-1.5 bg-[#564D4A]/8 rounded-full mt-1.5 overflow-hidden">
                                    <div class="h-full bg-orange-400 rounded-full" style="width: 100%"></div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-orange-500">+50 XP</span>
                        </div>
                        <div class="bg-white rounded-xl px-4 py-3 flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-[#564D4A]/5 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-clock text-[#564D4A]/30 text-[10px]"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-[#564D4A]">Win 5 spellen</p>
                                <div class="h-1.5 bg-[#564D4A]/8 rounded-full mt-1.5 overflow-hidden">
                                    <div class="h-full bg-[#5B2333] rounded-full" style="width: 40%"></div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-[#564D4A]/40">2/5</span>
                        </div>
                    </div>
                </div>

                {{-- Streak --}}
                <div data-animate="fade-up" data-animate-delay="2" class="bg-[#F7F4F3] rounded-2xl border border-[#564D4A]/6 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-red-500 flex items-center justify-center">
                            <i class="fa-solid fa-fire text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-[#564D4A]">Streak Systeem</p>
                            <p class="text-[10px] text-[#564D4A]/40">Consistentie wordt beloond</p>
                        </div>
                    </div>
                    {{-- Week calendar --}}
                    <div class="grid grid-cols-7 gap-1.5 mb-4">
                        @foreach(['Ma','Di','Wo','Do','Vr','Za','Zo'] as $i => $dag)
                            <div class="text-center">
                                <span class="text-[9px] font-medium text-[#564D4A]/30">{{ $dag }}</span>
                                <div class="mt-1 w-full aspect-square rounded-lg flex items-center justify-center
                                    {{ $i < 5 ? 'bg-green-100' : ($i === 5 ? 'bg-[#5B2333] ring-2 ring-[#5B2333]/30' : 'bg-[#564D4A]/5') }}">
                                    @if($i < 5)
                                        <i class="fa-solid fa-check text-green-500 text-[10px]"></i>
                                    @elseif($i === 5)
                                        <i class="fa-solid fa-fire text-white text-[10px]"></i>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="bg-white rounded-xl px-4 py-3 text-center">
                        <p class="text-2xl font-black text-[#5B2333]">6 <span class="text-sm font-bold text-[#564D4A]/40">dagen</span></p>
                        <p class="text-[10px] text-[#564D4A]/40 mt-0.5">Persoonlijk record: 34 dagen</p>
                    </div>
                    <p class="text-[10px] text-center text-[#564D4A]/40 mt-3">Speel morgen om je streak te behouden!</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="max-w-6xl mx-auto px-6 py-28">
        <div class="text-center mb-14">
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-quote-left text-[10px]"></i> WAT SPELERS ZEGGEN
            </span>
            <h2 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">Geliefd door puzzelaars</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div data-animate="fade-up" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6">
                <div class="flex items-center gap-1 mb-4">
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                </div>
                <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-5">"Ik speel elke ochtend tijdens mijn koffie. De streak-functie houdt me gemotiveerd — ik zit nu op 34 dagen!"</p>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#D6E4F0] flex items-center justify-center">
                        <span class="text-xs font-bold text-[#4a7fa5]">LS</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#564D4A]">Lisa S.</p>
                        <p class="text-[10px] text-[#564D4A]/40">34 dagen streak</p>
                    </div>
                </div>
            </div>
            <div data-animate="fade-up" data-animate-delay="1" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6">
                <div class="flex items-center gap-1 mb-4">
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                </div>
                <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-5">"Sudoku en Woord Raden zijn mijn favorieten. Fijn dat je de moeilijkheidsgraad zelf kunt kiezen. Pro is het dubbel en dwars waard."</p>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#D9EAD3] flex items-center justify-center">
                        <span class="text-xs font-bold text-[#5a8a4e]">MK</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#564D4A]">Mark K.</p>
                        <p class="text-[10px] text-[#564D4A]/40">Pro-lid</p>
                    </div>
                </div>
            </div>
            <div data-animate="fade-up" data-animate-delay="2" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6">
                <div class="flex items-center gap-1 mb-4">
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                </div>
                <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-5">"Mijn vriendin en ik strijden elke week om de hoogste score. Het leaderboard maakt het super competitief!"</p>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#FBE2D8] flex items-center justify-center">
                        <span class="text-xs font-bold text-[#c0705a]">JB</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#564D4A]">Jasper B.</p>
                        <p class="text-[10px] text-[#564D4A]/40">#3 op het leaderboard</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="max-w-6xl mx-auto px-6 py-28">
        <div data-animate="scale" class="hero-gradient rounded-3xl p-12 sm:p-16 text-center relative overflow-hidden">
            <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-15 pointer-events-none" alt="">
            <div class="relative z-10">
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    Klaar om je brein te trainen?
                </h2>
                <p class="mt-4 text-white/50 max-w-md mx-auto leading-relaxed">
                    Sluit je aan bij BrainForge en begin vandaag nog met je eerste uitdaging.
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-[#5B2333] font-bold text-sm px-8 py-4 rounded-2xl hover:bg-white/90 transition shadow-lg shadow-black/10">
                        <i class="fa-solid fa-bolt"></i> Gratis account aanmaken
                    </a>
                    <a href="{{ route('pages.pricing') }}" class="inline-flex items-center gap-2 text-white/70 hover:text-white font-semibold text-sm px-6 py-4 rounded-2xl border border-white/15 hover:bg-white/10 transition">
                        Bekijk pricing
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-layouts.marketing>
