{{-- resources/views/games/find-the-emoji.blade.php --}}
<x-layouts.dashboard :title="'Vind de Emoji'" active="daily">
    @php
        $isSolved = (bool)($run->solved ?? false);

        $startMs = (int)($state['started_ms'] ?? (
            $run->started_at ? $run->started_at->getTimestampMs() : now()->getTimestampMs()
        ));

        $fmtMs = function ($ms) {
            if ($ms === null) return '--:--';
            $sec = (int) round($ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            return $mm . ':' . $ss;
        };

        $finalTime = $isSolved ? $fmtMs($run->duration_ms) : null;

        // server fallback rows (existing HTML)
        $lbRows = collect($topTimes ?? collect())->values()->all();
        $meId = (int) auth()->id();

        $fteInit = [
            'me_id' => $meId,
            'scope' => (string)($scope ?? 'global'),

            'puzzle' => [
                'number' => (int)($puzzle['number'] ?? 0),
                'date' => (string)($puzzle['date'] ?? date('Y-m-d')),
                'label' => (string)($puzzle['label'] ?? 'Vind de Emoji'),
                'render_type' => (string)($puzzle['render_type'] ?? 'emoji'),
                'base' => (string)($puzzle['base'] ?? '😄'),
                'target' => (string)($puzzle['target'] ?? '😃'),
                'count' => (int)($puzzle['count'] ?? 300),
                'target_index' => (int)($puzzle['target_index'] ?? 0),
                'size' => (int)($puzzle['size'] ?? 30),
                'speed' => (float)($puzzle['speed'] ?? 1.35),
                'noise' => (float)($puzzle['noise'] ?? 0.45),
                'layout_seed' => (int)($puzzle['layout_seed'] ?? 1),
            ],
            'run' => [
                'solved' => $isSolved,
                'failed' => false,
                'started_ms' => $startMs,
                'final_time' => $finalTime,
            ],
            'leaderboard' => [
                // server fallback -> mapped to same shape as JSON response
                'rows' => collect($lbRows)->map(function ($row) {
                    // $row is array from controller mapping (duration_ms,time,user...)
                    return $row;
                })->values()->all(),
                'my_rank' => $myRank ?? null,
            ],
            'routes' => [
                'solve' => route('games.findtheemoji.solve'),
            ],
            'csrf' => csrf_token(),
        ];
    @endphp

    <style>[x-cloak]{display:none!important;}</style>

    <script>
        window.__FTE_INIT__ = @json($fteInit);
    </script>
    <script>
        window.__gameFinished = @json($isSolved);
        window.__gameStarted  = false;
        window.addEventListener('beforeunload', function () {
            if (window.__gameFinished || !window.__gameStarted) return;
            navigator.sendBeacon(
                '{{ route("games.abandon") }}',
                new Blob([JSON.stringify({ game_key: 'find-the-emoji', _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );
        });
    </script>

    <div x-data="findTheEmoji(window.__FTE_INIT__)" x-init="init()" class="flex flex-col gap-8 max-w-3xl mx-auto relative overflow-hidden">

        {{-- HERO --}}
        <div class="relative z-[1] overflow-hidden rounded-2xl border border-[#564D4A]/10 bg-[#5B2333]">
            <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-20" alt="">
            <div class="absolute inset-0 bg-gradient-to-r from-[#5B2333]/95 via-[#5B2333]/80 to-transparent"></div>

            <div class="relative p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 text-white text-xs font-semibold w-fit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Dagelijks spel
                        </div>

                        <h1 class="mt-3 text-[1.5rem] md:text-[1.8rem] font-black text-white tracking-tight leading-tight">
                            <template x-if="!isSolved">
                                <span>Vind de Emoji <span class="text-white/70">#<span x-text="puzzle.number"></span></span></span>
                            </template>
                            <template x-if="isSolved"><span>Goed zo! Je hebt het gevonden 🎉</span></template>
                        </h1>

                        <p class="mt-2 text-xs md:text-sm font-semibold text-white/80 leading-[1.3] italic">
                            <template x-if="!isSolved">
                                <span>Er zijn <span class="font-black text-white" x-text="puzzle.count"></span> stuiterende icoontjes. Eentje is anders. Klik erop.</span>
                            </template>
                            <template x-if="isSolved">
                                <span>Opgeslagen als resultaat van vandaag. Kom morgen terug voor een nieuwe.</span>
                            </template>
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span x-show="!isSolved" x-cloak class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 text-white text-xs font-semibold">
                            <i class="fa-solid fa-stopwatch"></i>
                            <span x-text="timerText">00:00</span>
                        </span>

                        <a href="{{ route('dashboard.daily') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-[#5B2333] text-xs font-semibold hover:bg-white/90 transition">
                            <i class="fa-solid fa-arrow-left"></i>
                            Terug
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- GAME CARD --}}
        <div x-ref="gameCard" class="relative z-[1] w-full bg-white rounded-2xl p-8 border border-[#564D4A]/10">

            {{-- SOLVED --}}
            <div x-show="isSolved" x-cloak>
                <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#8E936D] border border-[#8E936D]/10 flex items-center justify-center">
                            <i class="fa-solid fa-check text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-extrabold text-[#564D4A] leading-tight">Voltooid</p>
                            <p class="text-xs font-semibold text-[#564D4A]/55 leading-[1.3]">Je klikte op de juiste.</p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4">
                        <div class="rounded-2xl border border-[#564D4A]/10 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Tijd</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]" x-text="finalTime || timerText">00:00</p>
                            <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">Opgeslagen als resultaat van vandaag.</p>
                        </div>
                    </div>

                    {{-- ✅ Leaderboard --}}
                    <div class="mt-6 rounded-2xl border border-[#564D4A]/10 bg-white p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <h2 class="text-[1.05rem] font-extrabold text-[#564D4A]">Scorebord</h2>
                                <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                                    Snelste tijden van vandaag ({{ $scope === 'friends' ? 'Vrienden' : 'Wereldwijd' }}).
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                @foreach(($tabs ?? []) as $t)
                                    @php
                                        $active = ($scope ?? 'global') === $t['key'];
                                        $href = request()->fullUrlWithQuery(['scope' => $t['key']]);
                                    @endphp

                                    <a href="{{ $href }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold transition
                                              {{ $active ? 'bg-[#5B2333]/10 text-[#5B2333]' : 'bg-[#F7F4F3] text-[#564D4A] hover:bg-[#F7F4F3]/70' }}">
                                        <i class="{{ $t['icon'] }} text-[13px]"></i>
                                        {{ $t['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Server fallback --}}
                        <div x-show="!leaderboardReady" class="mt-4 grid gap-2">
                            @forelse(($topTimes ?? collect()) as $i => $row)
                                @php
                                    $rank = $i + 1;
                                    $p = (object)($row['user'] ?? []);
                                    $isMe = (int)($p->id ?? 0) === (int)auth()->id();
                                    $av = $row['user']['profile_picture_url'] ?? null;
                                    $time = $row['time'] ?? '--:--';
                                @endphp

                                <div class="flex items-center justify-between gap-4 rounded-2xl border border-[#564D4A]/10 bg-white hover:bg-[#F7F4F3] transition p-4 {{ $isMe ? 'ring-2 ring-[#5B2333]/20' : '' }}">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/10 flex items-center justify-center shrink-0">
                                            <span class="text-xs font-black text-[#564D4A]/60">#{{ $rank }}</span>
                                        </div>

                                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-white border border-[#564D4A]/10 shrink-0">
                                            @if($av)
                                                <img src="{{ $av }}" class="w-full h-full object-cover" alt="">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                                    <span class="text-[#564D4A] font-black text-sm">{{ strtoupper(mb_substr((string)($p->name ?? '?'), 0, 1)) }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-extrabold text-[#564D4A] truncate">{{ $p->name ?? 'Unknown' }}</p>
                                                @if($isMe)
                                                    <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">YOU</span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] font-semibold text-[#564D4A]/55">
                                                Level {{ (int)($p->level ?? 1) }}
                                            </p>
                                        </div>
                                    </div>

                                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/10 text-xs font-extrabold text-[#564D4A] shrink-0">
                                        <i class="fa-solid fa-stopwatch text-[#5B2333]"></i>
                                        {{ $time }}
                                    </span>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                                    <p class="text-sm font-semibold text-[#564D4A]/60">Nog geen tijden.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Alpine live list --}}
                        <div x-show="leaderboardReady" x-cloak class="mt-4 grid gap-2">
                            <template x-if="leaderboardRows.length === 0">
                                <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                                    <p class="text-sm font-semibold text-[#564D4A]/60">Nog geen tijden.</p>
                                </div>
                            </template>

                            <template x-for="(row, idx) in leaderboardRows" :key="row.user.id + ':' + row.duration_ms">
                                <div class="flex items-center justify-between gap-4 rounded-2xl border border-[#564D4A]/10 bg-white transition p-4"
                                     :class="(parseInt(row.user.id) === meId) ? 'ring-2 ring-[#5B2333]/20' : ''">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/10 flex items-center justify-center shrink-0">
                                            <span class="text-xs font-black text-[#564D4A]/60">#<span x-text="idx + 1"></span></span>
                                        </div>

                                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-white border border-[#564D4A]/10 shrink-0">
                                            <template x-if="row.user.profile_picture_url">
                                                <img :src="row.user.profile_picture_url" class="w-full h-full object-cover" alt="">
                                            </template>
                                            <template x-if="!row.user.profile_picture_url">
                                                <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                                    <span class="text-[#564D4A] font-black text-sm" x-text="(row.user.name || '?').slice(0,1).toUpperCase()"></span>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-extrabold text-[#564D4A] truncate" x-text="row.user.name"></p>
                                                <template x-if="parseInt(row.user.id) === meId">
                                                    <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">YOU</span>
                                                </template>
                                            </div>
                                            <p class="text-[11px] font-semibold text-[#564D4A]/55">
                                                Level <span x-text="row.user.level || 1"></span>
                                            </p>
                                        </div>
                                    </div>

                                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/10 text-xs font-extrabold text-[#564D4A] shrink-0">
                                        <i class="fa-solid fa-stopwatch text-[#5B2333]"></i>
                                        <span x-text="row.time"></span>
                                    </span>
                                </div>
                            </template>

                            <template x-if="myRank && myRank > leaderboardRows.length">
                                <div class="mt-2 rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-4 flex items-center justify-between">
                                    <p class="text-xs font-semibold text-[#564D4A]/60">Jouw positie</p>
                                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-[#564D4A]/10 text-xs font-extrabold text-[#564D4A]">
                                        #<span x-text="myRank"></span>
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PLAYING --}}
            <div x-show="!isSolved" x-cloak>
                <div x-show="!started" x-cloak class="text-center py-10">
                    <p class="text-xl font-extrabold text-[#564D4A]">Ben je er klaar voor?</p>
                    <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">Vind de emoji die er anders uitziet dan alle andere.</p>
                    <button @click="startGame()"
                        class="mt-6 inline-flex items-center gap-2 px-8 py-3 rounded-2xl bg-[#5B2333] text-white text-sm font-bold hover:bg-[#5B2333]/90 transition active:scale-[0.98]">
                        <i class="fa-solid fa-play"></i>
                        Spel starten
                    </button>
                </div>

                <div x-show="started" x-cloak>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Vind de vreemde eend</h2>
                        <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                            Alles stuitert als TV-ruis. Eén icoontje is anders — klik erop.
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold">
                        <i class="fa-solid fa-bullseye"></i>
                        <span>Je zoekt naar:</span>

                        {{-- emoji target --}}
                        <template x-if="puzzle.render_type !== 'img'">
                            <span class="text-[16px] leading-none" x-text="puzzle.target"></span>
                        </template>

                        {{-- image target (optioneel, voor later) --}}
                        <template x-if="puzzle.render_type === 'img'">
                            <img :src="puzzle.target" class="w-5 h-5 object-contain" alt="target">
                        </template>
                    </span>
                </div>

                <div class="mt-6 rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-4">
                    <div
                        x-ref="arena"
                        class="relative w-full h-[420px] md:h-[520px] rounded-2xl overflow-hidden bg-white border border-[#564D4A]/10"
                    >
                        <canvas x-ref="playCanvas" class="absolute inset-0 w-full h-full block"></canvas>

                        <div x-show="wrongPing" x-cloak
                             class="absolute left-4 bottom-4 px-3 py-2 rounded-xl bg-[#CE796B] text-white text-xs font-bold shadow-lg">
                            Verkeerde 😅
                        </div>
                    </div>

                    <p class="mt-3 text-[11px] font-semibold text-[#564D4A]/55">
                        Tip: zoom je browser naar 100% voor de nauwkeurigste klikdetectie.
                    </p>
                </div>
                </div>
            </div>
        </div>

        <div x-show="isSolved || isFailed" x-cloak class="relative z-[1]">
            <x-game-streak :streak="$streak" />
        </div>
    </div>

    <script>
        function findTheEmoji(init) {
            return {
                meId: parseInt(init.me_id || '0', 10),
                scope: init.scope || 'global',

                puzzle: init.puzzle,
                isSolved: !!init.run.solved,
                isFailed: !!(init.run && init.run.failed), // ✅ future-proof
                started: !!(init.run.solved || init.run.failed),
                startedMs: parseInt(init.run.started_ms || '0', 10),
                finalTime: init.run.final_time || null,

                timerText: '00:00',
                _timerId: null,

                // leaderboard (live)
                leaderboardReady: false,
                leaderboardRows: (init.leaderboard && Array.isArray(init.leaderboard.rows)) ? init.leaderboard.rows : [],
                myRank: init.leaderboard ? init.leaderboard.my_rank : null,

                // playfield
                _rafId: null,
                canvas: null,
                ctx: null,
                dpr: 1,
                W: 0,
                H: 0,

                // entities
                items: [],
                wrongPing: false,
                _wrongPingT: null,

                // assets if render_type = img
                baseImg: null,
                targetImg: null,
                _assetsReady: false,

                sessionSeed: 0,

                makeSessionSeed() {
                    try {
                        const a = new Uint32Array(1);
                        window.crypto.getRandomValues(a);
                        return a[0] >>> 0;
                    } catch (e) {
                        return (Math.floor(Math.random() * 0xFFFFFFFF) >>> 0);
                    }
                },

                async init() {
                    this.sessionSeed = this.makeSessionSeed();
                    this.leaderboardReady = true;
                    this.$watch('started',  v => { if (v && !window.__gameFinished) window.__gameStarted = true; });
                    this.$watch('isSolved', v => { if (v) window.__gameFinished = true; });
                    this.$watch('isFailed', v => { if (v) window.__gameFinished = true; });
                    if (this.isSolved) {
                        this.startTimer();
                        this.stopTimer();
                        this.stopLoop();
                    }
                },

                async startGame() {
                    this.started = true;
                    this.startedMs = Date.now();
                    fetch('{{ route("games.mark-started") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ game_key: 'find-the-emoji' })
                    });
                    this.startTimer();

                    this.canvas = this.$refs.playCanvas;
                    this.ctx = this.canvas.getContext('2d');
                    this.setupCanvasSize();
                    await this.waitForArenaSize();
                    this.buildItems();
                    this.hookClicks();

                    if (this.puzzle.render_type === 'img') {
                        await this.preloadImages();
                        this._assetsReady = true;
                        this.loop();
                    } else {
                        this._assetsReady = true;
                        this.loop();
                    }
                },

                // ===== Timer =====
                startTimer() {
                    this.stopTimer();
                    const pad = (n) => String(n).padStart(2, '0');

                    if (this.isSolved) {
                        this.timerText = this.finalTime || '00:00';
                        return;
                    }

                    const tick = () => {
                        const diff = Math.max(0, Date.now() - this.startedMs);
                        const s = Math.floor(diff / 1000);
                        const m = Math.floor(s / 60);
                        const r = s % 60;
                        this.timerText = `${pad(m)}:${pad(r)}`;
                    };

                    tick();
                    this._timerId = setInterval(tick, 1000);
                },

                stopTimer() {
                    if (this._timerId) {
                        clearInterval(this._timerId);
                        this._timerId = null;
                    }
                },

                // ===== Canvas sizing =====
                setupCanvasSize() {
                    const arena = this.$refs.arena;
                    if (!arena || !this.canvas) return;

                    const resize = () => {
                        const rect = arena.getBoundingClientRect();
                        this.dpr = window.devicePixelRatio || 1;

                        this.canvas.style.width = rect.width + 'px';
                        this.canvas.style.height = rect.height + 'px';
                        this.canvas.width = Math.round(rect.width * this.dpr);
                        this.canvas.height = Math.round(rect.height * this.dpr);

                        this.W = this.canvas.width;
                        this.H = this.canvas.height;
                    };

                    resize();

                    try {
                        const ro = new ResizeObserver(resize);
                        ro.observe(arena);
                    } catch (e) {}

                    window.addEventListener('resize', resize);
                },

                waitForArenaSize() {
                    return new Promise((resolve) => {
                        const check = () => {
                            // W/H worden gezet in setupCanvasSize()->resize()
                            if (this.W > 50 && this.H > 50) return resolve();
                            requestAnimationFrame(check);
                        };
                        check();
                    });
                },

                // ===== Deterministic RNG (mulberry32) =====
                rng(seed) {
                    let a = seed >>> 0;
                    return function () {
                        a |= 0;
                        a = (a + 0x6D2B79F5) | 0;
                        let t = Math.imul(a ^ (a >>> 15), 1 | a);
                        t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
                        return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
                    };
                },

                buildItems() {
                    const baseSeed = (this.puzzle.layout_seed || 1) >>> 0;
                    const mixedSeed = (baseSeed ^ (this.sessionSeed || 0)) >>> 0; // ✅ anders bij elke reload
                    const rand = this.rng(mixedSeed);

                    const count = parseInt(this.puzzle.count || 300, 10);
                    const sizePx = parseInt(this.puzzle.size || 30, 10);
                    const r = (sizePx * 0.55) * this.dpr;

                    // ✅ 1 vaste snelheid voor iedereen (in “px per frame @60fps”-gevoel)
                    const speed = (parseFloat(this.puzzle.speed || 1.35)) * this.dpr;

                    this.items = [];

                    for (let i = 0; i < count; i++) {
                        const x = r + rand() * Math.max(1, (this.W - r * 2));
                        const y = r + rand() * Math.max(1, (this.H - r * 2));

                        // ✅ random richting, maar altijd exact dezelfde snelheid
                        const ang = rand() * Math.PI * 2;
                        const vx = Math.cos(ang) * speed;
                        const vy = Math.sin(ang) * speed;

                        this.items.push({
                            i,
                            x, y,
                            vx, vy,
                            r,
                            speed, // ✅ onthoud vaste snelheid per item
                            isTarget: i === parseInt(this.puzzle.target_index || 0, 10),
                        });
                    }
                },

                preloadImages() {
                    const load = (src) => new Promise((resolve, reject) => {
                        const img = new Image();
                        img.onload = () => resolve(img);
                        img.onerror = reject;
                        img.src = src;
                    });

                    return Promise.all([
                        load(this.puzzle.base),
                        load(this.puzzle.target),
                    ]).then(([b, t]) => {
                        this.baseImg = b;
                        this.targetImg = t;
                    }).catch(() => {
                        this.puzzle.render_type = 'emoji';
                        this._assetsReady = true;
                    });
                },

                hookClicks() {
                    const arena = this.$refs.arena;
                    if (!arena) return;

                    arena.addEventListener('click', (e) => {
                        if (this.isSolved) return;

                        const rect = arena.getBoundingClientRect();
                        const cx = (e.clientX - rect.left) * this.dpr;
                        const cy = (e.clientY - rect.top) * this.dpr;

                        for (const it of this.items) {
                            const dx = cx - it.x;
                            const dy = cy - it.y;
                            if ((dx * dx + dy * dy) <= (it.r * it.r)) {
                                if (it.isTarget) {
                                    this.solve();
                                } else {
                                    this.pingWrong();
                                }
                                return;
                            }
                        }
                    });
                },

                pingWrong() {
                    this.wrongPing = true;
                    clearTimeout(this._wrongPingT);
                    this._wrongPingT = setTimeout(() => (this.wrongPing = false), 650);
                },

                // ===== Main loop =====
                loop() {
                    this.stopLoop();
                    const noise = (parseFloat(this.puzzle.noise || 0.45)) * this.dpr;
                    const sizePx = parseInt(this.puzzle.size || 30, 10);
                    const fontPx = Math.round(sizePx * this.dpr);

                    let lastTs = performance.now();

                    const draw = (ts) => {
                        if (!this._assetsReady) return;

                        // ✅ dt factor: 1.0 ≈ 60fps, zodat snelheid niet “anders voelt” per monitor/fps
                        const dt = Math.min(40, Math.max(8, ts - lastTs)) / 16.6667;
                        lastTs = ts;

                        this.ctx.clearRect(0, 0, this.W, this.H);

                        this.ctx.save();
                        this.ctx.fillStyle = 'rgba(86,77,74,0.03)';
                        this.ctx.fillRect(0, 0, this.W, this.H);
                        this.ctx.restore();

                        // (optioneel performance: font maar 1x per frame zetten)
                        if (this.puzzle.render_type !== 'img') {
                            this.ctx.font = `${fontPx}px "Apple Color Emoji","Segoe UI Emoji","Noto Color Emoji",sans-serif`;
                            this.ctx.textAlign = 'center';
                            this.ctx.textBaseline = 'middle';
                            this.ctx.globalAlpha = 1;
                            this.ctx.fillStyle = '#111827';
                        }

                        for (const it of this.items) {
                            // ✅ verplaatsen met dt
                            it.x += it.vx * dt;
                            it.y += it.vy * dt;

                            // ✅ bounces (alleen richting omkeren)
                            if (it.x < it.r) { it.x = it.r; it.vx *= -1; }
                            if (it.x > this.W - it.r) { it.x = this.W - it.r; it.vx *= -1; }
                            if (it.y < it.r) { it.y = it.r; it.vy *= -1; }
                            if (it.y > this.H - it.r) { it.y = this.H - it.r; it.vy *= -1; }

                            // ✅ FIX: forceer exact dezelfde snelheid (nooit sneller/slomer worden)
                            const v = Math.hypot(it.vx, it.vy) || 1;
                            it.vx = (it.vx / v) * it.speed;
                            it.vy = (it.vy / v) * it.speed;

                            const jx = (Math.random() * 2 - 1) * noise;
                            const jy = (Math.random() * 2 - 1) * noise;

                            if (this.puzzle.render_type === 'img' && this.baseImg && this.targetImg) {
                                const img = it.isTarget ? this.targetImg : this.baseImg;
                                const s = fontPx;
                                this.ctx.drawImage(img, (it.x + jx) - s / 2, (it.y + jy) - s / 2, s, s);
                            } else {
                                const ch = it.isTarget ? this.puzzle.target : this.puzzle.base;
                                this.ctx.fillText(ch, it.x + jx, it.y + jy);
                            }
                        }

                        this._rafId = requestAnimationFrame(draw);
                    };

                    this._rafId = requestAnimationFrame(draw);
                },

                stopLoop() {
                    if (this._rafId) {
                        cancelAnimationFrame(this._rafId);
                        this._rafId = null;
                    }
                },

                async solve() {
                    try {
                        const url = init.routes.solve + '?scope=' + encodeURIComponent(this.scope || 'global');

                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': init.csrf,
                            },
                            body: JSON.stringify({ solved: true })
                        });

                        const data = await res.json();
                        if (!data?.ok) return;

                        this.isSolved = true;
                        this.finalTime = data.final_time || this.finalTime;
                        this.timerText = this.finalTime || this.timerText;

                        // ✅ update leaderboard without reload
                        if (data?.leaderboard?.rows) {
                            this.leaderboardRows = data.leaderboard.rows;
                            this.myRank = data.leaderboard.my_rank ?? this.myRank;
                            this.scope = data.leaderboard.scope || this.scope;
                        }

                        this.stopTimer();
                        this.stopLoop();

                        // ✅ streak UI live updaten
                        if (data?.streak) {
                            window.dispatchEvent(new CustomEvent('cf:streak', { detail: data.streak }));
                        }

                        // confetti from layout canvas (works with your existing setup)
                        if (window.confetti && document.getElementById('mainConfettiCanvas')) {
                            const cannon = window.confetti.create(document.getElementById('mainConfettiCanvas'), { useWorker: true, resize: true });

                            cannon({
                                particleCount: 110,
                                angle: 60,
                                spread: 55,
                                startVelocity: 55,
                                gravity: 1.0,
                                ticks: 320,
                                origin: { x: 0.02, y: 1 },
                            });

                            cannon({
                                particleCount: 110,
                                angle: 120,
                                spread: 55,
                                startVelocity: 55,
                                gravity: 1.0,
                                ticks: 320,
                                origin: { x: 0.98, y: 1 },
                            });
                        }
                    } catch (e) {}
                },
            }
        }
    </script>
</x-layouts.dashboard>