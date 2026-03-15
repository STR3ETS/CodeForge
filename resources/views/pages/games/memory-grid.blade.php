<x-layouts.marketing>
    <x-slot:title>Memory Grid - Geheugentraining | BrainForge</x-slot:title>
    <x-slot:description>Onthoud patronen en test je geheugen met Memory Grid. Train je kortetermijngeheugen met dagelijkse puzzels op BrainForge.</x-slot:description>

    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <a href="{{ route('pages.games') }}" data-animate="fade-up" class="inline-flex items-center gap-2 text-[#564D4A]/40 hover:text-[#564D4A] text-sm font-semibold mb-6 transition">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Alle games
            </a>
            <div class="flex items-start gap-5">
                <div data-animate="fade-up" data-animate-delay="1" class="w-16 h-16 rounded-2xl bg-[#E8D5F0] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-brain text-[#7a4fa0] text-2xl"></i>
                </div>
                <div>
                    <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">Memory Grid</h1>
                    <p data-animate="fade-up" data-animate-delay="2" class="mt-2 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                        Onthoud patronen en test je geheugen met Memory Grid. Bekijk het patroon, onthoud de posities en reproduceer het uit je geheugen. Train je kortetermijngeheugen met dagelijkse puzzels.
                    </p>
                    <div data-animate="fade-up" data-animate-delay="3" class="flex flex-wrap gap-2 mt-4">
                        <span class="px-3 py-1 rounded-full bg-[#E8D5F0]/50 text-[#7a4fa0] text-xs font-bold">GEHEUGEN</span>
                        <span class="px-3 py-1 rounded-full bg-[#E8D5F0]/50 text-[#7a4fa0] text-xs font-bold">PATRONEN</span>
                        <span class="px-3 py-1 rounded-full bg-[#E8D5F0]/50 text-[#7a4fa0] text-xs font-bold">CONCENTRATIE</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="grid md:grid-cols-2 gap-12">
            <div>
                <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">Hoe werkt Memory Grid?</h2>
                <div class="space-y-4">
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[#5B2333] text-sm font-black">1</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">Bekijk het patroon</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">Een patroon van gekleurde vakjes verschijnt kort op het raster. Bestudeer het goed!</p>
                        </div>
                    </div>
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[#5B2333] text-sm font-black">2</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">Onthoud de posities</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">Het patroon verdwijnt en je moet de posities van de vakjes onthouden.</p>
                        </div>
                    </div>
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[#5B2333] text-sm font-black">3</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">Reproduceer het patroon uit je geheugen</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">Klik op de juiste vakjes om het patroon na te maken. Hoe nauwkeuriger, hoe meer XP je verdient!</p>
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
                        <p class="text-sm text-[#564D4A]/60">Klein raster met eenvoudige patronen om mee te beginnen.</p>
                    </div>
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2.5 py-1 rounded-lg bg-yellow-100 text-yellow-700 text-[11px] font-bold">NORMAAL</span>
                        </div>
                        <p class="text-sm text-[#564D4A]/60">Groter raster met meer vakjes om te onthouden.</p>
                    </div>
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2.5 py-1 rounded-lg bg-red-100 text-red-700 text-[11px] font-bold">MOEILIJK</span>
                        </div>
                        <p class="text-sm text-[#564D4A]/60">Complex patroon op een groot raster. Voor de echte geheugenexperts.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-20">
            <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight text-center mb-4">Waarom Memory Grid spelen?</h2>
            <p data-animate="fade-up" class="text-center text-[#564D4A]/50 mb-12 max-w-xl mx-auto">Memory Grid is meer dan een spelletje — het is een dagelijkse workout voor je brein.</p>
            <div class="grid sm:grid-cols-3 gap-6">
                <div data-animate="fade-up" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-memory text-[#5B2333]"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-2">Kortetermijngeheugen</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">Versterk je werkgeheugen door dagelijks patronen te onthouden en te reproduceren.</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="1" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-bullseye text-[#5B2333]"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-2">Concentratie</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">Train je focus en aandacht door je volledig te concentreren op het patroon.</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="2" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-eye text-[#5B2333]"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-2">Visueel geheugen</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">Ontwikkel je visuele geheugen en herken patronen steeds sneller.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-6 py-20 text-center">
        <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-4">Klaar om te spelen?</h2>
        <p data-animate="fade-up" class="text-[#564D4A]/50 mb-8">Maak gratis een account aan en speel vandaag nog Memory Grid.</p>
        <a href="{{ route('register') }}" data-animate="fade-up" class="inline-flex items-center gap-2 bg-[#5B2333] hover:bg-[#5B2333]/85 text-white font-bold text-sm px-8 py-4 rounded-2xl transition shadow-lg shadow-[#5B2333]/20">
            <i class="fa-solid fa-bolt"></i> Gratis starten
        </a>
    </section>
</x-layouts.marketing>
