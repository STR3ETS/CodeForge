<x-layouts.marketing>
    <x-slot:title>Pricing - Gratis of Pro | BrainForge</x-slot:title>
    <x-slot:description>BrainForge is gratis met 5 games per dag. Upgrade naar Pro voor onbeperkte games, IQ Test, cosmetics en meer vanaf €1,99/maand.</x-slot:description>

    {{-- Page header --}}
    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-tag text-[10px]"></i> PRICING
            </span>
            <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                Gratis spelen, of ga Pro
            </h1>
            <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                BrainForge is gratis te spelen met 5 games per dag. Wil je meer? Upgrade naar Pro voor onbeperkte games en extra features.
            </p>
        </div>
    </section>

    {{-- Pricing cards --}}
    <section class="max-w-6xl mx-auto px-6 py-20" x-data="{ yearly: true }">
        {{-- Toggle --}}
        <div data-animate="fade-up" class="flex items-center justify-center gap-4 mb-10">
            <span class="text-sm font-semibold transition-colors" :class="yearly ? 'text-[#564D4A]/40' : 'text-[#564D4A]'">Maandelijks</span>
            <button @click="yearly = !yearly" class="relative w-14 h-8 rounded-full transition-colors cursor-pointer" :class="yearly ? 'bg-[#5B2333]' : 'bg-[#564D4A]/20'">
                <div class="absolute top-1 w-6 h-6 bg-white rounded-full shadow transition-all" :class="yearly ? 'left-7' : 'left-1'"></div>
            </button>
            <span class="text-sm font-semibold transition-colors" :class="yearly ? 'text-[#564D4A]' : 'text-[#564D4A]/40'">
                Jaarlijks
                <span class="ml-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-bold">-43%</span>
            </span>
        </div>

        <div class="grid md:grid-cols-2 gap-8 max-w-3xl mx-auto">
            {{-- Free --}}
            <div data-animate="fade-up" class="bg-white rounded-2xl border border-[#564D4A]/8 p-8 flex flex-col">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-12 h-12 rounded-xl bg-[#564D4A]/8 flex items-center justify-center">
                        <i class="fa-solid fa-user text-[#564D4A]/40 text-lg"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-xl text-[#564D4A]">Gratis</h2>
                        <p class="text-xs text-[#564D4A]/40 font-medium">Voor altijd gratis</p>
                    </div>
                </div>

                <p class="text-5xl font-black text-[#564D4A] mb-1">0,-</p>
                <p class="text-sm text-[#564D4A]/30 font-medium mb-10">Geen creditcard nodig</p>

                @php
                    $freeFeatures = [
                        ['label' => 'Onbeperkt games per dag', 'included' => false],
                        ['label' => 'Alle 11 game types', 'included' => true],
                        ['label' => '3 moeilijkheden', 'included' => true],
                        ['label' => 'Streaks, XP, leaderboard & vrienden', 'included' => true],
                        ['label' => 'Epische & Legendarische cosmetics in de Shop', 'included' => false],
                        ['label' => 'Animated naamkleuren & custom badges', 'included' => false],
                        ['label' => 'GIF profielfoto & banner via Tenor', 'included' => false],
                        ['label' => 'Grotere uploads (5MB avatar, 8MB banner)', 'included' => false],
                        ['label' => 'Pro badge op je profiel', 'included' => false],
                        ['label' => 'Exclusieve IQ Test', 'included' => false],
                    ];
                @endphp
                <ul class="grid gap-4 mb-10">
                    @foreach($freeFeatures as $feat)
                        <li class="flex items-center gap-3 {{ $feat['included'] ? 'text-[#564D4A]/70' : 'text-[#564D4A]/30' }}">
                            @if($feat['included'])
                                <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-check text-green-600 text-[9px]"></i>
                                </div>
                            @else
                                <div class="w-5 h-5 rounded-full bg-[#564D4A]/6 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-xmark text-[#564D4A]/25 text-[9px]"></i>
                                </div>
                            @endif
                            <span class="text-sm">{{ $feat['label'] }}</span>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('register') }}" class="block w-full text-center bg-[#564D4A]/8 hover:bg-[#564D4A]/12 transition text-[#564D4A] font-bold text-sm py-4 rounded-xl mt-auto">
                    Gratis starten
                </a>
            </div>

            {{-- Pro --}}
            <div data-animate="fade-up" data-animate-delay="1" class="bg-[#5B2333] rounded-2xl p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-28 h-28 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center">
                            <i class="fa-solid fa-crown text-yellow-300 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-xl text-white">Pro</h2>
                            <p class="text-xs text-white/40 font-medium">Alles onbeperkt</p>
                        </div>
                    </div>

                    <div class="mb-1">
                        <p class="text-5xl font-black text-white" x-show="yearly">1,99 <span class="text-lg font-semibold text-white/40">/ maand</span></p>
                        <p class="text-5xl font-black text-white" x-show="!yearly" x-cloak>3,49 <span class="text-lg font-semibold text-white/40">/ maand</span></p>
                    </div>
                    <p class="text-sm text-white/30 font-medium mb-10" x-show="yearly">23,88 per jaar. Elk moment opzegbaar</p>
                    <p class="text-sm text-white/30 font-medium mb-10" x-show="!yearly" x-cloak>Maandelijks opzegbaar</p>

                    <ul class="grid gap-4 mb-10">
                        <li class="flex items-center gap-3 text-white/80">
                            <div class="w-5 h-5 rounded-full bg-yellow-300/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-yellow-300 text-[9px]"></i>
                            </div>
                            <span class="text-sm font-medium">Onbeperkt games per dag</span>
                        </li>
                        <li class="flex items-center gap-3 text-white/80">
                            <div class="w-5 h-5 rounded-full bg-yellow-300/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-yellow-300 text-[9px]"></i>
                            </div>
                            <span class="text-sm font-medium">Alle 11 game types</span>
                        </li>
                        <li class="flex items-center gap-3 text-white/80">
                            <div class="w-5 h-5 rounded-full bg-yellow-300/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-yellow-300 text-[9px]"></i>
                            </div>
                            <span class="text-sm font-medium">3 moeilijkheden</span>
                        </li>
                        <li class="flex items-center gap-3 text-white/80">
                            <div class="w-5 h-5 rounded-full bg-yellow-300/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-yellow-300 text-[9px]"></i>
                            </div>
                            <span class="text-sm font-medium">Streaks, XP, leaderboard & vrienden</span>
                        </li>
                        <li class="flex items-center gap-3 text-white/80">
                            <div class="w-5 h-5 rounded-full bg-yellow-300/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-yellow-300 text-[9px]"></i>
                            </div>
                            <span class="text-sm font-medium">Epische & Legendarische cosmetics in de Shop</span>
                        </li>
                        <li class="flex items-center gap-3 text-white/80">
                            <div class="w-5 h-5 rounded-full bg-yellow-300/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-yellow-300 text-[9px]"></i>
                            </div>
                            <span class="text-sm font-medium">Animated naamkleuren & custom badges</span>
                        </li>
                        <li class="flex items-center gap-3 text-white/80">
                            <div class="w-5 h-5 rounded-full bg-yellow-300/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-yellow-300 text-[9px]"></i>
                            </div>
                            <span class="text-sm font-medium">GIF profielfoto & banner via Tenor</span>
                        </li>
                        <li class="flex items-center gap-3 text-white/80">
                            <div class="w-5 h-5 rounded-full bg-yellow-300/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-yellow-300 text-[9px]"></i>
                            </div>
                            <span class="text-sm font-medium">Grotere uploads (5MB avatar, 8MB banner)</span>
                        </li>
                        <li class="flex items-center gap-3 text-white/80">
                            <div class="w-5 h-5 rounded-full bg-yellow-300/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-yellow-300 text-[9px]"></i>
                            </div>
                            <span class="text-sm font-medium">Pro badge op je profiel</span>
                        </li>
                        <li class="flex items-center gap-3 text-white/80">
                            <div class="w-5 h-5 rounded-full bg-yellow-300/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-yellow-300 text-[9px]"></i>
                            </div>
                            <span class="text-sm font-medium">Exclusieve IQ Test</span>
                        </li>
                    </ul>

                    @auth
                        @if(auth()->user()->plan === 'pro')
                            <a href="{{ route('subscription.portal') }}" class="block w-full text-center bg-white hover:bg-white/90 transition text-[#5B2333] font-bold text-sm py-4 rounded-xl">
                                Abonnement beheren
                            </a>
                        @else
                            <form method="POST" action="{{ route('subscription.checkout') }}" x-data>
                                @csrf
                                <input type="hidden" name="plan" :value="yearly ? 'yearly' : 'monthly'">
                                <button type="submit" class="cursor-pointer block w-full text-center bg-white hover:bg-white/90 transition text-[#5B2333] font-bold text-sm py-4 rounded-xl">
                                    Start met Pro
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="block w-full text-center bg-white hover:bg-white/90 transition text-[#5B2333] font-bold text-sm py-4 rounded-xl">
                            Start met Pro
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- Feature comparison --}}
    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-3xl mx-auto px-6 py-24">
            <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight text-center mb-14">Gratis vs. Pro vergelijken</h2>

            <div data-animate="fade-up" class="rounded-2xl border border-[#564D4A]/8 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-[#564D4A]/5">
                            <th class="text-left px-6 py-4 font-bold text-[#564D4A]">Feature</th>
                            <th class="text-center px-4 py-4 font-bold text-[#564D4A]">Gratis</th>
                            <th class="text-center px-4 py-4 font-bold text-[#5B2333]">
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-crown text-yellow-500 text-xs"></i> Pro
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#564D4A]/6">
                        <tr>
                            <td class="px-6 py-4 text-[#564D4A]/70">Games per dag</td>
                            <td class="text-center px-4 py-4 text-[#564D4A]/70">5</td>
                            <td class="text-center px-4 py-4 font-bold text-[#5B2333]">Onbeperkt</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-[#564D4A]/70">Alle 11 game types</td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-check text-green-500 text-xs"></i></td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-check text-green-500 text-xs"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-[#564D4A]/70">3 moeilijkheden</td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-check text-green-500 text-xs"></i></td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-check text-green-500 text-xs"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-[#564D4A]/70">Streaks & XP</td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-check text-green-500 text-xs"></i></td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-check text-green-500 text-xs"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-[#564D4A]/70">Leaderboard & vrienden</td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-check text-green-500 text-xs"></i></td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-check text-green-500 text-xs"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-[#564D4A]/70">Epische & Legendarische cosmetics</td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-xmark text-[#564D4A]/20 text-xs"></i></td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-check text-green-500 text-xs"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-[#564D4A]/70">Animated namen & custom badges</td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-xmark text-[#564D4A]/20 text-xs"></i></td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-check text-green-500 text-xs"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-[#564D4A]/70">GIF profielfoto & banner</td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-xmark text-[#564D4A]/20 text-xs"></i></td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-check text-green-500 text-xs"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-[#564D4A]/70">Grotere uploads</td>
                            <td class="text-center px-4 py-4 text-[#564D4A]/40 text-xs">2MB</td>
                            <td class="text-center px-4 py-4 font-bold text-[#5B2333] text-xs">5MB / 8MB</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-[#564D4A]/70">Pro badge</td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-xmark text-[#564D4A]/20 text-xs"></i></td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-crown text-yellow-500 text-xs"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-[#564D4A]/70">IQ Test</td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-xmark text-[#564D4A]/20 text-xs"></i></td>
                            <td class="text-center px-4 py-4"><i class="fa-solid fa-check text-green-500 text-xs"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Testimonial strip --}}
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="grid md:grid-cols-2 gap-6">
            <div data-animate="fade-up" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6">
                <div class="flex items-center gap-1 mb-3">
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                </div>
                <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">"Pro is het dubbel en dwars waard. Onbeperkt games voor minder dan een kopje koffie per maand."</p>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#D9EAD3] flex items-center justify-center">
                        <span class="text-xs font-bold text-[#5a8a4e]">TH</span>
                    </div>
                    <p class="text-xs font-bold text-[#564D4A]">Thomas H. <span class="font-normal text-[#564D4A]/40">— Pro-lid</span></p>
                </div>
            </div>
            <div data-animate="fade-up" data-animate-delay="1" class="bg-white rounded-2xl border border-[#564D4A]/6 p-6">
                <div class="flex items-center gap-1 mb-3">
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                </div>
                <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">"Begon gratis, maar na een week wilde ik meer dan 5 games per dag. Beste 1,99 die ik uitgeef."</p>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#FBE2D8] flex items-center justify-center">
                        <span class="text-xs font-bold text-[#c0705a]">SR</span>
                    </div>
                    <p class="text-xs font-bold text-[#564D4A]">Sophie R. <span class="font-normal text-[#564D4A]/40">— 52 dagen streak</span></p>
                </div>
            </div>
        </div>
    </section>

    {{-- Guarantee --}}
    <section class="max-w-3xl mx-auto px-6 pb-10">
        <div data-animate="fade-up" class="bg-green-50 border border-green-200 rounded-2xl p-8 text-center">
            <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-shield-halved text-green-600 text-xl"></i>
            </div>
            <h3 class="font-bold text-lg text-[#564D4A] mb-2">Altijd opzegbaar, zonder gedoe</h3>
            <p class="text-sm text-[#564D4A]/60 leading-relaxed max-w-md mx-auto">
                Niet tevreden? Zeg je abonnement op elk moment op en behoud toegang tot het einde van je betaalperiode. Geen verborgen kosten, geen vragen.
            </p>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-3xl mx-auto px-6 py-24">
            <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight text-center mb-14">Veelgestelde vragen</h2>

            <div class="grid gap-6" x-data="{ open: null }">
                @php
                    $faqs = [
                        ['q' => 'Is BrainForge echt gratis?', 'a' => 'Ja! Je kunt 5 games per dag gratis spelen, met toegang tot alle 11 game types, streaks, XP, het leaderboard en vrienden. Er is geen tijdslimiet op het gratis plan.'],
                        ['q' => 'Wat krijg ik extra met Pro?', 'a' => 'Met Pro kun je onbeperkt games per dag spelen, epische & legendarische cosmetics kopen in de shop (animated naamkleuren, custom badges en meer), GIF-profielfoto\'s en banners uploaden via Tenor, grotere bestanden uploaden, en je krijgt een Pro badge op je profiel.'],
                        ['q' => 'Kan ik Pro op elk moment opzeggen?', 'a' => 'Absoluut. Je kunt je Pro-abonnement op elk moment opzeggen. Je houdt toegang tot Pro-features tot het einde van je betaalperiode.'],
                        ['q' => 'Hoe werken streaks?', 'a' => 'Speel minstens 1 game per dag om je streak te behouden. Als je een dag overslaat, start je streak opnieuw. Je beste streak wordt altijd bewaard.'],
                        ['q' => 'Kan ik met vrienden spelen?', 'a' => 'Je kunt vrienden toevoegen, hun profielen bekijken en hun scores vergelijken met die van jou. Direct samen spelen is er nog niet, maar staat op de roadmap.'],
                        ['q' => 'Worden er nieuwe games toegevoegd?', 'a' => 'Ja! We werken continu aan nieuwe games en features. Houd de updates in de gaten.'],
                    ];
                @endphp

                @foreach($faqs as $i => $faq)
                    <div data-animate="fade-up" class="border border-[#564D4A]/8 rounded-xl overflow-hidden">
                        <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                                class="w-full flex items-center justify-between px-6 py-5 text-left cursor-pointer hover:bg-[#564D4A]/3 transition">
                            <span class="font-bold text-[#564D4A] pr-4">{{ $faq['q'] }}</span>
                            <i class="fa-solid fa-chevron-down text-[#564D4A]/30 text-xs transition-transform duration-200 shrink-0"
                               :class="open === {{ $i }} && 'rotate-180'"></i>
                        </button>
                        <div x-show="open === {{ $i }}" x-cloak x-collapse>
                            <p class="px-6 pb-5 text-sm text-[#564D4A]/60 leading-relaxed">{{ $faq['a'] }}</p>
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
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Begin vandaag nog</h2>
                <p class="mt-3 text-white/50 max-w-md mx-auto">Probeer BrainForge gratis. Upgrade wanneer je wilt.</p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-[#5B2333] font-bold text-sm px-8 py-4 rounded-2xl hover:bg-white/90 transition shadow-lg shadow-black/10">
                        <i class="fa-solid fa-bolt"></i> Gratis starten
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-layouts.marketing>
