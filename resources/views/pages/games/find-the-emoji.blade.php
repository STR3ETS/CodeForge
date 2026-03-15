<x-layouts.marketing>
    <x-slot:title>Find the Emoji - Emoji Puzzel | BrainForge</x-slot:title>
    <x-slot:description>Vind de juiste emoji-combinatie bij een beschrijving. Train je creativiteit en emoji-kennis met Find the Emoji op BrainForge.</x-slot:description>

    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <a href="{{ route('pages.games') }}" data-animate="fade-up" class="inline-flex items-center gap-2 text-[#564D4A]/40 hover:text-[#564D4A] text-sm font-semibold mb-6 transition">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Alle games
            </a>
            <div class="flex items-start gap-5">
                <div data-animate="fade-up" data-animate-delay="1" class="w-16 h-16 rounded-2xl bg-[#FBE2D8] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-face-grin text-[#c0705a] text-2xl"></i>
                </div>
                <div>
                    <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">Find the Emoji</h1>
                    <p data-animate="fade-up" data-animate-delay="2" class="mt-2 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                        Vind de juiste emoji-combinatie bij een beschrijving. Gebruik je creativiteit en emoji-kennis om de perfecte match te vinden.
                    </p>
                    <div data-animate="fade-up" data-animate-delay="3" class="flex flex-wrap gap-2 mt-4">
                        <span class="px-3 py-1 rounded-full bg-[#FBE2D8]/50 text-[#c0705a] text-xs font-bold">EMOJI</span>
                        <span class="px-3 py-1 rounded-full bg-[#FBE2D8]/50 text-[#c0705a] text-xs font-bold">CREATIVITEIT</span>
                        <span class="px-3 py-1 rounded-full bg-[#FBE2D8]/50 text-[#c0705a] text-xs font-bold">DAGELIJKS</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="grid md:grid-cols-2 gap-12">
            <div>
                <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">Hoe werkt Find the Emoji?</h2>
                <div class="space-y-4">
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[#5B2333] text-sm font-black">1</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">Lees de beschrijving</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">Bekijk de beschrijving en bedenk welke emoji('s) hier het beste bij passen.</p>
                        </div>
                    </div>
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[#5B2333] text-sm font-black">2</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">Kies de juiste emoji('s)</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">Selecteer de emoji of emoji-combinatie die het beste past bij de gegeven beschrijving.</p>
                        </div>
                    </div>
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[#5B2333] text-sm font-black">3</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">Bevestig je antwoord</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">Dien je antwoord in en ontdek of je de juiste emoji-combinatie hebt gevonden!</p>
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
                        <p class="text-sm text-[#564D4A]/60">1 emoji — eenvoudige beschrijvingen om mee te beginnen.</p>
                    </div>
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2.5 py-1 rounded-lg bg-yellow-100 text-yellow-700 text-[11px] font-bold">NORMAAL</span>
                        </div>
                        <p class="text-sm text-[#564D4A]/60">2 emoji's — combineer emoji's voor de juiste match.</p>
                    </div>
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2.5 py-1 rounded-lg bg-red-100 text-red-700 text-[11px] font-bold">MOEILIJK</span>
                        </div>
                        <p class="text-sm text-[#564D4A]/60">3 emoji's — complexe combinaties voor de echte puzzelaars.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-20">
            <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight text-center mb-4">Waarom Find the Emoji spelen?</h2>
            <p data-animate="fade-up" class="text-center text-[#564D4A]/50 mb-12 max-w-xl mx-auto">Find the Emoji is meer dan een spelletje — het is een dagelijkse workout voor je brein.</p>
            <div class="grid sm:grid-cols-3 gap-6">
                <div data-animate="fade-up" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-lightbulb text-[#5B2333]"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-2">Creatief denken</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">Ontwikkel je creatieve denkvaardigheden door emoji's te koppelen aan beschrijvingen.</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="1" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-face-smile text-[#5B2333]"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-2">Emoji-kennis</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">Leer nieuwe emoji's kennen en ontdek creatieve combinaties.</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="2" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-star text-[#5B2333]"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-2">Unieke puzzel</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">Een uniek spelconcept dat je nergens anders vindt. Elke dag nieuw!</p>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-6 py-20 text-center">
        <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-4">Klaar om te spelen?</h2>
        <p data-animate="fade-up" class="text-[#564D4A]/50 mb-8">Maak gratis een account aan en speel vandaag nog Find the Emoji.</p>
        <a href="{{ route('register') }}" data-animate="fade-up" class="inline-flex items-center gap-2 bg-[#5B2333] hover:bg-[#5B2333]/85 text-white font-bold text-sm px-8 py-4 rounded-2xl transition shadow-lg shadow-[#5B2333]/20">
            <i class="fa-solid fa-bolt"></i> Gratis starten
        </a>
    </section>
</x-layouts.marketing>
