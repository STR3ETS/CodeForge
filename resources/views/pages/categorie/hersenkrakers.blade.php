<x-layouts.marketing>
    <x-slot:title>Hersenkrakers - Online Breinpuzzels | BrainForge</x-slot:title>
    <x-slot:description>Daag je brein uit met onze hersenkrakers. Van sudoku tot doolhoven — train je logisch denkvermogen met dagelijkse puzzels op BrainForge.</x-slot:description>

    {{-- Page header --}}
    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <a href="{{ route('pages.games') }}" class="inline-flex items-center gap-1.5 text-sm text-[#564D4A]/50 hover:text-[#5B2333] transition mb-6">
                <i class="fa-solid fa-arrow-left text-xs"></i> Alle games
            </a>
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-puzzle-piece text-[10px]"></i> HERSENKRAKERS
            </span>
            <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                Hersenkrakers — Train je brein met puzzels
            </h1>
            <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                Daag je brein uit met onze collectie hersenkrakers. Van klassieke sudoku's tot uitdagende doolhoven — elke dag nieuwe puzzels om je logisch denkvermogen te trainen.
            </p>
        </div>
    </section>

    {{-- SEO content section --}}
    <section class="max-w-6xl mx-auto px-6 py-16">
        <div data-animate="fade-up" class="max-w-3xl">
            <h2 class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">Wat zijn hersenkrakers?</h2>
            <div class="space-y-4 text-[#564D4A]/60 leading-relaxed">
                <p>
                    Hersenkrakers zijn puzzels en breinspellen die je cognitieve vaardigheden uitdagen. Ze dwingen je om logisch na te denken, patronen te herkennen en creatieve oplossingen te vinden. Op BrainForge bieden we dagelijks nieuwe hersenkrakers aan die variëren van nummerpuzzels tot ruimtelijke uitdagingen.
                </p>
                <p>
                    Wetenschappelijk onderzoek toont aan dat regelmatig puzzelen je brein scherp houdt. Hersenkrakers stimuleren de aanmaak van nieuwe neurale verbindingen, verbeteren je probleemoplossend vermogen en kunnen zelfs helpen bij het vertragen van cognitieve achteruitgang. Door elke dag een paar puzzels op te lossen, investeer je actief in de gezondheid van je brein.
                </p>
                <p>
                    Of je nu een doorgewinterde puzzelaar bent of net begint — onze hersenkrakers zijn beschikbaar in meerdere moeilijkheidsniveaus. Begin met de makkelijke variant om het spelconcept te leren en werk je omhoog naar de moeilijke uitdagingen voor maximale breintraining en XP.
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
                <h2 data-animate="fade-up" data-animate-delay="1" class="text-2xl sm:text-3xl font-black text-[#564D4A] tracking-tight">Onze hersenkrakers</h2>
                <p data-animate="fade-up" data-animate-delay="2" class="mt-3 text-[#564D4A]/50 max-w-lg mx-auto">Vier uitdagende puzzelspellen die je logisch denkvermogen op de proef stellen.</p>
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
                        'description' => 'De klassieke nummerpuzzel. Vul het 9x9 grid zo in dat elke rij, kolom en 3x3 blok de cijfers 1 t/m 9 precies één keer bevat. Perfecte hersenkraker voor elke dag.',
                    ],
                    [
                        'name' => 'Maze Runner',
                        'icon' => 'fa-solid fa-route',
                        'bg' => 'bg-[#DBEAFE]',
                        'color' => 'text-[#1d4ed8]',
                        'tagBg' => 'bg-[#DBEAFE]/50',
                        'tags' => ['DOOLHOF', 'NAVIGATIE'],
                        'description' => 'Navigeer door een doolhof van start naar finish. Gebruik je ruimtelijk inzicht en geheugen om de snelste route te vinden. Elke dag een nieuw doolhof.',
                    ],
                    [
                        'name' => 'Block Drop',
                        'icon' => 'fa-solid fa-cube',
                        'bg' => 'bg-[#E8D5F0]',
                        'color' => 'text-[#7a4fa0]',
                        'tagBg' => 'bg-[#E8D5F0]/50',
                        'tags' => ['PUZZEL', 'STRATEGIE'],
                        'description' => 'Plaats blokken strategisch op het speelveld. Maak complete rijen en kolommen vrij om punten te scoren. Een ruimtelijke hersenkraker die vooruitdenken vereist.',
                    ],
                    [
                        'name' => 'Sequence Rush',
                        'icon' => 'fa-solid fa-arrow-up-1-9',
                        'bg' => 'bg-[#D9EAD3]',
                        'color' => 'text-[#5a8a4e]',
                        'tagBg' => 'bg-[#D9EAD3]/50',
                        'tags' => ['GETALLEN', 'SNELHEID'],
                        'description' => 'Ontdek het patroon in een reeks getallen en vul het volgende getal aan. Train je patroonherkenning met steeds complexere getallenreeksen.',
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
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Klaar voor een hersenkraker?</h2>
                <p class="mt-3 text-white/50 max-w-md mx-auto">Maak een gratis account en daag je brein elke dag uit met verse puzzels.</p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-[#5B2333] font-bold text-sm px-8 py-4 rounded-2xl hover:bg-white/90 transition shadow-lg shadow-black/10 mt-8">
                    <i class="fa-solid fa-bolt"></i> Gratis beginnen
                </a>
            </div>
        </div>
    </section>

</x-layouts.marketing>
