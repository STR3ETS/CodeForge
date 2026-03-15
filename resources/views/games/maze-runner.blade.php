{{-- resources/views/games/maze-runner.blade.php --}}
<x-layouts.dashboard :title="'Maze Runner'" active="daily">
    @php
        $isSolved = (bool)($run->solved ?? false);
        $isFailed = (bool)(!$isSolved && !empty($run->finished_at));

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
        $lbRows = collect($topTimes ?? collect())->values()->all();
        $meId = (int) auth()->id();

        $mrInit = [
            'me_id' => $meId,
            'scope' => (string)($scope ?? 'global'),
            'puzzle' => [
                'number' => (int)($puzzle['number'] ?? 0),
                'date' => (string)($puzzle['date'] ?? date('Y-m-d')),
                'size' => (int)($puzzle['size'] ?? 10),
                'grid' => $puzzle['grid'] ?? [],
                'start' => $puzzle['start'] ?? [0, 0],
                'end' => $puzzle['end'] ?? [9, 9],
            ],
            'run' => [
                'solved' => $isSolved,
                'failed' => $isFailed,
                'started_ms' => $startMs,
                'final_time' => $finalTime,
                'moves' => (int)($run->attempts ?? 0),
            ],
            'leaderboard' => [
                'rows' => collect($lbRows)->values()->all(),
                'my_rank' => $myRank ?? null,
            ],
            'routes' => [
                'solve' => route('games.mazerunner.solve'),
            ],
            'csrf' => csrf_token(),
        ];
    @endphp

    <style>
        [x-cloak]{display:none!important;}

        .maze-canvas-wrap {
            position: relative;
            max-width: 460px;
            margin: 0 auto;
            touch-action: none;
            -webkit-user-select: none;
            user-select: none;
        }
        .maze-canvas-wrap canvas {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 16px;
        }

        @keyframes maze-win {
            0% { transform: scale(1); }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }
        .anim-maze-win { animation: maze-win 0.4s ease-out; }
    </style>

    <script>
        window.__MR_INIT__ = @json($mrInit);
        window.__gameFinished = @json($isSolved || $isFailed);
        window.__gameStarted  = false;
        window.addEventListener('beforeunload', function () {
            if (window.__gameFinished || !window.__gameStarted) return;
            navigator.sendBeacon(
                '{{ route("games.abandon") }}',
                new Blob([JSON.stringify({ game_key: 'maze-runner', _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );
        });
    </script>

    <div x-data="mazeRunner(window.__MR_INIT__)" x-init="init()" class="flex flex-col gap-10 max-w-3xl mx-auto">

        {{-- HEADER --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-[1.5rem] md:text-[1.8rem] font-black text-[#564D4A] tracking-tight leading-tight">
                        <template x-if="!isSolved && !isFailed">
                            <span>Maze Runner <span class="text-[#564D4A]/40">#<span x-text="puzzle.number"></span></span></span>
                        </template>
                        <template x-if="isSolved"><span>Ontsnapt! 🏁</span></template>
                        <template x-if="isFailed"><span>Niet afgemaakt 😅</span></template>
                    </h1>
                    <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
                        <template x-if="!isSolved && !isFailed">
                            <span>Sleep door het doolhof van <strong>start</strong> naar de <strong class="text-[#22C55E]">uitgang</strong>.</span>
                        </template>
                        <template x-if="isSolved">
                            <span>Opgeslagen als resultaat van vandaag. Kom morgen terug!</span>
                        </template>
                        <template x-if="isFailed">
                            <span>Je hebt het spel verlaten. Probeer het morgen opnieuw!</span>
                        </template>
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <span x-show="started && !isSolved && !isFailed" x-cloak class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-[#564D4A]/6 bg-white text-[#564D4A] text-xs font-semibold">
                        <i class="fa-solid fa-stopwatch text-[#5B2333]"></i>
                        <span x-text="timerText">00:00</span>
                    </span>
                    <span x-show="started && !isSolved && !isFailed" x-cloak class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-[#564D4A]/6 bg-white text-[#564D4A] text-xs font-semibold">
                        <i class="fa-solid fa-shoe-prints text-[#5B2333]"></i>
                        <span x-text="moves"></span>
                    </span>
                    <a href="{{ route('dashboard.daily') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#5B2333] text-white text-xs font-semibold hover:bg-[#5B2333]/85 transition">
                        <i class="fa-solid fa-arrow-left"></i> Terug
                    </a>
                </div>
            </div>
        </div>

        {{-- GAME CARD --}}
        <div class="w-full bg-white rounded-2xl p-5 sm:p-8 border border-[#564D4A]/6">

            {{-- SOLVED STATE --}}
            <div x-show="isSolved" x-cloak>
                <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#8E936D] border border-[#8E936D]/10 flex items-center justify-center">
                            <i class="fa-solid fa-check text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-extrabold text-[#564D4A] leading-tight">Ontsnapt!</p>
                            <p class="text-xs font-semibold text-[#564D4A]/55 leading-[1.3]">Je hebt de uitweg gevonden.</p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Tijd</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]" x-text="finalTime || timerText">00:00</p>
                        </div>
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Stappen</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]" x-text="moves">0</p>
                        </div>
                    </div>

                    @include('games.partials.share-score-button', ['gameKey' => 'maze-runner'])

                    {{-- Leaderboard --}}
                    <div class="mt-6 rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <h2 class="text-[1.05rem] font-extrabold text-[#564D4A]">Scorebord</h2>
                                <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                                    Snelste tijden van vandaag.
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                @foreach(($tabs ?? []) as $t)
                                    @php $active = ($scope ?? 'global') === $t['key']; @endphp
                                    <a href="{{ request()->fullUrlWithQuery(['scope' => $t['key']]) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold transition
                                              {{ $active ? 'bg-[#5B2333]/10 text-[#5B2333]' : 'bg-[#F7F4F3] text-[#564D4A] hover:bg-[#F7F4F3]/70' }}">
                                        <i class="{{ $t['icon'] }} text-[13px]"></i>
                                        {{ $t['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4 grid gap-2">
                            <template x-for="(row, idx) in leaderboardRows" :key="row.user.id + ':' + row.duration_ms">
                                <div class="flex items-center justify-between gap-4 rounded-2xl border border-[#564D4A]/6 bg-white transition p-4"
                                     :class="(parseInt(row.user.id) === meId) ? 'ring-2 ring-[#5B2333]/20' : ''">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 flex items-center justify-center shrink-0">
                                            <span class="text-xs font-black text-[#564D4A]/60">#<span x-text="idx + 1"></span></span>
                                        </div>
                                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-white border border-[#564D4A]/6 shrink-0">
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
                                                    <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">JIJ</span>
                                                </template>
                                            </div>
                                            <p class="text-[11px] font-semibold text-[#564D4A]/55">
                                                Level <span x-text="row.user.level || 1"></span> · <span x-text="row.moves || 0"></span> stappen
                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-xs font-extrabold text-[#564D4A] shrink-0">
                                        <i class="fa-solid fa-stopwatch text-[#5B2333]"></i>
                                        <span x-text="row.time"></span>
                                    </span>
                                </div>
                            </template>
                            <template x-if="leaderboardRows.length === 0">
                                <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
                                    <p class="text-sm font-semibold text-[#564D4A]/60">Nog geen tijden.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FAILED STATE --}}
            <div x-show="isFailed" x-cloak>
                <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#CE796B] border border-[#CE796B]/10 flex items-center justify-center">
                            <i class="fa-solid fa-xmark text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-extrabold text-[#564D4A] leading-tight">Mislukt</p>
                            <p class="text-xs font-semibold text-[#564D4A]/55 leading-[1.3]">Je hebt het spel verlaten.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PLAYING STATE --}}
            <div x-show="!isSolved && !isFailed" x-cloak>

                {{-- Start screen --}}
                <div x-show="!started" x-cloak class="text-center py-10">
                    <div class="w-16 h-16 rounded-2xl bg-[#22C55E]/10 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-route text-[#22C55E] text-2xl"></i>
                    </div>
                    <p class="text-xl font-extrabold text-[#564D4A]">Vind de uitweg!</p>
                    <p class="mt-2 text-xs font-semibold text-[#564D4A]/55 max-w-md mx-auto">
                        Sleep met je vinger of muis door het doolhof van <strong>start</strong> naar de <strong class="text-[#22C55E]">groene uitgang</strong>.
                    </p>
                    <div class="mt-6">
                        <button @click="startGame()"
                            class="cursor-pointer inline-flex items-center gap-2 px-8 py-3 rounded-2xl bg-[#5B2333] text-white text-sm font-bold hover:bg-[#5B2333]/90 transition active:scale-[0.98]">
                            <i class="fa-solid fa-play"></i>
                            Spel starten
                        </button>
                    </div>
                </div>

                {{-- Game area --}}
                <div x-show="started" x-cloak>
                    <div class="maze-canvas-wrap" :class="{ 'anim-maze-win': wonAnim }"
                         x-ref="mazeWrap">
                        <canvas x-ref="mazeCanvas"></canvas>
                    </div>

                    <p class="mt-4 text-[11px] font-semibold text-[#564D4A]/40 text-center">
                        <i class="fa-solid fa-hand-pointer text-[#5B2333]/40 mr-1"></i>
                        <span class="hidden sm:inline">Sleep met je muis of gebruik <strong>pijltjestoetsen</strong> / <strong>WASD</strong></span>
                        <span class="sm:hidden">Sleep met je vinger door het doolhof</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- STREAK --}}
        <div x-show="isSolved || isFailed" x-cloak>
            <x-game-streak :streak="$streak" />
        </div>
    </div>

    <script>
        function mazeRunner(init) {
            return {
                meId: parseInt(init.me_id || '0', 10),
                scope: init.scope || 'global',
                puzzle: init.puzzle,

                isSolved: !!init.run.solved,
                isFailed: !!(init.run && init.run.failed),
                started: !!(init.run.solved || init.run.failed),
                startedMs: parseInt(init.run.started_ms || '0', 10),
                finalTime: init.run.final_time || null,
                moves: parseInt(init.run.moves || '0', 10),

                timerText: '00:00',
                _timerId: null,
                wonAnim: false,

                // Player position
                playerX: init.puzzle.start[0],
                playerY: init.puzzle.start[1],
                trail: new Set(),
                trailPath: [], // ordered list of {x, y} for drawing the line
                _dragging: false,

                // Canvas
                _canvas: null,
                _ctx: null,
                _cellPx: 0,
                _wallPx: 0,
                _offsetX: 0,
                _offsetY: 0,
                _dpr: 1,

                // Leaderboard
                leaderboardRows: (init.leaderboard && Array.isArray(init.leaderboard.rows)) ? init.leaderboard.rows : [],
                myRank: init.leaderboard ? init.leaderboard.my_rank : null,

                // Colors
                _colors: {
                    wall: '#3D3530',
                    bg: '#FFFFFF',
                    trail: 'rgba(91, 35, 51, 0.12)',
                    player: '#5B2333',
                    endBg: 'rgba(34, 197, 94, 0.18)',
                    endFlag: '#22C55E',
                    startBg: 'rgba(91, 35, 51, 0.08)',
                },

                init() {
                    this.$watch('started', v => { if (v && !window.__gameFinished) window.__gameStarted = true; });
                    this.$watch('isSolved', v => { if (v) window.__gameFinished = true; });
                    this.$watch('isFailed', v => { if (v) window.__gameFinished = true; });

                    if (this.isSolved) {
                        this.moves = init.run.moves || 0;
                    }

                    // Keyboard controls (still supported)
                    window.addEventListener('keydown', (e) => {
                        if (this.isSolved || this.isFailed || !this.started) return;
                        switch (e.key) {
                            case 'ArrowUp': case 'w': case 'W': e.preventDefault(); this.moveDir(0, -1); break;
                            case 'ArrowDown': case 's': case 'S': e.preventDefault(); this.moveDir(0, 1); break;
                            case 'ArrowLeft': case 'a': case 'A': e.preventDefault(); this.moveDir(-1, 0); break;
                            case 'ArrowRight': case 'd': case 'D': e.preventDefault(); this.moveDir(1, 0); break;
                        }
                    });
                },

                startGame() {
                    this.started = true;
                    this.startedMs = Date.now();
                    this.playerX = this.puzzle.start[0];
                    this.playerY = this.puzzle.start[1];
                    this.moves = 0;
                    this.trail = new Set();
                    this.trail.add(this.playerY + ',' + this.playerX);
                    this.trailPath = [{ x: this.playerX, y: this.playerY }];

                    fetch('{{ route("games.mark-started") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ game_key: 'maze-runner' })
                    });

                    this.startTimer();

                    // Wait for canvas to appear
                    this.$nextTick(() => {
                        this.setupCanvas();
                    });
                },

                setupCanvas() {
                    const canvas = this.$refs.mazeCanvas;
                    const wrap = this.$refs.mazeWrap;
                    if (!canvas || !wrap) return;

                    this._canvas = canvas;
                    this._ctx = canvas.getContext('2d');
                    this._dpr = window.devicePixelRatio || 1;

                    const wrapW = wrap.clientWidth;
                    const size = this.puzzle.size;

                    // Wall thickness and cell size
                    this._wallPx = Math.max(4, Math.round(wrapW * 0.014));
                    this._cellPx = Math.floor((wrapW - (size + 1) * this._wallPx) / size);
                    const totalPx = size * this._cellPx + (size + 1) * this._wallPx;

                    // Padding to center
                    this._offsetX = Math.floor((wrapW - totalPx) / 2);
                    this._offsetY = this._offsetX;

                    const canvasLogicalW = totalPx + this._offsetX * 2;
                    const canvasLogicalH = totalPx + this._offsetY * 2;

                    canvas.width = canvasLogicalW * this._dpr;
                    canvas.height = canvasLogicalH * this._dpr;
                    canvas.style.width = canvasLogicalW + 'px';
                    canvas.style.height = canvasLogicalH + 'px';
                    this._ctx.scale(this._dpr, this._dpr);

                    this.drawMaze();

                    // Mouse events
                    canvas.addEventListener('mousedown', (e) => this.onPointerDown(e));
                    canvas.addEventListener('mousemove', (e) => this.onPointerMove(e));
                    window.addEventListener('mouseup', () => { this._dragging = false; });

                    // Touch events
                    canvas.addEventListener('touchstart', (e) => {
                        e.preventDefault();
                        this.onPointerDown(e.touches[0]);
                    }, { passive: false });
                    canvas.addEventListener('touchmove', (e) => {
                        e.preventDefault();
                        this.onPointerMove(e.touches[0]);
                    }, { passive: false });
                    canvas.addEventListener('touchend', () => { this._dragging = false; });
                    canvas.addEventListener('touchcancel', () => { this._dragging = false; });

                    // Resize observer
                    const ro = new ResizeObserver(() => {
                        const newW = wrap.clientWidth;
                        if (Math.abs(newW - wrapW) > 5) {
                            this.setupCanvas();
                        }
                    });
                    ro.observe(wrap);
                },

                // Get cell coordinates from pointer position
                pointerToCell(evt) {
                    const rect = this._canvas.getBoundingClientRect();
                    const x = evt.clientX - rect.left - this._offsetX;
                    const y = evt.clientY - rect.top - this._offsetY;

                    const step = this._cellPx + this._wallPx;
                    const cx = Math.floor((x - this._wallPx) / step);
                    const cy = Math.floor((y - this._wallPx) / step);

                    if (cx < 0 || cx >= this.puzzle.size || cy < 0 || cy >= this.puzzle.size) return null;

                    // Check pointer is inside the cell area (not on a wall)
                    const cellStartX = this._wallPx + cx * step;
                    const cellStartY = this._wallPx + cy * step;
                    if (x < cellStartX || x > cellStartX + this._cellPx) return null;
                    if (y < cellStartY || y > cellStartY + this._cellPx) return null;

                    return { cx, cy };
                },

                onPointerDown(evt) {
                    if (this.isSolved || this.isFailed || !this.started) return;
                    const cell = this.pointerToCell(evt);
                    if (!cell) return;

                    // Allow starting drag from the player position or an adjacent cell
                    if (cell.cx === this.playerX && cell.cy === this.playerY) {
                        this._dragging = true;
                    } else {
                        // Try to move to the clicked cell if adjacent
                        const dx = cell.cx - this.playerX;
                        const dy = cell.cy - this.playerY;
                        if (Math.abs(dx) + Math.abs(dy) === 1) {
                            if (this.moveDir(dx, dy)) {
                                this._dragging = true;
                            }
                        }
                    }
                },

                onPointerMove(evt) {
                    if (!this._dragging || this.isSolved || this.isFailed) return;
                    const cell = this.pointerToCell(evt);
                    if (!cell) return;
                    if (cell.cx === this.playerX && cell.cy === this.playerY) return;

                    const dx = cell.cx - this.playerX;
                    const dy = cell.cy - this.playerY;

                    // Only move to adjacent cells
                    if (Math.abs(dx) + Math.abs(dy) !== 1) return;

                    this.moveDir(dx, dy);
                },

                moveDir(dx, dy) {
                    if (this.isSolved) return false;

                    const cell = this.puzzle.grid[this.playerY][this.playerX];

                    if (dx === -1 && cell.left) return false;
                    if (dx === 1 && cell.right) return false;
                    if (dy === -1 && cell.top) return false;
                    if (dy === 1 && cell.bottom) return false;

                    const nx = this.playerX + dx;
                    const ny = this.playerY + dy;

                    if (nx < 0 || nx >= this.puzzle.size || ny < 0 || ny >= this.puzzle.size) return false;

                    this.playerX = nx;
                    this.playerY = ny;
                    this.moves++;
                    this.trail.add(ny + ',' + nx);
                    this.trailPath.push({ x: nx, y: ny });

                    this.drawMaze();

                    // Check win
                    if (nx === this.puzzle.end[0] && ny === this.puzzle.end[1]) {
                        this._dragging = false;
                        this.wonAnim = true;
                        setTimeout(() => this.solve(), 500);
                    }

                    return true;
                },

                drawMaze() {
                    const ctx = this._ctx;
                    if (!ctx) return;

                    const size = this.puzzle.size;
                    const cell = this._cellPx;
                    const wall = this._wallPx;
                    const step = cell + wall;
                    const ox = this._offsetX;
                    const oy = this._offsetY;
                    const total = size * cell + (size + 1) * wall;
                    const c = this._colors;
                    const canvasW = this._canvas.width / this._dpr;
                    const canvasH = this._canvas.height / this._dpr;

                    // Clear
                    ctx.clearRect(0, 0, canvasW, canvasH);

                    // Background fill (walls color — then we carve out cells)
                    ctx.fillStyle = c.wall;
                    ctx.beginPath();
                    ctx.roundRect(ox, oy, total, total, 8);
                    ctx.fill();

                    // Draw cells (passages) — all white, no trail fill
                    for (let ry = 0; ry < size; ry++) {
                        for (let cx = 0; cx < size; cx++) {
                            const x = ox + wall + cx * step;
                            const y = oy + wall + ry * step;
                            const g = this.puzzle.grid[ry][cx];

                            const isStart = cx === this.puzzle.start[0] && ry === this.puzzle.start[1];
                            const isEnd = cx === this.puzzle.end[0] && ry === this.puzzle.end[1];

                            if (isEnd) {
                                ctx.fillStyle = c.endBg;
                            } else {
                                ctx.fillStyle = c.bg;
                            }
                            ctx.fillRect(x, y, cell, cell);

                            // Carve passages between cells
                            if (!g.right && cx < size - 1) {
                                ctx.fillStyle = c.bg;
                                ctx.fillRect(x + cell, y, wall, cell);
                            }
                            if (!g.bottom && ry < size - 1) {
                                ctx.fillStyle = c.bg;
                                ctx.fillRect(x, y + cell, cell, wall);
                            }
                        }
                    }

                    // Helper: get pixel center of a cell
                    const cellCenter = (cx, cy) => ({
                        px: ox + wall + cx * step + cell / 2,
                        py: oy + wall + cy * step + cell / 2,
                    });

                    // Draw trail line
                    if (this.trailPath.length > 1) {
                        const lineW = Math.max(3, Math.round(cell * 0.16));
                        ctx.strokeStyle = '#5B2333';
                        ctx.lineWidth = lineW;
                        ctx.lineCap = 'round';
                        ctx.lineJoin = 'round';
                        ctx.beginPath();
                        const first = cellCenter(this.trailPath[0].x, this.trailPath[0].y);
                        ctx.moveTo(first.px, first.py);
                        for (let i = 1; i < this.trailPath.length; i++) {
                            const p = cellCenter(this.trailPath[i].x, this.trailPath[i].y);
                            ctx.lineTo(p.px, p.py);
                        }
                        ctx.stroke();
                    }

                    // Draw end flag
                    const ef = cellCenter(this.puzzle.end[0], this.puzzle.end[1]);
                    ctx.font = `${Math.round(cell * 0.4)}px sans-serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('🏁', ef.px, ef.py);

                    // Draw player
                    const pp = cellCenter(this.playerX, this.playerY);
                    const radius = cell * 0.3;

                    ctx.fillStyle = c.player;
                    ctx.beginPath();
                    ctx.arc(pp.px, pp.py, radius, 0, Math.PI * 2);
                    ctx.fill();
                },

                // Timer
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
                        this.timerText = `${pad(m)}:${pad(s % 60)}`;
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
                            body: JSON.stringify({ moves: this.moves })
                        });

                        const data = await res.json();
                        if (!data?.ok) return;

                        this.isSolved = true;
                        this.finalTime = data.final_time || this.finalTime;
                        this.timerText = this.finalTime || this.timerText;

                        if (data?.leaderboard?.rows) {
                            this.leaderboardRows = data.leaderboard.rows;
                            this.myRank = data.leaderboard.my_rank ?? this.myRank;
                            this.scope = data.leaderboard.scope || this.scope;
                        }

                        this.stopTimer();

                        if (data?.streak) {
                            window.dispatchEvent(new CustomEvent('cf:streak', { detail: data.streak }));
                        }

                        // Confetti
                        if (window.confetti && document.getElementById('mainConfettiCanvas')) {
                            const cannon = window.confetti.create(document.getElementById('mainConfettiCanvas'), { useWorker: true, resize: true });
                            cannon({ particleCount: 110, angle: 60, spread: 55, startVelocity: 55, gravity: 1.0, ticks: 320, origin: { x: 0.02, y: 1 } });
                            cannon({ particleCount: 110, angle: 120, spread: 55, startVelocity: 55, gravity: 1.0, ticks: 320, origin: { x: 0.98, y: 1 } });
                        }
                    } catch (e) {}
                },
            }
        }
    </script>
</x-layouts.dashboard>
