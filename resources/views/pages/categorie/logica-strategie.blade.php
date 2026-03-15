<x-layouts.marketing>
    <x-slot:title>Logica & Strategie Spellen | BrainForge</x-slot:title>
    <x-slot:description>Scherp je logisch denkvermogen met puzzels en strategiespellen. Sudoku, Word Forge, Color Sort en meer op BrainForge.</x-slot:description>

    {{-- Page header --}}
    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <a href="{{ route('pages.games') }}" class="inline-flex items-center gap-1.5 text-sm text-[#564D4A]/50 hover:text-[#5B2333] transition mb-6">
                <i class="fa-solid fa-arrow-left text-xs"></i> Alle games
            </a>
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-chess text-[10px]"></i> LOGICA & STRATEGIE
            </span>
            <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                Logica & Strategie — Denk vooruit, win meer
            </h1>
            <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                Scherp je logisch denkvermogen met puzzels en strategiespellen die je dwingen om vooruit te denken, patronen te herkennen en slimme beslissingen te nemen.
            </p>
        </div>
    </section>

    {{-- SEO content section --}}
    <section class="max-w-6xl mx-auto px-6 py-16">
        <div data-animate="fade-up" class="max-w-3xl">
            <h2 class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">Waarom logica- en strategiespellen?</h2>
            <div class="space-y-4 text-[#564D4A]/60 leading-relaxed">
                <p>
                    Logisch denken is een vaardigheid die je overal in het leven nodig hebt — van het oplossen van problemen op werk tot het nemen van betere beslissingen in het dagelijks leven. Logica- en strategiespellen bieden een uitdagende en vermakelijke manier om deze vaardigheden te ontwikkelen en te onderhouden.
                </p>
                <p>
                    Bij strategiespellen leer je om meerdere stappen vooruit te denken, consequenties van je keuzes te overwegen en efficiënte oplossingen te vinden. Dit soort analytisch denken versterkt je prefrontale cortex — het deel van je brein dat verantwoordelijk is voor planning, besluitvorming en abstract denken.
                </p>
                <p>
                    Onze logica- en strategiespellen variëren van woordpuzzels tot ruimtelijke uitdagingen. Of je nu een klassieke Sudoku oplost, woorden raadt in Word Forge, of kleuren sorteert in Color Sort — elk spel traint een ander aspect van je strategisch denkvermogen. Speel dagelijks en merk het verschil.
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
                <h2 data-animate="fade-up" data-animate-delay="1" class="text-2xl sm:text-3xl font-black text-[#564D4A] tracking-tight">Onze logica- en strategiespellen</h2>
                <p data-animate="fade-up" data-animate-delay="2" class="mt-3 text-[#564D4A]/50 max-w-lg mx-auto">Vier spellen die je dwingen om slim en strategisch te denken.</p>
            </div>

            @php
                $games = [
                    [
                        'name' => 'Sudoku',
                        'icon' => 'fa-solid fa-table-cells-large',
                        'bg' => 'bg-[#D0EAE8]',
                        'color' => 'text-[#3a8a85]',
                        'tagBg' => 'bg-[#D0EAE8]/50',
                        'tags' => ['KLASSIEK', 'LOGICA'],
                        'description' => 'De ultieme logische puzzel. Vul het 9x9 grid zo in dat elke rij, kolom en 3x3 blok de cijfers 1 t/m 9 precies één keer bevat. Puur logisch redeneren, geen gokwerk.',
                    ],
                    [
                        'name' => 'Word Forge',
                        'icon' => 'fa-solid fa-font',
                        'bg' => 'bg-[#D6E4F0]',
                        'color' => 'text-[#4a7fa5]',
                        'tagBg' => 'bg-[#D6E4F0]/50',
                        'tags' => ['WOORDEN', 'LOGICA'],
                        'description' => 'Raad het verborgen woord in maximaal 6 pogingen. Na elke gok krijg je hints over welke letters correct zijn. Gebruik logische eliminatie om het woord te achterhalen.',
                    ],
                    [
                        'name' => 'Color Sort',
                        'icon' => 'fa-solid fa-layer-group',
                        'bg' => 'bg-[#FEF3C7]',
                        'color' => 'text-[#b45309]',
                        'tagBg' => 'bg-[#FEF3C7]/50',
                        'tags' => ['PUZZEL', 'STRATEGIE'],
                        'description' => 'Sorteer gekleurde blokken over stapels zodat elke stapel slechts één kleur bevat. Een strategische puzzel waar je meerdere stappen vooruit moet denken.',
                    ],
                    [
                        'name' => 'Block Drop',
                        'icon' => 'fa-solid fa-cube',
                        'bg' => 'bg-[#E8D5F0]',
                        'color' => 'text-[#7a4fa0]',
                        'tagBg' => 'bg-[#E8D5F0]/50',
                        'tags' => ['PUZZEL', 'STRATEGIE'],
                        'description' => 'Plaats blokken strategisch op het speelveld en maak complete rijen en kolommen vrij. Plan je zetten vooruit om de hoogste score te behalen in deze ruimtelijke strategiepuzzel.',
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
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Klaar om strategisch te denken?</h2>
                <p class="mt-3 text-white/50 max-w-md mx-auto">Maak een gratis account en test je logisch denkvermogen met dagelijkse puzzels.</p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-[#5B2333] font-bold text-sm px-8 py-4 rounded-2xl hover:bg-white/90 transition shadow-lg shadow-black/10 mt-8">
                    <i class="fa-solid fa-bolt"></i> Gratis beginnen
                </a>
            </div>
        </div>
    </section>

</x-layouts.marketing>
