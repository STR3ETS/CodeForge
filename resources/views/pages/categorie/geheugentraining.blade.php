<x-layouts.marketing>
    <x-slot:title>Geheugentraining - Online Geheugenspellen | BrainForge</x-slot:title>
    <x-slot:description>Verbeter je geheugen met dagelijkse geheugenspellen. Memory Grid, Color Match en meer — train je brein op BrainForge.</x-slot:description>

    {{-- Page header --}}
    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <a href="{{ route('pages.games') }}" class="inline-flex items-center gap-1.5 text-sm text-[#564D4A]/50 hover:text-[#5B2333] transition mb-6">
                <i class="fa-solid fa-arrow-left text-xs"></i> Alle games
            </a>
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-brain text-[10px]"></i> GEHEUGENTRAINING
            </span>
            <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                Geheugentraining — Verbeter je geheugen dagelijks
            </h1>
            <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                Train je geheugen met onze speciaal ontworpen geheugenspellen. Van visuele patronen tot kleurherkenning — versterk je kortetermijn- en langetermijngeheugen.
            </p>
        </div>
    </section>

    {{-- SEO content section --}}
    <section class="max-w-6xl mx-auto px-6 py-16">
        <div data-animate="fade-up" class="max-w-3xl">
            <h2 class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">Waarom geheugentraining belangrijk is</h2>
            <div class="space-y-4 text-[#564D4A]/60 leading-relaxed">
                <p>
                    Je geheugen is een van de belangrijkste cognitieve functies. Het stelt je in staat om te leren, ervaringen op te slaan en dagelijkse taken uit te voeren. Net als een spier wordt je geheugen sterker als je het regelmatig traint. Geheugenspellen zijn een effectieve en leuke manier om dit te doen.
                </p>
                <p>
                    Onderzoek wijst uit dat dagelijkse geheugentraining het werkgeheugen kan verbeteren, de concentratie verhoogt en zelfs kan bijdragen aan het voorkomen van geheugenverlies op latere leeftijd. Door actief je brein te trainen met geheugenspellen, bouw je aan een sterkere cognitieve reserve.
                </p>
                <p>
                    Op BrainForge bieden we diverse geheugenspellen aan die verschillende aspecten van je geheugen trainen. Van het onthouden van posities en patronen tot het snel herkennen van kleuren onder tijdsdruk — elk spel richt zich op een ander onderdeel van je geheugen. Speel dagelijks voor het beste resultaat.
                </p>
            </div>
        </div>
    </section>

    {{-- Featured games grid --}}
    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-20">
            <div class="text-center mb-14">
                <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                    <i class="fa-solid fa-gamepad text-[10px]"></i> GAMES
                </span>
                <h2 data-animate="fade-up" data-animate-delay="1" class="text-2xl sm:text-3xl font-black text-[#564D4A] tracking-tight">Onze geheugenspellen</h2>
                <p data-animate="fade-up" data-animate-delay="2" class="mt-3 text-[#564D4A]/50 max-w-lg mx-auto">Drie spellen die je geheugen op verschillende manieren trainen.</p>
            </div>

            @php
                $games = [
                    [
                        'name' => 'Memory Grid',
                        'icon' => 'fa-solid fa-brain',
                        'bg' => 'bg-[#E8D5F0]',
                        'color' => 'text-[#7a4fa0]',
                        'tagBg' => 'bg-[#E8D5F0]/50',
                        'tags' => ['GEHEUGEN', 'CONCENTRATIE'],
                        'description' => 'Onthoud de posities van emoji-paren in een grid. Je krijgt een paar seconden om alles te bekijken, daarna moet je alle paren vinden met zo min mogelijk zetten. Perfecte training voor je visueel geheugen.',
                    ],
                    [
                        'name' => 'Color Match',
                        'icon' => 'fa-solid fa-palette',
                        'bg' => 'bg-[#FFE4E6]',
                        'color' => 'text-[#be123c]',
                        'tagBg' => 'bg-[#FFE4E6]/50',
                        'tags' => ['STROOP', 'SNELHEID'],
                        'description' => 'Je ziet een kleurwoord in een andere kleur geschreven. Klik op de knop die past bij de kleur van de tekst, niet het woord zelf. Dit Stroop-effect traint je werkgeheugen en cognitieve flexibiliteit.',
                    ],
                    [
                        'name' => 'Find the Emoji',
                        'icon' => 'fa-solid fa-face-grin',
                        'bg' => 'bg-[#FBE2D8]',
                        'color' => 'text-[#c0705a]',
                        'tagBg' => 'bg-[#FBE2D8]/50',
                        'tags' => ['EMOJI', 'CREATIVITEIT'],
                        'description' => 'Krijg een beschrijving en vind de juiste emoji-combinatie die erbij past. Train je associatief geheugen en creativiteit door verbanden te leggen tussen woorden en symbolen.',
                    ],
                ];
            @endphp

            <div class="grid gap-6">
                @foreach($games as $game)
                    <div data-animate="fade-up" class="bg-white rounded-2xl border border-[#564D4A]/6 p-8 hover:shadow-md hover:shadow-black/3 transition-all group">
                        <div class="flex flex-col md:flex-row md:items-start gap-6">
                            <div class="w-16 h-16 rounded-2xl {{ $game['bg'] }} flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <i class="{{ $game['icon'] }} {{ $game['color'] }} text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <h3 class="font-black text-xl text-[#564D4A]">{{ $game['name'] }}</h3>
                                    @foreach($game['tags'] as $i => $tag)
                                        <span class="px-2.5 py-0.5 rounded-md {{ $i === 0 ? $game['tagBg'] . ' ' . $game['color'] : 'bg-[#564D4A]/5 text-[#564D4A]/40' }} text-[10px] font-bold">{{ $tag }}</span>
                                    @endforeach
                                </div>
                                <p class="text-[#564D4A]/60 leading-relaxed">{{ $game['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="hero-gradient rounded-3xl p-12 sm:p-16 text-center relative overflow-hidden">
            <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-15 pointer-events-none" alt="">
            <div class="relative z-10">
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Train je geheugen vandaag nog</h2>
                <p class="mt-3 text-white/50 max-w-md mx-auto">Maak een gratis account en speel dagelijks geheugenspellen om je brein scherp te houden.</p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-[#5B2333] font-bold text-sm px-8 py-4 rounded-2xl hover:bg-white/90 transition shadow-lg shadow-black/10 mt-8">
                    <i class="fa-solid fa-bolt"></i> Gratis beginnen
                </a>
            </div>
        </div>
    </section>

</x-layouts.marketing>
