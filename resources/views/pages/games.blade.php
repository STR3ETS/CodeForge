<x-layouts.marketing>
    <x-slot:title>Alle Games - Online Breinspellen | BrainForge</x-slot:title>
    <x-slot:description>Ontdek alle 13 breinspellen op BrainForge. Van woordpuzzels tot geheugentraining — elke dag nieuwe uitdagingen in 3 moeilijkheden.</x-slot:description>

    {{-- Page header --}}
    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-gamepad text-[10px]"></i> ONZE GAMES
            </span>
            <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                13 games, elke dag nieuw
            </h1>
            <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                Elke dag krijg je 13 verse uitdagingen. Kies je moeilijkheidsgraad en test je brein met puzzels die variëren van woordspellen tot geheugentraining.
            </p>
        </div>
    </section>

    {{-- Games grid --}}
    <section class="max-w-6xl mx-auto px-6 py-20">
        @php
            $games = [
                [
                    'name' => 'Woord Raden',
                    'icon' => 'fa-solid fa-font',
                    'bg' => 'bg-[#D6E4F0]',
                    'color' => 'text-[#4a7fa5]',
                    'tagBg' => 'bg-[#D6E4F0]/50',
                    'tags' => ['WOORDEN', 'LOGICA'],
                    'description' => 'Raad het verborgen woord in maximaal 6 pogingen. Na elke gok krijg je hints: groen betekent de juiste letter op de juiste plek, geel betekent de letter zit in het woord maar op een andere positie.',
                    'difficulty' => 'Makkelijk: 7 letters met hints | Normaal: 6 letters | Moeilijk: 5 letters, minder hints',
                ],
                [
                    'name' => 'Vind de Emoji',
                    'icon' => 'fa-solid fa-face-grin',
                    'bg' => 'bg-[#FBE2D8]',
                    'color' => 'text-[#c0705a]',
                    'tagBg' => 'bg-[#FBE2D8]/50',
                    'tags' => ['EMOJI', 'CREATIVITEIT'],
                    'description' => 'Krijg een beschrijving en vind de juiste emoji-combinatie die erbij past. Test je creativiteit en emoji-kennis in deze unieke uitdaging.',
                    'difficulty' => 'Makkelijk: 1 emoji | Normaal: 2 emoji\'s | Moeilijk: 3 emoji\'s',
                ],
                [
                    'name' => 'Reeks Raden',
                    'icon' => 'fa-solid fa-arrow-up-1-9',
                    'bg' => 'bg-[#D9EAD3]',
                    'color' => 'text-[#5a8a4e]',
                    'tagBg' => 'bg-[#D9EAD3]/50',
                    'tags' => ['GETALLEN', 'SNELHEID'],
                    'description' => 'Ontdek het patroon in een reeks getallen en vul het volgende getal aan. Hoe sneller je bent, hoe meer punten je verdient.',
                    'difficulty' => 'Makkelijk: eenvoudige reeksen | Normaal: complexere patronen | Moeilijk: meerdere bewerkingen',
                ],
                [
                    'name' => 'Vlaggen Quiz',
                    'icon' => 'fa-solid fa-flag',
                    'bg' => 'bg-[#FFF3CD]',
                    'color' => 'text-[#9a7a20]',
                    'tagBg' => 'bg-[#FFF3CD]/50',
                    'tags' => ['VLAGGEN', 'KENNIS'],
                    'description' => 'Herken vlaggen van landen over de hele wereld. Van bekende Europese vlaggen tot exotische eilandstaten — test je aardrijkskundekennis.',
                    'difficulty' => 'Makkelijk: bekende landen | Normaal: alle continenten | Moeilijk: obscure staten',
                ],
                [
                    'name' => 'Blok Drop',
                    'icon' => 'fa-solid fa-cube',
                    'bg' => 'bg-[#E8D5F0]',
                    'color' => 'text-[#7a4fa0]',
                    'tagBg' => 'bg-[#E8D5F0]/50',
                    'tags' => ['PUZZEL', 'STRATEGIE'],
                    'description' => 'Plaats blokken strategisch op het speelveld. Maak complete rijen en kolommen vrij om punten te scoren. Denk vooruit en plan je zetten.',
                    'difficulty' => 'Makkelijk: groter veld | Normaal: standaard veld | Moeilijk: kleiner veld, complexere vormen',
                ],
                [
                    'name' => 'Sudoku',
                    'icon' => 'fa-solid fa-table-cells',
                    'bg' => 'bg-[#D0EAE8]',
                    'color' => 'text-[#3a8a85]',
                    'tagBg' => 'bg-[#D0EAE8]/50',
                    'tags' => ['KLASSIEK', 'LOGICA'],
                    'description' => 'De klassieke nummerpuzzel. Vul het 9x9 grid zo in dat elke rij, kolom en 3x3 blok de cijfers 1 t/m 9 precies één keer bevat.',
                    'difficulty' => 'Makkelijk: meer vooringevuld | Normaal: standaard | Moeilijk: minimale hints',
                ],
                [
                    'name' => 'Geheugen Grid',
                    'icon' => 'fa-solid fa-brain',
                    'bg' => 'bg-[#F3E8F9]',
                    'color' => 'text-[#7a4fa0]',
                    'tagBg' => 'bg-[#F3E8F9]/50',
                    'tags' => ['GEHEUGEN', 'CONCENTRATIE'],
                    'description' => 'Onthoud de posities van emoji-paren in een 4×4 grid. Je krijgt 4 seconden om alles te bekijken, daarna moet je alle paren vinden met zo min mogelijk zetten.',
                    'difficulty' => 'Score op basis van aantal zetten — hoe minder, hoe beter',
                ],
                [
                    'name' => 'Kleuren Match',
                    'icon' => 'fa-solid fa-palette',
                    'bg' => 'bg-[#FFE4E6]',
                    'color' => 'text-[#be123c]',
                    'tagBg' => 'bg-[#FFE4E6]/50',
                    'tags' => ['STROOP', 'SNELHEID'],
                    'description' => 'Je ziet een kleurwoord in een andere kleur geschreven. Klik op de knop die past bij de kleur van de tekst, niet het woord zelf. Dit is het Stroop-effect — je brein moet de automatische leesreflex onderdrukken.',
                    'difficulty' => '20 rondes — score op snelheid en nauwkeurigheid',
                ],
                [
                    'name' => 'Reactietijd',
                    'icon' => 'fa-solid fa-bolt',
                    'bg' => 'bg-[#FEF9C3]',
                    'color' => 'text-[#a16207]',
                    'tagBg' => 'bg-[#FEF9C3]/50',
                    'tags' => ['REFLEXEN', 'SNELHEID'],
                    'description' => 'Wacht tot het scherm groen wordt en klik zo snel mogelijk. Je speelt 5 rondes en je gemiddelde reactietijd is je score. Wie heeft de snelste reflexen?',
                    'difficulty' => '5 rondes — laagste gemiddelde reactietijd wint',
                ],
                [
                    'name' => 'Doolhof Renner',
                    'icon' => 'fa-solid fa-route',
                    'bg' => 'bg-[#DBEAFE]',
                    'color' => 'text-[#1d4ed8]',
                    'tagBg' => 'bg-[#DBEAFE]/50',
                    'tags' => ['DOOLHOF', 'NAVIGATIE'],
                    'description' => 'Navigeer door een 10×10 doolhof van de linkerbovenhoek naar de rechteronderhoek. Gebruik pijltjestoetsen, WASD of swipe om je weg te vinden. Hoe sneller je het doolhof oplost, hoe hoger je scoort.',
                    'difficulty' => 'Score op basis van snelheid — elke dag een nieuw doolhof',
                ],
                [
                    'name' => 'Kleuren Sorteer',
                    'icon' => 'fa-solid fa-layer-group',
                    'bg' => 'bg-[#FEF3C7]',
                    'color' => 'text-[#b45309]',
                    'tagBg' => 'bg-[#FEF3C7]/50',
                    'tags' => ['PUZZEL', 'STRATEGIE'],
                    'description' => 'Sorteer gekleurde blokken over 5 stapels zodat elke stapel slechts één kleur bevat. Je kunt een blok alleen plaatsen op dezelfde kleur of een lege stapel. Denk strategisch — er is weinig ruimte!',
                    'difficulty' => 'Moeilijk — 4 kleuren, 5 stapels, minimale ruimte om te manoeuvreren',
                ],
                [
                    'name' => 'Reken Rush',
                    'icon' => 'fa-solid fa-calculator',
                    'bg' => 'bg-[#DBEAFE]',
                    'color' => 'text-[#1d4ed8]',
                    'tagBg' => 'bg-[#DBEAFE]/50',
                    'tags' => ['REKENEN', 'SNELHEID'],
                    'description' => 'Los 15 wiskundige sommen zo snel mogelijk op. De moeilijkheid neemt toe van simpele optelsommen tot vermenigvuldigingen en delingen. Vier antwoordopties per som — snelheid en nauwkeurigheid bepalen je score.',
                    'difficulty' => 'Progressief — rondes 1-5 makkelijk, 6-10 gemiddeld, 11-15 moeilijk',
                ],
                [
                    'name' => 'Geo Gok',
                    'icon' => 'fa-solid fa-earth-europe',
                    'bg' => 'bg-[#CCFBF1]',
                    'color' => 'text-[#0D9488]',
                    'tagBg' => 'bg-[#CCFBF1]/50',
                    'tags' => ['AARDRIJKSKUNDE', 'ONTDEKKEN'],
                    'description' => 'Je wordt ergens op de wereld gedropt in Google Street View. Kijk rond, zoek aanwijzingen en plaats een pin op de kaart. Hoe dichter bij de echte locatie, hoe hoger je op het leaderboard staat.',
                    'difficulty' => 'Variabel — van herkenbare steden tot afgelegen plekken',
                ],
            ];
        @endphp

        <div class="grid gap-6">
            @foreach($games as $game)
                <div data-animate="fade-up" class="bg-white rounded-2xl border border-[#564D4A]/6 p-8 hover:shadow-md hover:shadow-black/3 transition-all group">
                    <div class="flex flex-col md:flex-row md:items-start gap-6">
                        <div class="game-icon w-16 h-16 rounded-2xl {{ $game['bg'] }} flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <i class="{{ $game['icon'] }} {{ $game['color'] }} text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <h2 class="font-black text-xl text-[#564D4A]">{{ $game['name'] }}</h2>
                                @foreach($game['tags'] as $i => $tag)
                                    <span class="px-2.5 py-0.5 rounded-md {{ $i === 0 ? $game['tagBg'] . ' ' . $game['color'] : 'bg-[#564D4A]/5 text-[#564D4A]/40' }} text-[10px] font-bold">{{ $tag }}</span>
                                @endforeach
                            </div>
                            <p class="text-[#564D4A]/60 leading-relaxed">{{ $game['description'] }}</p>
                            <div class="mt-4 flex items-start gap-2">
                                <i class="fa-solid fa-sliders text-[#5B2333]/40 text-xs mt-1"></i>
                                <p class="text-sm text-[#564D4A]/40">{{ $game['difficulty'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>


    {{-- XP & scoring explanation --}}
    <section class="max-w-6xl mx-auto px-6 py-24">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-5">
                    <i class="fa-solid fa-star text-[10px]"></i> XP SYSTEEM
                </span>
                <h2 data-animate="fade-up" data-animate-delay="1" class="text-2xl sm:text-3xl font-black text-[#564D4A] tracking-tight">Hoe scoor je punten?</h2>
                <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/60 leading-relaxed">
                    Na elke game verdien je XP op basis van moeilijkheid en snelheid. Hoe moeilijker de uitdaging, hoe meer XP je krijgt. Voltooi dagelijkse quests voor bonuspunten.
                </p>
            </div>
            <div data-animate="fade-up" class="grid gap-4">
                <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-xl px-5 py-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-seedling text-green-600"></i>
                        <span class="font-bold text-sm text-[#564D4A]">Makkelijk</span>
                    </div>
                    <span class="font-black text-green-700 text-sm">+10 - 25 XP</span>
                </div>
                <div class="flex items-center justify-between bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-fire text-yellow-600"></i>
                        <span class="font-bold text-sm text-[#564D4A]">Normaal</span>
                    </div>
                    <span class="font-black text-yellow-700 text-sm">+25 - 50 XP</span>
                </div>
                <div class="flex items-center justify-between bg-red-50 border border-red-200 rounded-xl px-5 py-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-skull text-red-600"></i>
                        <span class="font-bold text-sm text-[#564D4A]">Moeilijk</span>
                    </div>
                    <span class="font-black text-red-700 text-sm">+50 - 100 XP</span>
                </div>
                <div class="flex items-center justify-between bg-purple-50 border border-purple-200 rounded-xl px-5 py-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-scroll text-purple-600"></i>
                        <span class="font-bold text-sm text-[#564D4A]">Quest bonus</span>
                    </div>
                    <span class="font-black text-purple-700 text-sm">+15 - 75 XP</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Difficulty explanation --}}
    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-20">
            <div class="text-center mb-14">
                <h2 data-animate="fade-up" class="text-2xl sm:text-3xl font-black text-[#564D4A] tracking-tight">Kies je niveau</h2>
                <p data-animate="fade-up" data-animate-delay="1" class="mt-3 text-[#564D4A]/50 max-w-lg mx-auto">Elke game heeft 3 moeilijkheden. Begin makkelijk en werk je omhoog.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 max-w-3xl mx-auto">
                <div data-animate="fade-up" class="rounded-2xl border border-green-200 bg-green-50/50 p-6 text-center">
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-seedling text-green-600"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-1">Makkelijk</h3>
                    <p class="text-sm text-[#564D4A]/50">Ideaal om te beginnen. Meer hints, simpelere puzzels.</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="1" class="rounded-2xl border border-yellow-200 bg-yellow-50/50 p-6 text-center">
                    <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-fire text-yellow-600"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-1">Normaal</h3>
                    <p class="text-sm text-[#564D4A]/50">De standaard uitdaging. Gebalanceerd en uitdagend.</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="2" class="rounded-2xl border border-red-200 bg-red-50/50 p-6 text-center">
                    <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-skull text-red-600"></i>
                    </div>
                    <h3 class="font-bold text-[#564D4A] mb-1">Moeilijk</h3>
                    <p class="text-sm text-[#564D4A]/50">Voor de echte puzzelaars. Minimale hints, maximale uitdaging.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="hero-gradient rounded-3xl p-12 sm:p-16 text-center relative overflow-hidden">
            <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-15 pointer-events-none" alt="">
            <div class="relative z-10">
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Probeer ze allemaal</h2>
                <p class="mt-3 text-white/50 max-w-md mx-auto">Maak een gratis account en speel vandaag nog je eerste games.</p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-[#5B2333] font-bold text-sm px-8 py-4 rounded-2xl hover:bg-white/90 transition shadow-lg shadow-black/10 mt-8">
                    <i class="fa-solid fa-bolt"></i> Gratis beginnen
                </a>
            </div>
        </div>
    </section>

</x-layouts.marketing>
