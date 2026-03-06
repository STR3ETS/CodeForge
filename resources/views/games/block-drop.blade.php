{{-- resources/views/games/block-drop.blade.php --}}
<x-layouts.dashboard :title="'Blok Drop'" active="daily">
    @php
        $isSolved = (bool)($run->solved ?? false);
        $isFailed = (bool)($isFailed ?? false);
        $meId     = (int) auth()->id();

        $init = [
            'me_id'      => $meId,
            'scope'      => (string)($scope ?? 'global'),
            'puzzle'     => [
                'number' => (int)($puzzleMeta['number'] ?? 0),
                'date'   => (string)($puzzleMeta['date'] ?? date('Y-m-d')),
                'seed'   => (int)($puzzleMeta['seed'] ?? 0),
            ],
            'run' => [
                'solved'     => $isSolved,
                'failed'     => $isFailed,
                'final_time' => $finalTime ?? null,
            ],
            'leaderboard' => [
                'rows'    => collect($topTimes ?? collect())->values()->all(),
                'my_rank' => $myRank ?? null,
            ],
            'routes' => [
                'finish' => route('games.blockdrop.finish'),
            ],
            'csrf' => csrf_token(),
        ];
    @endphp

    <style>
        [x-cloak]{display:none!important;}
        #blockDropCanvas {
            image-rendering: pixelated;
            display: block;
            margin: 0 auto;
            border: 1.5px solid rgba(86,77,74,0.15);
            border-radius: 12px;
            background: #F7F4F3;
        }
        #nextCanvas {
            image-rendering: pixelated;
            display: block;
            border: 1.5px solid rgba(86,77,74,0.15);
            border-radius: 8px;
            background: #F7F4F3;
        }
    </style>

    <script>window.__TT_INIT__ = @json($init);</script>
    <script>
        window.__gameFinished = @json($isSolved || $isFailed);
        window.__gameStarted  = false;
        window.addEventListener('beforeunload', function () {
            if (window.__gameFinished || !window.__gameStarted) return;
            navigator.sendBeacon(
                '{{ route("games.abandon") }}',
                new Blob([JSON.stringify({ game_key: 'block-drop', _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );
        });
    </script>

    <div x-data="blockDropUi(window.__TT_INIT__)" x-init="init()" class="flex flex-col gap-8 max-w-3xl mx-auto relative overflow-hidden">

        {{-- HERO --}}
        <div class="relative z-[1] overflow-hidden rounded-2xl border border-[#564D4A]/10 bg-[#5B2333]">
            <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-20" alt="">
            <div class="absolute inset-0 bg-gradient-to-r from-[#5B2333]/95 via-[#5B2333]/80 to-transparent"></div>

            <div class="relative p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 text-white text-xs font-semibold w-fit">
                            <i class="fa-solid fa-table-cells"></i>
                            Dagelijks spel
                        </div>

                        <h1 class="mt-3 text-[1.5rem] md:text-[1.8rem] font-black text-white tracking-tight leading-tight">
                            <template x-if="!isSolved && !isFailed">
                                <span>Blok Drop <span class="text-white/70">#<span x-text="puzzle.number"></span></span></span>
                            </template>
                            <template x-if="isSolved"><span>Mooi! 10 rijen gewist 🎉</span></template>
                            <template x-if="isFailed"><span>Helaas… board vol 😅</span></template>
                        </h1>

                        <p class="mt-2 text-xs md:text-sm font-semibold text-white/80 leading-[1.3] italic">
                            <template x-if="!isSolved && !isFailed">
                                <span>Wis <span class="font-black text-white">10 rijen</span> zo snel mogelijk. Board vol = game over.</span>
                            </template>
                            <template x-if="isSolved"><span>Opgeslagen als resultaat van vandaag. Kom morgen terug voor een nieuwe.</span></template>
                            <template x-if="isFailed"><span>Board vol gelopen. Probeer het morgen opnieuw.</span></template>
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span x-show="!isSolved && !isFailed" x-cloak class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 text-white text-xs font-semibold">
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

                <div class="mt-5">
                    <div class="flex items-center justify-between text-[11px] font-semibold text-white/80">
                        <span>Voortgang</span>
                        <span><span x-text="linesCleared"></span> / 10 rijen</span>
                    </div>
                    <div class="mt-2 w-full h-[7px] rounded-full bg-white/15 overflow-hidden">
                        <div class="h-full rounded-full bg-white transition-all duration-300" :style="`width: ${Math.round((linesCleared/10)*100)}%`"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RESULT SCREEN --}}
        <div x-show="isSolved || isFailed" x-cloak class="relative z-[1] w-full bg-white rounded-2xl p-8 border border-[#564D4A]/10">
            <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                <div class="flex items-center gap-3">
                    <template x-if="isSolved">
                        <div class="w-10 h-10 rounded-xl bg-[#8E936D] border border-[#8E936D]/10 flex items-center justify-center">
                            <i class="fa-solid fa-check text-white"></i>
                        </div>
                    </template>
                    <template x-if="isFailed">
                        <div class="w-10 h-10 rounded-xl bg-[#CE796B] border border-[#CE796B]/10 flex items-center justify-center">
                            <i class="fa-solid fa-xmark text-white"></i>
                        </div>
                    </template>
                    <div>
                        <p class="text-sm font-extrabold text-[#564D4A] leading-tight" x-text="isSolved ? 'Voltooid' : 'Mislukt'"></p>
                        <p class="text-xs font-semibold text-[#564D4A]/55 leading-[1.3]"
                           x-text="isSolved ? '10 rijen gewist!' : 'Board vol gelopen.'"></p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-[#564D4A]/10 bg-white p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Tijd</p>
                        <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]" x-text="finalTime || '--:--'"></p>
                        <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">Tijd om 10 rijen te wissen</p>
                    </div>
                    <div class="rounded-2xl border border-[#564D4A]/10 bg-white p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Rijen gewist</p>
                        <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]" x-text="linesCleared"></p>
                        <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">Doel: 10 rijen</p>
                    </div>
                </div>

                {{-- Leaderboard --}}
                <div class="mt-6 rounded-2xl border border-[#564D4A]/10 bg-white p-5" x-show="isSolved" x-cloak>
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <h2 class="text-[1.05rem] font-extrabold text-[#564D4A]">Scorebord</h2>
                            <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                                Snelste tijden van vandaag ({{ ($scope ?? 'global') === 'friends' ? 'Vrienden' : 'Wereldwijd' }}).
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach(($tabs ?? []) as $t)
                                @php
                                    $active = ($scope ?? 'global') === $t['key'];
                                    $href   = request()->fullUrlWithQuery(['scope' => $t['key']]);
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
                                        <p class="text-[11px] font-semibold text-[#564D4A]/55">Level <span x-text="row.user.level || 1"></span></p>
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

        {{-- GAME AREA --}}
        <div x-show="!isSolved && !isFailed" x-cloak class="relative z-[1] w-full bg-white rounded-2xl p-6 border border-[#564D4A]/10">

            <div x-show="!started" class="text-center py-8">
                <p class="text-xl font-extrabold text-[#564D4A]">Ben je er klaar voor?</p>
                <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">Gebruik pijltjestoetsen om te bewegen en draaien. Spatie = hard drop.</p>
                <div class="mt-4 grid grid-cols-3 gap-2 max-w-[200px] mx-auto text-xs font-semibold text-[#564D4A]/70">
                    <div></div>
                    <div class="rounded-lg bg-[#F7F4F3] border border-[#564D4A]/10 px-2 py-2 text-center flex flex-col items-center gap-1">
                        <i class="fa-solid fa-arrow-up text-[13px]"></i>
                        <span class="text-[10px]">Draaien</span>
                    </div>
                    <div></div>
                    <div class="rounded-lg bg-[#F7F4F3] border border-[#564D4A]/10 px-2 py-2 text-center flex flex-col items-center gap-1">
                        <i class="fa-solid fa-arrow-left text-[13px]"></i>
                        <span class="text-[10px]">Links</span>
                    </div>
                    <div class="rounded-lg bg-[#F7F4F3] border border-[#564D4A]/10 px-2 py-2 text-center flex flex-col items-center gap-1">
                        <i class="fa-solid fa-arrow-down text-[13px]"></i>
                        <span class="text-[10px]">Zacht</span>
                    </div>
                    <div class="rounded-lg bg-[#F7F4F3] border border-[#564D4A]/10 px-2 py-2 text-center flex flex-col items-center gap-1">
                        <i class="fa-solid fa-arrow-right text-[13px]"></i>
                        <span class="text-[10px]">Rechts</span>
                    </div>
                    <div class="col-span-3 rounded-lg bg-[#F7F4F3] border border-[#564D4A]/10 px-2 py-2 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-angles-down text-[13px]"></i>
                        <span class="text-[10px]">Spatie - Hard drop</span>
                    </div>
                </div>
                <button @click="startGame()"
                    class="mt-6 inline-flex items-center gap-2 px-8 py-3 rounded-2xl bg-[#5B2333] text-white text-sm font-bold hover:bg-[#5B2333]/90 transition active:scale-[0.98]">
                    <i class="fa-solid fa-play"></i>
                    Spel starten
                </button>
            </div>

            <div x-show="started" class="flex gap-4 items-start">
                {{-- Board — left aligned, natural size --}}
                <div id="blockDropBoardWrap" class="shrink-0">
                    <canvas id="blockDropCanvas" width="200" height="400" class="block rounded-xl"></canvas>
                </div>

                {{-- Right panel --}}
                <div class="flex-1 min-w-0 flex flex-col gap-3">

                    {{-- Hold + Next side by side --}}
                    <div class="flex gap-3">
                        <div class="flex-1 rounded-xl border border-[#564D4A]/10 bg-[#F7F4F3] p-4">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-[#564D4A]/45 mb-2">Hold</p>
                            <canvas id="holdCanvas" width="80" height="48" class="block mx-auto rounded-lg" style="background:#F7F4F3;"></canvas>
                        </div>

                        <div class="flex-[2] rounded-xl border border-[#564D4A]/10 bg-[#F7F4F3] p-4">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-[#564D4A]/45 mb-2">Next</p>
                            <canvas id="nextCanvas" width="240" height="48" class="block mx-auto rounded-lg" style="background:#F7F4F3;"></canvas>
                        </div>
                    </div>

                    {{-- Lines cleared --}}
                    <div class="rounded-xl border border-[#564D4A]/10 bg-[#F7F4F3] p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Rijen gewist</p>
                        <p class="mt-1 text-2xl font-black text-[#564D4A]" x-text="linesCleared + ' / 10'"></p>
                    </div>

                    {{-- Controls --}}
                    <div class="rounded-xl border border-[#564D4A]/10 bg-[#F7F4F3] p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-[#564D4A]/45 mb-2">Bediening</p>
                        <div class="flex flex-col gap-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-[#564D4A]/60">Bewegen</span>
                                <span class="font-bold text-[#564D4A] bg-white border border-[#564D4A]/10 rounded-lg px-2 py-1">← →</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-[#564D4A]/60">Draaien</span>
                                <span class="font-bold text-[#564D4A] bg-white border border-[#564D4A]/10 rounded-lg px-2 py-1">↑ / Z</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-[#564D4A]/60">Hard drop</span>
                                <span class="font-bold text-[#564D4A] bg-white border border-[#564D4A]/10 rounded-lg px-2 py-1">Spatie</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-[#564D4A]/60">Vasthouden</span>
                                <span class="font-bold text-[#564D4A] bg-white border border-[#564D4A]/10 rounded-lg px-2 py-1">C / ⇧</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div x-show="isSolved || isFailed" x-cloak class="relative z-[1]">
            <x-game-streak :streak="$streak" />
        </div>
    </div>

    <script>
    // ─── Tetris Engine ───────────────────────────────────────────────────────────

    const COLS = 10, ROWS = 20;
    let CELL = 20;

    // SRS tetromino definitions [rotation states][row][col]
    const PIECES = {
        I: {
            color: '#5B2333',
            shapes: [
                [[0,0,0,0],[1,1,1,1],[0,0,0,0],[0,0,0,0]],
                [[0,0,1,0],[0,0,1,0],[0,0,1,0],[0,0,1,0]],
                [[0,0,0,0],[0,0,0,0],[1,1,1,1],[0,0,0,0]],
                [[0,1,0,0],[0,1,0,0],[0,1,0,0],[0,1,0,0]],
            ]
        },
        O: {
            color: '#8E936D',
            shapes: [
                [[0,1,1,0],[0,1,1,0],[0,0,0,0],[0,0,0,0]],
                [[0,1,1,0],[0,1,1,0],[0,0,0,0],[0,0,0,0]],
                [[0,1,1,0],[0,1,1,0],[0,0,0,0],[0,0,0,0]],
                [[0,1,1,0],[0,1,1,0],[0,0,0,0],[0,0,0,0]],
            ]
        },
        T: {
            color: '#CE796B',
            shapes: [
                [[0,1,0],[1,1,1],[0,0,0]],
                [[0,1,0],[0,1,1],[0,1,0]],
                [[0,0,0],[1,1,1],[0,1,0]],
                [[0,1,0],[1,1,0],[0,1,0]],
            ]
        },
        S: {
            color: '#7A9E7E',
            shapes: [
                [[0,1,1],[1,1,0],[0,0,0]],
                [[0,1,0],[0,1,1],[0,0,1]],
                [[0,0,0],[0,1,1],[1,1,0]],
                [[1,0,0],[1,1,0],[0,1,0]],
            ]
        },
        Z: {
            color: '#C4956A',
            shapes: [
                [[1,1,0],[0,1,1],[0,0,0]],
                [[0,0,1],[0,1,1],[0,1,0]],
                [[0,0,0],[1,1,0],[0,1,1]],
                [[0,1,0],[1,1,0],[1,0,0]],
            ]
        },
        J: {
            color: '#7B8FA6',
            shapes: [
                [[1,0,0],[1,1,1],[0,0,0]],
                [[0,1,1],[0,1,0],[0,1,0]],
                [[0,0,0],[1,1,1],[0,0,1]],
                [[0,1,0],[0,1,0],[1,1,0]],
            ]
        },
        L: {
            color: '#9B6B9B',
            shapes: [
                [[0,0,1],[1,1,1],[0,0,0]],
                [[0,1,0],[0,1,0],[0,1,1]],
                [[0,0,0],[1,1,1],[1,0,0]],
                [[1,1,0],[0,1,0],[0,1,0]],
            ]
        },
    };

    const PIECE_KEYS = ['I','O','T','S','Z','J','L'];

    function mulberry32(seed) {
        let s = seed >>> 0;
        return function() {
            s = (s + 0x6D2B79F5) >>> 0;
            let t = s;
            t = Math.imul(t ^ (t >>> 15), 1 | t);
            t ^= t + Math.imul(t ^ (t >>> 7), 61 | t);
            return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
        };
    }

    function makeBag7(rand) {
        let bag = [];
        return function next() {
            if (bag.length === 0) {
                bag = [...PIECE_KEYS];
                // Fisher-Yates shuffle
                for (let i = bag.length - 1; i > 0; i--) {
                    const j = Math.floor(rand() * (i + 1));
                    [bag[i], bag[j]] = [bag[j], bag[i]];
                }
            }
            return bag.pop();
        };
    }

    class BlockDropEngine {
        constructor(seed, onLines, onGameOver) {
            this.rand      = mulberry32(seed);
            this.nextPiece = makeBag7(this.rand);
            this.onLines   = onLines;
            this.onGameOver = onGameOver;

            this.board      = Array.from({length: ROWS}, () => Array(COLS).fill(null));
            this.lines      = 0;
            this.running    = false;

            this.current      = null;
            this.cx           = 0; // current piece col offset
            this.cy           = 0; // current piece row offset
            this.cr           = 0; // rotation
            this.previewQueue = []; // next 3 pieces
            this.heldKey      = null; // held piece
            this.canHold      = true; // one hold per piece

            this.lastDrop   = 0;
            this.lockTimer  = null;
            this.onGround   = false;

            this.canvas     = document.getElementById('blockDropCanvas');
            this.ctx        = this.canvas.getContext('2d');
            this.nextCanvas = document.getElementById('nextCanvas');
            this.nctx       = this.nextCanvas.getContext('2d');
            this.holdCanvas = document.getElementById('holdCanvas');
            this.hctx       = this.holdCanvas.getContext('2d');

            // Dynamic cell size: fit both card width AND viewport height
            const wrap   = document.getElementById('blockDropBoardWrap');
            const availW = wrap ? wrap.clientWidth : 300;
            const availH = Math.min(window.innerHeight - 320, 420); // leave room for hero + stats
            const cellByW = Math.floor(availW / COLS);
            const cellByH = Math.floor(availH / ROWS);
            CELL = Math.max(18, Math.min(cellByW, cellByH));
            this.canvas.width  = CELL * COLS;
            this.canvas.height = CELL * ROWS;

            // Pre-fill 3-piece preview queue
            this.previewQueue = [this.nextPiece(), this.nextPiece(), this.nextPiece()];

            this._rafId   = null;
            this._keydown = this._onKey.bind(this);
            this._keyup   = this._onKeyUp.bind(this);
            this._dasDir  = 0;
            this._dasTimer = null;
            this._arrTimer = null;
            document.addEventListener('keydown', this._keydown);
            document.addEventListener('keyup',   this._keyup);

            this._spawnNext();
        }

        gravityMs() {
            // Speeds up every 2 lines
            const level = Math.floor(this.lines / 2);
            return Math.max(80, 500 - level * 40);
        }

        _shape(key, rot) {
            return PIECES[key].shapes[rot % PIECES[key].shapes.length];
        }

        _spawnNext() {
            this.canHold = true; // allow hold again for this new piece
            this._spawnFromQueue();
        }

        _spawnFromQueue() {
            this.current = this.previewQueue.shift();
            this.previewQueue.push(this.nextPiece());

            const shape = this._shape(this.current, 0);
            this.cx = Math.floor((COLS - shape[0].length) / 2);
            this.cy = 0;
            this.cr = 0;
            this.onGround = false;

            if (this._collides(this.current, this.cr, this.cx, this.cy)) {
                this.running = false;
                this._render();
                this._stopDas();
                document.removeEventListener('keydown', this._keydown);
                document.removeEventListener('keyup',   this._keyup);
                this.onGameOver();
                return;
            }

            this._render();
        }

        _hold() {
            if (!this.canHold || !this.running || !this.current) return;
            this.canHold = false;
            this._clearLockTimer();
            this.onGround = false;

            const prev = this.heldKey;
            this.heldKey = this.current;
            this.current = null;

            if (prev !== null) {
                // Swap: respawn the previously held piece
                this.current = prev;
                const shape = this._shape(prev, 0);
                this.cx = Math.floor((COLS - shape[0].length) / 2);
                this.cy = 0;
                this.cr = 0;
                if (this._collides(this.current, this.cr, this.cx, this.cy)) {
                    this.running = false;
                    this._render();
                    this._stopDas();
                document.removeEventListener('keydown', this._keydown);
                document.removeEventListener('keyup',   this._keyup);
                    this.onGameOver();
                    return;
                }
                this._render();
            } else {
                // No held piece yet: just grab next from queue
                this._spawnFromQueue();
            }
        }

        _collides(key, rot, cx, cy) {
            const shape = this._shape(key, rot);
            for (let r = 0; r < shape.length; r++) {
                for (let c = 0; c < shape[r].length; c++) {
                    if (!shape[r][c]) continue;
                    const nr = cy + r, nc = cx + c;
                    if (nr >= ROWS || nc < 0 || nc >= COLS) return true;
                    if (nr >= 0 && this.board[nr][nc]) return true;
                }
            }
            return false;
        }

        _lock() {
            const shape = this._shape(this.current, this.cr);
            for (let r = 0; r < shape.length; r++) {
                for (let c = 0; c < shape[r].length; c++) {
                    if (!shape[r][c]) continue;
                    const nr = this.cy + r;
                    if (nr >= 0) this.board[nr][this.cx + c] = PIECES[this.current].color;
                }
            }
            this._clearLines();
            this._spawnNext();
        }

        _clearLines() {
            let cleared = 0;
            for (let r = ROWS - 1; r >= 0; r--) {
                if (this.board[r].every(c => c !== null)) {
                    this.board.splice(r, 1);
                    this.board.unshift(Array(COLS).fill(null));
                    cleared++;
                    r++; // re-check same index
                }
            }
            if (cleared > 0) {
                this.lines += cleared;
                this.onLines(this.lines);
                if (this.lines >= 10) {
                    this.running = false;
                    this._render();
                    this._stopDas();
                document.removeEventListener('keydown', this._keydown);
                document.removeEventListener('keyup',   this._keyup);
                    // onLines callback handles win
                }
            }
        }

        _ghostY() {
            let gy = this.cy;
            while (!this._collides(this.current, this.cr, this.cx, gy + 1)) gy++;
            return gy;
        }

        _render() {
            const ctx = this.ctx;
            ctx.fillStyle = '#F7F4F3';
            ctx.fillRect(0, 0, COLS * CELL, ROWS * CELL);

            // Grid lines
            ctx.strokeStyle = 'rgba(86,77,74,0.07)';
            ctx.lineWidth = 0.5;
            for (let c = 0; c <= COLS; c++) {
                ctx.beginPath(); ctx.moveTo(c * CELL, 0); ctx.lineTo(c * CELL, ROWS * CELL); ctx.stroke();
            }
            for (let r = 0; r <= ROWS; r++) {
                ctx.beginPath(); ctx.moveTo(0, r * CELL); ctx.lineTo(COLS * CELL, r * CELL); ctx.stroke();
            }

            // Board cells
            for (let r = 0; r < ROWS; r++) {
                for (let c = 0; c < COLS; c++) {
                    if (this.board[r][c]) {
                        this._drawCell(ctx, c, r, this.board[r][c]);
                    }
                }
            }

            // Ghost piece (outlined, no fill)
            if (this.current && this.running) {
                const gy = this._ghostY();
                const shape = this._shape(this.current, this.cr);
                ctx.strokeStyle = PIECES[this.current].color;
                ctx.lineWidth = 1.5;
                ctx.globalAlpha = 0.3;
                for (let r = 0; r < shape.length; r++) {
                    for (let c = 0; c < shape[r].length; c++) {
                        if (!shape[r][c]) continue;
                        ctx.strokeRect(this.cx * CELL + c * CELL + 2, gy * CELL + r * CELL + 2, CELL - 4, CELL - 4);
                    }
                }
                ctx.globalAlpha = 1;
            }

            // Active piece
            if (this.current) {
                const shape = this._shape(this.current, this.cr);
                for (let r = 0; r < shape.length; r++) {
                    for (let c = 0; c < shape[r].length; c++) {
                        if (!shape[r][c]) continue;
                        this._drawCell(ctx, this.cx + c, this.cy + r, PIECES[this.current].color);
                    }
                }
            }

            // Next 3 pieces preview
            const nctx = this.nctx;
            const nw = this.nextCanvas.width, nh = this.nextCanvas.height;
            nctx.fillStyle = '#F7F4F3';
            nctx.fillRect(0, 0, nw, nh);
            const pc = 11; // fixed preview cell size
            const slotW = Math.floor(nw / 3);
            this.previewQueue.forEach((key, i) => {
                const shape = this._shape(key, 0);
                // Only use filled rows for centering
                const filledShape = shape.filter(row => row.some(v => v));
                const offX = i * slotW + Math.floor((slotW - shape[0].length * pc) / 2);
                const offY = Math.floor((nh - filledShape.length * pc) / 2);
                filledShape.forEach((row, fr) => {
                    row.forEach((v, c) => {
                        if (!v) return;
                        nctx.fillStyle = PIECES[key].color;
                        nctx.fillRect(offX + c * pc + 1, offY + fr * pc + 1, pc - 2, pc - 2);
                        nctx.fillStyle = 'rgba(255,255,255,0.18)';
                        nctx.fillRect(offX + c * pc + 1, offY + fr * pc + 1, pc - 2, 2);
                        nctx.fillRect(offX + c * pc + 1, offY + fr * pc + 1, 2, pc - 2);
                    });
                });
            });

            // Hold canvas
            const hctx = this.hctx;
            const hw = this.holdCanvas.width, hh = this.holdCanvas.height;
            hctx.fillStyle = '#F7F4F3';
            hctx.fillRect(0, 0, hw, hh);
            if (this.heldKey) {
                const hcolor = this.canHold ? PIECES[this.heldKey].color : 'rgba(86,77,74,0.25)';
                const hshape = this._shape(this.heldKey, 0);
                const filledH = hshape.filter(row => row.some(v => v));
                const hOffX = Math.floor((hw - hshape[0].length * pc) / 2);
                const hOffY = Math.floor((hh - filledH.length * pc) / 2);
                filledH.forEach((row, fr) => {
                    row.forEach((v, c) => {
                        if (!v) return;
                        hctx.fillStyle = hcolor;
                        hctx.fillRect(hOffX + c * pc + 1, hOffY + fr * pc + 1, pc - 2, pc - 2);
                        if (this.canHold) {
                            hctx.fillStyle = 'rgba(255,255,255,0.18)';
                            hctx.fillRect(hOffX + c * pc + 1, hOffY + fr * pc + 1, pc - 2, 2);
                            hctx.fillRect(hOffX + c * pc + 1, hOffY + fr * pc + 1, 2, pc - 2);
                        }
                    });
                });
            }
        }

        _drawCell(ctx, c, r, color) {
            ctx.fillStyle = color;
            ctx.fillRect(c * CELL + 2, r * CELL + 2, CELL - 4, CELL - 4);
            // Subtle inner border
            ctx.fillStyle = 'rgba(255,255,255,0.18)';
            ctx.fillRect(c * CELL + 2, r * CELL + 2, CELL - 4, 2);
            ctx.fillRect(c * CELL + 2, r * CELL + 2, 2, CELL - 4);
        }

        _tryMove(dc) {
            if (!this._collides(this.current, this.cr, this.cx + dc, this.cy)) {
                this.cx += dc;
                this._checkGround();
                this._render();
            }
        }

        _tryRotate() {
            const nr = (this.cr + 1) % PIECES[this.current].shapes.length;
            // Wall kick offsets
            const kicks = [0, -1, 1, -2, 2];
            for (const kick of kicks) {
                if (!this._collides(this.current, nr, this.cx + kick, this.cy)) {
                    this.cr = nr;
                    this.cx += kick;
                    this._checkGround();
                    this._render();
                    return;
                }
            }
        }

        _softDrop() {
            if (!this._collides(this.current, this.cr, this.cx, this.cy + 1)) {
                this.cy++;
                this._checkGround();
                this._render();
                this.lastDrop = performance.now();
            } else {
                this._handleGround();
            }
        }

        _hardDrop() {
            while (!this._collides(this.current, this.cr, this.cx, this.cy + 1)) {
                this.cy++;
            }
            this._clearLockTimer();
            this._lock();
        }

        _checkGround() {
            const nowOnGround = this._collides(this.current, this.cr, this.cx, this.cy + 1);
            if (nowOnGround && !this.onGround) {
                this.onGround = true;
                this._startLockTimer();
            } else if (!nowOnGround && this.onGround) {
                this.onGround = false;
                this._clearLockTimer();
            }
        }

        _handleGround() {
            if (!this.onGround) {
                this.onGround = true;
                this._startLockTimer();
            }
        }

        _startLockTimer() {
            this._clearLockTimer();
            this.lockTimer = setTimeout(() => {
                if (this.running && this._collides(this.current, this.cr, this.cx, this.cy + 1)) {
                    this._lock();
                }
            }, 500);
        }

        _clearLockTimer() {
            if (this.lockTimer) { clearTimeout(this.lockTimer); this.lockTimer = null; }
        }

        _onKey(e) {
            if (!this.running || !this.current) return;
            switch (e.code) {
                case 'ArrowLeft':
                    e.preventDefault();
                    if (!e.repeat) this._startDas(-1);
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    if (!e.repeat) this._startDas(1);
                    break;
                case 'ArrowDown':   e.preventDefault(); this._softDrop();  break;
                case 'ArrowUp':
                case 'KeyZ':        e.preventDefault(); this._tryRotate(); break;
                case 'Space':       e.preventDefault(); this._hardDrop();  break;
                case 'KeyC':
                case 'ShiftLeft':
                case 'ShiftRight':  e.preventDefault(); this._hold();      break;
            }
        }

        _onKeyUp(e) {
            if (e.code === 'ArrowLeft'  && this._dasDir === -1) this._stopDas();
            if (e.code === 'ArrowRight' && this._dasDir ===  1) this._stopDas();
        }

        _startDas(dir) {
            this._stopDas();
            this._dasDir = dir;
            this._tryMove(dir);
            // DAS: 150ms delay, then ARR every 50ms
            this._dasTimer = setTimeout(() => {
                this._arrTimer = setInterval(() => {
                    if (!this.running || !this.current) { this._stopDas(); return; }
                    this._tryMove(this._dasDir);
                }, 50);
            }, 150);
        }

        _stopDas() {
            if (this._dasTimer) { clearTimeout(this._dasTimer);  this._dasTimer = null; }
            if (this._arrTimer) { clearInterval(this._arrTimer); this._arrTimer = null; }
            this._dasDir = 0;
        }

        _loop(ts) {
            if (!this.running) return;
            if (ts - this.lastDrop >= this.gravityMs()) {
                this.lastDrop = ts;
                if (!this._collides(this.current, this.cr, this.cx, this.cy + 1)) {
                    this.cy++;
                    this.onGround = false;
                    this._clearLockTimer();
                    this._render();
                } else {
                    this._handleGround();
                }
            }
            this._rafId = requestAnimationFrame(ts => this._loop(ts));
        }

        start() {
            this.running  = true;
            this.lastDrop = performance.now();
            this._render();
            this._rafId = requestAnimationFrame(ts => this._loop(ts));
        }

        destroy() {
            this.running = false;
            if (this._rafId) cancelAnimationFrame(this._rafId);
            this._clearLockTimer();
            this._stopDas();
                document.removeEventListener('keydown', this._keydown);
                document.removeEventListener('keyup',   this._keyup);
        }
    }

    // ─── Alpine UI ───────────────────────────────────────────────────────────────

    function blockDropUi(init) {
        return {
            meId:   parseInt(init.me_id || '0', 10),
            scope:  init.scope || 'global',
            puzzle: init.puzzle,

            isSolved: !!init.run.solved,
            isFailed: !!init.run.failed,
            finalTime: init.run.final_time || null,

            linesCleared: 0,
            started: false,
            startMs: 0,

            timerText: '00:00',
            _timerId: null,

            leaderboardReady: false,
            leaderboardRows: (init.leaderboard && Array.isArray(init.leaderboard.rows)) ? init.leaderboard.rows : [],
            myRank: init.leaderboard ? init.leaderboard.my_rank : null,

            _engine: null,

            init() {
                this.leaderboardReady = true;
                this.$watch('started',  v => { if (v && !window.__gameFinished) window.__gameStarted = true; });
                this.$watch('isSolved', v => { if (v) window.__gameFinished = true; });
                this.$watch('isFailed', v => { if (v) window.__gameFinished = true; });

                if (this.isSolved) {
                    this.linesCleared = 10;
                    this.timerText = this.finalTime || '--:--';
                }
                if (this.isFailed) this.linesCleared = 0;

                // Callbacks from engine
                const self = this;

                window.__tt_onLines = function(lines) {
                    self.linesCleared = Math.min(lines, 10);
                    if (lines >= 10 && !self.isSolved) {
                        self._onWin();
                    }
                };

                window.__tt_onFail = function() {
                    if (!self.isSolved && !self.isFailed) self._onFail();
                };
            },

            startTimer() {
                this.stopTimer();
                const pad = n => String(n).padStart(2, '0');
                const tick = () => {
                    const diff = Math.max(0, Date.now() - this.startMs);
                    const s = Math.floor(diff / 1000);
                    this.timerText = `${pad(Math.floor(s/60))}:${pad(s%60)}`;
                };
                tick();
                this._timerId = setInterval(tick, 500);
            },

            stopTimer() {
                if (this._timerId) { clearInterval(this._timerId); this._timerId = null; }
            },

            holdPiece() {
                if (this._engine) this._engine._hold();
            },

            startGame() {
                if (this.isSolved || this.isFailed) return;
                this.started = true;
                this.startMs = Date.now();
                fetch('{{ route("games.mark-started") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ game_key: 'block-drop' })
                });
                this.startTimer();

                this.$nextTick(() => {
                    this._engine = new BlockDropEngine(
                        init.puzzle.seed,
                        window.__tt_onLines,
                        window.__tt_onFail,
                    );
                    this._engine.start();
                });
            },

            async _onWin() {
                this.stopTimer();
                const durationMs = Date.now() - this.startMs;
                const pad = n => String(n).padStart(2, '0');
                const s = Math.floor(durationMs / 1000);
                this.finalTime = `${pad(Math.floor(s/60))}:${pad(s%60)}`;
                this.timerText = this.finalTime;
                this.isSolved  = true;

                if (this._engine) this._engine.destroy();

                try {
                    const res = await fetch(init.routes.finish + '?scope=' + encodeURIComponent(this.scope), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': init.csrf,
                        },
                        body: JSON.stringify({ duration_ms: durationMs, failed: false }),
                    });
                    const data = await res.json();

                    if (data?.leaderboard?.rows) {
                        this.leaderboardRows = data.leaderboard.rows;
                        this.myRank = data.leaderboard.my_rank ?? this.myRank;
                    }
                    if (data?.streak) {
                        window.dispatchEvent(new CustomEvent('cf:streak', { detail: data.streak }));
                    }
                } catch (e) {}

                // Confetti
                if (window.confetti && document.getElementById('mainConfettiCanvas')) {
                    const cannon = window.confetti.create(document.getElementById('mainConfettiCanvas'), { useWorker: true, resize: true });
                    cannon({ particleCount: 110, angle: 60,  spread: 55, startVelocity: 55, gravity: 1.0, ticks: 320, origin: { x: 0.02, y: 1 } });
                    cannon({ particleCount: 110, angle: 120, spread: 55, startVelocity: 55, gravity: 1.0, ticks: 320, origin: { x: 0.98, y: 1 } });
                }
            },

            async _onFail() {
                this.stopTimer();
                this.isFailed = true;

                if (this._engine) this._engine.destroy();

                try {
                    await fetch(init.routes.finish, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': init.csrf,
                        },
                        body: JSON.stringify({ failed: true }),
                    });
                } catch (e) {}
            },
        };
    }
    </script>
</x-layouts.dashboard>
