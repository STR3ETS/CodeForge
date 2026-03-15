{{-- resources/views/dashboard/index.blade.php --}}
<x-layouts.dashboard :title="'Dashboard'" active="dashboard">
    @php
        $u = auth()->user();
        $avatarUrl = $u->profile_picture ? asset('storage/' . $u->profile_picture) : null;
        $xpMeta = $u?->levelMeta() ?? ['xp' => 0, 'level' => 1, 'nextXp' => 5000, 'percent' => 0, 'inLevel' => 0, 'nextInLevel' => 5000];

        $limit = $limit ?? (($u->plan === 'pro') ? null : 5);
        $done = (int) ($u->daily_challenges_done ?? 0);
        $remaining = $remaining ?? (is_null($limit) ? null : max(0, $limit - $done));

        $quests = collect($quests ?? [])->values();
        $questsDoneCount = $quests->filter(fn($q) => !empty($q['is_done']))->count();
        $questsTotalCount = $quests->count();
        $questsRemaining = max(0, $questsTotalCount - $questsDoneCount);

        $today = now()->toDateString();

        $accentMap = [
            'word-forge'     => ['bg' => 'bg-[#D6E4F0]', 'text' => 'text-[#4a7fa5]'],
            'find-the-emoji' => ['bg' => 'bg-[#FBE2D8]', 'text' => 'text-[#c0705a]'],
            'sequence-rush'  => ['bg' => 'bg-[#D9EAD3]', 'text' => 'text-[#5a8a4e]'],
            'flag-guess'     => ['bg' => 'bg-[#FFF3CD]', 'text' => 'text-[#9a7a20]'],
            'block-drop'     => ['bg' => 'bg-[#E8D5F0]', 'text' => 'text-[#7a4fa0]'],
            'sudoku'         => ['bg' => 'bg-[#D0EAE8]', 'text' => 'text-[#3a8a85]'],
            'memory-grid'    => ['bg' => 'bg-[#F3E8F9]', 'text' => 'text-[#7a4fa0]'],
            'color-match'    => ['bg' => 'bg-[#FFE4E6]', 'text' => 'text-[#be123c]'],
            'reaction-time'  => ['bg' => 'bg-[#FEF9C3]', 'text' => 'text-[#a16207]'],
            'maze-runner'    => ['bg' => 'bg-[#DBEAFE]', 'text' => 'text-[#1d4ed8]'],
            'color-sort'     => ['bg' => 'bg-[#FEF3C7]', 'text' => 'text-[#b45309]'],
            'math-rush'      => ['bg' => 'bg-[#DBEAFE]', 'text' => 'text-[#1d4ed8]'],
            'geo-guess'      => ['bg' => 'bg-[#CCFBF1]', 'text' => 'text-[#0D9488]'],
        ];

        $games = [
            ['key' => 'word-forge',    'name' => 'Woord Raden',     'icon' => 'fa-solid fa-font',                'color' => '#5B2333', 'route' => 'games.wordforge'],
            ['key' => 'find-the-emoji','name' => 'Vind de Emoji',   'icon' => 'fa-solid fa-face-smile-wink',     'color' => '#E8A838', 'route' => 'games.findtheemoji'],
            ['key' => 'sequence-rush', 'name' => 'Reeks Raden',       'icon' => 'fa-solid fa-arrow-up-1-9',        'color' => '#3B82F6', 'route' => 'games.sequence'],
            ['key' => 'flag-guess',    'name' => 'Vlaggen Quiz',     'icon' => 'fa-solid fa-flag',                'color' => '#10B981', 'route' => 'games.flagguess'],
            ['key' => 'block-drop',    'name' => 'Blok Drop',        'icon' => 'fa-solid fa-cube',                'color' => '#8B5CF6', 'route' => 'games.blockdrop'],
            ['key' => 'sudoku',        'name' => 'Mini Sudoku',      'icon' => 'fa-solid fa-table-cells-large',   'color' => '#3a8a85', 'route' => 'games.sudoku'],
            ['key' => 'memory-grid',   'name' => 'Geheugen Grid',    'icon' => 'fa-solid fa-brain',               'color' => '#7a4fa0', 'route' => 'games.memorygrid'],
            ['key' => 'color-match',   'name' => 'Kleuren Match',    'icon' => 'fa-solid fa-palette',             'color' => '#be123c', 'route' => 'games.colormatch'],
            ['key' => 'reaction-time', 'name' => 'Reactietijd',      'icon' => 'fa-solid fa-bolt',                'color' => '#EAB308', 'route' => 'games.reactiontime'],
            ['key' => 'maze-runner',   'name' => 'Doolhof Renner',   'icon' => 'fa-solid fa-route',                'color' => '#1d4ed8', 'route' => 'games.mazerunner'],
            ['key' => 'color-sort',    'name' => 'Kleuren Sorteer',  'icon' => 'fa-solid fa-layer-group',          'color' => '#b45309', 'route' => 'games.colorsort'],
            ['key' => 'math-rush',    'name' => 'Reken Rush',       'icon' => 'fa-solid fa-calculator',           'color' => '#1D4ED8', 'route' => 'games.mathrush'],
            ['key' => 'geo-guess',    'name' => 'Geo Gok',          'icon' => 'fa-solid fa-earth-europe',         'color' => '#0D9488', 'route' => 'games.geoguess'],
        ];

        $runs = \App\Models\DailyGameRun::where('user_id', $u->id)
            ->where('puzzle_date', $today)
            ->get()
            ->keyBy('game_key');

        $fmtMs = function ($ms) {
            if (!$ms) return null;
            $sec = (int) round($ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            return $mm . ':' . $ss;
        };

        foreach ($games as &$g) {
            $run = $runs->get($g['key']);
            if (!$run || (!$run->solved && !$run->finished_at && $run->attempts <= 0)) {
                $g['status'] = 'open';
                $g['label'] = 'Nog niet gespeeld';
                $g['status_time'] = null;
            } elseif ($run->solved) {
                $g['status'] = 'done';
                $g['label'] = 'Voltooid';
                if ($g['key'] === 'geo-guess') {
                    $dm = (int) $run->duration_ms;
                    $g['status_time'] = $dm < 1000 ? $dm . ' m' : number_format($dm / 1000, 1, ',', '.') . ' km';
                } else {
                    $g['status_time'] = $fmtMs($run->duration_ms);
                }
            } else {
                $g['status'] = 'failed';
                $g['label'] = 'Niet gehaald';
                $g['status_time'] = null;
            }
        }
        unset($g);

        $solvedCount = collect($games)->where('status', 'done')->count();
        $playedCount = collect($games)->whereIn('status', ['done', 'failed'])->count();
        $isFree = ($u->plan ?? 'free') !== 'pro';
        $isPro = !$isFree;
        $limitReached = $isFree && !is_null($limit) && $done >= $limit;
        $pendingRequests = \App\Models\Friendship::where('friend_id', $u->id)->where('status', 'pending')->count();

        $totalGamesPlayed = \App\Models\DailyGameRun::where('user_id', $u->id)->whereNotNull('finished_at')->count();
        $bestStreak = \App\Models\DailyGameStreak::where('user_id', $u->id)->max('best_streak') ?? 0;
        $currentStreak = (int) $u->streak;
        $friendCount = $u->friends()->count();

        // Subscription info for Pro users
        $subscription = $isPro ? $u->subscription('pro') : null;
        $subEndsAt = $subscription?->ends_at;
        $subCancelled = $subscription?->canceled() ?? false;
    @endphp

    <div class="flex flex-col gap-10" x-data="{ showUpgrade: false }">

        {{-- WELCOME --}}
        <div>
            <h1 class="text-2xl font-black text-[#564D4A] tracking-tight">
                Welkom terug, {{ $u->name }}
            </h1>
            <p class="mt-1 text-sm text-[#564D4A]/40 font-medium">
                @if($solvedCount === count($games))
                    Je hebt alle spellen van vandaag afgerond!
                @elseif($playedCount > 0)
                    Je hebt {{ $playedCount }} van de {{ count($games) }} spellen gespeeld vandaag.
                @else
                    Klaar voor je dagelijkse uitdagingen?
                @endif
            </p>
        </div>

        {{-- PRO STATUS or UPGRADE BANNER --}}
        @if($isPro)
            <div class="rounded-2xl bg-white border border-[#564D4A]/6 p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-xl bg-[#5B2333]/8 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-crown text-[#5B2333]"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-black text-[#564D4A]">Pro Abonnement</p>
                                @if($subCancelled)
                                    <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-600 text-[10px] font-bold">Opgezegd</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-600 text-[10px] font-bold">Actief</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-[#564D4A]/40 font-medium mt-0.5">
                                @if($subCancelled && $subEndsAt)
                                    Toegang tot {{ $subEndsAt->format('d-m-Y') }}
                                @elseif($subscription?->created_at)
                                    Lid sinds {{ $subscription->created_at->format('d-m-Y') }}
                                @else
                                    Onbeperkt games, GIF's, cosmetics & Pro badge
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('subscription.portal') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#564D4A]/5 hover:bg-[#564D4A]/10 text-[#564D4A]/60 hover:text-[#564D4A] text-xs font-semibold transition">
                        <i class="fa-solid fa-gear text-[10px]"></i> Beheren
                    </a>
                </div>
            </div>
        @else
            {{-- Upgrade banner --}}
            <div class="rounded-2xl overflow-hidden relative">
                <div class="absolute inset-0 bg-gradient-to-br from-[#5B2333] to-[#7a3349]"></div>
                <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-10 pointer-events-none" alt="">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-28 h-28 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                <div class="relative p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-crown text-yellow-300 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-base font-black text-white leading-tight">Upgrade naar Pro</p>
                            <p class="text-[12px] text-white/50 mt-1 font-medium">
                                Onbeperkt games, GIF's, exclusieve cosmetics & Pro badge vanaf <span class="text-yellow-300 font-bold">1,99/maand</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('pages.pricing') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white hover:bg-white/90 text-[#5B2333] text-xs font-bold transition">
                            <i class="fa-solid fa-bolt text-[10px]"></i> Start met Pro
                        </a>
                        <a href="{{ route('pages.pricing') }}" class="inline-flex items-center gap-1 text-white/40 hover:text-white/70 text-[11px] font-semibold transition">
                            Meer info <i class="fa-solid fa-arrow-right text-[9px]"></i>
                        </a>
                    </div>
                </div>

                {{-- Game limit bar --}}
                <div class="relative border-t border-white/10 px-6 sm:px-8 py-3 flex items-center gap-4">
                    <p class="text-[11px] text-white/40 font-semibold shrink-0">Dagelijkse limiet</p>
                    <div class="flex-1 h-[4px] rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full rounded-full bg-white/50 transition-all" style="width: {{ min(100, ($done / max(1, $limit)) * 100) }}%"></div>
                    </div>
                    <p class="text-[11px] text-white/50 font-bold shrink-0">{{ $done }}/{{ $limit }}</p>
                </div>
            </div>
        @endif

        {{-- PROFILE COMPLETION --}}
        @php
            $profileSteps = [
                ['key' => 'avatar',  'label' => 'Profielfoto toevoegen',  'icon' => 'fa-solid fa-camera',      'xp' => 100, 'done' => !empty($u->profile_picture), 'color' => 'bg-[#5B2333]/8 text-[#5B2333]'],
                ['key' => 'banner',  'label' => 'Banner uploaden',        'icon' => 'fa-solid fa-image',       'xp' => 100, 'done' => !empty($u->profile_banner),  'color' => 'bg-purple-50 text-purple-500'],
                ['key' => 'game',    'label' => 'Eerste spel spelen',     'icon' => 'fa-solid fa-gamepad',     'xp' => 50,  'done' => $totalGamesPlayed > 0,       'color' => 'bg-orange-50 text-orange-500'],
                ['key' => 'friend',  'label' => 'Eerste vriend toevoegen','icon' => 'fa-solid fa-user-plus',   'xp' => 75,  'done' => $friendCount > 0,            'color' => 'bg-blue-50 text-blue-500'],
                ['key' => 'streak',  'label' => '3-daagse streak halen',  'icon' => 'fa-solid fa-fire',        'xp' => 150, 'done' => $currentStreak >= 3 || $bestStreak >= 3, 'color' => 'bg-yellow-50 text-yellow-600'],
            ];
            $profileDone = collect($profileSteps)->where('done', true)->count();
            $profileTotal = count($profileSteps);
            $profilePercent = (int) round(($profileDone / max(1, $profileTotal)) * 100);
            $allProfileDone = $profileDone === $profileTotal;
        @endphp

        @if(!$allProfileDone)
            <div class="rounded-2xl bg-white border border-[#564D4A]/6 p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-base font-black text-[#564D4A] tracking-tight">Maak je profiel compleet</h2>
                        <p class="mt-1 text-[11px] font-medium text-[#564D4A]/40">
                            Voltooi stappen en verdien bonus XP
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold shrink-0">
                        {{ $profileDone }}/{{ $profileTotal }}
                    </span>
                </div>

                {{-- Progress bar --}}
                <div class="mt-4 w-full h-[6px] rounded-full bg-[#564D4A]/8 overflow-hidden">
                    <div class="h-full rounded-full bg-[#5B2333] transition-all" style="width: {{ $profilePercent }}%"></div>
                </div>

                {{-- Steps --}}
                <div class="mt-4 grid gap-2">
                    @foreach($profileSteps as $step)
                        @php
                            $stepRoute = match($step['key']) {
                                'avatar', 'banner' => route('profile'),
                                'game' => route('dashboard.daily'),
                                'friend' => route('friends.index'),
                                'streak' => route('dashboard.daily'),
                                default => '#',
                            };
                        @endphp

                        @if($step['done'])
                            <div class="flex items-center justify-between gap-3 p-3.5 rounded-xl bg-[#F7F4F3]/60">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl bg-green-500/10 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-check text-green-500 text-sm"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-[#564D4A]/40 line-through">{{ $step['label'] }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-green-500/10 text-green-600 text-[10px] font-bold shrink-0">
                                    <i class="fa-solid fa-check text-[8px]"></i> +{{ $step['xp'] }} XP
                                </span>
                            </div>
                        @else
                            <a href="{{ $stepRoute }}" class="flex items-center justify-between gap-3 p-3.5 rounded-xl border border-[#564D4A]/6 bg-white hover:border-[#5B2333]/20 hover:shadow-sm hover:shadow-[#5B2333]/5 transition group">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl {{ $step['color'] }} flex items-center justify-center shrink-0">
                                        <i class="{{ $step['icon'] }} text-sm"></i>
                                    </div>
                                    <p class="text-sm font-bold text-[#564D4A] group-hover:text-[#5B2333] transition">{{ $step['label'] }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#5B2333]/8 text-[#5B2333] text-[10px] font-bold shrink-0">
                                    <i class="fa-solid fa-bolt text-[8px]"></i> +{{ $step['xp'] }} XP
                                </span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- TODAY'S GAMES --}}
        <div>
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-lg font-black text-[#564D4A] tracking-tight">Vandaag spelen</h2>
                    <p class="mt-1 text-xs font-medium text-[#564D4A]/40">
                        {{ $solvedCount === count($games) ? 'Alles gedaan voor vandaag!' : ($solvedCount . ' van ' . count($games) . ' afgerond') }}
                    </p>
                </div>
                <a href="{{ route('dashboard.daily') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/8 hover:bg-[#5B2333]/12 transition text-xs font-bold text-[#5B2333]">
                    <i class="fa-solid fa-bullseye-arrow text-[11px]"></i> Dagelijkse quests
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($games as $g)
                    @php
                        $isLocked = $limitReached && $g['status'] === 'open';
                        $accent = $accentMap[$g['key']] ?? ['bg' => 'bg-[#EEF1F4]', 'text' => 'text-[#564D4A]'];
                    @endphp

                    @if($isLocked)
                    <div @click="showUpgrade = true" @keydown.enter="showUpgrade = true"
                        tabindex="0" role="button"
                        class="group rounded-2xl border border-[#564D4A]/6 bg-white overflow-hidden transition cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#5B2333]/20 opacity-40 hover:opacity-55">
                    @elseif($g['status'] === 'done' || $g['status'] === 'failed')
                    <a href="{{ route($g['route']) }}"
                        class="group rounded-2xl border border-[#564D4A]/6 bg-white overflow-hidden transition hover:border-[#564D4A]/15">
                    @else
                    <a href="{{ route($g['route']) }}"
                        class="group rounded-2xl border border-[#564D4A]/6 bg-white overflow-hidden transition hover:border-[#5B2333]/30 hover:shadow-sm hover:shadow-[#5B2333]/5">
                    @endif

                        {{-- Icon area --}}
                        <div class="h-[80px] {{ $accent['bg'] }} flex items-center justify-center relative">
                            <i class="{{ $g['icon'] }} {{ $accent['text'] }} text-2xl"></i>

                            @if($g['status'] === 'done')
                                <span class="absolute top-2.5 right-2.5 w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                                    <i class="fa-solid fa-check text-white text-[9px]"></i>
                                </span>
                            @elseif($g['status'] === 'failed')
                                <span class="absolute top-2.5 right-2.5 w-6 h-6 rounded-full bg-red-500 flex items-center justify-center">
                                    <i class="fa-solid fa-xmark text-white text-[9px]"></i>
                                </span>
                            @elseif($isLocked)
                                <span class="absolute top-2.5 right-2.5 w-6 h-6 rounded-full bg-white/80 border border-[#564D4A]/10 flex items-center justify-center">
                                    <i class="fa-solid fa-lock text-[#564D4A]/40 text-[9px]"></i>
                                </span>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-4">
                            <p class="text-sm font-black text-[#564D4A]">{{ $g['name'] }}</p>
                            <p class="mt-1 text-[11px] font-medium
                                {{ $g['status'] === 'done' ? 'text-green-600' : ($g['status'] === 'failed' ? 'text-red-500' : 'text-[#564D4A]/35') }}">
                                @if($g['status'] === 'done')
                                    Opgelost @if($g['status_time']) in {{ $g['status_time'] }} @endif
                                @elseif($g['status'] === 'failed')
                                    Niet gehaald
                                @elseif($isLocked)
                                    Op slot
                                @else
                                    Speel nu
                                @endif
                            </p>
                        </div>

                    @if($isLocked)
                    </div>
                    @else
                    </a>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- QUICK LINKS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('leaderboard') }}" class="rounded-2xl border border-[#564D4A]/6 bg-white p-5 hover:border-[#5B2333]/20 hover:shadow-sm hover:shadow-[#5B2333]/5 transition group">
                <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-medal text-yellow-600 text-sm"></i>
                </div>
                <p class="text-sm font-black text-[#564D4A] group-hover:text-[#5B2333] transition">Scorebord</p>
                <p class="mt-1 text-[11px] font-medium text-[#564D4A]/40">Bekijk de ranglijst</p>
            </a>

            <a href="{{ route('friends.index') }}" class="rounded-2xl border border-[#564D4A]/6 bg-white p-5 hover:border-[#5B2333]/20 hover:shadow-sm hover:shadow-[#5B2333]/5 transition group">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center mb-3 relative">
                    <i class="fa-solid fa-user-group text-purple-500 text-sm"></i>
                    @if($pendingRequests > 0)
                        <span class="absolute -top-1 -right-1 min-w-5 h-5 px-1 inline-flex items-center justify-center rounded-full bg-[#5B2333] text-white text-[10px] font-bold">
                            {{ $pendingRequests }}
                        </span>
                    @endif
                </div>
                <p class="text-sm font-black text-[#564D4A] group-hover:text-[#5B2333] transition">Vrienden</p>
                <p class="mt-1 text-[11px] font-medium text-[#564D4A]/40">
                    {{ $pendingRequests > 0 ? $pendingRequests . ' nieuw' . ($pendingRequests !== 1 ? 'e' : '') . ' verzoek' . ($pendingRequests !== 1 ? 'en' : '') : 'Zoek & voeg toe' }}
                </p>
            </a>

            <a href="{{ route('profile') }}" class="rounded-2xl border border-[#564D4A]/6 bg-white p-5 hover:border-[#5B2333]/20 hover:shadow-sm hover:shadow-[#5B2333]/5 transition group">
                <div class="w-10 h-10 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-user text-[#5B2333] text-sm"></i>
                </div>
                <p class="text-sm font-black text-[#564D4A] group-hover:text-[#5B2333] transition">Mijn Profiel</p>
                <p class="mt-1 text-[11px] font-medium text-[#564D4A]/40">Stats, badges & instellingen</p>
            </a>

        </div>

        {{-- UPGRADE MODAL (free users) --}}
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
                        Je hebt vandaag al <span class="font-bold text-[#5B2333]">{{ $limit }} gratis spellen</span> gespeeld.
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
                            <i class="fa-solid fa-gem text-purple-500 text-sm"></i>
                            <span class="text-sm font-semibold text-[#564D4A]">Epische & Legendarische cosmetics in de Shop</span>
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

    {{-- PRO ACTIVATED MODAL --}}
    @if(session('pro_activated'))
        <div x-data="{ show: true }"
             x-show="show"
             x-cloak
             x-init="$nextTick(() => {
                 const c = document.getElementById('mainConfettiCanvas');
                 if (c) c.style.zIndex = '150';
                 if (typeof window.fireMainConfetti === 'function') {
                     window.fireMainConfetti({ gameKey: 'pro-activated', date: '{{ now()->toDateString() }}' });
                 }
             })"
             class="fixed inset-0 z-[100] flex items-center justify-center p-4"
             @keydown.escape.window="show = false; document.getElementById('mainConfettiCanvas')?.style.removeProperty('z-index');">
            <div class="absolute inset-0 bg-[#564D4A]/40" @click="show = false; document.getElementById('mainConfettiCanvas')?.style.removeProperty('z-index');"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden" @click.stop
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                {{-- Hero banner --}}
                <div class="relative h-36 sidebar-gradient flex items-center justify-center overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <img src="/assets/stacked-waves-haikei.png" class="w-full h-full object-cover" alt="">
                    </div>
                    <div class="relative flex flex-col items-center">
                        <div class="w-18 h-18 rounded-2xl bg-white/20 border border-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-crown text-yellow-300 text-3xl"></i>
                        </div>
                    </div>

                    <button @click="show = false; document.getElementById('mainConfettiCanvas')?.style.removeProperty('z-index');"
                        class="cursor-pointer absolute top-3 right-3 w-8 h-8 rounded-xl bg-white/20 hover:bg-white/35 flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark text-white text-xs"></i>
                    </button>

                    <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white text-[#5B2333] text-[10px] font-bold">
                        <i class="fa-solid fa-sparkles text-[9px]"></i> Welkom bij Pro
                    </span>
                </div>

                {{-- Content --}}
                <div class="p-7 text-center">
                    <h3 class="text-xl font-black text-[#564D4A]">Je bent nu Pro!</h3>
                    <p class="mt-2 text-sm text-[#564D4A]/50 font-medium leading-relaxed">
                        Bedankt voor je upgrade. Alle Pro-features zijn direct beschikbaar!
                    </p>

                    <div class="mt-5 space-y-2.5 text-left">
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-infinity text-[#5B2333] text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#564D4A]">Onbeperkt spellen</p>
                                <p class="text-[11px] text-[#564D4A]/40 font-medium">Geen dagelijkse limiet meer</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <div class="w-8 h-8 rounded-lg bg-[#B88B2A]/10 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-wand-magic-sparkles text-[#B88B2A] text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#564D4A]">GIF profielfoto & banner</p>
                                <p class="text-[11px] text-[#564D4A]/40 font-medium">Pas je profiel aan met animaties</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-gem text-purple-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#564D4A]">Exclusieve cosmetics</p>
                                <p class="text-[11px] text-[#564D4A]/40 font-medium">Epische & Legendarische items, animated namen & custom badges</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <div class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-badge-check text-cyan-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#564D4A]">Pro badge</p>
                                <p class="text-[11px] text-[#564D4A]/40 font-medium">Laat zien dat je Pro bent</p>
                            </div>
                        </div>
                    </div>

                    <button @click="show = false; document.getElementById('mainConfettiCanvas')?.style.removeProperty('z-index');"
                        class="mt-6 w-full py-3.5 rounded-xl bg-[#5B2333] text-white font-bold text-sm hover:bg-[#5B2333]/90 transition cursor-pointer">
                        <i class="fa-solid fa-bolt text-yellow-300 mr-2"></i> Laten we spelen!
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-layouts.dashboard>
