{{-- resources/views/dashboard/leaderboard.blade.php --}}
<x-layouts.dashboard :title="'Leaderboard'" active="leaderboard">
    @php
        $scope = $scope ?? request('scope', 'global');

        $tabs = [
            ['key' => 'global',  'label' => 'Wereldwijd',  'icon' => 'fa-solid fa-globe'],
            ['key' => 'friends', 'label' => 'Vrienden',    'icon' => 'fa-solid fa-user-group'],
        ];

        $scopeLabel = collect($tabs)->firstWhere('key', $scope)['label'] ?? 'Worldwide';

        $avatarOf = function ($u) {
            return $u && !empty($u->profile_picture) ? asset('storage/' . $u->profile_picture) : null;
        };

        $lvlTop = collect($topLevels ?? [])->values();
        $lvl1 = $lvlTop->get(0);
        $lvl2 = $lvlTop->get(1);
        $lvl3 = $lvlTop->get(2);
        $lvlRest = $lvlTop->slice(3)->values();

        $fmtMs = function (int $ms): string {
            $s = $ms / 1000;
            if ($s < 60) return number_format($s, 1) . 's';
            $m = (int) floor($s / 60);
            $sec = (int) round($s - $m * 60);
            return $m . 'm ' . $sec . 's';
        };

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

        $meId = auth()->id();
        $meRankInTop = $lvlTop->search(fn($p) => (int)($p->id ?? 0) === (int)$meId);
        $meRankInTop = $meRankInTop === false ? null : ($meRankInTop + 1);
    @endphp

    <div class="flex flex-col gap-8">

        {{-- CLEAN TEXT HEADER --}}
        <div>
            <h1 class="text-[1.5rem] md:text-[1.8rem] font-black text-[#564D4A] tracking-tight leading-tight">
                Scorebord
            </h1>
            <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
                Vergelijk levels, streaks en activiteit. Jij vs de rest.
            </p>
        </div>

        {{-- SCOPE TABS --}}
        <div class="flex flex-wrap items-center gap-2">
            @foreach($tabs as $t)
                @php
                    $active = $scope === $t['key'];
                    $href = request()->fullUrlWithQuery(['scope' => $t['key']]);
                @endphp
                <a href="{{ $href }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold transition border
                          {{ $active
                                ? 'bg-[#5B2333] text-white border-[#5B2333]'
                                : 'bg-white text-[#564D4A] border-[#564D4A]/6 hover:border-[#564D4A]/15' }}">
                    <i class="{{ $t['icon'] }} text-[13px]"></i>
                    {{ $t['label'] }}
                </a>
            @endforeach
        </div>

        {{-- PODIUM: HOOGSTE LEVEL --}}
        <div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Hoogste level</h2>
                    <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                        De spelers met de meeste XP en levels.
                    </p>
                </div>
                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold">
                    <i class="fa-solid fa-crown"></i> Podium
                </span>
            </div>

            {{-- Podium cards --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                {{-- #2 --}}
                @php $p = $lvl2; $isMe = $p ? ((int)$p->id === (int)$meId) : false; $av = $avatarOf($p); @endphp
                <div class="md:order-1 order-2 rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-2 text-xs font-bold text-[#564D4A]/60">
                            <i class="fa-solid fa-medal text-[#6B7280]"></i> #2
                        </span>
                        @if($isMe)
                            <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">JIJ</span>
                        @endif
                    </div>
                    <div class="mt-4 flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-white border border-[#564D4A]/6 ring-4 ring-[#BFC6D1]/45 flex items-center justify-center">
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
                            {{ $p->name ?? 'Leeg' }}
                        </p>
                        <div class="mt-3 w-full rounded-2xl border border-[#564D4A]/6 bg-white p-3 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-[#564D4A]/55">Level</span>
                            <span class="text-sm font-black text-[#564D4A]">
                                {{ $p ? (int)($p->level ?? 1) : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- #1 --}}
                @php $p = $lvl1; $isMe = $p ? ((int)$p->id === (int)$meId) : false; $av = $avatarOf($p); @endphp
                <div class="md:order-2 order-1 rounded-2xl border border-[#564D4A]/6 bg-gradient-to-br from-[#D6B05E]/18 to-white p-5 relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-[#D6B05E]/15"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-2 text-xs font-bold text-[#B88B2A]">
                                <i class="fa-solid fa-crown"></i> #1
                            </span>
                            @if($isMe)
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">JIJ</span>
                            @endif
                        </div>
                        <div class="mt-4 flex flex-col items-center text-center">
                            <div class="relative">
                                <div class="w-20 h-20 rounded-full overflow-hidden bg-white border border-[#564D4A]/6 ring-4 ring-[#D6B05E]/40 flex items-center justify-center">
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
                                <div class="absolute -top-2 -right-2 w-9 h-9 rounded-xl bg-white border border-[#564D4A]/6 flex items-center justify-center">
                                    <i class="fa-solid fa-crown text-[#B88B2A]"></i>
                                </div>
                            </div>
                            <p class="mt-3 text-base font-black text-[#564D4A] truncate max-w-[220px]">
                                {{ $p->name ?? 'Leeg' }}
                            </p>
                            <div class="mt-3 w-full rounded-2xl border border-[#564D4A]/6 bg-white p-3 flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-[#564D4A]/55">Level</span>
                                <span class="text-base font-black text-[#564D4A]">
                                    {{ $p ? (int)($p->level ?? 1) : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- #3 --}}
                @php $p = $lvl3; $isMe = $p ? ((int)$p->id === (int)$meId) : false; $av = $avatarOf($p); @endphp
                <div class="md:order-3 order-3 rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-2 text-xs font-bold text-[#564D4A]/60">
                            <i class="fa-solid fa-medal text-[#9A5A2E]"></i> #3
                        </span>
                        @if($isMe)
                            <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">JIJ</span>
                        @endif
                    </div>
                    <div class="mt-4 flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full overflow-hidden bg-white border border-[#564D4A]/6 ring-4 ring-[#C48A5A]/40 flex items-center justify-center">
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
                            {{ $p->name ?? 'Leeg' }}
                        </p>
                        <div class="mt-3 w-full rounded-2xl border border-[#564D4A]/6 bg-white p-3 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-[#564D4A]/55">Level</span>
                            <span class="text-sm font-black text-[#564D4A]">
                                {{ $p ? (int)($p->level ?? 1) : '-' }}
                            </span>
                        </div>
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
                    <div class="group flex items-center justify-between gap-4 rounded-2xl border border-[#564D4A]/6 bg-white hover:bg-[#F7F4F3] transition p-4 {{ $isMe ? 'ring-2 ring-[#5B2333]/20' : '' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 flex items-center justify-center shrink-0">
                                <span class="text-xs font-black text-[#564D4A]/60">#{{ $rank }}</span>
                            </div>
                            <div class="w-10 h-10 rounded-xl overflow-hidden bg-white border border-[#564D4A]/6 shrink-0">
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
                                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">JIJ</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-xs font-extrabold text-[#564D4A] shrink-0">
                            <i class="fa-solid fa-up-long text-[#5B2333]"></i>
                            Level {{ (int)($p->level ?? 1) }}
                        </span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
                        <p class="text-sm font-semibold text-[#564D4A]/60">Nog geen spelers om te tonen.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- SPEED RECORDS PER GAME --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach($speedBoards ?? [] as $gameKey => $board)
                @php
                    $accent     = $accentMap[$gameKey] ?? ['bg' => 'bg-[#5B2333]/10', 'text' => 'text-[#5B2333]'];
                    $accentBg   = $accent['bg'];
                    $accentText = $accent['text'];
                    $spTop  = $board['rows'];
                    $sp1    = $spTop->get(0);
                    $sp2    = $spTop->get(1);
                    $sp3    = $spTop->get(2);
                    $spRest = $spTop->slice(3)->values();
                @endphp

                <div class="w-full bg-white rounded-2xl p-6 border border-[#564D4A]/6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl {{ $accentBg }} flex items-center justify-center shrink-0">
                                <i class="{{ $board['icon'] }} {{ $accentText }} text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-[#564D4A]">{{ $board['title'] }}</h2>
                                <p class="text-[11px] font-semibold text-[#564D4A]/50">{{ ($board['rank_by'] ?? 'time') === 'attempts' ? 'Minste zetten vandaag' : (($board['rank_by'] ?? 'time') === 'reaction' ? 'Snelste reactie vandaag' : 'Snelst vandaag') }}</p>
                            </div>
                        </div>
                        @if(($board['rank_by'] ?? 'time') === 'attempts')
                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold shrink-0">
                                <i class="fa-solid fa-hand-pointer"></i> Zetten
                            </span>
                        @elseif(($board['rank_by'] ?? 'time') === 'reaction')
                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold shrink-0">
                                <i class="fa-solid fa-bolt"></i> Reactie
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold shrink-0">
                                <i class="fa-solid fa-stopwatch"></i> Snelheid
                            </span>
                        @endif
                    </div>

                    @if($spTop->isEmpty())
                        <div class="mt-5 rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
                            <p class="text-xs font-semibold text-[#564D4A]/60">Niemand heeft dit vandaag al afgerond.</p>
                        </div>
                    @else
                        <div class="mt-5 grid grid-cols-1 gap-2">
                            @foreach([
                                ['rank'=>1,'row'=>$sp1,'ring'=>'ring-[#D6B05E]/40','badge'=>'bg-[#D6B05E]/20 text-[#B88B2A]','icon'=>'fa-solid fa-crown'],
                                ['rank'=>2,'row'=>$sp2,'ring'=>'ring-[#BFC6D1]/45','badge'=>'bg-[#BFC6D1]/25 text-[#6B7280]','icon'=>'fa-solid fa-medal'],
                                ['rank'=>3,'row'=>$sp3,'ring'=>'ring-[#C48A5A]/40','badge'=>'bg-[#C48A5A]/20 text-[#9A5A2E]','icon'=>'fa-solid fa-medal'],
                            ] as $slot)
                                @if($slot['row'])
                                    @php
                                        $rp   = $slot['row']['user'];
                                        $ms   = $slot['row']['best_ms'];
                                        $rankBy = $board['rank_by'] ?? 'time';
                                        $rankByAttempts = $rankBy === 'attempts';
                                        $rankByReaction = $rankBy === 'reaction';
                                        $bestAttempts = $slot['row']['best_attempts'] ?? null;
                                        $isMe = $rp ? ((int)$rp->id === (int)$meId) : false;
                                        $av   = $avatarOf($rp);
                                    @endphp
                                    <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-4">
                                        <div class="flex items-center justify-between">
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold {{ $slot['badge'] }}">
                                                <i class="{{ $slot['icon'] }}"></i> #{{ $slot['rank'] }}
                                            </span>
                                            @if($isMe)
                                                <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">JIJ</span>
                                            @endif
                                        </div>
                                        <div class="mt-3 flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full overflow-hidden bg-white border border-[#564D4A]/6 ring-4 {{ $slot['ring'] }} flex items-center justify-center shrink-0">
                                                @if($rp && $av)
                                                    <img src="{{ $av }}" class="w-full h-full object-cover" alt="">
                                                @elseif($rp)
                                                    <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                                        <span class="text-[#564D4A] font-black text-sm">{{ strtoupper(mb_substr($rp->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-extrabold text-[#564D4A] truncate">{{ $rp->name ?? '-' }}</p>
                                                <p class="text-[11px] font-semibold text-[#564D4A]/55">Level {{ (int)($rp->level ?? 1) }}</p>
                                            </div>
                                            @if($rankByAttempts)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-white border border-[#564D4A]/6 text-xs font-extrabold text-[#564D4A] shrink-0">
                                                    <i class="fa-solid fa-hand-pointer text-[#5B2333]"></i>
                                                    {{ $bestAttempts }} zetten
                                                </span>
                                            @elseif($rankByReaction)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-white border border-[#564D4A]/6 text-xs font-extrabold text-[#564D4A] shrink-0">
                                                    <i class="fa-solid fa-bolt text-[#5B2333]"></i>
                                                    {{ $ms }}ms
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-white border border-[#564D4A]/6 text-xs font-extrabold text-[#564D4A] shrink-0">
                                                    <i class="fa-solid fa-stopwatch text-[#5B2333]"></i>
                                                    {{ $fmtMs($ms) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        @if($spRest->isNotEmpty())
                            <div class="mt-3 grid gap-2">
                                @foreach($spRest as $i => $row)
                                    @php
                                        $rp   = $row['user'];
                                        $ms   = $row['best_ms'];
                                        $rankBy = $board['rank_by'] ?? 'time';
                                        $rankByAttempts = $rankBy === 'attempts';
                                        $rankByReaction = $rankBy === 'reaction';
                                        $bestAttempts = $row['best_attempts'] ?? null;
                                        $rank = $i + 4;
                                        $isMe = (int)($rp->id ?? 0) === (int)$meId;
                                        $av   = $avatarOf($rp);
                                    @endphp
                                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-[#564D4A]/6 bg-white hover:bg-[#F7F4F3] transition p-3 {{ $isMe ? 'ring-2 ring-[#5B2333]/20' : '' }}">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-8 h-8 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 flex items-center justify-center shrink-0">
                                                <span class="text-xs font-black text-[#564D4A]/60">#{{ $rank }}</span>
                                            </div>
                                            <div class="w-8 h-8 rounded-full overflow-hidden bg-white border border-[#564D4A]/6 shrink-0">
                                                @if($av)
                                                    <img src="{{ $av }}" class="w-full h-full object-cover" alt="">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                                        <span class="text-[#564D4A] font-black text-xs">{{ strtoupper(mb_substr($rp->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-extrabold text-[#564D4A] truncate">{{ $rp->name }}</p>
                                                @if($isMe)
                                                    <span class="text-[10px] font-bold text-[#5B2333]">JIJ</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($rankByAttempts)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-xs font-extrabold text-[#564D4A] shrink-0">
                                                <i class="fa-solid fa-hand-pointer text-[#5B2333]"></i>
                                                {{ $bestAttempts }} zetten
                                            </span>
                                        @elseif($rankByReaction)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-xs font-extrabold text-[#564D4A] shrink-0">
                                                <i class="fa-solid fa-bolt text-[#5B2333]"></i>
                                                {{ $ms }}ms
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-xs font-extrabold text-[#564D4A] shrink-0">
                                                <i class="fa-solid fa-stopwatch text-[#5B2333]"></i>
                                                {{ $fmtMs($ms) }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>

    </div>
</x-layouts.dashboard>