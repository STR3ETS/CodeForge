{{-- resources/views/games/sudoku.blade.php --}}
<x-layouts.dashboard :title="'Mini Sudoku'" active="daily">
    @php
        $isSolved = (bool)($run->solved ?? false);
        $isFailed = (bool)($isFailed ?? false);

        $startMs = (int)($state['started_ms'] ?? (
            $run->started_at ? $run->started_at->getTimestampMs() : now()->getTimestampMs()
        ));

        $fmtMs = function ($ms) {
            if ($ms === null) return '--:--';
            $sec = (int) round($ms / 1000);
            $mm  = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss  = str_pad((string) ($sec % 60),      2, '0', STR_PAD_LEFT);
            return $mm . ':' . $ss;
        };

        $finalTime = $isSolved ? $fmtMs($run->duration_ms) : null;
        $meId      = (int) auth()->id();

        // given is {idx: value}, convert keys to ints for JS
        $givenMap = [];
        foreach ($puzzle['given'] as $idx => $val) {
            $givenMap[(int)$idx] = (int)$val;
        }

        $init = [
            'me_id' => $meId,
            'scope' => (string)($scope ?? 'global'),

            'puzzle' => [
                'number' => (int)($puzzle['number'] ?? 0),
                'given'  => $givenMap,
                'hidden' => array_values($puzzle['hidden']),
            ],

            'state' => [
                'started_ms' => $startMs,
            ],

            'run' => [
                'solved'     => $isSolved,
                'failed'     => $isFailed,
                'final_time' => $finalTime,
            ],

            'leaderboard' => [
                'rows'    => collect($topTimes ?? collect())->values()->all(),
                'my_rank' => $myRank ?? null,
            ],

            'routes' => [
                'check' => route('games.sudoku.check'),
            ],

            'csrf' => csrf_token(),
        ];
    @endphp

    <style>[x-cloak]{display:none!important;}</style>
    <style>
        @keyframes sdkShake {
            0%,100%{transform:translateX(0)}
            20%{transform:translateX(-5px)}
            40%{transform:translateX(5px)}
            60%{transform:translateX(-3px)}
            80%{transform:translateX(3px)}
        }
        .sdk-shake { animation: sdkShake .35s ease-in-out; }

        .sdk-box-right  { border-right: 2px solid #564D4A40 !important; }
        .sdk-box-bottom { border-bottom: 2px solid #564D4A40 !important; }
    </style>

    <script>window.__SDK_INIT__ = @json($init);</script>
    <script>
        window.__gameFinished = @json($isSolved || $isFailed);
        window.__gameStarted  = false;
        window.addEventListener('beforeunload', function () {
            if (window.__gameFinished || !window.__gameStarted) return;
            navigator.sendBeacon(
                '{{ route("games.abandon") }}',
                new Blob([JSON.stringify({ game_key: 'sudoku', _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );
        });
    </script>

    <div x-data="sudokuGame(window.__SDK_INIT__)" x-init="init()" class="flex flex-col gap-8 max-w-3xl mx-auto">

        {{-- HEADER --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-[1.5rem] md:text-[1.8rem] font-black text-[#564D4A] tracking-tight leading-tight">
                        <template x-if="!isSolved && !isFailed">
                            <span>Mini Sudoku <span class="text-[#564D4A]/40">#<span x-text="puzzle.number"></span></span></span>
                        </template>
                        <template x-if="isSolved"><span>Uitstekend! Opgelost 🎉</span></template>
                        <template x-if="isFailed"><span>Oei… volgende keer beter! 😅</span></template>
                    </h1>
                    <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
                        <template x-if="!isSolved && !isFailed">
                            <span>Vul de lege vakjes in. Elke rij, kolom en 2×2 vak bevat 1-4. Je hebt één kans om te controleren.</span>
                        </template>
                        <template x-if="isSolved"><span>Resultaat opgeslagen. Kom morgen terug voor een nieuw puzzel.</span></template>
                        <template x-if="isFailed"><span>Niet alle vakjes klopten. Probeer het morgen opnieuw!</span></template>
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <span x-show="!isSolved && !isFailed" x-cloak
                          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-[#564D4A]/6 bg-white text-[#564D4A] text-xs font-semibold">
                        <i class="fa-solid fa-stopwatch text-[#5B2333]"></i>
                        <span x-text="timerText">00:00</span>
                    </span>
                    <a href="{{ route('dashboard.daily') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#5B2333] text-white text-xs font-semibold hover:bg-[#5B2333]/85 transition">
                        <i class="fa-solid fa-arrow-left"></i> Terug
                    </a>
                </div>
            </div>
        </div>

        {{-- GAME CARD --}}
        <div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/6">

            {{-- FINISHED --}}
            <div x-show="isSolved || isFailed" x-cloak>
                <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
                    <div class="flex items-center gap-3">
                        <template x-if="isSolved">
                            <div class="w-10 h-10 rounded-xl bg-[#8E936D] flex items-center justify-center">
                                <i class="fa-solid fa-check text-white"></i>
                            </div>
                        </template>
                        <template x-if="isFailed">
                            <div class="w-10 h-10 rounded-xl bg-[#CE796B] flex items-center justify-center">
                                <i class="fa-solid fa-xmark text-white"></i>
                            </div>
                        </template>
                        <div>
                            <p class="text-sm font-extrabold text-[#564D4A] leading-tight"
                               x-text="isSolved ? 'Sudoku opgelost!' : 'Mislukt'"></p>
                            <p class="text-xs font-semibold text-[#564D4A]/55"
                               x-text="isSolved ? 'Alle vakjes correct ingevuld.' : 'Niet alle vakjes klopten.'"></p>
                        </div>
                    </div>

                    <div class="mt-4" x-show="isSolved">
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Tijd</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]" x-text="finalTime || '--:--'"></p>
                        </div>
                    </div>

                    @include('games.partials.share-score-button', ['gameKey' => 'sudoku'])

                    {{-- Leaderboard --}}
                    <div class="mt-6 rounded-2xl border border-[#564D4A]/6 bg-white p-5" x-show="isSolved" x-cloak>
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <h2 class="text-[1.05rem] font-extrabold text-[#564D4A]">Scorebord</h2>
                                <p class="mt-1 text-xs font-semibold text-[#564D4A]/50">
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

                        <div class="mt-4 grid gap-2">
                            <template x-if="leaderboardRows.length === 0">
                                <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
                                    <p class="text-sm font-semibold text-[#564D4A]/60">Nog geen tijden.</p>
                                </div>
                            </template>

                            <template x-for="(row, idx) in leaderboardRows" :key="row.user.id + ':' + row.duration_ms">
                                <div class="flex items-center justify-between gap-4 rounded-2xl border border-[#564D4A]/6 bg-white p-4"
                                     :class="parseInt(row.user.id) === meId ? 'ring-2 ring-[#5B2333]/20' : ''">
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
                                                    <span class="text-[#564D4A] font-black text-sm" x-text="(row.user.name||'?').slice(0,1).toUpperCase()"></span>
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
                                            <p class="text-[11px] font-semibold text-[#564D4A]/55">Level <span x-text="row.user.level||1"></span></p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-xs font-extrabold text-[#564D4A] shrink-0">
                                        <i class="fa-solid fa-stopwatch text-[#5B2333]"></i>
                                        <span x-text="row.time"></span>
                                    </span>
                                </div>
                            </template>

                            <template x-if="myRank && myRank > leaderboardRows.length">
                                <div class="mt-2 rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-4 flex items-center justify-between">
                                    <p class="text-xs font-semibold text-[#564D4A]/60">Jouw positie</p>
                                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-[#564D4A]/6 text-xs font-extrabold text-[#564D4A]">
                                        #<span x-text="myRank"></span>
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PLAYING --}}
            <div x-show="!isSolved && !isFailed" x-cloak>

                {{-- Start screen --}}
                <div x-show="!started" x-cloak class="text-center py-10">
                    <p class="text-xl font-extrabold text-[#564D4A]">Ben je er klaar voor?</p>
                    <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">Vul de 4×4 sudoku in. Elke rij, kolom en 2×2 vak bevat de cijfers 1, 2, 3 en 4.</p>
                    <button @click="startGame()"
                        class="mt-6 inline-flex items-center gap-2 px-8 py-3 rounded-2xl bg-[#5B2333] text-white text-sm font-bold hover:bg-[#5B2333]/90 transition active:scale-[0.98]">
                        <i class="fa-solid fa-play"></i>
                        Spel starten
                    </button>
                </div>

                {{-- Game area --}}
                <div x-show="started" x-cloak>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Vul de sudoku in</h2>
                            <p class="mt-1 text-xs font-semibold text-[#564D4A]/50">Klik een leeg vakje, kies dan een cijfer (1-4).</p>
                        </div>
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold shrink-0">
                            <i class="fa-solid fa-table-cells-large"></i>
                            4×4
                        </span>
                    </div>

                    {{-- Grid --}}
                    <div class="mt-6 flex flex-col items-center">
                        <div class="inline-grid grid-cols-4 border-2 border-[#564D4A]/30 rounded-xl overflow-hidden">
                            <template x-for="(cell, idx) in grid" :key="idx">
                                <button
                                    type="button"
                                    @click="selectCell(idx)"
                                    :disabled="isGiven(idx) || isSolved || isFailed"
                                    :class="cellClass(idx)"
                                    class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center text-[1.4rem] sm:text-[1.7rem] font-black transition select-none border border-[#564D4A]/6 focus:outline-none"
                                    x-text="cell > 0 ? cell : ''"
                                ></button>
                            </template>
                        </div>

                        {{-- Number picker --}}
                        <div class="mt-5 flex items-center gap-3" x-show="selectedCell !== null && !isSolved && !isFailed" x-cloak>
                            <template x-for="n in [1,2,3,4]" :key="n">
                                <button
                                    type="button"
                                    @click="setValue(n)"
                                    class="w-12 h-12 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-lg font-black hover:bg-[#5B2333]/20 active:scale-95 transition">
                                    <span x-text="n"></span>
                                </button>
                            </template>
                            <button
                                type="button"
                                @click="setValue(0)"
                                class="w-12 h-12 rounded-xl bg-[#564D4A]/8 text-[#564D4A]/50 text-sm font-bold hover:bg-[#564D4A]/15 active:scale-95 transition"
                                title="Wis">
                                <i class="fa-solid fa-delete-left"></i>
                            </button>
                        </div>

                        {{-- Placeholder so layout doesn't jump --}}
                        <div class="mt-5 h-12" x-show="selectedCell === null" x-cloak></div>
                    </div>

                    {{-- Check button --}}
                    <div class="mt-6">
                        <button
                            @click="checkSolution()"
                            :disabled="!allFilled() || submitting"
                            :class="allFilled() && !submitting
                                ? 'bg-[#5B2333] hover:bg-[#5B2333]/90 text-white cursor-pointer'
                                : 'bg-[#564D4A]/10 text-[#564D4A]/40 cursor-not-allowed'"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-2xl py-3 text-sm font-bold transition active:scale-[0.99]">
                            <i class="fa-solid fa-check-double"></i>
                            <span x-text="submitting ? 'Controleren...' : (allFilled() ? 'Controleer oplossing' : 'Vul alle vakjes in')"></span>
                        </button>

                    </div>
                </div>
            </div>
        </div>

        {{-- Streak --}}
        <div x-show="isSolved || isFailed" x-cloak>
            <x-game-streak :streak="$streak" />
        </div>
    </div>

    <script>
        function sudokuGame(init) {
            const GIVEN  = init.puzzle.given;   // {idx: value}
            const HIDDEN = init.puzzle.hidden;  // [idx, ...]

            // Build initial grid: given cells filled, hidden cells 0
            const initialGrid = Array(16).fill(0);
            for (const [idx, val] of Object.entries(GIVEN)) {
                initialGrid[parseInt(idx)] = parseInt(val);
            }

            return {
                meId:   parseInt(init.me_id || '0', 10),
                scope:  init.scope || 'global',

                puzzle:     init.puzzle,
                grid:       [...initialGrid],
                selectedCell: null,
                wrongCells: [],

                isSolved: !!init.run.solved,
                isFailed: !!init.run.failed,
                started:  !!(init.run.solved || init.run.failed),

                startedMs: parseInt(init.state.started_ms || '0', 10),
                finalTime: init.run.final_time || null,

                timerText: '00:00',
                _timerId:  null,
                submitting: false,

                leaderboardRows: (init.leaderboard?.rows) || [],
                myRank:          init.leaderboard?.my_rank || null,

                init() {
                    this.$watch('started',  v => { if (v && !window.__gameFinished) window.__gameStarted = true; });
                    this.$watch('isSolved', v => { if (v) window.__gameFinished = true; });
                    this.$watch('isFailed', v => { if (v) window.__gameFinished = true; });

                    if (this.isSolved) {
                        this.timerText = this.finalTime || '00:00';
                    }
                },

                isGiven(idx) {
                    return Object.prototype.hasOwnProperty.call(GIVEN, idx) || Object.prototype.hasOwnProperty.call(GIVEN, String(idx));
                },

                allFilled() {
                    return HIDDEN.every(idx => this.grid[idx] > 0);
                },

                selectCell(idx) {
                    if (this.isGiven(idx) || this.isSolved || this.isFailed || !this.started) return;
                    this.selectedCell = this.selectedCell === idx ? null : idx;
                },

                setValue(val) {
                    if (this.selectedCell === null) return;
                    this.grid[this.selectedCell] = val;
                    this.wrongCells = this.wrongCells.filter(i => i !== this.selectedCell);
                    if (val > 0) this.selectedCell = null;
                },

                cellClass(idx) {
                    const given    = this.isGiven(idx);
                    const selected = this.selectedCell === idx;
                    const wrong    = this.wrongCells.includes(idx);
                    const empty    = !given && this.grid[idx] === 0;

                    // Box borders (thicker lines between 2×2 boxes)
                    const col = idx % 4;
                    const row = Math.floor(idx / 4);
                    let extra = '';
                    if (col === 1) extra += ' sdk-box-right';
                    if (row === 1) extra += ' sdk-box-bottom';

                    if (given)    return 'bg-[#F7F4F3] text-[#564D4A] cursor-default' + extra;
                    if (wrong)    return 'bg-[#CE796B]/15 text-[#CE796B] ring-2 ring-inset ring-[#CE796B]/40 sdk-shake cursor-pointer' + extra;
                    if (selected) return 'bg-[#5B2333]/10 text-[#5B2333] ring-2 ring-inset ring-[#5B2333]/30 cursor-pointer' + extra;
                    if (empty)    return 'bg-white text-[#564D4A]/30 hover:bg-[#F7F4F3] cursor-pointer' + extra;
                    return 'bg-white text-[#564D4A] hover:bg-[#F7F4F3] cursor-pointer' + extra;
                },

                startGame() {
                    this.started   = true;
                    this.startedMs = Date.now();
                    fetch('{{ route("games.mark-started") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': init.csrf },
                        body: JSON.stringify({ game_key: 'sudoku' })
                    });
                    this.startTimer();
                },

                startTimer() {
                    this.stopTimer();
                    if (this.isSolved || this.isFailed) return;
                    const pad = n => String(n).padStart(2, '0');
                    const tick = () => {
                        const diff = Math.max(0, Date.now() - this.startedMs);
                        const s = Math.floor(diff / 1000);
                        this.timerText = `${pad(Math.floor(s/60))}:${pad(s%60)}`;
                    };
                    tick();
                    this._timerId = setInterval(tick, 1000);
                },

                stopTimer() {
                    if (this._timerId) { clearInterval(this._timerId); this._timerId = null; }
                },

                async checkSolution() {
                    if (this.submitting || this.isSolved || this.isFailed || !this.allFilled()) return;
                    this.submitting = true;
                    this.wrongCells = [];

                    try {
                        const res = await fetch(init.routes.check + '?scope=' + encodeURIComponent(this.scope), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': init.csrf,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ grid: this.grid }),
                        });

                        const data = await res.json();
                        if (!data?.ok) return;

                        if (data.streak) {
                            window.dispatchEvent(new CustomEvent('cf:streak', { detail: data.streak }));
                        }

                        if (data.correct) {
                            this.isSolved  = true;
                            this.finalTime = data.final_time;
                            this.timerText = data.final_time || '00:00';
                            this.stopTimer();

                            if (data.leaderboard?.rows) {
                                this.leaderboardRows = data.leaderboard.rows;
                                this.myRank          = data.leaderboard.my_rank ?? this.myRank;
                            }

                            if (window.confetti && document.getElementById('mainConfettiCanvas')) {
                                const cannon = window.confetti.create(document.getElementById('mainConfettiCanvas'), { useWorker: true, resize: true });
                                cannon({ particleCount: 110, angle: 60,  spread: 55, startVelocity: 55, gravity: 1.0, ticks: 320, origin: { x: 0.02, y: 1 } });
                                cannon({ particleCount: 110, angle: 120, spread: 55, startVelocity: 55, gravity: 1.0, ticks: 320, origin: { x: 0.98, y: 1 } });
                            }
                        } else {
                            // Show wrong cells, then mark failed
                            this.wrongCells = data.wrong_cells || [];
                            this.isFailed   = true;
                            this.stopTimer();
                        }

                    } catch (e) {
                        // network error — do nothing
                    } finally {
                        this.submitting = false;
                    }
                },
            };
        }
    </script>
</x-layouts.dashboard>
