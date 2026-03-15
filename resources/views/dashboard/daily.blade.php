{{-- resources/views/dashboard/daily.blade.php --}}
<x-layouts.dashboard :title="'Dagelijkse Uitdagingen'" active="daily">
    @php
        $u = auth()->user();

        $filteredGames = collect($games ?? [])->values();

        $isFree = ($u->plan ?? 'free') !== 'pro';
        $isPro = !$isFree;
        $dailyDone = (int) ($u->daily_challenges_done ?? 0);
        $dailyLimit = $isFree ? 5 : null;
        $limitReached = $isFree && $dailyDone >= 5;

        $accentMap = [
            'find-the-emoji' => ['bg' => 'bg-[#FBE2D8]', 'text' => 'text-[#c0705a]'],
            'word-forge'     => ['bg' => 'bg-[#D6E4F0]', 'text' => 'text-[#4a7fa5]'],
            'sequence-rush'  => ['bg' => 'bg-[#D9EAD3]', 'text' => 'text-[#5a8a4e]'],
            'flag-guess'     => ['bg' => 'bg-[#FFF3CD]', 'text' => 'text-[#9a7a20]'],
            'block-drop'     => ['bg' => 'bg-[#E8D5F0]', 'text' => 'text-[#7a4fa0]'],
            'sudoku'         => ['bg' => 'bg-[#D0EAE8]', 'text' => 'text-[#3a8a85]'],
            'memory-grid'    => ['bg' => 'bg-[#F3E8F9]', 'text' => 'text-[#7a4fa0]'],
            'color-match'    => ['bg' => 'bg-[#FFE4E6]', 'text' => 'text-[#be123c]'],
            'reaction-time'  => ['bg' => 'bg-[#FEF9C3]', 'text' => 'text-[#a16207]'],
            'maze-runner'    => ['bg' => 'bg-[#DBEAFE]', 'text' => 'text-[#1d4ed8]'],
            'color-sort'     => ['bg' => 'bg-[#FEF3C7]', 'text' => 'text-[#b45309]'],
        ];

        $diffOrder = ['Easy', 'Medium', 'Hard', 'Extreme'];
        $diffLabelsNl = ['Easy' => 'Makkelijk', 'Medium' => 'Gemiddeld', 'Hard' => 'Moeilijk', 'Extreme' => 'Extreem'];
        $groupedGames = $filteredGames->groupBy(fn($g) => $g['difficulty'] ?? 'Other');

        $solvedCount = $filteredGames->filter(fn($g) => ($g['status'] ?? null) === 'solved')->count();
        $totalCount = $filteredGames->count();
    @endphp

    <div class="flex flex-col gap-10" x-data="{ showUpgrade: false }">

        {{-- HEADER --}}
        <div>
            <h1 class="text-2xl font-black text-[#564D4A] tracking-tight">Dagelijkse Uitdagingen</h1>
            <p class="mt-1 text-sm text-[#564D4A]/40 font-medium">
                Speel dagelijkse spellen om XP te verdienen en je streak levend te houden.
                <span class="text-[#564D4A]/60 font-bold">{{ $solvedCount }}/{{ $totalCount }}</span> afgerond.
            </p>
        </div>

        {{-- GAME LIMIT BAR (free users) --}}
        @if($isFree)
            <div class="rounded-2xl bg-white border border-[#564D4A]/6 p-4 flex items-center gap-4">
                <div class="w-9 h-9 rounded-xl bg-[#5B2333]/8 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-gamepad text-[#5B2333] text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1.5">
                        <p class="text-xs font-bold text-[#564D4A]">Dagelijkse limiet</p>
                        <p class="text-[11px] font-bold text-[#564D4A]/50">{{ $dailyDone }}/{{ $dailyLimit }}</p>
                    </div>
                    <div class="w-full h-[5px] rounded-full bg-[#564D4A]/8 overflow-hidden">
                        <div class="h-full rounded-full bg-[#5B2333] transition-all" style="width: {{ min(100, ($dailyDone / max(1, $dailyLimit)) * 100) }}%"></div>
                    </div>
                </div>
                @if($limitReached)
                    <button @click="showUpgrade = true" class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#5B2333] text-white text-[11px] font-bold hover:bg-[#5B2333]/90 transition cursor-pointer">
                        <i class="fa-solid fa-crown text-yellow-300 text-[9px]"></i> Upgrade
                    </button>
                @endif
            </div>
        @endif

        {{-- GAMES BY DIFFICULTY --}}
        <div class="flex flex-col gap-6">
            @foreach($diffOrder as $diffLabel)
                @if($groupedGames->has($diffLabel))
                    @php
                        $gamesInGroup = $groupedGames->get($diffLabel);
                        $diffStyle = match($diffLabel) {
                            'Easy'    => 'text-[#6b7052]',
                            'Medium'  => 'text-[#b8712d]',
                            'Hard'    => 'text-[#a04f43]',
                            'Extreme' => 'text-[#5B2333]',
                            default   => 'text-[#564D4A]',
                        };
                        $diffBadge = match($diffLabel) {
                            'Easy'    => 'bg-[#8E936D]/15 text-[#6b7052]',
                            'Medium'  => 'bg-[#F4A261]/15 text-[#b8712d]',
                            'Hard'    => 'bg-[#CE796B]/15 text-[#a04f43]',
                            'Extreme' => 'bg-[#5B2333]/15 text-[#5B2333]',
                            default   => 'bg-[#564D4A]/8 text-[#564D4A]',
                        };
                        $groupSolved = $gamesInGroup->filter(fn($g) => ($g['status'] ?? null) === 'solved')->count();
                    @endphp

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $diffBadge }}">
                                    <i class="fa-solid fa-signal text-[9px]"></i> {{ $diffLabelsNl[$diffLabel] ?? $diffLabel }}
                                </span>
                                <span class="text-[11px] font-semibold text-[#564D4A]/30">{{ $groupSolved }}/{{ $gamesInGroup->count() }} afgerond</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($gamesInGroup as $g)
                                @php
                                    $key = $g['key'] ?? ('game-' . $loop->index);
                                    $requiresPro = !empty($g['proOnly']);
                                    $locked = $requiresPro && $isFree;
                                    $gameStatus = $g['status'] ?? null;
                                    $freeLocked = $limitReached && !$gameStatus;
                                    $available = !empty($g['available']);
                                    $clickable = $available && !$locked && !$freeLocked;
                                    $accent = $accentMap[$key] ?? ['bg' => 'bg-[#EEF1F4]', 'text' => 'text-[#564D4A]'];
                                    $href = ($clickable && !empty($g['href'])) ? $g['href'] : '#';
                                    $dailyNo = $g['number'] ?? (100 + $loop->index);
                                    $rewardXp = (int) ($g['reward_xp'] ?? 0);
                                @endphp

                                @if($freeLocked)
                                <div @click="showUpgrade = true"
                                    class="group rounded-2xl border border-[#564D4A]/6 bg-white overflow-hidden transition cursor-pointer opacity-40 hover:opacity-55">
                                @elseif(!$clickable)
                                <span class="group rounded-2xl border border-[#564D4A]/6 bg-white overflow-hidden opacity-50 cursor-not-allowed">
                                @else
                                <a href="{{ $href }}"
                                    class="group rounded-2xl border border-[#564D4A]/6 bg-white overflow-hidden transition hover:border-[#5B2333]/30 hover:shadow-sm hover:shadow-[#5B2333]/5">
                                @endif

                                    {{-- Icon area --}}
                                    <div class="h-[80px] {{ $accent['bg'] }} flex items-center justify-center relative">
                                        <i class="{{ $g['icon'] ?? 'fa-solid fa-gamepad' }} {{ $accent['text'] }} text-2xl"></i>

                                        @if($gameStatus === 'solved')
                                            <span class="absolute top-2.5 right-2.5 w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                                                <i class="fa-solid fa-check text-white text-[9px]"></i>
                                            </span>
                                        @elseif($gameStatus === 'failed')
                                            <span class="absolute top-2.5 right-2.5 w-6 h-6 rounded-full bg-red-500 flex items-center justify-center">
                                                <i class="fa-solid fa-xmark text-white text-[9px]"></i>
                                            </span>
                                        @elseif($locked || $freeLocked)
                                            <span class="absolute top-2.5 right-2.5 w-6 h-6 rounded-full bg-white/80 border border-[#564D4A]/10 flex items-center justify-center">
                                                <i class="fa-solid fa-lock text-[#564D4A]/40 text-[9px]"></i>
                                            </span>
                                        @endif

                                        @if($requiresPro)
                                            <span class="absolute top-2.5 left-2.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/90 text-[#F46036] text-[9px] font-bold">
                                                <i class="fa-solid fa-crown text-[8px]"></i> PRO
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div class="p-4">
                                        <p class="text-sm font-black text-[#564D4A]">
                                            {{ $g['title'] ?? 'Game' }}
                                            <span class="text-[#564D4A]/30 font-bold">#{{ $dailyNo }}</span>
                                        </p>
                                        <p class="mt-0.5 text-[11px] text-[#564D4A]/40 font-medium">{{ $g['desc'] ?? 'Daily game' }}</p>

                                        <div class="mt-3 flex items-center justify-between">
                                            {{-- Status --}}
                                            <p class="text-[11px] font-semibold
                                                {{ $gameStatus === 'solved' ? 'text-green-600' : ($gameStatus === 'failed' ? 'text-red-500' : 'text-[#564D4A]/35') }}">
                                                @if($gameStatus === 'solved')
                                                    Opgelost @if(!empty($g['status_time'])) in {{ $g['status_time'] }} @endif
                                                @elseif($gameStatus === 'failed')
                                                    Niet gehaald
                                                @elseif($locked || $freeLocked)
                                                    Op slot
                                                @else
                                                    Speel nu
                                                @endif
                                            </p>

                                            {{-- XP reward --}}
                                            @if($rewardXp > 0)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                                                    {{ $gameStatus === 'solved' ? 'bg-green-100 text-green-600' : 'bg-[#5B2333]/8 text-[#5B2333]' }}">
                                                    <i class="fa-solid fa-coins text-[8px]"></i>
                                                    {{ $gameStatus === 'solved' ? '+' . $rewardXp : '+' . $rewardXp . ' XP' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                @if($freeLocked)
                                </div>
                                @elseif(!$clickable)
                                </span>
                                @else
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Quest reward flash --}}
        @if(session('quest_rewarded'))
            <div class="rounded-2xl border border-green-200 bg-green-50 p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-500 text-white flex items-center justify-center shrink-0">
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

        {{-- UPGRADE MODAL --}}
        <template x-if="showUpgrade">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak
                 role="dialog" aria-modal="true" aria-labelledby="upgradeTitle"
                 @keydown.escape.window="showUpgrade = false">
                <div class="absolute inset-0 bg-[#564D4A]/60 backdrop-blur-md" @click="showUpgrade = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center" @click.stop>
                    <button @click="showUpgrade = false" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-[#564D4A]/5 hover:bg-[#564D4A]/10 flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-[#564D4A]/50 text-sm"></i>
                    </button>

                    <div class="w-14 h-14 rounded-2xl bg-[#5B2333] flex items-center justify-center mx-auto mb-5">
                        <i class="fa-solid fa-crown text-yellow-300 text-xl"></i>
                    </div>

                    <h3 id="upgradeTitle" class="text-xl font-black text-[#564D4A]">Dagelijkse limiet bereikt</h3>
                    <p class="mt-2 text-sm text-[#564D4A]/50 font-medium leading-relaxed">
                        Je hebt vandaag al <span class="font-bold text-[#5B2333]">{{ $dailyLimit }} gratis spellen</span> gespeeld.
                        Upgrade naar <span class="font-bold text-[#5B2333]">Pro</span> om onbeperkt te spelen!
                    </p>

                    <div class="mt-6 space-y-2.5">
                        <div class="flex items-center gap-3 text-left px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <i class="fa-solid fa-infinity text-[#5B2333] text-sm"></i>
                            <span class="text-sm font-semibold text-[#564D4A]">Onbeperkt spellen per dag</span>
                        </div>
                        <div class="flex items-center gap-3 text-left px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <i class="fa-solid fa-wand-magic-sparkles text-[#B88B2A] text-sm"></i>
                            <span class="text-sm font-semibold text-[#564D4A]">Custom profieluitstraling (GIF's & meer)</span>
                        </div>
                        <div class="flex items-center gap-3 text-left px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <i class="fa-solid fa-bolt text-[#E8A838] text-sm"></i>
                            <span class="text-sm font-semibold text-[#564D4A]">Exclusieve Pro badge op je profiel</span>
                        </div>
                    </div>

                    <div class="mt-7 flex flex-col gap-3">
                        <a href="{{ route('pages.pricing') }}" class="block w-full py-3.5 rounded-xl bg-[#5B2333] text-white font-bold text-sm text-center hover:bg-[#5B2333]/90 transition">
                            <i class="fa-solid fa-crown text-yellow-300 mr-2"></i> Upgrade naar Pro — 1,99/maand
                        </a>
                        <button @click="showUpgrade = false" class="w-full py-3 rounded-xl bg-[#564D4A]/5 text-[#564D4A]/50 font-semibold text-sm hover:bg-[#564D4A]/10 transition cursor-pointer">
                            Misschien later
                        </button>
                    </div>
                </div>
            </div>
        </template>

    </div>
</x-layouts.dashboard>
