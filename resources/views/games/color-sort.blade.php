{{-- resources/views/games/color-sort.blade.php --}}
<x-layouts.dashboard :title="'Color Sort'" active="daily">
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

        $csInit = [
            'me_id' => $meId,
            'scope' => (string)($scope ?? 'global'),
            'puzzle' => $puzzle,
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
                'solve' => route('games.colorsort.solve'),
            ],
            'csrf' => csrf_token(),
        ];
    @endphp

    <style>
        [x-cloak]{display:none!important;}

        .cs-stacks {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 0 8px;
        }
        @media (min-width: 640px) {
            .cs-stacks { gap: 16px; }
        }

        .cs-tube {
            width: 60px;
            display: flex;
            flex-direction: column-reverse;
            align-items: center;
            border: 2.5px solid #D1CBC8;
            border-top: none;
            border-radius: 0 0 14px 14px;
            padding: 4px;
            gap: 4px;
            min-height: 220px;
            background: #F7F4F3;
            cursor: pointer;
            transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
            position: relative;
        }
        @media (min-width: 640px) {
            .cs-tube {
                width: 72px;
                min-height: 264px;
                padding: 5px;
                gap: 5px;
            }
        }
        .cs-tube:hover {
            border-color: #B0A8A4;
        }
        .cs-tube.is-valid-target {
            border-color: #5B2333;
            box-shadow: 0 0 0 3px rgba(91, 35, 51, 0.12);
        }
        .cs-tube.is-selected {
            border-color: #5B2333;
            transform: scale(1.03);
        }

        .cs-block {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 6px;
            transition: transform 0.18s ease, opacity 0.18s;
        }

        .cs-block.is-lifted {
            transform: translateY(-18px);
            z-index: 10;
        }

        .cs-block[data-color="red"]    { background: #EF4444; }
        .cs-block[data-color="blue"]   { background: #3B82F6; }
        .cs-block[data-color="green"]  { background: #22C55E; }

        @keyframes cs-pop {
            0% { transform: scale(1); }
            50% { transform: scale(1.06); }
            100% { transform: scale(1); }
        }
        .anim-cs-pop { animation: cs-pop 0.3s ease-out; }

        @keyframes cs-shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            75% { transform: translateX(4px); }
        }
        .anim-cs-shake { animation: cs-shake 0.3s ease-out; }
    </style>

    <script>
        window.__CS_INIT__ = @json($csInit);
        window.__gameFinished = @json($isSolved || $isFailed);
        window.__gameStarted  = false;
        window.addEventListener('beforeunload', function () {
            if (window.__gameFinished || !window.__gameStarted) return;
            navigator.sendBeacon(
                '{{ route("games.abandon") }}',
                new Blob([JSON.stringify({ game_key: 'color-sort', _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );
        });
    </script>

    <div x-data="colorSort(window.__CS_INIT__)" x-init="init()" class="flex flex-col gap-10 max-w-3xl mx-auto">

        {{-- HEADER --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-[1.5rem] md:text-[1.8rem] font-black text-[#564D4A] tracking-tight leading-tight">
                        <template x-if="!isSolved && !isFailed">
                            <span>Color Sort <span class="text-[#564D4A]/40">#<span x-text="puzzle.number"></span></span></span>
                        </template>
                        <template x-if="isSolved"><span>Gesorteerd! 🎨</span></template>
                        <template x-if="isFailed"><span>Niet afgemaakt 😅</span></template>
                    </h1>
                    <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
                        <template x-if="!isSolved && !isFailed">
                            <span>Sorteer de blokken op kleur. Je kan alleen op <strong>dezelfde kleur</strong> of een <strong>lege stapel</strong> plaatsen.</span>
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
                        <i class="fa-solid fa-arrow-right-arrow-left text-[#5B2333]"></i>
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
                            <p class="text-sm font-extrabold text-[#564D4A] leading-tight">Gesorteerd!</p>
                            <p class="text-xs font-semibold text-[#564D4A]/55 leading-[1.3]">Alle kleuren netjes op een stapel.</p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Tijd</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]" x-text="finalTime || timerText">00:00</p>
                        </div>
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Zetten</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]" x-text="moves">0</p>
                        </div>
                    </div>

                    @include('games.partials.share-score-button', ['gameKey' => 'color-sort'])

                    {{-- Leaderboard --}}
                    <div class="mt-6 rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <h2 class="text-[1.05rem] font-extrabold text-[#564D4A]">Scorebord</h2>
                                <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">Snelste tijden van vandaag.</p>
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
                                                Level <span x-text="row.user.level || 1"></span> · <span x-text="row.moves || 0"></span> zetten
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
                    <div class="w-16 h-16 rounded-2xl bg-[#F59E0B]/10 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-layer-group text-[#F59E0B] text-2xl"></i>
                    </div>
                    <p class="text-xl font-extrabold text-[#564D4A]">Sorteer de kleuren!</p>
                    <p class="mt-2 text-xs font-semibold text-[#564D4A]/55 max-w-md mx-auto">
                        Verplaats blokken zodat elke stapel <strong>één kleur</strong> bevat.
                        Tik op een stapel om het bovenste blok te pakken, tik op een andere om te plaatsen.
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
                    <div class="cs-stacks">
                        <template x-for="(stack, si) in stacks" :key="'stack-'+si">
                            <div class="cs-tube"
                                 :class="{
                                     'is-selected': selectedStack === si,
                                     'is-valid-target': selectedStack !== null && selectedStack !== si && canPlace(si),
                                     'anim-cs-shake': shakeStack === si,
                                 }"
                                 @click="tapStack(si)"
                                 @animationend="if (shakeStack === si) shakeStack = null">
                                <template x-for="(block, bi) in stack" :key="'b-'+si+'-'+bi+'-'+block">
                                    <div class="cs-block"
                                         :data-color="block"
                                         :class="{
                                             'is-lifted': selectedStack === si && bi === stack.length - 1,
                                             'anim-cs-pop': popBlock === si + ':' + bi,
                                         }"
                                         @animationend="if (popBlock === si + ':' + bi) popBlock = null">
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <p class="mt-5 text-[11px] font-semibold text-[#564D4A]/40 text-center">
                        <i class="fa-solid fa-hand-pointer text-[#5B2333]/40 mr-1"></i>
                        Tik op een stapel om een blok te pakken, tik op een andere om te plaatsen
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
        function colorSort(init) {
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

                // Game state
                stacks: [],
                selectedStack: null,
                shakeStack: null,
                popBlock: null,
                stackHeight: init.puzzle.stack_height || 4,

                // Leaderboard
                leaderboardRows: (init.leaderboard && Array.isArray(init.leaderboard.rows)) ? init.leaderboard.rows : [],
                myRank: init.leaderboard ? init.leaderboard.my_rank : null,

                init() {
                    // Deep copy initial stacks
                    this.stacks = JSON.parse(JSON.stringify(init.puzzle.stacks));

                    this.$watch('started', v => { if (v && !window.__gameFinished) window.__gameStarted = true; });
                    this.$watch('isSolved', v => { if (v) window.__gameFinished = true; });
                    this.$watch('isFailed', v => { if (v) window.__gameFinished = true; });

                    if (this.isSolved) {
                        this.moves = init.run.moves || 0;
                    }
                },

                startGame() {
                    this.started = true;
                    this.startedMs = Date.now();
                    this.moves = 0;
                    this.selectedStack = null;
                    this.stacks = JSON.parse(JSON.stringify(init.puzzle.stacks));

                    fetch('{{ route("games.mark-started") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ game_key: 'color-sort' })
                    });

                    this.startTimer();
                },

                tapStack(si) {
                    if (this.isSolved || this.isFailed) return;

                    if (this.selectedStack === null) {
                        // Pick up from this stack
                        if (this.stacks[si].length === 0) return;
                        this.selectedStack = si;
                    } else if (this.selectedStack === si) {
                        // Deselect
                        this.selectedStack = null;
                    } else {
                        // Try to place
                        if (this.canPlace(si)) {
                            const block = this.stacks[this.selectedStack].pop();
                            this.stacks[si].push(block);
                            this.popBlock = si + ':' + (this.stacks[si].length - 1);
                            this.moves++;
                            this.selectedStack = null;

                            // Force reactivity
                            this.stacks = [...this.stacks];

                            // Check win
                            if (this.checkWin()) {
                                setTimeout(() => this.solve(), 400);
                            }
                        } else {
                            // Invalid move — shake
                            this.shakeStack = si;
                        }
                    }
                },

                canPlace(targetIdx) {
                    if (this.selectedStack === null) return false;
                    const target = this.stacks[targetIdx];
                    const source = this.stacks[this.selectedStack];
                    if (source.length === 0) return false;
                    if (target.length >= this.stackHeight) return false;
                    if (target.length === 0) return true;
                    return target[target.length - 1] === source[source.length - 1];
                },

                checkWin() {
                    for (const stack of this.stacks) {
                        if (stack.length === 0) continue;
                        if (stack.length !== this.stackHeight) return false;
                        if (new Set(stack).size !== 1) return false;
                    }
                    return true;
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
