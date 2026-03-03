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
            'find-the-emoji' => 'bg-[#FBE2D8]',
            'word-forge' => 'bg-[#FBE2D8]',
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
                            Daily games
                        </div>

                        <h1 class="mt-3 text-[1.5rem] md:text-[1.8rem] font-black text-white tracking-tight leading-tight">
                            Daily Challenges
                        </h1>

                        <p class="mt-2 text-xs md:text-sm font-semibold text-white/80 leading-[1.3]">
                            Play daily games to earn XP and keep your streak alive.
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
                            All
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'free']) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold transition
                                  {{ $filter === 'free'
                                        ? 'bg-white text-[#5B2333]'
                                        : 'bg-white/10 text-white hover:bg-white/15' }}">
                            <i class="fa-solid fa-unlock text-[13px]"></i>
                            Free
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

        {{-- Games list (like screenshot) --}}
        <div class="grid gap-3">
            @foreach($filteredGames as $g)
                @php
                    $key = $g['key'] ?? ('game-' . $loop->index);

                    $requiresPro = !empty($g['proOnly']);
                    $locked = $requiresPro && !$isPro;
                    $available = !empty($g['available']);

                    $accent = $accentMap[$key] ?? 'bg-[#EEF1F4]';
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
                                        Solved @if(!empty($g['status_time'])) in {{ $g['status_time'] }} @endif
                                    </span>
                                    @elseif(($g['status'] ?? null) === 'failed')
                                    <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#CE796B] text-white text-[10px] font-bold">
                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                        Failed
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
                                    @if($isDailyGame)
                                        <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333] text-[10px] font-bold">
                                            <i class="fa-solid fa-bolt text-[10px]"></i> Daily
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT PANEL --}}
                        <div class="w-[92px] sm:w-[110px] flex items-center justify-center relative {{ $accent }}">
                            <div class="w-12 h-12 rounded-2xl bg-white/70 border border-[#564D4A]/10 flex items-center justify-center">
                                <i class="{{ $g['icon'] ?? 'fa-solid fa-gamepad' }} text-[#564D4A] text-[18px]"></i>
                            </div>

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

        {{-- Daily Quests --}}
        <div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/10">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Daily Quests</h2>
                    <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                        Complete quests to earn XP and rewards.
                    </p>
                </div>

                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold">
                    <i class="fa-solid fa-rotate"></i>
                    Resets daily
                </span>
            </div>

            @php
                $quests = collect($quests ?? [])->values()->all();
                $doneCount = collect($quests)->filter(fn($q) => !empty($q['is_done']))->count();
                $totalCount = count($quests);
                $overallPercent = (int) round(($doneCount / max(1, $totalCount)) * 100);

                $allDone = (bool)($questsAllDone ?? false);
                $allClaimed = (bool)($questsAllClaimed ?? false);

                $canClaim = $allDone && !$allClaimed;
                $rewarded = session('quest_rewarded');
            @endphp

            @if(!is_null($rewarded))
                <div class="mt-4 rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#8E936D] text-white flex items-center justify-center">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <p class="text-sm font-extrabold text-[#564D4A] leading-tight">Rewards claimed</p>
                            <p class="text-xs font-semibold text-[#564D4A]/55">You received +{{ (int)$rewarded }} XP.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-6 rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-[#564D4A]/10 flex items-center justify-center">
                            <i class="fa-solid fa-bolt text-[#5B2333]"></i>
                        </div>

                        <div>
                            <p class="text-sm font-extrabold text-[#564D4A] leading-tight">
                                Today’s progress
                            </p>
                            <p class="text-xs font-semibold text-[#564D4A]/55">
                                {{ $doneCount }} / {{ $totalCount }} quests completed
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-[#5B2333]">
                            {{ $overallPercent }}%
                        </span>

                        <form method="POST" action="{{ route('dashboard.daily.quests.claim') }}">
                            @csrf
                            <button
                                class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-xs font-semibold transition
                                    {{ $canClaim ? 'bg-[#5B2333] hover:bg-[#5B2333]/85 text-white' : 'bg-white border border-[#564D4A]/10 text-[#564D4A]/50 cursor-not-allowed' }}"
                                {{ $canClaim ? '' : 'disabled' }}>
                                <i class="fa-solid {{ $allClaimed ? 'fa-check' : 'fa-gift' }} mr-2"></i>
                                {{ $allClaimed ? 'Claimed' : 'Claim rewards' }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-4 w-full h-[8px] rounded-full bg-[#564D4A]/10 overflow-hidden">
                    <div class="h-full rounded-full bg-[#5B2333]" style="width: {{ $overallPercent }}%"></div>
                </div>

                <div class="mt-3 flex items-center justify-between text-[11px] font-semibold text-[#564D4A]/55">
                    <span>Next reset at 00:00</span>
                    <span>{{ max(0, $totalCount - $doneCount) }} remaining</span>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
                @forelse ($quests as $q)
                    @php
                        $isDone = !empty($q['is_done']);
                        $claimed = !empty($q['claimed']);
                        $percent = (int) round(min(100, ((int)($q['progress'] ?? 0) / max(1, (int)($q['goal'] ?? 1))) * 100));
                    @endphp

                    <div class="rounded-2xl border border-[#564D4A]/10 bg-white p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-2xl bg-[#5B2333]/10 flex items-center justify-center">
                                    <i class="{{ $q['icon'] ?? 'fa-solid fa-bolt' }} text-[#5B2333] text-[16px]"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-extrabold text-[#564D4A] leading-tight truncate">
                                        {{ $q['title'] ?? 'Quest' }}
                                    </p>
                                    <p class="mt-1 text-xs font-semibold text-[#564D4A]/55 leading-[1.3]">
                                        {{ $q['desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>

                            <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold
                                {{ $claimed ? 'bg-[#8E936D]/15 text-[#8E936D]' : ($isDone ? 'bg-[#5B2333]/10 text-[#5B2333]' : 'bg-[#564D4A]/5 text-[#564D4A]/60') }}">
                                {{ $claimed ? 'CLAIMED' : ($isDone ? 'DONE' : strtoupper((string)($q['tag'] ?? ''))) }}
                            </span>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-[#564D4A]/55">Progress</span>
                            <span class="text-[11px] font-bold text-[#564D4A]">
                                {{ (int)($q['progress'] ?? 0) }} / {{ (int)($q['goal'] ?? 1) }}
                            </span>
                        </div>

                        <div class="mt-2 w-full h-[7px] rounded-full bg-[#564D4A]/10 overflow-hidden">
                            <div class="h-full rounded-full {{ $isDone ? 'bg-[#5B2333]' : 'bg-[#564D4A]/30' }}"
                                style="width: {{ $percent }}%"></div>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#564D4A]/55">
                                <i class="fa-solid fa-coins text-[#564D4A]/35"></i>
                                Reward
                            </span>

                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold
                                {{ $isDone ? 'bg-[#5B2333]/10 text-[#5B2333]' : 'bg-[#F7F4F3] text-[#564D4A]' }}">
                                {{ $q['reward'] ?? '+0 XP' }}
                            </span>
                        </div>

                        <div class="mt-4">
                            <button class="w-full inline-flex items-center justify-center rounded-xl py-2.5 text-xs font-semibold transition
                                {{ $claimed ? 'bg-[#8E936D] text-white cursor-not-allowed' : ($isDone ? 'bg-[#8E936D] text-white cursor-not-allowed' : 'bg-white border border-[#564D4A]/10 text-[#564D4A]/50 cursor-not-allowed') }}"
                                disabled>
                                <i class="fa-solid fa-check mr-2"></i>
                                {{ $claimed ? 'Claimed' : ($isDone ? 'Completed' : 'Not completed') }}
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                        <p class="text-sm font-semibold text-[#564D4A]/60">No quests configured.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-layouts.dashboard>