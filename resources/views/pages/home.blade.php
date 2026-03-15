<x-layouts.marketing :navDark="true">
    <x-slot:title>BrainForge - Train je brein, elke dag opnieuw</x-slot:title>
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
                        <span class="text-xs font-semibold text-white/80">11 dagelijkse brain games</span>
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
                            <p class="font-bold text-sm text-[#564D4A]">Find the Emoji</p>
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
                            <p class="font-bold text-sm text-[#564D4A]">Flag Guess</p>
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
                            <p class="font-bold text-sm text-[#564D4A]">Block Drop</p>
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
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div data-animate="fade-up" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-2xl bg-[#D6E4F0] flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-font text-[#4a7fa5] text-2xl"></i>
                    </div>
                    <p class="font-bold text-sm">Word Forge</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="1" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-2xl bg-[#FBE2D8] flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-face-grin text-[#c0705a] text-2xl"></i>
                    </div>
                    <p class="font-bold text-sm">Find the Emoji</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="2" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-2xl bg-[#D9EAD3] flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-arrow-up-1-9 text-[#5a8a4e] text-2xl"></i>
                    </div>
                    <p class="font-bold text-sm">Sequence Rush</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="3" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-2xl bg-[#FFF3CD] flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-flag text-[#9a7a20] text-2xl"></i>
                    </div>
                    <p class="font-bold text-sm">Flag Guess</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="4" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-2xl bg-[#E8D5F0] flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-cube text-[#7a4fa0] text-2xl"></i>
                    </div>
                    <p class="font-bold text-sm">Block Drop</p>
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
                </div>

                {{-- Dashboard screenshot placeholder --}}
                <div data-animate="fade-up" data-animate-delay="1" class="relative">
                    <div class="bg-[#564D4A]/5 rounded-2xl border-2 border-dashed border-[#564D4A]/15 p-8 aspect-[4/3] flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 rounded-2xl bg-[#564D4A]/8 flex items-center justify-center mb-4">
                            <i class="fa-solid fa-image text-[#564D4A]/25 text-2xl"></i>
                        </div>
                        <p class="font-bold text-[#564D4A]/30 text-sm">Screenshot van je dashboard</p>
                        <p class="text-xs text-[#564D4A]/20 mt-1">Dagelijkse games, streak & quests</p>
                    </div>
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

    {{-- How it works teaser --}}
    <section class="max-w-6xl mx-auto px-6 py-28">
        <div class="text-center mb-16">
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-route text-[10px]"></i> HOE HET WERKT
            </span>
            <h2 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">In 3 stappen aan de slag</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-10">
            <div data-animate="fade-up" class="text-center">
                <div class="w-16 h-16 rounded-2xl bg-[#5B2333] flex items-center justify-center mx-auto mb-5">
                    <span class="text-white font-black text-xl">1</span>
                </div>
                <h3 class="font-bold text-lg text-[#564D4A] mb-2">Maak een account</h3>
                <p class="text-sm text-[#564D4A]/50 leading-relaxed">Gratis aanmelden in 30 seconden. Geen creditcard nodig.</p>
            </div>
            <div data-animate="fade-up" data-animate-delay="1" class="text-center">
                <div class="w-16 h-16 rounded-2xl bg-[#5B2333] flex items-center justify-center mx-auto mb-5">
                    <span class="text-white font-black text-xl">2</span>
                </div>
                <h3 class="font-bold text-lg text-[#564D4A] mb-2">Kies je uitdaging</h3>
                <p class="text-sm text-[#564D4A]/50 leading-relaxed">Elke dag 11 nieuwe games. Kies makkelijk, normaal of moeilijk.</p>
            </div>
            <div data-animate="fade-up" data-animate-delay="2" class="text-center">
                <div class="w-16 h-16 rounded-2xl bg-[#5B2333] flex items-center justify-center mx-auto mb-5">
                    <span class="text-white font-black text-xl">3</span>
                </div>
                <h3 class="font-bold text-lg text-[#564D4A] mb-2">Bouw je streak op</h3>
                <p class="text-sm text-[#564D4A]/50 leading-relaxed">Speel elke dag, verdien XP en klim op het leaderboard.</p>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('pages.how') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#5B2333] hover:text-[#7a3349] transition">
                Meer over hoe het werkt <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </section>

    {{-- Social proof / stats --}}
    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-20">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div data-animate="fade-up">
                    <p class="text-4xl font-black text-[#5B2333]">11</p>
                    <p class="text-sm text-[#564D4A]/50 font-medium mt-1">Unieke games</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="1">
                    <p class="text-4xl font-black text-[#5B2333]">3</p>
                    <p class="text-sm text-[#564D4A]/50 font-medium mt-1">Moeilijkheden</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="2">
                    <p class="text-4xl font-black text-[#5B2333]">24/7</p>
                    <p class="text-sm text-[#564D4A]/50 font-medium mt-1">Speelbaar</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="3">
                    <p class="text-4xl font-black text-[#5B2333]">0,-</p>
                    <p class="text-sm text-[#564D4A]/50 font-medium mt-1">Om te starten</p>
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
                <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-5">"Sudoku en Word Forge zijn mijn favorieten. Fijn dat je de moeilijkheidsgraad zelf kunt kiezen. Pro is het dubbel en dwars waard."</p>
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
