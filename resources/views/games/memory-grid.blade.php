{{-- resources/views/games/memory-grid.blade.php --}}
<x-layouts.dashboard :title="'Memory Grid'" active="daily">
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

        $mgInit = [
            'me_id' => $meId,
            'scope' => (string)($scope ?? 'global'),
            'puzzle' => [
                'number' => (int)($puzzle['number'] ?? 0),
                'date' => (string)($puzzle['date'] ?? date('Y-m-d')),
                'cols' => (int)($puzzle['cols'] ?? 4),
                'rows' => (int)($puzzle['rows'] ?? 4),
                'pairCount' => (int)($puzzle['pairCount'] ?? 8),
                'cards' => $puzzle['cards'] ?? [],
                'memorizeTime' => (int)($puzzle['memorizeTime'] ?? 4),
            ],
            'run' => [
                'solved' => $isSolved,
                'failed' => $isFailed,
                'started_ms' => $startMs,
                'final_time' => $finalTime,
                'attempts' => (int)($run->attempts ?? 0),
            ],
            'leaderboard' => [
                'rows' => collect($lbRows)->values()->all(),
                'my_rank' => $myRank ?? null,
            ],
            'routes' => [
                'solve' => route('games.memorygrid.solve'),
            ],
            'csrf' => csrf_token(),
        ];
    @endphp

    <style>
        [x-cloak]{display:none!important;}

        .memory-card {
            perspective: 800px;
            cursor: pointer;
        }
        .memory-card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.45s cubic-bezier(.4,.2,.2,1);
            transform-style: preserve-3d;
        }
        .memory-card.flipped .memory-card-inner {
            transform: rotateY(180deg);
        }
        .memory-card-front,
        .memory-card-back {
            position: absolute;
            inset: 0;
            border-radius: 0.75rem;
            backface-visibility: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .memory-card-front {
            transform: rotateY(180deg);
        }
        .memory-card-back {
            background: linear-gradient(135deg, #5B2333 0%, #7a3349 100%);
            border: 2px solid rgba(91,35,51,0.15);
        }
        .memory-card-back::after {
            content: '?';
            font-size: 1.25rem;
            font-weight: 900;
            color: rgba(255,255,255,0.25);
        }
        .memory-card.matched .memory-card-inner {
            transform: rotateY(180deg);
        }
        .memory-card.matched .memory-card-front {
            background: #f0fdf4;
            border: 2px solid #86efac;
        }
        .memory-card.wrong .memory-card-front {
            background: #fef2f2;
            border: 2px solid #fca5a5;
        }
        .memory-card-front {
            background: white;
            border: 2px solid rgba(86,77,74,0.08);
        }

        @keyframes card-match-pop {
            0% { transform: rotateY(180deg) scale(1); }
            50% { transform: rotateY(180deg) scale(1.1); }
            100% { transform: rotateY(180deg) scale(1); }
        }
        .memory-card.matched .memory-card-inner {
            animation: card-match-pop 0.35s ease-out;
        }

        @keyframes memorize-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(91,35,51,0.2); }
            50% { box-shadow: 0 0 0 6px rgba(91,35,51,0.05); }
        }
        .memorize-active {
            animation: memorize-pulse 1.5s ease-in-out infinite;
        }
    </style>

    <script>
        window.__MG_INIT__ = @json($mgInit);
        window.__gameFinished = @json($isSolved || $isFailed);
        window.__gameStarted  = false;
        window.addEventListener('beforeunload', function () {
            if (window.__gameFinished || !window.__gameStarted) return;
            navigator.sendBeacon(
                '{{ route("games.abandon") }}',
                new Blob([JSON.stringify({ game_key: 'memory-grid', _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );
        });
    </script>

    <div x-data="memoryGrid(window.__MG_INIT__)" x-init="init()" class="flex flex-col gap-10 max-w-3xl mx-auto">

        {{-- HEADER --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-[1.5rem] md:text-[1.8rem] font-black text-[#564D4A] tracking-tight leading-tight">
                        <template x-if="!isSolved && !isFailed">
                            <span>Memory Grid <span class="text-[#564D4A]/40">#<span x-text="puzzle.number"></span></span></span>
                        </template>
                        <template x-if="isSolved"><span>Alle paren gevonden! 🧠</span></template>
                        <template x-if="isFailed"><span>Niet afgemaakt 😅</span></template>
                    </h1>
                    <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
                        <template x-if="!isSolved && !isFailed">
                            <span>Onthoud de posities en vind alle <span class="font-black text-[#564D4A]" x-text="puzzle.pairCount"></span> paren zo snel mogelijk.</span>
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
                    <span x-show="playing && !memorizing" x-cloak class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-[#564D4A]/6 bg-white text-[#564D4A] text-xs font-semibold">
                        <i class="fa-solid fa-hand-pointer text-[#5B2333]"></i>
                        <span x-text="attempts">0</span> zetten
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
                            <p class="text-xs font-semibold text-[#564D4A]/55 leading-[1.3]">Alle paren gevonden.</p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Tijd</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]" x-text="finalTime || timerText">00:00</p>
                        </div>
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Zetten</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]" x-text="attempts">0</p>
                        </div>
                    </div>

                    @include('games.partials.share-score-button', ['gameKey' => 'memory-grid'])

                    {{-- Leaderboard --}}
                    <div class="mt-6 rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <h2 class="text-[1.05rem] font-extrabold text-[#564D4A]">Scorebord</h2>
                                <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                                    Minste zetten van vandaag.
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
                                                Level <span x-text="row.user.level || 1"></span> · <span x-text="row.time"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-xs font-extrabold text-[#564D4A] shrink-0">
                                        <i class="fa-solid fa-hand-pointer text-[#5B2333]"></i>
                                        <span x-text="row.attempts"></span> zetten
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
                        <i class="fa-solid fa-brain text-[#5B2333] text-2xl"></i>
                    </div>
                    <p class="text-xl font-extrabold text-[#564D4A]">Ben je er klaar voor?</p>
                    <p class="mt-2 text-xs font-semibold text-[#564D4A]/55 max-w-sm mx-auto">
                        Je krijgt <span class="font-black text-[#564D4A]" x-text="puzzle.memorizeTime"></span> seconden om alle kaarten te onthouden. Daarna draai je ze om en zoek je de paren.
                    </p>
                    <button @click="startGame()"
                        class="cursor-pointer mt-6 inline-flex items-center gap-2 px-8 py-3 rounded-2xl bg-[#5B2333] text-white text-sm font-bold hover:bg-[#5B2333]/90 transition active:scale-[0.98]">
                        <i class="fa-solid fa-play"></i>
                        Spel starten
                    </button>
                </div>

                {{-- Grid --}}
                <div x-show="started" x-cloak>
                    {{-- Memorize countdown --}}
                    <div x-show="memorizing" x-cloak class="mb-4">
                        <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-[#F59E0B]/10 border border-[#F59E0B]/15">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-eye text-[#B88B2A] text-sm"></i>
                                <span class="text-xs font-bold text-[#B88B2A]">Onthoud de posities!</span>
                            </div>
                            <span class="text-sm font-black text-[#B88B2A]" x-text="memorizeCountdown + 's'"></span>
                        </div>
                        <div class="mt-2 h-1.5 rounded-full bg-[#F59E0B]/15 overflow-hidden">
                            <div class="h-full bg-[#F59E0B] rounded-full transition-all duration-1000 ease-linear"
                                 :style="'width: ' + (memorizeCountdown / puzzle.memorizeTime * 100) + '%'"></div>
                        </div>
                    </div>

                    {{-- Card grid --}}
                    <div class="grid gap-2 sm:gap-3"
                         :style="'grid-template-columns: repeat(' + puzzle.cols + ', minmax(0, 1fr))'">
                        <template x-for="(card, idx) in cards" :key="card.pos">
                            <div class="memory-card aspect-square"
                                 :class="{
                                     'flipped': card.flipped || card.matched || memorizing,
                                     'matched': card.matched,
                                     'wrong': card.wrong,
                                 }"
                                 @click="flipCard(idx)">
                                <div class="memory-card-inner">
                                    <div class="memory-card-back"></div>
                                    <div class="memory-card-front">
                                        <span class="text-2xl sm:text-3xl md:text-4xl select-none" x-text="card.emoji"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Playing hint --}}
                    <div x-show="!memorizing" x-cloak class="mt-4 flex items-center justify-between gap-3">
                        <p class="text-[11px] font-semibold text-[#564D4A]/45">
                            <i class="fa-solid fa-lightbulb text-[#F59E0B] mr-1"></i>
                            Klik op twee kaarten om ze om te draaien. Vind alle paren!
                        </p>
                        <span class="text-[11px] font-bold text-[#564D4A]/35" x-text="matchedPairs + '/' + puzzle.pairCount + ' paren'"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- STREAK --}}
        <div x-show="isSolved || isFailed" x-cloak>
            <x-game-streak :streak="$streak" />
        </div>
    </div>

    <script>
        function memoryGrid(init) {
            return {
                meId: parseInt(init.me_id || '0', 10),
                scope: init.scope || 'global',
                puzzle: init.puzzle,

                isSolved: !!init.run.solved,
                isFailed: !!(init.run && init.run.failed),
                started: !!(init.run.solved || init.run.failed),
                startedMs: parseInt(init.run.started_ms || '0', 10),
                finalTime: init.run.final_time || null,
                attempts: parseInt(init.run.attempts || '0', 10),

                timerText: '00:00',
                _timerId: null,

                // Game state
                cards: [],
                memorizing: false,
                memorizeCountdown: 0,
                playing: false,
                matchedPairs: 0,
                flippedIndices: [],
                lockBoard: false,

                // Leaderboard
                leaderboardRows: (init.leaderboard && Array.isArray(init.leaderboard.rows)) ? init.leaderboard.rows : [],
                myRank: init.leaderboard ? init.leaderboard.my_rank : null,

                init() {
                    // Build cards from puzzle data
                    this.cards = this.puzzle.cards.map(c => ({
                        ...c,
                        flipped: false,
                        matched: false,
                        wrong: false,
                    }));

                    this.$watch('started', v => { if (v && !window.__gameFinished) window.__gameStarted = true; });
                    this.$watch('isSolved', v => { if (v) window.__gameFinished = true; });
                    this.$watch('isFailed', v => { if (v) window.__gameFinished = true; });

                    if (this.isSolved) {
                        this.matchedPairs = this.puzzle.pairCount;
                        this.cards.forEach(c => { c.flipped = true; c.matched = true; });
                    }
                },

                startGame() {
                    this.started = true;
                    this.startedMs = Date.now();

                    fetch('{{ route("games.mark-started") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ game_key: 'memory-grid' })
                    });

                    // Memorize phase: show all cards
                    this.memorizing = true;
                    this.memorizeCountdown = this.puzzle.memorizeTime;

                    const countdownInterval = setInterval(() => {
                        this.memorizeCountdown--;
                        if (this.memorizeCountdown <= 0) {
                            clearInterval(countdownInterval);
                            this.memorizing = false;
                            this.playing = true;
                            this.startTimer();
                        }
                    }, 1000);
                },

                flipCard(idx) {
                    if (this.memorizing || this.lockBoard || this.isSolved) return;

                    const card = this.cards[idx];
                    if (card.flipped || card.matched) return;

                    card.flipped = true;
                    this.flippedIndices.push(idx);

                    if (this.flippedIndices.length === 2) {
                        this.attempts++;
                        this.lockBoard = true;

                        const [a, b] = this.flippedIndices;
                        const cardA = this.cards[a];
                        const cardB = this.cards[b];

                        if (cardA.pairId === cardB.pairId) {
                            // Match!
                            cardA.matched = true;
                            cardB.matched = true;
                            this.matchedPairs++;
                            this.flippedIndices = [];
                            this.lockBoard = false;

                            if (this.matchedPairs === this.puzzle.pairCount) {
                                this.solve();
                            }
                        } else {
                            // No match — flash wrong, then flip back
                            cardA.wrong = true;
                            cardB.wrong = true;

                            setTimeout(() => {
                                cardA.flipped = false;
                                cardB.flipped = false;
                                cardA.wrong = false;
                                cardB.wrong = false;
                                this.flippedIndices = [];
                                this.lockBoard = false;
                            }, 700);
                        }
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
                            body: JSON.stringify({ attempts: this.attempts })
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
