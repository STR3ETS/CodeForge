{{-- resources/views/games/color-match.blade.php --}}
<x-layouts.dashboard :title="'Color Match'" active="daily">
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

        $cmInit = [
            'me_id' => $meId,
            'scope' => (string)($scope ?? 'global'),
            'puzzle' => [
                'number' => (int)($puzzle['number'] ?? 0),
                'date' => (string)($puzzle['date'] ?? date('Y-m-d')),
                'totalRounds' => (int)($puzzle['totalRounds'] ?? 20),
                'rounds' => $puzzle['rounds'] ?? [],
            ],
            'run' => [
                'solved' => $isSolved,
                'failed' => $isFailed,
                'started_ms' => $startMs,
                'final_time' => $finalTime,
                'mistakes' => max(0, (int)($run->attempts ?? 0) - (int)($puzzle['totalRounds'] ?? 20)),
            ],
            'leaderboard' => [
                'rows' => collect($lbRows)->values()->all(),
                'my_rank' => $myRank ?? null,
            ],
            'routes' => [
                'solve' => route('games.colormatch.solve'),
            ],
            'csrf' => csrf_token(),
        ];
    @endphp

    <style>
        [x-cloak]{display:none!important;}

        @keyframes color-flash-correct {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        @keyframes color-flash-wrong {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }
        .anim-correct { animation: color-flash-correct 0.3s ease-out; }
        .anim-wrong { animation: color-flash-wrong 0.4s ease-out; }

        @keyframes word-enter {
            0% { opacity: 0; transform: scale(0.8) translateY(10px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .anim-word-enter { animation: word-enter 0.25s cubic-bezier(.16,1,.3,1); }

        .color-btn {
            transition: transform 0.1s, box-shadow 0.1s;
        }
        .color-btn:active {
            transform: scale(0.95);
        }
    </style>

    <script>
        window.__CM_INIT__ = @json($cmInit);
        window.__gameFinished = @json($isSolved || $isFailed);
        window.__gameStarted  = false;
        window.addEventListener('beforeunload', function () {
            if (window.__gameFinished || !window.__gameStarted) return;
            navigator.sendBeacon(
                '{{ route("games.abandon") }}',
                new Blob([JSON.stringify({ game_key: 'color-match', _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );
        });
    </script>

    <div x-data="colorMatch(window.__CM_INIT__)" x-init="init()" class="flex flex-col gap-8 max-w-3xl mx-auto">

        {{-- HEADER --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-[1.5rem] md:text-[1.8rem] font-black text-[#564D4A] tracking-tight leading-tight">
                        <template x-if="!isSolved && !isFailed">
                            <span>Color Match <span class="text-[#564D4A]/40">#<span x-text="puzzle.number"></span></span></span>
                        </template>
                        <template x-if="isSolved"><span>Goed gedaan! 🎨</span></template>
                        <template x-if="isFailed"><span>Niet afgemaakt 😅</span></template>
                    </h1>
                    <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
                        <template x-if="!isSolved && !isFailed">
                            <span>Klik op de <strong>kleur van de tekst</strong>, niet het woord. <span class="font-black text-[#564D4A]" x-text="puzzle.totalRounds"></span> rondes.</span>
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
                    <span x-show="!isSolved && !isFailed" x-cloak class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-[#564D4A]/6 bg-white text-[#564D4A] text-xs font-semibold">
                        <i class="fa-solid fa-stopwatch text-[#5B2333]"></i>
                        <span x-text="timerText">00:00</span>
                    </span>
                    <span x-show="started && !isSolved && !isFailed" x-cloak class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-[#564D4A]/6 bg-white text-[#564D4A] text-xs font-semibold">
                        <span x-text="currentRound + '/' + puzzle.totalRounds"></span>
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
                            <p class="text-xs font-semibold text-[#564D4A]/55 leading-[1.3]">Alle <span x-text="puzzle.totalRounds"></span> rondes afgerond.</p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Tijd</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]" x-text="finalTime || timerText">00:00</p>
                        </div>
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Fouten</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black" :class="mistakes === 0 ? 'text-[#8E936D]' : 'text-[#CE796B]'" x-text="mistakes">0</p>
                        </div>
                    </div>

                    @include('games.partials.share-score-button', ['gameKey' => 'color-match'])

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
                    <div class="w-16 h-16 rounded-2xl bg-[#5B2333]/10 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-palette text-[#5B2333] text-2xl"></i>
                    </div>
                    <p class="text-xl font-extrabold text-[#564D4A]">Ben je er klaar voor?</p>
                    <p class="mt-2 text-xs font-semibold text-[#564D4A]/55 max-w-md mx-auto">
                        Je ziet een kleurwoord in een <strong>andere kleur</strong>. Klik op de knop die past bij de <strong>kleur van de tekst</strong>, niet het woord zelf. Dit is het Stroop-effect!
                    </p>
                    <div class="mt-4 inline-flex items-center gap-3 px-4 py-3 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6">
                        <span class="text-lg font-black" style="color: #3B82F6;">ROOD</span>
                        <i class="fa-solid fa-arrow-right text-[#564D4A]/30 text-xs"></i>
                        <span class="text-xs font-bold text-[#564D4A]/60">Antwoord = <span class="font-black text-[#3B82F6]">Blauw</span></span>
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

                    {{-- Color word display --}}
                    <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-8 sm:p-12 text-center mb-6"
                         :class="{ 'anim-correct': flashCorrect, 'anim-wrong': flashWrong }">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-[#564D4A]/30 mb-4">Welke kleur heeft deze tekst?</p>
                        <p class="text-4xl sm:text-5xl md:text-6xl font-black select-none anim-word-enter"
                           :key="'round-' + currentRound"
                           :style="'color: ' + currentInkHex">
                            <span x-text="currentWord"></span>
                        </p>
                        <div class="mt-4 flex items-center justify-center gap-3">
                            <span x-show="mistakes > 0" x-cloak class="text-[11px] font-bold text-[#CE796B]">
                                <i class="fa-solid fa-xmark"></i> <span x-text="mistakes"></span> fouten
                            </span>
                        </div>
                    </div>

                    {{-- Answer buttons --}}
                    <div class="grid grid-cols-2 gap-3">
                        <template x-for="opt in currentOptions" :key="opt.idx">
                            <button @click="answer(opt.idx)"
                                class="color-btn cursor-pointer relative rounded-2xl border-2 p-4 sm:p-5 text-center font-bold text-sm sm:text-base transition"
                                :style="'border-color: ' + opt.hex + '20; background: ' + opt.hex + '10; color: ' + opt.hex"
                                :disabled="locked">
                                <span class="relative z-[1]" x-text="opt.name"></span>
                                <div class="absolute inset-0 rounded-2xl opacity-0 hover:opacity-100 transition"
                                     :style="'background: ' + opt.hex + '15'"></div>
                            </button>
                        </template>
                    </div>

                    <p class="mt-4 text-[11px] font-semibold text-[#564D4A]/40 text-center">
                        <i class="fa-solid fa-lightbulb text-[#F59E0B] mr-1"></i>
                        Let op: klik de kleur van de <strong>letters</strong>, niet wat er staat!
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
        function colorMatch(init) {
            return {
                meId: parseInt(init.me_id || '0', 10),
                scope: init.scope || 'global',
                puzzle: init.puzzle,

                isSolved: !!init.run.solved,
                isFailed: !!(init.run && init.run.failed),
                started: !!(init.run.solved || init.run.failed),
                startedMs: parseInt(init.run.started_ms || '0', 10),
                finalTime: init.run.final_time || null,
                mistakes: parseInt(init.run.mistakes || '0', 10),

                timerText: '00:00',
                _timerId: null,

                // Game state
                currentRound: 0,
                totalClicks: 0,
                locked: false,
                flashCorrect: false,
                flashWrong: false,

                // Current round data
                currentWord: '',
                currentInkHex: '',
                currentInkIdx: -1,
                currentOptions: [],

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
                },

                startGame() {
                    this.started = true;
                    this.startedMs = Date.now();
                    this.currentRound = 0;

                    fetch('{{ route("games.mark-started") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ game_key: 'color-match' })
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
                    this.currentWord = round.word;
                    this.currentInkHex = round.inkHex;
                    this.currentInkIdx = round.inkIdx;
                    this.currentOptions = round.options;
                    this.locked = false;
                },

                answer(optIdx) {
                    if (this.locked || this.isSolved) return;

                    this.totalClicks++;

                    if (optIdx === this.currentInkIdx) {
                        // Correct!
                        this.flashCorrect = true;
                        this.locked = true;
                        setTimeout(() => {
                            this.flashCorrect = false;
                            this.currentRound++;
                            this.loadRound();
                        }, 300);
                    } else {
                        // Wrong!
                        this.mistakes++;
                        this.flashWrong = true;
                        this.locked = true;
                        setTimeout(() => {
                            this.flashWrong = false;
                            this.locked = false;
                        }, 400);
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
                            body: JSON.stringify({ total_clicks: this.totalClicks })
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
