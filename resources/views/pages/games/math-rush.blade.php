<x-layouts.marketing>
    <x-slot:title>Reken Rush - Snelle Rekenpuzzel | BrainForge</x-slot:title>
    <x-slot:description>Train je rekensnelheid met Reken Rush op BrainForge. Los 15 sommen zo snel mogelijk op met toenemende moeilijkheid. Dagelijks een nieuwe uitdaging!</x-slot:description>

    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <a href="{{ route('pages.games') }}" data-animate="fade-up" class="inline-flex items-center gap-2 text-[#564D4A]/40 hover:text-[#564D4A] text-sm font-semibold mb-6 transition">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Alle games
            </a>
            <div class="flex items-start gap-5">
                <div data-animate="fade-up" data-animate-delay="1" class="w-16 h-16 rounded-2xl bg-[#DBEAFE] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-calculator text-[#1D4ED8] text-2xl"></i>
                </div>
                <div>
                    <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">Reken Rush</h1>
                    <p data-animate="fade-up" data-animate-delay="2" class="mt-2 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                        Los 15 wiskundige sommen zo snel mogelijk op. De moeilijkheid neemt toe van simpele optelsommen tot deling en vermenigvuldiging. Snelheid en nauwkeurigheid bepalen je score!
                    </p>
                    <div data-animate="fade-up" data-animate-delay="3" class="flex flex-wrap gap-2 mt-4">
                        <span class="px-3 py-1 rounded-full bg-[#DBEAFE]/50 text-[#1D4ED8] text-xs font-bold">REKENEN</span>
                        <span class="px-3 py-1 rounded-full bg-[#DBEAFE]/50 text-[#1D4ED8] text-xs font-bold">SNELHEID</span>
                        <span class="px-3 py-1 rounded-full bg-[#DBEAFE]/50 text-[#1D4ED8] text-xs font-bold">PROGRESSIE</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="grid md:grid-cols-2 gap-12">
            <div>
                <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">Hoe werkt Reken Rush?</h2>
                <div class="space-y-4">
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[#5B2333] text-sm font-black">1</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">Lees de som</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">Je krijgt een wiskundige som te zien met vier antwoordopties. De operaties variëren van optellen en aftrekken tot vermenigvuldigen en delen.</p>
                        </div>
                    </div>
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[#5B2333] text-sm font-black">2</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">Kies het juiste antwoord</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">Klik op het juiste antwoord of gebruik toetsen 1-4 voor extra snelheid. Bij een fout antwoord probeer je opnieuw — maar fouten kosten tijd!</p>
                        </div>
                    </div>
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[#5B2333] text-sm font-black">3</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">Versla de klok</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">Na 15 rondes wordt je totale tijd gemeten. Vergelijk je score met andere spelers op het leaderboard!</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">Moeilijkheidsniveaus</h2>
                <div class="space-y-3">
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2.5 py-1 rounded-lg bg-green-100 text-green-700 text-[11px] font-bold">MAKKELIJK</span>
                            <span class="text-[11px] text-[#564D4A]/40 font-semibold">Ronde 1-5</span>
                        </div>
                        <p class="text-sm text-[#564D4A]/60">Eenvoudige optel- en aftreksommen met kleine getallen (2-19).</p>
                    </div>
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2.5 py-1 rounded-lg bg-yellow-100 text-yellow-700 text-[11px] font-bold">GEMIDDELD</span>
                            <span class="text-[11px] text-[#564D4A]/40 font-semibold">Ronde 6-10</span>
                        </div>
                        <p class="text-sm text-[#564D4A]/60">Grotere getallen en vermenigvuldigingen. Rekenvaardigheid wordt getest!</p>
                    </div>
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2.5 py-1 rounded-lg bg-red-100 text-red-700 text-[11px] font-bold">MOEILIJK</span>
                            <span class="text-[11px] text-[#564D4A]/40 font-semibold">Ronde 11-15</span>
                        </div>
                        <p class="text-sm text-[#564D4A]/60">Complexe vermenigvuldigingen, delingen en grote getallen. Voor de echte rekenmeesters!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-20">
            <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight text-center mb-4">Waarom Reken Rush spelen?</h2>
            <p data-animate="fade-up" class="text-center text-[#564D4A]/50 mb-12 max-w-xl mx-auto">Reken Rush is meer dan een spelletje — het is een dagelijkse workout voor je rekenvaardigheden.</p>
            <div class="grid sm:grid-cols-3 gap-6">
                <div data-animate="fade-up" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-brain text-[#5B2333]"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-2">Rekensnelheid verbeteren</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">Train je brein om sneller te rekenen door dagelijks te oefenen met toenemende moeilijkheid.</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="1" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-chart-line text-[#5B2333]"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-2">Voortgang bijhouden</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">Bekijk je scores, streaks en statistieken. Zie hoe je elke dag een beetje beter wordt.</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="2" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-trophy text-[#5B2333]"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-2">Competitief spelen</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">Vergelijk je scores met vrienden en klim op het leaderboard naar de top.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-6 py-20 text-center">
        <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-4">Klaar om te rekenen?</h2>
        <p data-animate="fade-up" class="text-[#564D4A]/50 mb-8">Maak gratis een account aan en speel vandaag nog Reken Rush.</p>
        <a href="{{ route('register') }}" data-animate="fade-up" class="inline-flex items-center gap-2 bg-[#5B2333] hover:bg-[#5B2333]/85 text-white font-bold text-sm px-8 py-4 rounded-2xl transition shadow-lg shadow-[#5B2333]/20">
            <i class="fa-solid fa-calculator"></i> Gratis starten
        </a>
    </section>
</x-layouts.marketing>
