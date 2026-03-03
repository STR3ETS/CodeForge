{{-- resources/views/dashboard/leaderboard.blade.php --}}
<x-layouts.dashboard :title="'Leaderboard'" active="leaderboard">
    @php
        $scope = $scope ?? request('scope', 'global');

        $tabs = [
            ['key' => 'global',  'label' => 'Worldwide',   'icon' => 'fa-solid fa-globe'],
            ['key' => 'friends', 'label' => 'Friends',     'icon' => 'fa-solid fa-user-group'],
            ['key' => 'nl',      'label' => 'Netherlands', 'icon' => 'fa-solid fa-flag'],
            ['key' => 'eu',      'label' => 'Europe',      'icon' => 'fa-solid fa-earth-europe'],
        ];

        $scopeLabel = collect($tabs)->firstWhere('key', $scope)['label'] ?? 'Worldwide';

        // Helpers
        $avatarOf = function ($u) {
            return $u && !empty($u->profile_picture) ? asset('storage/' . $u->profile_picture) : null;
        };

        // LEVEL board data
        $lvlTop = collect($topLevels ?? [])->values();
        $lvl1 = $lvlTop->get(0);
        $lvl2 = $lvlTop->get(1);
        $lvl3 = $lvlTop->get(2);
        $lvlRest = $lvlTop->slice(3)->values();

        // STREAK board data
        $stTop = collect($topStreaks ?? [])->values();
        $st1 = $stTop->get(0);
        $st2 = $stTop->get(1);
        $st3 = $stTop->get(2);
        $stRest = $stTop->slice(3)->values();

        // GAMES board data
        $gmTop = collect($topGames ?? [])->values();
        $gm1 = $gmTop->get(0);
        $gm2 = $gmTop->get(1);
        $gm3 = $gmTop->get(2);
        $gmRest = $gmTop->slice(3)->values();

        // “You” quick status (based on level list only)
        $meId = auth()->id();
        $meRankInTop = $lvlTop->search(fn($p) => (int)($p->id ?? 0) === (int)$meId);
        $meRankInTop = $meRankInTop === false ? null : ($meRankInTop + 1);
    @endphp

    <div class="flex flex-col gap-8">

        {{-- HERO HEADER --}}
        <div class="relative overflow-hidden rounded-2xl border border-[#564D4A]/10 bg-[#5B2333]">
            {{-- Background decoration --}}
            <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-20" alt="">
            <div class="absolute inset-0 bg-gradient-to-r from-[#5B2333]/95 via-[#5B2333]/80 to-transparent"></div>

            <div class="relative p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 text-white text-xs font-semibold w-fit">
                            <i class="fa-solid fa-trophy"></i>
                            Top players
                        </div>

                        <h1 class="mt-3 text-[1.5rem] md:text-[1.8rem] font-black text-white tracking-tight leading-tight">
                            Leaderboard
                        </h1>

                        <p class="mt-2 text-xs md:text-sm font-semibold text-white/80 leading-[1.3]">
                            Compare levels, streaks and activity. You vs the rest. 😄
                        </p>
                    </div>

                    {{-- Scope tabs --}}
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach($tabs as $t)
                            @php
                                $active = $scope === $t['key'];
                                $href = request()->fullUrlWithQuery(['scope' => $t['key']]);
                            @endphp

                            <a href="{{ $href }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold transition
                                      {{ $active
                                            ? 'bg-white text-[#5B2333]'
                                            : 'bg-white/10 text-white hover:bg-white/15' }}">
                                <i class="{{ $t['icon'] }} text-[13px]"></i>
                                {{ $t['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/10">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Highest level</h2>
                    <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                        The players with the most XP and levels.
                    </p>
                </div>

                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold">
                    <i class="fa-solid fa-crown"></i>
                    Podium
                </span>
            </div>

            {{-- Podium cards --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                {{-- #2 --}}
                @php $p = $lvl2; $isMe = $p ? ((int)$p->id === (int)$meId) : false; $av = $avatarOf($p); @endphp
                <div class="md:order-1 order-2 rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-2 text-xs font-bold text-[#564D4A]/60">
                            <i class="fa-solid fa-medal text-[#6B7280]"></i> #2
                        </span>
                        @if($isMe)
                            <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">YOU</span>
                        @endif
                    </div>

                    <div class="mt-4 flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-white border border-[#564D4A]/10 ring-4 ring-[#BFC6D1]/45 flex items-center justify-center">
                            @if($p && $av)
                                <img src="{{ $av }}" class="w-full h-full object-cover" alt="">
                            @elseif($p)
                                <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                    <span class="text-[#564D4A] font-black text-lg">{{ strtoupper(mb_substr($p->name, 0, 1)) }}</span>
                                </div>
                            @else
                                <i class="fa-solid fa-lock text-[#564D4A]/45"></i>
                            @endif
                        </div>

                        <p class="mt-3 text-sm font-extrabold text-[#564D4A] truncate max-w-[220px]">
                            {{ $p->name ?? 'Empty' }}
                        </p>

                        <div class="mt-3 w-full rounded-2xl border border-[#564D4A]/10 bg-white p-3 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-[#564D4A]/55">Level</span>
                            <span class="text-sm font-black text-[#564D4A]">
                                {{ $p ? (int)($p->level ?? 1) : '-' }}
                            </span>
                        </div>

                        @if($p)
                            <p class="mt-2 text-[11px] font-semibold text-[#564D4A]/55">
                                {{ number_format((int)($p->xp ?? 0), 0, ',', '.') }} Total XP
                            </p>
                        @endif
                    </div>
                </div>

                {{-- #1 --}}
                @php $p = $lvl1; $isMe = $p ? ((int)$p->id === (int)$meId) : false; $av = $avatarOf($p); @endphp
                <div class="md:order-2 order-1 rounded-2xl border border-[#564D4A]/10 bg-gradient-to-br from-[#D6B05E]/18 to-white p-5 relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-[#D6B05E]/15"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-2 text-xs font-bold text-[#B88B2A]">
                                <i class="fa-solid fa-crown"></i> #1
                            </span>
                            @if($isMe)
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">YOU</span>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-col items-center text-center">
                            <div class="relative">
                                <div class="w-20 h-20 rounded-full overflow-hidden bg-white border border-[#564D4A]/10 ring-4 ring-[#D6B05E]/40 flex items-center justify-center">
                                    @if($p && $av)
                                        <img src="{{ $av }}" class="w-full h-full object-cover" alt="">
                                    @elseif($p)
                                        <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                            <span class="text-[#564D4A] font-black text-xl">{{ strtoupper(mb_substr($p->name, 0, 1)) }}</span>
                                        </div>
                                    @else
                                        <i class="fa-solid fa-lock text-[#564D4A]/45"></i>
                                    @endif
                                </div>

                                <div class="absolute -top-2 -right-2 w-9 h-9 rounded-xl bg-white border border-[#564D4A]/10 flex items-center justify-center">
                                    <i class="fa-solid fa-crown text-[#B88B2A]"></i>
                                </div>
                            </div>

                            <p class="mt-3 text-base font-black text-[#564D4A] truncate max-w-[220px]">
                                {{ $p->name ?? 'Empty' }}
                            </p>

                            <div class="mt-3 w-full rounded-2xl border border-[#564D4A]/10 bg-white p-3 flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-[#564D4A]/55">Level</span>
                                <span class="text-base font-black text-[#564D4A]">
                                    {{ $p ? (int)($p->level ?? 1) : '-' }}
                                </span>
                            </div>

                            @if($p)
                                <p class="mt-2 text-[11px] font-semibold text-[#564D4A]/55">
                                    {{ number_format((int)($p->xp ?? 0), 0, ',', '.') }} Total XP
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- #3 --}}
                @php $p = $lvl3; $isMe = $p ? ((int)$p->id === (int)$meId) : false; $av = $avatarOf($p); @endphp
                <div class="md:order-3 order-3 rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-2 text-xs font-bold text-[#564D4A]/60">
                            <i class="fa-solid fa-medal text-[#9A5A2E]"></i> #3
                        </span>
                        @if($isMe)
                            <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">YOU</span>
                        @endif
                    </div>

                    <div class="mt-4 flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full overflow-hidden bg-white border border-[#564D4A]/10 ring-4 ring-[#C48A5A]/40 flex items-center justify-center">
                            @if($p && $av)
                                <img src="{{ $av }}" class="w-full h-full object-cover" alt="">
                            @elseif($p)
                                <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                    <span class="text-[#564D4A] font-black text-base">{{ strtoupper(mb_substr($p->name, 0, 1)) }}</span>
                                </div>
                            @else
                                <i class="fa-solid fa-lock text-[#564D4A]/45"></i>
                            @endif
                        </div>

                        <p class="mt-3 text-sm font-extrabold text-[#564D4A] truncate max-w-[220px]">
                            {{ $p->name ?? 'Empty' }}
                        </p>

                        <div class="mt-3 w-full rounded-2xl border border-[#564D4A]/10 bg-white p-3 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-[#564D4A]/55">Level</span>
                            <span class="text-sm font-black text-[#564D4A]">
                                {{ $p ? (int)($p->level ?? 1) : '-' }}
                            </span>
                        </div>

                        @if($p)
                            <p class="mt-2 text-[11px] font-semibold text-[#564D4A]/55">
                                {{ number_format((int)($p->xp ?? 0), 0, ',', '.') }} Total XP
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- List (rank 4+) --}}
            <div class="mt-5 grid gap-2">
                @forelse($lvlRest as $i => $p)
                    @php
                        $rank = $i + 4;
                        $isMe = (int)($p->id ?? 0) === (int)$meId;
                        $av = $avatarOf($p);
                    @endphp

                    <div class="group flex items-center justify-between gap-4 rounded-2xl border border-[#564D4A]/10 bg-white hover:bg-[#F7F4F3] transition p-4 {{ $isMe ? 'ring-2 ring-[#5B2333]/20' : '' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/10 flex items-center justify-center shrink-0">
                                <span class="text-xs font-black text-[#564D4A]/60">#{{ $rank }}</span>
                            </div>

                            <div class="w-10 h-10 rounded-xl overflow-hidden bg-white border border-[#564D4A]/10 shrink-0">
                                @if($av)
                                    <img src="{{ $av }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                        <span class="text-[#564D4A] font-black text-sm">{{ strtoupper(mb_substr($p->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-extrabold text-[#564D4A] truncate">{{ $p->name }}</p>
                                    @if(($p->plan ?? null) === 'pro')
                                        <i class="fa-solid fa-rectangle-pro text-[#F46036] text-[16px]"></i>
                                    @endif
                                    @if($isMe)
                                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">YOU</span>
                                    @endif
                                </div>
                                <p class="text-[11px] font-semibold text-[#564D4A]/55">
                                    {{ number_format((int)($p->xp ?? 0), 0, ',', '.') }} Total XP
                                </p>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/10 text-xs font-extrabold text-[#564D4A] shrink-0">
                            <i class="fa-solid fa-up-long text-[#5B2333]"></i>
                            Level {{ (int)($p->level ?? 1) }}
                        </span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                        <p class="text-sm font-semibold text-[#564D4A]/60">No players to show yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- =========================
            SECONDARY BOARDS (2-col)
        ========================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- =========================
                2) LONGEST STREAK
            ========================== --}}
            <div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/10">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Longest streak</h2>
                        <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                            Who can keep it going the longest?
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold">
                        <i class="fa-solid fa-fire-flame-curved"></i>
                        Heat
                    </span>
                </div>

                {{-- Top 3 --}}
                <div class="mt-6 grid grid-cols-1 gap-2">
                    @foreach([
                        ['rank'=>1,'row'=>$st1,'ring'=>'ring-[#D6B05E]/40','badge'=>'bg-[#D6B05E]/20 text-[#B88B2A]','icon'=>'fa-solid fa-crown'],
                        ['rank'=>2,'row'=>$st2,'ring'=>'ring-[#BFC6D1]/45','badge'=>'bg-[#BFC6D1]/25 text-[#6B7280]','icon'=>'fa-solid fa-medal'],
                        ['rank'=>3,'row'=>$st3,'ring'=>'ring-[#C48A5A]/40','badge'=>'bg-[#C48A5A]/20 text-[#9A5A2E]','icon'=>'fa-solid fa-medal'],
                    ] as $slot)
                        @php
                            $row = $slot['row'];
                            $p = $row['user'] ?? null;
                            $value = (int)($row['value'] ?? 0);
                            $isMe = $p ? ((int)$p->id === (int)$meId) : false;
                            $av = $avatarOf($p);
                        @endphp

                        <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold {{ $slot['badge'] }}">
                                    <i class="{{ $slot['icon'] }}"></i>
                                    #{{ $slot['rank'] }}
                                </span>
                                @if($isMe)
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">YOU</span>
                                @endif
                            </div>

                            <div class="mt-4 flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-white border border-[#564D4A]/10 ring-4 {{ $slot['ring'] }} flex items-center justify-center shrink-0">
                                    @if($p && $av)
                                        <img src="{{ $av }}" class="w-full h-full object-cover" alt="">
                                    @elseif($p)
                                        <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                            <span class="text-[#564D4A] font-black">{{ strtoupper(mb_substr($p->name, 0, 1)) }}</span>
                                        </div>
                                    @else
                                        <i class="fa-solid fa-lock text-[#564D4A]/45"></i>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm font-extrabold text-[#564D4A] truncate">{{ $p->name ?? 'Empty' }}</p>
                                    <p class="text-[11px] font-semibold text-[#564D4A]/55">
                                        {{ $p ? ('Level ' . (int)($p->level ?? 1)) : '' }}
                                    </p>
                                </div>

                                <div class="ml-auto inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-[#564D4A]/10 text-xs font-extrabold text-[#564D4A] shrink-0">
                                    <i class="fa-solid fa-fire-flame-curved text-[#5B2333]"></i>
                                    {{ $p ? $value . ' days' : '-' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- List --}}
                <div class="mt-5 grid gap-2">
                    @forelse($stRest as $i => $row)
                        @php
                            $rank = $i + 4;
                            $p = $row['user'];
                            $value = (int)$row['value'];
                            $isMe = (int)($p->id ?? 0) === (int)$meId;
                            $av = $avatarOf($p);
                        @endphp

                        <div class="group flex items-center justify-between gap-4 rounded-2xl border border-[#564D4A]/10 bg-white hover:bg-[#F7F4F3] transition p-4 {{ $isMe ? 'ring-2 ring-[#5B2333]/20' : '' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/10 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-black text-[#564D4A]/60">#{{ $rank }}</span>
                                </div>

                                <div class="w-10 h-10 rounded-xl overflow-hidden bg-white border border-[#564D4A]/10 shrink-0">
                                    @if($av)
                                        <img src="{{ $av }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                            <span class="text-[#564D4A] font-black text-sm">{{ strtoupper(mb_substr($p->name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-extrabold text-[#564D4A] truncate">{{ $p->name }}</p>
                                        @if($isMe)
                                            <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">YOU</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] font-semibold text-[#564D4A]/55">Level {{ (int)($p->level ?? 1) }}</p>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/10 text-xs font-extrabold text-[#564D4A] shrink-0">
                                <i class="fa-solid fa-fire-flame-curved text-[#5B2333]"></i>
                                {{ $value }} days
                            </span>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                            <p class="text-sm font-semibold text-[#564D4A]/60">No streaks to show yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- =========================
                3) MOST GAMES PLAYED (same style as streak)
            ========================== --}}
            <div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/10">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Most games played</h2>
                        <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                            The most active players.
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold">
                        <i class="fa-solid fa-gamepad"></i>
                        Activity
                    </span>
                </div>

                {{-- Top 3 (same layout as streak) --}}
                <div class="mt-6 grid grid-cols-1 gap-2">
                    @foreach([
                        ['rank'=>1,'row'=>$gm1,'ring'=>'ring-[#D6B05E]/40','badge'=>'bg-[#D6B05E]/20 text-[#B88B2A]','icon'=>'fa-solid fa-crown'],
                        ['rank'=>2,'row'=>$gm2,'ring'=>'ring-[#BFC6D1]/45','badge'=>'bg-[#BFC6D1]/25 text-[#6B7280]','icon'=>'fa-solid fa-medal'],
                        ['rank'=>3,'row'=>$gm3,'ring'=>'ring-[#C48A5A]/40','badge'=>'bg-[#C48A5A]/20 text-[#9A5A2E]','icon'=>'fa-solid fa-medal'],
                    ] as $slot)
                        @php
                            $row = $slot['row'];
                            $p = $row['user'] ?? null;
                            $value = (int)($row['value'] ?? 0);

                            $isMe = $p ? ((int)$p->id === (int)$meId) : false;
                            $av = $avatarOf($p);
                        @endphp

                        <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold {{ $slot['badge'] }}">
                                    <i class="{{ $slot['icon'] }}"></i>
                                    #{{ $slot['rank'] }}
                                </span>

                                @if($isMe)
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">YOU</span>
                                @endif
                            </div>

                            <div class="mt-4 flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-white border border-[#564D4A]/10 ring-4 {{ $slot['ring'] }} flex items-center justify-center shrink-0">
                                    @if($p && $av)
                                        <img src="{{ $av }}" class="w-full h-full object-cover" alt="">
                                    @elseif($p)
                                        <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                            <span class="text-[#564D4A] font-black">{{ strtoupper(mb_substr($p->name, 0, 1)) }}</span>
                                        </div>
                                    @else
                                        <i class="fa-solid fa-lock text-[#564D4A]/45"></i>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm font-extrabold text-[#564D4A] truncate">{{ $p->name ?? 'Empty' }}</p>
                                    <p class="text-[11px] font-semibold text-[#564D4A]/55">
                                        {{ $p ? ('Level ' . (int)($p->level ?? 1)) : '' }}
                                    </p>
                                </div>

                                <div class="ml-auto inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-[#564D4A]/10 text-xs font-extrabold text-[#564D4A] shrink-0">
                                    <i class="fa-solid fa-gamepad text-[#5B2333]"></i>
                                    {{ $p ? $value . ' games' : '-' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- List --}}
                <div class="mt-5 grid gap-2">
                    @forelse($gmRest as $i => $row)
                        @php
                            $rank = $i + 4;
                            $p = $row['user'];
                            $value = (int)$row['value'];

                            $isMe = (int)($p->id ?? 0) === (int)$meId;
                            $av = $avatarOf($p);
                        @endphp

                        <div class="group flex items-center justify-between gap-4 rounded-2xl border border-[#564D4A]/10 bg-white hover:bg-[#F7F4F3] transition p-4 {{ $isMe ? 'ring-2 ring-[#5B2333]/20' : '' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/10 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-black text-[#564D4A]/60">#{{ $rank }}</span>
                                </div>

                                <div class="w-10 h-10 rounded-xl overflow-hidden bg-white border border-[#564D4A]/10 shrink-0">
                                    @if($av)
                                        <img src="{{ $av }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                            <span class="text-[#564D4A] font-black text-sm">{{ strtoupper(mb_substr($p->name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-extrabold text-[#564D4A] truncate">{{ $p->name }}</p>
                                        @if($isMe)
                                            <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">YOU</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] font-semibold text-[#564D4A]/55">Level {{ (int)($p->level ?? 1) }}</p>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/10 text-xs font-extrabold text-[#564D4A] shrink-0">
                                <i class="fa-solid fa-gamepad text-[#5B2333]"></i>
                                {{ $value }} games
                            </span>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                            <p class="text-sm font-semibold text-[#564D4A]/60">No activity to show yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-layouts.dashboard>