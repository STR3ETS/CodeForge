{{-- resources/views/games/reaction-time.blade.php --}}
<x-layouts.dashboard :title="'Reactietijd'" active="daily">
    @php
        $isSolved = (bool)($run->solved ?? false);
        $isFailed = (bool)(!$isSolved && !empty($run->finished_at));

        $finalAvgMs = $isSolved ? (int)($run->duration_ms ?? 0) : null;
        $reactionTimes = ($isSolved && !empty($run->state['reaction_times'])) ? $run->state['reaction_times'] : [];

        $lbRows = collect($topTimes ?? collect())->values()->all();
        $meId = (int) auth()->id();

        $rtInit = [
            'me_id' => $meId,
            'scope' => (string)($scope ?? 'global'),
            'puzzle' => [
                'number' => (int)($puzzle['number'] ?? 0),
                'date' => (string)($puzzle['date'] ?? date('Y-m-d')),
                'totalRounds' => (int)($puzzle['totalRounds'] ?? 5),
                'waitTimes' => $puzzle['waitTimes'] ?? [],
            ],
            'run' => [
                'solved' => $isSolved,
                'failed' => $isFailed,
                'avg_ms' => $finalAvgMs,
                'reaction_times' => $reactionTimes,
            ],
            'leaderboard' => [
                'rows' => collect($lbRows)->values()->all(),
                'my_rank' => $myRank ?? null,
            ],
            'routes' => [
                'solve' => route('games.reactiontime.solve'),
            ],
            'csrf' => csrf_token(),
        ];
    @endphp

    <style>
        [x-cloak]{display:none!important;}

        @keyframes rt-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.03); }
        }
        .anim-pulse { animation: rt-pulse 0.3s ease-out; }

        @keyframes rt-go-flash {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }
        .anim-go { animation: rt-go-flash 0.2s cubic-bezier(.16,1,.3,1); }

        @keyframes rt-early-shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-5px); }
            80% { transform: translateX(5px); }
        }
        .anim-early { animation: rt-early-shake 0.4s ease-out; }

        .rt-zone {
            transition: background-color 0.15s ease, border-color 0.15s ease;
            cursor: pointer;
            user-select: none;
            -webkit-user-select: none;
        }
    </style>

    <script>
        window.__RT_INIT__ = @json($rtInit);
        window.__gameFinished = @json($isSolved || $isFailed);
        window.__gameStarted  = false;
        window.addEventListener('beforeunload', function () {
            if (window.__gameFinished || !window.__gameStarted) return;
            navigator.sendBeacon(
                '{{ route("games.abandon") }}',
                new Blob([JSON.stringify({ game_key: 'reaction-time', _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );
        });
    </script>

    <div x-data="reactionTime(window.__RT_INIT__)" x-init="init()" class="flex flex-col gap-10 max-w-3xl mx-auto">

        {{-- HEADER --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-[1.5rem] md:text-[1.8rem] font-black text-[#564D4A] tracking-tight leading-tight">
                        <template x-if="!isSolved && !isFailed">
                            <span>Reactietijd <span class="text-[#564D4A]/40">#<span x-text="puzzle.number"></span></span></span>
                        </template>
                        <template x-if="isSolved"><span>Goed gedaan! ⚡</span></template>
                        <template x-if="isFailed"><span>Niet afgemaakt 😅</span></template>
                    </h1>
                    <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
                        <template x-if="!isSolved && !isFailed">
                            <span>Klik zo snel mogelijk als het scherm <strong class="text-[#22C55E]">groen</strong> wordt. <span class="font-black text-[#564D4A]" x-text="puzzle.totalRounds"></span> rondes.</span>
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
                    <span x-show="started && !isSolved && !isFailed" x-cloak class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-[#564D4A]/6 bg-white text-[#564D4A] text-xs font-semibold">
                        Ronde <span x-text="currentRound + 1"></span>/<span x-text="puzzle.totalRounds"></span>
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

                    {{-- Results --}}
                    <div class="mt-4 rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Gemiddelde reactietijd</p>
                        <p class="mt-2 text-[2.2rem] leading-none font-black" :class="avgMs <= 250 ? 'text-[#22C55E]' : avgMs <= 350 ? 'text-[#EAB308]' : 'text-[#CE796B]'">
                            <span x-text="avgMs"></span><span class="text-lg">ms</span>
                        </p>
                    </div>

                    {{-- Individual times --}}
                    <div class="mt-3 grid grid-cols-5 gap-2" x-show="reactionTimes.length > 0">
                        <template x-for="(rt, idx) in reactionTimes" :key="idx">
                            <div class="rounded-xl border border-[#564D4A]/6 bg-white p-3 text-center">
                                <p class="text-[10px] font-bold text-[#564D4A]/40">R<span x-text="idx + 1"></span></p>
                                <p class="text-sm font-black text-[#564D4A]" x-text="rt + 'ms'"></p>
                            </div>
                        </template>
                    </div>

                    @include('games.partials.share-score-button', ['gameKey' => 'reaction-time'])

                    {{-- Leaderboard --}}
                    <div class="mt-6 rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <h2 class="text-[1.05rem] font-extrabold text-[#564D4A]">Scorebord</h2>
                                <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                                    Snelste reactietijden van vandaag.
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
                            <template x-for="(row, idx) in leaderboardRows" :key="row.user.id + ':' + row.avg_ms">
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
                                                Level <span x-text="row.user.level || 1"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-xs font-extrabold text-[#564D4A] shrink-0">
                                        <i class="fa-solid fa-bolt text-[#EAB308]"></i>
                                        <span x-text="row.avg_ms + 'ms'"></span>
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
                    <div class="w-16 h-16 rounded-2xl bg-[#EAB308]/10 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-bolt text-[#EAB308] text-2xl"></i>
                    </div>
                    <p class="text-xl font-extrabold text-[#564D4A]">Test je reactiesnelheid!</p>
                    <p class="mt-2 text-xs font-semibold text-[#564D4A]/55 max-w-md mx-auto">
                        Wacht tot het scherm <strong class="text-[#22C55E]">groen</strong> wordt en klik dan zo snel mogelijk.
                        Je speelt <strong x-text="puzzle.totalRounds"></strong> rondes — je gemiddelde reactietijd is je score.
                    </p>
                    <div class="mt-4 inline-flex items-center gap-4 px-5 py-3 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-xs font-semibold text-[#564D4A]/60">
                        <span class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full bg-[#CE796B]"></span> Wacht...
                        </span>
                        <i class="fa-solid fa-arrow-right text-[#564D4A]/20"></i>
                        <span class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full bg-[#22C55E]"></span> Klik!
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
                    {{-- Progress dots --}}
                    <div class="flex items-center justify-center gap-3 mb-6">
                        <template x-for="(rt, idx) in reactionTimes" :key="'dot-'+idx">
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-black border-2"
                                     :class="rt !== null ? 'bg-[#22C55E]/10 border-[#22C55E]/30 text-[#22C55E]' : (idx === currentRound ? 'bg-[#EAB308]/10 border-[#EAB308]/30 text-[#EAB308]' : 'bg-[#564D4A]/5 border-[#564D4A]/10 text-[#564D4A]/30')">
                                    <span x-text="rt !== null ? rt : (idx + 1)"></span>
                                </div>
                                <span x-show="rt !== null" x-cloak class="text-[9px] font-bold text-[#564D4A]/40">ms</span>
                            </div>
                        </template>
                    </div>

                    {{-- Reaction zone --}}
                    <div @click="handleClick()"
                         class="rt-zone rounded-2xl border-2 p-8 sm:p-12 md:p-16 text-center select-none min-h-[250px] sm:min-h-[300px] flex flex-col items-center justify-center"
                         :class="{
                             'border-[#564D4A]/10 bg-[#F7F4F3]': phase === 'idle',
                             'border-[#CE796B]/30 bg-[#CE796B]': phase === 'waiting',
                             'border-[#22C55E]/30 bg-[#22C55E] anim-go': phase === 'go',
                             'border-[#3B82F6]/30 bg-[#3B82F6]/10': phase === 'result',
                             'border-[#CE796B]/30 bg-[#CE796B]/10 anim-early': phase === 'early',
                         }">

                        {{-- Idle --}}
                        <template x-if="phase === 'idle'">
                            <div>
                                <p class="text-lg font-extrabold text-[#564D4A]/40">Klik om te beginnen</p>
                            </div>
                        </template>

                        {{-- Waiting (red) --}}
                        <template x-if="phase === 'waiting'">
                            <div>
                                <i class="fa-solid fa-hourglass-half text-4xl text-white/80 mb-3"></i>
                                <p class="text-2xl sm:text-3xl font-black text-white">Wacht...</p>
                                <p class="mt-2 text-sm font-semibold text-white/60">Niet klikken!</p>
                            </div>
                        </template>

                        {{-- Go (green) --}}
                        <template x-if="phase === 'go'">
                            <div>
                                <i class="fa-solid fa-bolt text-5xl text-white/90 mb-3"></i>
                                <p class="text-3xl sm:text-4xl font-black text-white">KLIK NU!</p>
                            </div>
                        </template>

                        {{-- Result --}}
                        <template x-if="phase === 'result'">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-[#3B82F6]/60 mb-2">Reactietijd</p>
                                <p class="text-4xl sm:text-5xl font-black text-[#3B82F6]">
                                    <span x-text="lastReactionTime"></span><span class="text-xl">ms</span>
                                </p>
                                <p class="mt-3 text-sm font-semibold text-[#564D4A]/50">Klik voor volgende ronde</p>
                            </div>
                        </template>

                        {{-- Too early --}}
                        <template x-if="phase === 'early'">
                            <div>
                                <i class="fa-solid fa-xmark text-4xl text-[#CE796B] mb-3"></i>
                                <p class="text-2xl font-black text-[#CE796B]">Te vroeg!</p>
                                <p class="mt-2 text-sm font-semibold text-[#564D4A]/50">Klik om opnieuw te proberen</p>
                            </div>
                        </template>
                    </div>

                    {{-- Running average --}}
                    <div x-show="completedRounds > 0" x-cloak class="mt-4 text-center">
                        <p class="text-[11px] font-bold text-[#564D4A]/40">
                            Gemiddeld: <span class="font-black text-[#564D4A]" x-text="runningAvg + 'ms'"></span>
                        </p>
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
        function reactionTime(init) {
            return {
                meId: parseInt(init.me_id || '0', 10),
                scope: init.scope || 'global',
                puzzle: init.puzzle,

                isSolved: !!init.run.solved,
                isFailed: !!(init.run && init.run.failed),
                started: !!(init.run.solved || init.run.failed),
                avgMs: parseInt(init.run.avg_ms || '0', 10),
                reactionTimes: init.run.reaction_times && init.run.reaction_times.length
                    ? init.run.reaction_times.map(Number)
                    : new Array(init.puzzle.totalRounds).fill(null),

                // Game state
                currentRound: 0,
                completedRounds: 0,
                phase: 'idle', // idle | waiting | go | result | early
                lastReactionTime: 0,
                runningAvg: 0,

                _waitTimeout: null,
                _goTimestamp: 0,

                // Leaderboard
                leaderboardRows: (init.leaderboard && Array.isArray(init.leaderboard.rows)) ? init.leaderboard.rows : [],
                myRank: init.leaderboard ? init.leaderboard.my_rank : null,

                init() {
                    this.$watch('started', v => { if (v && !window.__gameFinished) window.__gameStarted = true; });
                    this.$watch('isSolved', v => { if (v) window.__gameFinished = true; });
                    this.$watch('isFailed', v => { if (v) window.__gameFinished = true; });

                    if (this.isSolved && init.run.reaction_times && init.run.reaction_times.length) {
                        this.reactionTimes = init.run.reaction_times.map(Number);
                        this.currentRound = this.puzzle.totalRounds;
                        this.completedRounds = this.puzzle.totalRounds;
                    }
                },

                startGame() {
                    this.started = true;
                    this.currentRound = 0;
                    this.completedRounds = 0;
                    this.reactionTimes = new Array(this.puzzle.totalRounds).fill(null);

                    fetch('{{ route("games.mark-started") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ game_key: 'reaction-time' })
                    });

                    this.phase = 'idle';
                },

                handleClick() {
                    if (this.isSolved || this.isFailed) return;

                    switch (this.phase) {
                        case 'idle':
                            this.startWaiting();
                            break;
                        case 'waiting':
                            // Clicked too early!
                            clearTimeout(this._waitTimeout);
                            this.phase = 'early';
                            break;
                        case 'go':
                            // Record reaction time
                            const rt = Math.round(performance.now() - this._goTimestamp);
                            this.lastReactionTime = rt;
                            this.reactionTimes[this.currentRound] = rt;
                            this.completedRounds++;
                            this.currentRound++;
                            this.updateRunningAvg();
                            this.phase = 'result';

                            // Check if all rounds done
                            if (this.currentRound >= this.puzzle.totalRounds) {
                                setTimeout(() => this.solve(), 600);
                            }
                            break;
                        case 'result':
                            if (this.currentRound < this.puzzle.totalRounds) {
                                this.startWaiting();
                            }
                            break;
                        case 'early':
                            // Retry same round
                            this.startWaiting();
                            break;
                    }
                },

                startWaiting() {
                    this.phase = 'waiting';
                    const waitMs = this.puzzle.waitTimes[this.currentRound] || (1500 + Math.random() * 3500);

                    this._waitTimeout = setTimeout(() => {
                        this.phase = 'go';
                        this._goTimestamp = performance.now();
                    }, waitMs);
                },

                updateRunningAvg() {
                    const times = this.reactionTimes.filter(t => t !== null);
                    if (times.length === 0) {
                        this.runningAvg = 0;
                        return;
                    }
                    this.runningAvg = Math.round(times.reduce((a, b) => a + b, 0) / times.length);
                },

                async solve() {
                    const times = this.reactionTimes.filter(t => t !== null);
                    const avg = Math.round(times.reduce((a, b) => a + b, 0) / times.length);
                    this.avgMs = avg;

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
                                avg_ms: avg,
                                reaction_times: times,
                            })
                        });

                        const data = await res.json();
                        if (!data?.ok) return;

                        this.isSolved = true;
                        this.phase = 'idle';

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
