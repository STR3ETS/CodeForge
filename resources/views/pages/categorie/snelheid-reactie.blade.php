<x-layouts.marketing>
    <x-slot:title>Snelheid & Reactie Games | BrainForge</x-slot:title>
    <x-slot:description>Test je reactiesnelheid en reflexen met onze snelheidsgames. Reaction Time, Sequence Rush, Color Match op BrainForge.</x-slot:description>

    {{-- Page header --}}
    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <a href="{{ route('pages.games') }}" class="inline-flex items-center gap-1.5 text-sm text-[#564D4A]/50 hover:text-[#5B2333] transition mb-6">
                <i class="fa-solid fa-arrow-left text-xs"></i> Alle games
            </a>
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-bolt text-[10px]"></i> SNELHEID & REACTIE
            </span>
            <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                Snelheid & Reactie — Test je reflexen
            </h1>
            <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                Hoe snel reageer jij? Test en verbeter je reactiesnelheid met games die je reflexen, snelheid en cognitieve verwerkingssnelheid uitdagen.
            </p>
        </div>
    </section>

    {{-- SEO content section --}}
    <section class="max-w-6xl mx-auto px-6 py-16">
        <div data-animate="fade-up" class="max-w-3xl">
            <h2 class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">Waarom snelheidstraining belangrijk is</h2>
            <div class="space-y-4 text-[#564D4A]/60 leading-relaxed">
                <p>
                    Reactiesnelheid is meer dan alleen snel klikken. Het is een maatstaf voor hoe efficiënt je brein informatie verwerkt en erop reageert. Een snellere reactietijd betekent dat je brein beter is in het waarnemen van prikkels, het nemen van beslissingen en het aansturen van je lichaam — vaardigheden die je in het dagelijks leven, bij sport en op het werk nodig hebt.
                </p>
                <p>
                    Door regelmatig snelheidsgames te spelen, train je de verbindingen tussen je zintuigen en je motorische reacties. Onderzoek laat zien dat cognitieve verwerkingssnelheid verbeterd kan worden door gerichte training. Onze games zijn ontworpen om precies dat te doen — op een leuke en verslavende manier.
                </p>
                <p>
                    Van pure reactietijdtests tot games die snelheid combineren met kennis en patroonherkenning — onze snelheidsgames bieden gevarieerde uitdagingen. Meet je voortgang, vergelijk je scores en word elke dag een fractie sneller. Kleine verbeteringen tellen op tot grote resultaten.
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
                <h2 data-animate="fade-up" data-animate-delay="1" class="text-2xl sm:text-3xl font-black text-[#564D4A] tracking-tight">Onze snelheidsgames</h2>
                <p data-animate="fade-up" data-animate-delay="2" class="mt-3 text-[#564D4A]/50 max-w-lg mx-auto">Vier games die je reactiesnelheid en reflexen op de proef stellen.</p>
            </div>

            @php
                $games = [
                    [
                        'name' => 'Reaction Time',
                        'icon' => 'fa-solid fa-bolt',
                        'bg' => 'bg-[#FEF9C3]',
                        'color' => 'text-[#a16207]',
                        'tagBg' => 'bg-[#FEF9C3]/50',
                        'tags' => ['REFLEXEN', 'SNELHEID'],
                        'description' => 'Wacht tot het scherm groen wordt en klik zo snel mogelijk. Je speelt 5 rondes en je gemiddelde reactietijd is je score. De ultieme test voor je pure reactiesnelheid.',
                    ],
                    [
                        'name' => 'Sequence Rush',
                        'icon' => 'fa-solid fa-arrow-up-1-9',
                        'bg' => 'bg-[#D9EAD3]',
                        'color' => 'text-[#5a8a4e]',
                        'tagBg' => 'bg-[#D9EAD3]/50',
                        'tags' => ['GETALLEN', 'SNELHEID'],
                        'description' => 'Ontdek het patroon in een reeks getallen en vul het volgende getal aan. Hoe sneller je het patroon herkent en antwoordt, hoe meer punten je scoort. Snelheid en slim denken gecombineerd.',
                    ],
                    [
                        'name' => 'Color Match',
                        'icon' => 'fa-solid fa-palette',
                        'bg' => 'bg-[#FFE4E6]',
                        'color' => 'text-[#be123c]',
                        'tagBg' => 'bg-[#FFE4E6]/50',
                        'tags' => ['STROOP', 'SNELHEID'],
                        'description' => 'Klik zo snel mogelijk op de kleur van de tekst, niet het woord zelf. Het Stroop-effect maakt dit lastiger dan het klinkt — je brein moet snel schakelen tussen lezen en waarnemen.',
                    ],
                    [
                        'name' => 'Flag Guess',
                        'icon' => 'fa-solid fa-flag',
                        'bg' => 'bg-[#FFF3CD]',
                        'color' => 'text-[#9a7a20]',
                        'tagBg' => 'bg-[#FFF3CD]/50',
                        'tags' => ['VLAGGEN', 'KENNIS'],
                        'description' => 'Herken vlaggen van landen over de hele wereld zo snel mogelijk. Combineer je aardrijkskundekennis met snelheid — hoe sneller je antwoordt, hoe hoger je scoort.',
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
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Hoe snel ben jij?</h2>
                <p class="mt-3 text-white/50 max-w-md mx-auto">Maak een gratis account en ontdek je reactiesnelheid met dagelijkse challenges.</p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-[#5B2333] font-bold text-sm px-8 py-4 rounded-2xl hover:bg-white/90 transition shadow-lg shadow-black/10 mt-8">
                    <i class="fa-solid fa-bolt"></i> Gratis beginnen
                </a>
            </div>
        </div>
    </section>

</x-layouts.marketing>
