<x-layouts.marketing>
    <x-slot:title>Hoe het werkt - BrainForge</x-slot:title>

    {{-- Page header --}}
    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-route text-[10px]"></i> HOE HET WERKT
            </span>
            <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                Zo werkt BrainForge
            </h1>
            <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                BrainForge is simpel: log in, kies een game, en train je brein. Hier leggen we precies uit hoe alles in elkaar zit.
            </p>
        </div>
    </section>

    {{-- Steps --}}
    <section class="max-w-4xl mx-auto px-6 py-24">
        <div class="grid gap-16">
            {{-- Step 1 --}}
            <div data-animate="fade-up" class="flex gap-8 items-start">
                <div class="w-14 h-14 rounded-2xl bg-[#5B2333] flex items-center justify-center shrink-0">
                    <span class="text-white font-black text-lg">1</span>
                </div>
                <div>
                    <h2 class="text-xl font-black text-[#564D4A] mb-3">Maak een gratis account</h2>
                    <p class="text-[#564D4A]/60 leading-relaxed">
                        Meld je aan met je e-mailadres of via Google. Het kost je minder dan een minuut en je hebt geen creditcard nodig. Je account is voor altijd gratis — upgraden naar Pro is optioneel.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6">
                            <i class="fa-solid fa-envelope text-[#564D4A]/30 text-xs"></i>
                            <span class="text-xs font-semibold text-[#564D4A]/60">E-mail registratie</span>
                        </div>
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6">
                            <i class="fa-brands fa-google text-[#564D4A]/30 text-xs"></i>
                            <span class="text-xs font-semibold text-[#564D4A]/60">Google login</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2 --}}
            <div data-animate="fade-up" class="flex gap-8 items-start">
                <div class="w-14 h-14 rounded-2xl bg-[#5B2333] flex items-center justify-center shrink-0">
                    <span class="text-white font-black text-lg">2</span>
                </div>
                <div>
                    <h2 class="text-xl font-black text-[#564D4A] mb-3">Bekijk je dagelijkse uitdagingen</h2>
                    <p class="text-[#564D4A]/60 leading-relaxed">
                        Op je dashboard vind je elke dag 11 nieuwe games. Elke game heeft zijn eigen moeilijkheidsgraad. Kies wat bij je past en ga aan de slag.
                    </p>
                    <div class="mt-5 grid grid-cols-3 gap-3 max-w-sm">
                        <div class="rounded-xl bg-green-50 border border-green-200 p-3 text-center">
                            <p class="text-xs font-bold text-green-700">Makkelijk</p>
                        </div>
                        <div class="rounded-xl bg-yellow-50 border border-yellow-200 p-3 text-center">
                            <p class="text-xs font-bold text-yellow-700">Normaal</p>
                        </div>
                        <div class="rounded-xl bg-red-50 border border-red-200 p-3 text-center">
                            <p class="text-xs font-bold text-red-700">Moeilijk</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3 --}}
            <div data-animate="fade-up" class="flex gap-8 items-start">
                <div class="w-14 h-14 rounded-2xl bg-[#5B2333] flex items-center justify-center shrink-0">
                    <span class="text-white font-black text-lg">3</span>
                </div>
                <div>
                    <h2 class="text-xl font-black text-[#564D4A] mb-3">Speel de game</h2>
                    <p class="text-[#564D4A]/60 leading-relaxed">
                        Elke game duurt gemiddeld 2-5 minuten. Je krijgt direct feedback na elke actie. Heb je de puzzel opgelost? Dan verdien je XP op basis van je moeilijkheid en snelheid.
                    </p>
                </div>
            </div>

            {{-- Step 4 --}}
            <div data-animate="fade-up" class="flex gap-8 items-start">
                <div class="w-14 h-14 rounded-2xl bg-[#5B2333] flex items-center justify-center shrink-0">
                    <span class="text-white font-black text-lg">4</span>
                </div>
                <div>
                    <h2 class="text-xl font-black text-[#564D4A] mb-3">Bouw je streak & klim het leaderboard op</h2>
                    <p class="text-[#564D4A]/60 leading-relaxed">
                        Speel elke dag minstens 1 game om je streak te behouden. Hoe langer je streak, hoe meer het oplevert. Je XP telt mee voor het leaderboard waar je jezelf vergelijkt met andere spelers.
                    </p>
                    <div class="mt-5 inline-flex items-center gap-3 px-4 py-3 rounded-xl bg-orange-50 border border-orange-200">
                        <i class="fa-solid fa-fire text-orange-500"></i>
                        <span class="text-sm font-semibold text-orange-700">Mis een dag = streak kwijt. Consistentie is key!</span>
                    </div>
                </div>
            </div>

            {{-- Step 5 --}}
            <div data-animate="fade-up" class="flex gap-8 items-start">
                <div class="w-14 h-14 rounded-2xl bg-[#5B2333] flex items-center justify-center shrink-0">
                    <span class="text-white font-black text-lg">5</span>
                </div>
                <div>
                    <h2 class="text-xl font-black text-[#564D4A] mb-3">Voltooi dagelijkse quests</h2>
                    <p class="text-[#564D4A]/60 leading-relaxed">
                        Naast de gewone games zijn er dagelijkse quests — speciale opdrachten zoals "Speel 3 games" of "Win een game op moeilijk". Voltooi ze voor extra XP en beloningen.
                    </p>
                </div>
            </div>

            {{-- Step 6 --}}
            <div data-animate="fade-up" class="flex gap-8 items-start">
                <div class="w-14 h-14 rounded-2xl bg-[#5B2333] flex items-center justify-center shrink-0">
                    <span class="text-white font-black text-lg">6</span>
                </div>
                <div>
                    <h2 class="text-xl font-black text-[#564D4A] mb-3">Voeg vrienden toe</h2>
                    <p class="text-[#564D4A]/60 leading-relaxed">
                        Zoek vrienden op, voeg ze toe en bekijk hun profiel, scores en streaks. Samen spelen is leuker — en een beetje competitie houdt je scherp.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Visual walkthrough --}}
    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-24">
            <div class="text-center mb-14">
                <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                    <i class="fa-solid fa-images text-[10px]"></i> EEN KIJKJE NEMEN
                </span>
                <h2 data-animate="fade-up" data-animate-delay="1" class="text-2xl font-black text-[#564D4A] tracking-tight">Zo ziet BrainForge eruit</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                {{-- Dashboard screenshot --}}
                <div data-animate="fade-up" class="rounded-2xl border border-[#564D4A]/6 overflow-hidden">
                    <div class="bg-[#564D4A]/5 aspect-[4/3] flex flex-col items-center justify-center text-center p-6">
                        <div class="w-12 h-12 rounded-xl bg-[#564D4A]/8 flex items-center justify-center mb-3">
                            <i class="fa-solid fa-chart-line text-[#564D4A]/25 text-lg"></i>
                        </div>
                        <p class="font-bold text-[#564D4A]/30 text-xs">Dashboard overzicht</p>
                    </div>
                    <div class="p-4">
                        <p class="font-bold text-sm text-[#564D4A]">Je persoonlijke dashboard</p>
                        <p class="text-xs text-[#564D4A]/50 mt-1">Games, streak, XP en quests in één oogopslag.</p>
                    </div>
                </div>

                {{-- Game screenshot --}}
                <div data-animate="fade-up" data-animate-delay="1" class="rounded-2xl border border-[#564D4A]/6 overflow-hidden">
                    <div class="bg-[#D6E4F0]/20 aspect-[4/3] flex flex-col items-center justify-center text-center p-6">
                        <div class="w-12 h-12 rounded-xl bg-[#D6E4F0] flex items-center justify-center mb-3">
                            <i class="fa-solid fa-font text-[#4a7fa5] text-lg"></i>
                        </div>
                        <p class="font-bold text-[#4a7fa5]/50 text-xs">Word Forge in actie</p>
                    </div>
                    <div class="p-4">
                        <p class="font-bold text-sm text-[#564D4A]">Spelervaring</p>
                        <p class="text-xs text-[#564D4A]/50 mt-1">Directe feedback bij elke poging met kleurhints.</p>
                    </div>
                </div>

                {{-- Profile screenshot --}}
                <div data-animate="fade-up" data-animate-delay="2" class="rounded-2xl border border-[#564D4A]/6 overflow-hidden">
                    <div class="bg-[#E8D5F0]/20 aspect-[4/3] flex flex-col items-center justify-center text-center p-6">
                        <div class="w-12 h-12 rounded-xl bg-[#E8D5F0] flex items-center justify-center mb-3">
                            <i class="fa-solid fa-user text-[#7a4fa0] text-lg"></i>
                        </div>
                        <p class="font-bold text-[#7a4fa0]/50 text-xs">Profiel & statistieken</p>
                    </div>
                    <div class="p-4">
                        <p class="font-bold text-sm text-[#564D4A]">Je profiel</p>
                        <p class="text-xs text-[#564D4A]/50 mt-1">Bekijk je stats, badges en vergelijk met vrienden.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Daily routine --}}
    <section class="max-w-4xl mx-auto px-6 py-24">
        <div class="text-center mb-14">
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-clock text-[10px]"></i> DAGELIJKSE ROUTINE
            </span>
            <h2 data-animate="fade-up" data-animate-delay="1" class="text-2xl font-black text-[#564D4A] tracking-tight">Past in elke dag</h2>
            <p data-animate="fade-up" data-animate-delay="2" class="mt-3 text-[#564D4A]/50 max-w-lg mx-auto">Elke sessie duurt maar 5-15 minuten. Hier een voorbeeld van hoe het er voor je uit kan zien:</p>
        </div>

        <div data-animate="fade-up" class="bg-white rounded-2xl border border-[#564D4A]/6 divide-y divide-[#564D4A]/6">
            <div class="flex items-center gap-5 p-5">
                <div class="w-12 text-center shrink-0">
                    <p class="text-xs font-bold text-[#564D4A]/30">08:00</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-mug-hot text-green-600 text-sm"></i>
                </div>
                <div>
                    <p class="font-bold text-sm text-[#564D4A]">Koffie + eerste game</p>
                    <p class="text-xs text-[#564D4A]/50">Word Forge op makkelijk — 2 minuten</p>
                </div>
            </div>
            <div class="flex items-center gap-5 p-5">
                <div class="w-12 text-center shrink-0">
                    <p class="text-xs font-bold text-[#564D4A]/30">12:30</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-utensils text-yellow-600 text-sm"></i>
                </div>
                <div>
                    <p class="font-bold text-sm text-[#564D4A]">Lunchpauze challenge</p>
                    <p class="text-xs text-[#564D4A]/50">Sudoku op normaal + Flag Guess — 8 minuten</p>
                </div>
            </div>
            <div class="flex items-center gap-5 p-5">
                <div class="w-12 text-center shrink-0">
                    <p class="text-xs font-bold text-[#564D4A]/30">21:00</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-couch text-purple-600 text-sm"></i>
                </div>
                <div>
                    <p class="font-bold text-sm text-[#564D4A]">Avond wind-down</p>
                    <p class="text-xs text-[#564D4A]/50">Block Drop op moeilijk — 5 minuten. Quest voltooid!</p>
                </div>
            </div>
        </div>

        <div data-animate="fade-up" class="mt-6 text-center">
            <div class="inline-flex items-center gap-3 px-5 py-3 rounded-xl bg-green-50 border border-green-200">
                <i class="fa-solid fa-check-double text-green-500"></i>
                <span class="text-sm font-semibold text-green-700">Totaal: 15 minuten. Streak behouden. 3 quests klaar.</span>
            </div>
        </div>
    </section>

    {{-- Features overview --}}
    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-24">
            <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight text-center mb-14">Wat je krijgt</h2>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div data-animate="fade-up" class="rounded-2xl border border-[#564D4A]/6 p-6">
                    <i class="fa-solid fa-gamepad text-[#5B2333] text-lg mb-3"></i>
                    <h3 class="font-bold text-[#564D4A] mb-1">11 unieke games</h3>
                    <p class="text-sm text-[#564D4A]/50">Van woorden tot vlaggen, van logica tot strategie.</p>
                </div>
                <div data-animate="fade-up" class="rounded-2xl border border-[#564D4A]/6 p-6">
                    <i class="fa-solid fa-calendar-day text-[#5B2333] text-lg mb-3"></i>
                    <h3 class="font-bold text-[#564D4A] mb-1">Dagelijkse refresh</h3>
                    <p class="text-sm text-[#564D4A]/50">Elke dag nieuwe puzzels. Nooit twee keer hetzelfde.</p>
                </div>
                <div data-animate="fade-up" class="rounded-2xl border border-[#564D4A]/6 p-6">
                    <i class="fa-solid fa-fire text-[#5B2333] text-lg mb-3"></i>
                    <h3 class="font-bold text-[#564D4A] mb-1">Streak systeem</h3>
                    <p class="text-sm text-[#564D4A]/50">Speel elke dag voor een groeiende streak.</p>
                </div>
                <div data-animate="fade-up" class="rounded-2xl border border-[#564D4A]/6 p-6">
                    <i class="fa-solid fa-star text-[#5B2333] text-lg mb-3"></i>
                    <h3 class="font-bold text-[#564D4A] mb-1">XP & progressie</h3>
                    <p class="text-sm text-[#564D4A]/50">Verdien punten en zie je voortgang groeien.</p>
                </div>
                <div data-animate="fade-up" class="rounded-2xl border border-[#564D4A]/6 p-6">
                    <i class="fa-solid fa-trophy text-[#5B2333] text-lg mb-3"></i>
                    <h3 class="font-bold text-[#564D4A] mb-1">Leaderboard</h3>
                    <p class="text-sm text-[#564D4A]/50">Vergelijk je scores met de rest van de wereld.</p>
                </div>
                <div data-animate="fade-up" class="rounded-2xl border border-[#564D4A]/6 p-6">
                    <i class="fa-solid fa-user-group text-[#5B2333] text-lg mb-3"></i>
                    <h3 class="font-bold text-[#564D4A] mb-1">Vrienden</h3>
                    <p class="text-sm text-[#564D4A]/50">Voeg vrienden toe en daag ze uit.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="hero-gradient rounded-3xl p-12 sm:p-16 text-center relative overflow-hidden">
            <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-15 pointer-events-none" alt="">
            <div class="relative z-10">
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Overtuigd?</h2>
                <p class="mt-3 text-white/50 max-w-md mx-auto">Begin vandaag nog. Het is gratis en duurt minder dan een minuut.</p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-[#5B2333] font-bold text-sm px-8 py-4 rounded-2xl hover:bg-white/90 transition shadow-lg shadow-black/10 mt-8">
                    <i class="fa-solid fa-bolt"></i> Account aanmaken
                </a>
            </div>
        </div>
    </section>

</x-layouts.marketing>
