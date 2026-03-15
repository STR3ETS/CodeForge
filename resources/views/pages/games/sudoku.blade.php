<x-layouts.marketing>
    <x-slot:title>Mini Sudoku - Online Sudoku | BrainForge</x-slot:title>
    <x-slot:description>Speel dagelijks een verse Mini Sudoku puzzel. Train je logisch denkvermogen met sudoku op BrainForge. 3 moeilijkheden!</x-slot:description>

    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <a href="{{ route('pages.games') }}" data-animate="fade-up" class="inline-flex items-center gap-2 text-[#564D4A]/40 hover:text-[#564D4A] text-sm font-semibold mb-6 transition">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Alle games
            </a>
            <div class="flex items-start gap-5">
                <div data-animate="fade-up" data-animate-delay="1" class="w-16 h-16 rounded-2xl bg-[#D0EAE8] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-table-cells-large text-[#3a8a85] text-2xl"></i>
                </div>
                <div>
                    <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">Mini Sudoku</h1>
                    <p data-animate="fade-up" data-animate-delay="2" class="mt-2 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                        Speel dagelijks een verse Mini Sudoku puzzel. Vul het bord in volgens de sudoku-regels en train je logisch denkvermogen.
                    </p>
                    <div data-animate="fade-up" data-animate-delay="3" class="flex flex-wrap gap-2 mt-4">
                        <span class="px-3 py-1 rounded-full bg-[#D0EAE8]/50 text-[#3a8a85] text-xs font-bold">LOGICA</span>
                        <span class="px-3 py-1 rounded-full bg-[#D0EAE8]/50 text-[#3a8a85] text-xs font-bold">GETALLEN</span>
                        <span class="px-3 py-1 rounded-full bg-[#D0EAE8]/50 text-[#3a8a85] text-xs font-bold">PUZZEL</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="grid md:grid-cols-2 gap-12">
            <div>
                <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">Hoe werkt Mini Sudoku?</h2>
                <div class="space-y-4">
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[#5B2333] text-sm font-black">1</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">Analyseer het bord</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">Bekijk de vooraf ingevulde getallen en zoek naar logische aanknopingspunten.</p>
                        </div>
                    </div>
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[#5B2333] text-sm font-black">2</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">Vul getallen in volgens sudoku-regels</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">Elk getal mag maar één keer voorkomen per rij, kolom en blok. Gebruik logica om de juiste plek te vinden.</p>
                        </div>
                    </div>
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[#5B2333] text-sm font-black">3</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">Los de hele puzzel op</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">Vul alle lege vakjes in om de puzzel te voltooien. Hoe sneller, hoe meer XP je verdient!</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">Moeilijkheden</h2>
                <div class="space-y-3">
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2.5 py-1 rounded-lg bg-green-100 text-green-700 text-[11px] font-bold">MAKKELIJK</span>
                        </div>
                        <p class="text-sm text-[#564D4A]/60">Meer vooraf ingevulde getallen voor een soepele start.</p>
                    </div>
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2.5 py-1 rounded-lg bg-yellow-100 text-yellow-700 text-[11px] font-bold">NORMAAL</span>
                        </div>
                        <p class="text-sm text-[#564D4A]/60">Standaard moeilijkheid met een goede balans tussen uitdaging en speelplezier.</p>
                    </div>
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2.5 py-1 rounded-lg bg-red-100 text-red-700 text-[11px] font-bold">MOEILIJK</span>
                        </div>
                        <p class="text-sm text-[#564D4A]/60">Minimale hints — alleen voor de echte sudoku-experts.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-20">
            <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight text-center mb-4">Waarom Mini Sudoku spelen?</h2>
            <p data-animate="fade-up" class="text-center text-[#564D4A]/50 mb-12 max-w-xl mx-auto">Mini Sudoku is meer dan een spelletje — het is een dagelijkse workout voor je brein.</p>
            <div class="grid sm:grid-cols-3 gap-6">
                <div data-animate="fade-up" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-brain text-[#5B2333]"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-2">Logisch redeneren</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">Train je logisch denkvermogen door stap voor stap de puzzel op te lossen.</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="1" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-bullseye text-[#5B2333]"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-2">Concentratie</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">Verbeter je focus en concentratie door je te verdiepen in de puzzel.</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="2" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-lightbulb text-[#5B2333]"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-2">Probleemoplossend denken</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">Ontwikkel je probleemoplossende vaardigheden met elke puzzel die je kraakt.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-6 py-20 text-center">
        <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-4">Klaar om te spelen?</h2>
        <p data-animate="fade-up" class="text-[#564D4A]/50 mb-8">Maak gratis een account aan en speel vandaag nog Mini Sudoku.</p>
        <a href="{{ route('register') }}" data-animate="fade-up" class="inline-flex items-center gap-2 bg-[#5B2333] hover:bg-[#5B2333]/85 text-white font-bold text-sm px-8 py-4 rounded-2xl transition shadow-lg shadow-[#5B2333]/20">
            <i class="fa-solid fa-bolt"></i> Gratis starten
        </a>
    </section>
</x-layouts.marketing>
