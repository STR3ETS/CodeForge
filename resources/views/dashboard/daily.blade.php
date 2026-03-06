{{-- resources/views/dashboard/daily.blade.php --}}
<x-layouts.dashboard :title="'Daily Challenges'" active="daily">
    @php
        $u = auth()->user();
        $filter = request('filter', 'all'); // all | free | pro

        $filteredGames = collect($games ?? [])->filter(function ($g) use ($filter) {
            if ($filter === 'free') return empty($g['proOnly']);
            if ($filter === 'pro')  return !empty($g['proOnly']);
            return true;
        })->values();

        $freeCount = collect($games ?? [])->where('proOnly', false)->count();
        $proCount  = collect($games ?? [])->where('proOnly', true)->count();

        // Accent colors for the right panel
        $accentMap = [
            'find-the-emoji' => ['bg' => 'bg-[#FBE2D8]', 'text' => 'text-[#c0705a]'],
            'word-forge'     => ['bg' => 'bg-[#D6E4F0]', 'text' => 'text-[#4a7fa5]'],
            'sequence-rush'  => ['bg' => 'bg-[#D9EAD3]', 'text' => 'text-[#5a8a4e]'],
            'flag-guess'     => ['bg' => 'bg-[#FFF3CD]', 'text' => 'text-[#9a7a20]'],
            'block-drop'     => ['bg' => 'bg-[#E8D5F0]', 'text' => 'text-[#7a4fa0]'],
            'sudoku'         => ['bg' => 'bg-[#D0EAE8]', 'text' => 'text-[#3a8a85]'],
        ];
    @endphp

    <div class="flex flex-col gap-8">

        {{-- HERO HEADER (same style as leaderboard) --}}
        <div class="relative overflow-hidden rounded-2xl border border-[#564D4A]/10 bg-[#5B2333]">
            <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-20" alt="">
            <div class="absolute inset-0 bg-gradient-to-r from-[#5B2333]/95 via-[#5B2333]/80 to-transparent"></div>

            <div class="relative p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 text-white text-xs font-semibold w-fit">
                            <i class="fa-solid fa-bullseye-arrow"></i>
                            Dagelijkse spellen
                        </div>

                        <h1 class="mt-3 text-[1.5rem] md:text-[1.8rem] font-black text-white tracking-tight leading-tight">
                            Dagelijkse Uitdagingen
                        </h1>

                        <p class="mt-2 text-xs md:text-sm font-semibold text-white/80 leading-[1.3]">
                            Speel dagelijkse spellen om XP te verdienen en je streak levend te houden.
                        </p>
                    </div>

                    {{-- Filter tabs (same tab style as leaderboard) --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold transition
                                  {{ $filter === 'all'
                                        ? 'bg-white text-[#5B2333]'
                                        : 'bg-white/10 text-white hover:bg-white/15' }}">
                            <i class="fa-solid fa-layer-group text-[13px]"></i>
                            Alles
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'free']) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold transition
                                  {{ $filter === 'free'
                                        ? 'bg-white text-[#5B2333]'
                                        : 'bg-white/10 text-white hover:bg-white/15' }}">
                            <i class="fa-solid fa-unlock text-[13px]"></i>
                            Gratis
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'pro']) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold transition
                                  {{ $filter === 'pro'
                                        ? 'bg-white text-[#5B2333]'
                                        : 'bg-white/10 text-white hover:bg-white/15' }}">
                            <i class="fa-solid fa-lock text-[13px]"></i>
                            Pro
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Games list grouped by difficulty --}}
        @php
            $diffOrder = ['Easy', 'Medium', 'Hard', 'Extreme'];
            $groupedGames = $filteredGames->groupBy(fn($g) => $g['difficulty'] ?? 'Other');
            $diffHeaderStyle = [
                'Easy'    => ['text' => 'text-[#6b7052]',  'icon' => 'fa-solid fa-signal',           'bar' => 'bg-[#8E936D]'],
                'Medium'  => ['text' => 'text-[#b8712d]',  'icon' => 'fa-solid fa-signal',           'bar' => 'bg-[#F4A261]'],
                'Hard'    => ['text' => 'text-[#a04f43]',  'icon' => 'fa-solid fa-signal',           'bar' => 'bg-[#CE796B]'],
                'Extreme' => ['text' => 'text-[#5B2333]',  'icon' => 'fa-solid fa-signal',           'bar' => 'bg-[#5B2333]'],
            ];
        @endphp

        <div class="flex flex-col gap-3">
            @foreach($diffOrder as $diffLabel)
                @if($groupedGames->has($diffLabel))
                @php $gamesInGroup = $groupedGames->get($diffLabel); @endphp

                <div>
                    <div class="grid gap-3">
                    @foreach($gamesInGroup as $g)
                @php
                    $key = $g['key'] ?? ('game-' . $loop->index);

                    $requiresPro = !empty($g['proOnly']);
                    $locked = $requiresPro && !$isPro;
                    $available = !empty($g['available']);

                    $accent     = $accentMap[$key] ?? ['bg' => 'bg-[#EEF1F4]', 'text' => 'text-[#564D4A]'];
                    $accentBg   = $accent['bg'];
                    $accentText = $accent['text'];
                    $dailyNo = $g['number'] ?? (100 + $loop->index);

                    $isDailyGame = (($g['tag'] ?? '') === 'Daily Game');

                    $clickable = $available && !$locked;
                    $cardClasses = $clickable
                        ? 'hover:border-[#5B2333]/50'
                        : 'opacity-50';
                @endphp

                @php
                    $href = ($clickable && !empty($g['href'])) ? $g['href'] : '#';
                @endphp

                <a href="{{ $href }}"
                    class="group block min-h-[80px] rounded-2xl border border-[#564D4A]/10 bg-white overflow-hidden transition {{ $cardClasses }} {{ $locked ? 'cursor-not-allowed' : '' }}"
                    {{ $clickable ? '' : 'aria-disabled=true' }}>

                    <div class="flex items-stretch">
                        {{-- LEFT --}}
                        <div class="flex-1 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 text-[11px] font-semibold text-[#564D4A]/55">
                                        <span>{{ $g['desc'] ?? 'Daily game' }}</span>
                                    </div>

                                    <div class="mt-2">
                                        <p class="text-lg font-black text-[#564D4A] leading-tight">
                                            {{ $g['title'] ?? 'Game' }} <span class="text-[#564D4A]/50">#{{ $dailyNo }}</span>
                                        </p>
                                    </div>
                                </div>

                                {{-- badges --}}
                                <div class="flex items-center gap-2 shrink-0">
                                    @if(($g['status'] ?? null) === 'solved')
                                    <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#8E936D] text-white text-[10px] font-bold">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                        Opgelost @if(!empty($g['status_time'])) in {{ $g['status_time'] }} @endif
                                    </span>
                                    @elseif(($g['status'] ?? null) === 'failed')
                                    <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#CE796B] text-white text-[10px] font-bold">
                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                        Mislukt
                                    </span>
                                    @endif
                                    @php
                                        $rewardXp = (int) ($g['reward_xp'] ?? 0);

                                        // ✅ als Free en geen XP-rewards meer vandaag, dan badge dimmen (alleen als nog niet solved)
                                        $xpCapReached = (!$isPro && !is_null($remaining ?? null) && (int)$remaining <= 0);
                                        $rewardBlocked = $xpCapReached && (($g['status'] ?? null) !== 'solved');
                                    @endphp
                                    @if($rewardXp > 0)
                                        <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-[10px] font-bold
                                            {{ $rewardBlocked ? 'bg-[#564D4A]/5 text-[#564D4A]/60' : 'bg-[#5B2333]/10 text-[#5B2333]' }}">
                                            <i class="fa-solid fa-coins text-[10px]"></i>
                                            {{ $rewardBlocked ? '0 XP' : ('+' . $rewardXp . ' XP') }}
                                        </span>
                                    @endif
                                    @if($requiresPro)
                                        <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#F46036]/10 text-[#F46036] text-[10px] font-bold">
                                            <i class="fa-solid fa-rectangle-pro text-[11px]"></i> Pro
                                        </span>
                                    @endif
                                    @php
                                        $diff = $g['difficulty'] ?? null;
                                        $diffStyle = match($diff) {
                                            'Easy'    => 'bg-[#8E936D]/15 text-[#6b7052]',
                                            'Medium'  => 'bg-[#F4A261]/15 text-[#b8712d]',
                                            'Hard'    => 'bg-[#CE796B]/15 text-[#a04f43]',
                                            'Extreme' => 'bg-[#5B2333]/15 text-[#5B2333]',
                                            default   => null,
                                        };
                                    @endphp
                                    @if($diff && $diffStyle)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $diffStyle }}">
                                            <i class="fa-solid fa-signal text-[9px]"></i> {{ $diff }}
                                        </span>
                                    @endif
                                    @if($isDailyGame)
                                        <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333] text-[10px] font-bold">
                                            <i class="fa-solid fa-bolt text-[10px]"></i> Daily
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT PANEL --}}
                        <div class="w-[92px] sm:w-[110px] flex items-center justify-center relative {{ $accentBg }}">
                            <i class="{{ $g['icon'] ?? 'fa-solid fa-gamepad' }} {{ $accentText }} text-[22px]"></i>

                            @if($locked)
                                <div class="absolute top-3 right-3 w-8 h-8 rounded-xl bg-white/85 border border-[#564D4A]/10 flex items-center justify-center">
                                    <i class="fa-solid fa-lock text-[#5B2333] text-[12px]"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
                    @endforeach
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        {{-- Quest reward flash --}}
        @if(session('quest_rewarded'))
            <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#8E936D] text-white flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <p class="text-sm font-extrabold text-[#564D4A] leading-tight">Beloning ontvangen!</p>
                    <p class="text-xs font-semibold text-[#564D4A]/55">Je ontving +{{ (int) session('quest_rewarded') }} XP.</p>
                </div>
            </div>
        @endif

        {{-- Daily Quests --}}
        @include('dashboard.partials.quest-section', [
            'sectionTitle'  => 'Dagelijkse Quests',
            'sectionDesc'   => 'Voltooi quests om XP te verdienen. Reset elke dag om 00:00.',
            'resetLabel'    => 'Reset dagelijks',
            'questList'     => $quests ?? [],
        ])

        {{-- Weekly Quests --}}
        @include('dashboard.partials.quest-section', [
            'sectionTitle'  => 'Wekelijkse Quests',
            'sectionDesc'   => 'Moeilijkere uitdagingen met grotere beloningen. Reset elke maandag.',
            'resetLabel'    => 'Reset wekelijks',
            'questList'     => $weeklyQuests ?? [],
        ])

    </div>
</x-layouts.dashboard>