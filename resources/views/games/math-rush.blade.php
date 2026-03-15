{{-- resources/views/games/math-rush.blade.php --}}
<x-layouts.dashboard :title="'Reken Rush'" active="daily">
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
        $finalScore = $isSolved ? (int)($run->state['score'] ?? 0) : 0;
        $finalMistakes = $isSolved ? (int)($run->state['mistakes'] ?? 0) : 0;
        $lbRows = collect($topTimes ?? collect())->values()->all();
        $meId = (int) auth()->id();

        $mrInit = [
            'me_id' => $meId,
            'scope' => (string)($scope ?? 'global'),
            'puzzle' => [
                'number' => (int)($puzzle['number'] ?? 0),
                'date' => (string)($puzzle['date'] ?? date('Y-m-d')),
                'totalRounds' => (int)($puzzle['totalRounds'] ?? 15),
                'rounds' => $puzzle['rounds'] ?? [],
            ],
            'run' => [
                'solved' => $isSolved,
                'failed' => $isFailed,
                'started_ms' => $startMs,
                'final_time' => $finalTime,
                'score' => $finalScore,
                'mistakes' => $finalMistakes,
            ],
            'leaderboard' => [
                'rows' => collect($lbRows)->values()->all(),
                'my_rank' => $myRank ?? null,
            ],
            'routes' => [
                'solve' => route('games.mathrush.solve'),
            ],
            'csrf' => csrf_token(),
        ];
    @endphp

    <style>
        [x-cloak]{display:none!important;}

        @keyframes mr-correct {
            0% { transform: scale(1); }
            50% { transform: scale(1.04); }
            100% { transform: scale(1); }
        }
        @keyframes mr-wrong {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }
        .anim-correct { animation: mr-correct 0.3s ease-out; }
        .anim-wrong { animation: mr-wrong 0.4s ease-out; }

        @keyframes mr-question-enter {
            0% { opacity: 0; transform: scale(0.85) translateY(8px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .anim-question { animation: mr-question-enter 0.25s cubic-bezier(.16,1,.3,1); }

        @keyframes mr-score-pop {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }
        .anim-score-pop { animation: mr-score-pop 0.3s ease-out; }

        .mr-option {
            transition: transform 0.1s, box-shadow 0.1s;
        }
        .mr-option:active {
            transform: scale(0.95);
        }

        .mr-timer-bar {
            transition: width 0.1s linear;
        }
    </style>

    <script>
        window.__MR_INIT__ = @json($mrInit);
        window.__gameFinished = @json($isSolved || $isFailed);
        window.__gameStarted  = false;
        window.addEventListener('beforeunload', function () {
            if (window.__gameFinished || !window.__gameStarted) return;
            navigator.sendBeacon(
                '{{ route("games.abandon") }}',
                new Blob([JSON.stringify({ game_key: 'math-rush', _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );
        });
    </script>

    <div x-data="mathRush(window.__MR_INIT__)" x-init="init()" class="flex flex-col gap-10 max-w-3xl mx-auto">

        {{-- HEADER --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-[1.5rem] md:text-[1.8rem] font-black text-[#564D4A] tracking-tight leading-tight">
                        <template x-if="!isSolved && !isFailed">
                            <span>Reken Rush <span class="text-[#564D4A]/40">#<span x-text="puzzle.number"></span></span></span>
                        </template>
                        <template x-if="isSolved"><span>Goed gedaan! 🔢</span></template>
                        <template x-if="isFailed"><span>Niet afgemaakt 😅</span></template>
                    </h1>
                    <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
                        <template x-if="!isSolved && !isFailed">
                            <span>Los <span class="font-black text-[#564D4A]" x-text="puzzle.totalRounds"></span> sommen zo snel mogelijk op. Snelheid + nauwkeurigheid = score!</span>
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
                        <i class="fa-solid fa-star text-[#EAB308]"></i>
                        <span x-text="score"></span>
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
                            <p class="text-sm font-extrabold text-[#564D4A] leading-tight">Voltooid</p>
                            <p class="text-xs font-semibold text-[#564D4A]/55 leading-[1.3]">Alle <span x-text="puzzle.totalRounds"></span> sommen opgelost.</p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-4 text-center">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Tijd</p>
                            <p class="mt-2 text-[1.5rem] leading-none font-black text-[#564D4A]" x-text="finalTime || timerText">00:00</p>
                        </div>
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-4 text-center">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Score</p>
                            <p class="mt-2 text-[1.5rem] leading-none font-black text-[#EAB308]" x-text="score">0</p>
                        </div>
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-4 text-center">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Fouten</p>
                            <p class="mt-2 text-[1.5rem] leading-none font-black" :class="mistakes === 0 ? 'text-[#8E936D]' : 'text-[#CE796B]'" x-text="mistakes">0</p>
                        </div>
                    </div>

                    @include('games.partials.share-score-button', ['gameKey' => 'math-rush'])

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
                                                Level <span x-text="row.user.level || 1"></span> · <span x-text="row.mistakes || 0"></span> fouten
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
                    <div class="w-16 h-16 rounded-2xl bg-[#3B82F6]/10 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-calculator text-[#3B82F6] text-2xl"></i>
                    </div>
                    <p class="text-xl font-extrabold text-[#564D4A]">Ben je er klaar voor?</p>
                    <p class="mt-2 text-xs font-semibold text-[#564D4A]/55 max-w-md mx-auto">
                        Los <strong x-text="puzzle.totalRounds"></strong> sommen zo snel mogelijk op.
                        De moeilijkheid neemt toe: <strong>makkelijk</strong> → <strong>gemiddeld</strong> → <strong>moeilijk</strong>.
                        Fouten kosten tijd!
                    </p>
                    <div class="mt-4 inline-flex items-center gap-4 px-5 py-3 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-xs font-semibold text-[#564D4A]/60">
                        <span class="flex items-center gap-2">
                            <span class="px-2 py-1 rounded-lg bg-[#22C55E]/10 text-[#22C55E] font-black">+−</span> Makkelijk
                        </span>
                        <i class="fa-solid fa-arrow-right text-[#564D4A]/20"></i>
                        <span class="flex items-center gap-2">
                            <span class="px-2 py-1 rounded-lg bg-[#EAB308]/10 text-[#EAB308] font-black">×÷</span> Moeilijk
                        </span>
                    </div>
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
                    {{-- Progress bar --}}
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#564D4A]/45">Voortgang</span>
                            <span class="text-[11px] font-bold text-[#564D4A]/45"><span x-text="currentRound"></span>/<span x-text="puzzle.totalRounds"></span></span>
                        </div>
                        <div class="h-2 rounded-full bg-[#564D4A]/8 overflow-hidden">
                            <div class="h-full bg-[#5B2333] rounded-full transition-all duration-300 ease-out"
                                 :style="'width: ' + (currentRound / puzzle.totalRounds * 100) + '%'"></div>
                        </div>
                    </div>

                    {{-- Difficulty indicator --}}
                    <div class="flex items-center justify-center gap-2 mb-4">
                        <template x-if="currentTier === 0">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#22C55E]/10 text-[#22C55E] text-[11px] font-bold">
                                <i class="fa-solid fa-circle text-[6px]"></i> Makkelijk
                            </span>
                        </template>
                        <template x-if="currentTier === 1">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#EAB308]/10 text-[#EAB308] text-[11px] font-bold">
                                <i class="fa-solid fa-circle text-[6px]"></i> Gemiddeld
                            </span>
                        </template>
                        <template x-if="currentTier === 2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#CE796B]/10 text-[#CE796B] text-[11px] font-bold">
                                <i class="fa-solid fa-circle text-[6px]"></i> Moeilijk
                            </span>
                        </template>
                    </div>

                    {{-- Question display --}}
                    <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-6 sm:p-10 text-center mb-6"
                         :class="{ 'anim-correct': flashCorrect, 'anim-wrong': flashWrong }">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-[#564D4A]/30 mb-3">Wat is het antwoord?</p>
                        <div class="anim-question" :key="'q-' + currentRound">
                            <p class="text-4xl sm:text-5xl md:text-6xl font-black text-[#564D4A] select-none">
                                <span x-text="currentA"></span>
                                <span class="mx-2" :class="{
                                    'text-[#3B82F6]': currentOp === '+',
                                    'text-[#CE796B]': currentOp === '-',
                                    'text-[#EAB308]': currentOp === '×',
                                    'text-[#8B5CF6]': currentOp === '÷',
                                }" x-text="currentOp"></span>
                                <span x-text="currentB"></span>
                                <span class="text-[#564D4A]/25 ml-1">=</span>
                                <span class="text-[#564D4A]/25 ml-1">?</span>
                            </p>
                        </div>
                        <div class="mt-4 flex items-center justify-center gap-4">
                            <span class="text-[11px] font-bold text-[#EAB308]">
                                <i class="fa-solid fa-star mr-0.5"></i> <span x-text="score"></span> punten
                            </span>
                            <span x-show="mistakes > 0" x-cloak class="text-[11px] font-bold text-[#CE796B]">
                                <i class="fa-solid fa-xmark mr-0.5"></i> <span x-text="mistakes"></span> fouten
                            </span>
                        </div>
                    </div>

                    {{-- Answer buttons --}}
                    <div class="grid grid-cols-2 gap-3">
                        <template x-for="(opt, idx) in currentOptions" :key="'opt-' + currentRound + '-' + idx">
                            <button @click="answer(idx)"
                                class="mr-option cursor-pointer rounded-2xl border-2 border-[#564D4A]/10 bg-white hover:border-[#5B2333]/30 hover:bg-[#5B2333]/5 p-4 sm:p-5 text-center font-black text-xl sm:text-2xl text-[#564D4A] transition"
                                :disabled="locked"
                                :class="{
                                    'border-[#22C55E] bg-[#22C55E]/10 text-[#22C55E]': showResult && idx === correctIdx,
                                    'border-[#CE796B] bg-[#CE796B]/10 text-[#CE796B]': showResult && lastAnswer === idx && idx !== correctIdx,
                                }">
                                <span x-text="opt"></span>
                            </button>
                        </template>
                    </div>

                    <p class="mt-4 text-[11px] font-semibold text-[#564D4A]/40 text-center">
                        <i class="fa-solid fa-keyboard text-[#564D4A]/25 mr-1"></i>
                        Tip: gebruik toetsen <strong>1-4</strong> voor sneller antwoorden
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
        function mathRush(init) {
            return {
                meId: parseInt(init.me_id || '0', 10),
                scope: init.scope || 'global',
                puzzle: init.puzzle,

                isSolved: !!init.run.solved,
                isFailed: !!(init.run && init.run.failed),
                started: !!(init.run.solved || init.run.failed),
                startedMs: parseInt(init.run.started_ms || '0', 10),
                finalTime: init.run.final_time || null,
                score: parseInt(init.run.score || '0', 10),
                mistakes: parseInt(init.run.mistakes || '0', 10),

                timerText: '00:00',
                _timerId: null,

                // Game state
                currentRound: 0,
                locked: false,
                flashCorrect: false,
                flashWrong: false,
                showResult: false,
                lastAnswer: -1,
                answers: [],

                // Current round data
                currentA: 0,
                currentB: 0,
                currentOp: '+',
                currentOptions: [],
                correctIdx: -1,
                currentTier: 0,

                // Leaderboard
                leaderboardRows: (init.leaderboard && Array.isArray(init.leaderboard.rows)) ? init.leaderboard.rows : [],
                myRank: init.leaderboard ? init.leaderboard.my_rank : null,

                init() {
                    this.$watch('started', v => { if (v && !window.__gameFinished) window.__gameStarted = true; });
                    this.$watch('isSolved', v => { if (v) window.__gameFinished = true; });
                    this.$watch('isFailed', v => { if (v) window.__gameFinished = true; });

                    if (this.isSolved) {
                        this.currentRound = this.puzzle.totalRounds;
                    }

                    // Keyboard shortcuts (1-4)
                    document.addEventListener('keydown', (e) => {
                        if (this.isSolved || this.isFailed || !this.started || this.locked) return;
                        const num = parseInt(e.key);
                        if (num >= 1 && num <= 4) {
                            e.preventDefault();
                            this.answer(num - 1);
                        }
                    });
                },

                startGame() {
                    this.started = true;
                    this.startedMs = Date.now();
                    this.currentRound = 0;
                    this.score = 0;
                    this.mistakes = 0;
                    this.answers = [];

                    fetch('{{ route("games.mark-started") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ game_key: 'math-rush' })
                    });

                    this.startTimer();
                    this.loadRound();
                },

                loadRound() {
                    if (this.currentRound >= this.puzzle.totalRounds) {
                        this.solve();
                        return;
                    }

                    const round = this.puzzle.rounds[this.currentRound];
                    this.currentA = round.a;
                    this.currentB = round.b;
                    this.currentOp = round.op;
                    this.currentOptions = round.options;
                    this.correctIdx = round.correctIdx;
                    this.currentTier = round.tier;
                    this.locked = false;
                    this.showResult = false;
                    this.lastAnswer = -1;
                },

                answer(idx) {
                    if (this.locked || this.isSolved || idx < 0 || idx >= this.currentOptions.length) return;

                    this.lastAnswer = idx;
                    this.locked = true;

                    if (idx === this.correctIdx) {
                        // Correct!
                        this.flashCorrect = true;
                        this.showResult = true;

                        // Score: base points + speed bonus
                        const elapsed = Date.now() - this.startedMs;
                        const tierBonus = [10, 20, 30][this.currentTier] || 10;
                        this.score += tierBonus;

                        this.answers.push({ round: this.currentRound, correct: true, answered: idx });

                        setTimeout(() => {
                            this.flashCorrect = false;
                            this.showResult = false;
                            this.currentRound++;
                            this.loadRound();
                        }, 400);
                    } else {
                        // Wrong!
                        this.mistakes++;
                        this.flashWrong = true;
                        this.showResult = true;

                        this.answers.push({ round: this.currentRound, correct: false, answered: idx });

                        setTimeout(() => {
                            this.flashWrong = false;
                            this.showResult = false;
                            this.locked = false;
                            this.lastAnswer = -1;
                        }, 600);
                    }
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
                    this._timerId = setInterval(tick, 100);
                },

                stopTimer() {
                    if (this._timerId) {
                        clearInterval(this._timerId);
                        this._timerId = null;
                    }
                },

                async solve() {
                    const durationMs = Date.now() - this.startedMs;
                    this.stopTimer();

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
                            body: JSON.stringify({
                                duration_ms: durationMs,
                                score: this.score,
                                mistakes: this.mistakes,
                                answers: this.answers,
                            })
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
