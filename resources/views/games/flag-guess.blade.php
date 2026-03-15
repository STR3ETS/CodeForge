{{-- resources/views/games/flag-guess.blade.php --}}
<x-layouts.dashboard :title="'Vlag Raden'" active="daily">
    @php
        $isSolved = (bool)($run->solved ?? false);
        $isFailed = (bool)($isFailed ?? false);

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

        $meId = (int) auth()->id();

        $init = [
            'me_id' => $meId,
            'scope' => (string)($scope ?? 'global'),

            'puzzle' => [
                'number'     => (int)($puzzleMeta['number'] ?? 0),
                'date'       => (string)($puzzleMeta['date'] ?? date('Y-m-d')),
                'total'      => (int)($puzzleMeta['total'] ?? 3),
                'max_wrong'  => (int)($puzzleMeta['max_wrong'] ?? 2),
                'penalty_ms' => (int)($puzzleMeta['penalty_ms'] ?? 3000),
            ],

            'question' => [
                'idx'      => (int)($question['idx'] ?? 0),
                'code'     => (string)($question['code'] ?? ''),
                'flag_url' => (string)($question['flag_url'] ?? ''),
                'options'  => (array)($question['options'] ?? []),
                'answer'   => (string)($question['answer'] ?? ''),
            ],

            'state' => [
                'current_idx' => (int)($state['current_idx'] ?? 0),
                'wrong'       => (int)($state['wrong'] ?? 0),
                'answered'    => (int)($state['answered'] ?? 0),
            ],

            'run' => [
                'solved'     => $isSolved,
                'failed'     => $isFailed,
                'started_ms' => $startMs,
                'final_time' => $finalTime,
            ],

            'leaderboard' => [
                'rows'    => collect($topTimes ?? collect())->map(fn($row) => $row)->values()->all(),
                'my_rank' => $myRank ?? null,
            ],

            'routes' => [
                'answer' => route('games.flagguess.answer'),
            ],

            'csrf' => csrf_token(),
        ];
    @endphp

    <style>[x-cloak]{display:none!important;}</style>
    <style>
        @keyframes fgShake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }
        .fg-shake {
            animation: fgShake .38s ease-in-out;
        }
    </style>

    <script>
        window.__FG_INIT__ = @json($init);
    </script>
    <script>
        window.__gameFinished = @json($isSolved || $isFailed);
        window.__gameStarted  = false;
        window.addEventListener('beforeunload', function () {
            if (window.__gameFinished || !window.__gameStarted) return;
            navigator.sendBeacon(
                '{{ route("games.abandon") }}',
                new Blob([JSON.stringify({ game_key: 'flag-guess', _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );
        });
    </script>

    <div x-data="flagGuess(window.__FG_INIT__)" x-init="init()" class="flex flex-col gap-10 max-w-3xl mx-auto relative overflow-hidden">

        {{-- HEADER --}}
        <div class="relative z-[1]">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-[1.5rem] md:text-[1.8rem] font-black text-[#564D4A] tracking-tight leading-tight">
                        <template x-if="!isSolved && !isFailed">
                            <span>Vlag Raden <span class="text-[#564D4A]/40">#<span x-text="puzzle.number"></span></span></span>
                        </template>
                        <template x-if="isSolved"><span>Mooi! Perfect gedaan 🎉</span></template>
                        <template x-if="isFailed"><span>Oei… volgende keer beter! 😅</span></template>
                    </h1>
                    <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
                        <template x-if="!isSolved && !isFailed">
                            <span>Identificeer het land bij de vlag. Max <span class="font-black text-[#564D4A]" x-text="puzzle.max_wrong"></span> fouten.</span>
                        </template>
                        <template x-if="isSolved"><span>Opgeslagen als resultaat van vandaag. Kom morgen terug voor een nieuwe.</span></template>
                        <template x-if="isFailed"><span>Te veel fouten. Probeer het morgen opnieuw!</span></template>
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <span x-show="!isSolved && !isFailed" x-cloak class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-[#564D4A]/6 bg-white text-[#564D4A] text-xs font-semibold">
                        <i class="fa-solid fa-stopwatch text-[#5B2333]"></i>
                        <span x-text="timerText">00:00</span>
                    </span>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-[#564D4A]/6 bg-white text-[#564D4A] text-xs font-semibold">
                        <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                        Fouten: <span class="font-black" x-text="wrong"></span>/<span x-text="puzzle.max_wrong"></span>
                    </span>
                    <a href="{{ route('dashboard.daily') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#5B2333] text-white text-xs font-semibold hover:bg-[#5B2333]/85 transition">
                        <i class="fa-solid fa-arrow-left"></i> Terug
                    </a>
                </div>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-between text-[11px] font-semibold text-[#564D4A]/55">
                    <span>Voortgang</span>
                    <span><span x-text="Math.min(currentIdx, puzzle.total)"></span> / <span x-text="puzzle.total"></span></span>
                </div>
                <div class="mt-2 w-full h-[6px] rounded-full bg-[#564D4A]/10 overflow-hidden">
                    <div class="h-full rounded-full bg-[#5B2333]" :style="`width: ${progressPercent()}%`"></div>
                </div>
            </div>
        </div>

        {{-- GAME CARD --}}
        <div class="relative z-[1] w-full bg-white rounded-2xl p-8 border border-[#564D4A]/6">

            {{-- FINISHED --}}
            <div x-show="isSolved || isFailed" x-cloak>
                <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
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
                               x-text="isSolved ? 'Alle vlaggen herkend. Goed gedaan!' : 'Te veel fouten.'"></p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Tijd</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]" x-text="finalTime || '--:--'">--:--</p>
                            <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">
                                Inclusief straf: <span class="font-black" x-text="wrong * (puzzle.penalty_ms/1000)"></span>s
                            </p>
                        </div>

                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Fouten</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]">
                                <span x-text="wrong"></span>
                            </p>
                            <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">Max toegestaan: <span x-text="puzzle.max_wrong"></span></p>
                        </div>
                    </div>

                    @include('games.partials.share-score-button', ['gameKey' => 'flag-guess'])

                    {{-- Leaderboard (only on solved) --}}
                    <div class="mt-6 rounded-2xl border border-[#564D4A]/6 bg-white p-5" x-show="isSolved" x-cloak>
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

                        <div x-show="leaderboardReady" x-cloak class="mt-4 grid gap-2">
                            <template x-if="leaderboardRows.length === 0">
                                <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
                                    <p class="text-sm font-semibold text-[#564D4A]/60">Nog geen tijden.</p>
                                </div>
                            </template>

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
                                                    <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#5B2333]/10 text-[#5B2333]">YOU</span>
                                                </template>
                                            </div>
                                            <p class="text-[11px] font-semibold text-[#564D4A]/55">
                                                Level <span x-text="row.user.level || 1"></span>
                                            </p>
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
                <div x-show="!started" x-cloak class="text-center py-10">
                    <p class="text-xl font-extrabold text-[#564D4A]">Ben je er klaar voor?</p>
                    <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">Identificeer 3 landvlaggen. Foute antwoorden geven straftijd.</p>
                    <button @click="startGame()"
                        class="mt-6 inline-flex items-center gap-2 px-8 py-3 rounded-2xl bg-[#5B2333] text-white text-sm font-bold hover:bg-[#5B2333]/90 transition active:scale-[0.98]">
                        <i class="fa-solid fa-play"></i>
                        Spel starten
                    </button>
                </div>

                <div x-show="started" x-cloak>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Bij welk land hoort deze vlag?</h2>
                        <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                            Kies het juiste land. Fout antwoord geeft een fout (en straftijd).
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold shrink-0">
                        <i class="fa-solid fa-layer-group"></i>
                        Vlag <span class="font-black" x-text="currentIdx + 1"></span>/<span x-text="puzzle.total"></span>
                    </span>
                </div>

                <div class="mt-6 rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-6">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Vlag</p>

                    <div class="mt-3 rounded-2xl border border-[#564D4A]/6 bg-white p-6">
                        {{-- Flag image --}}
                        <div class="flex justify-center">
                            <img
                                :src="question.flag_url"
                                :key="question.flag_url"
                                class="h-36 w-auto rounded-xl shadow-md border border-[#564D4A]/6 object-cover"
                                alt="Flag"
                                draggable="false"
                            >
                        </div>

                        {{-- Options --}}
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="opt in question.options" :key="opt.code">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-2xl px-5 py-4 text-sm font-extrabold border transition"
                                    :class="[optionClass(opt.code), shakeClass(opt.code)]"
                                    @click="pick(opt.code)"
                                    :disabled="submitting || revealActive"
                                >
                                    <span x-text="opt.name"></span>

                                    <template x-if="pickedCode === opt.code && pickedOk !== null">
                                        <span class="ml-2 inline-flex items-center justify-center">
                                            <i class="fa-solid" :class="pickedOk ? 'fa-check' : 'fa-xmark'"></i>
                                        </span>
                                    </template>

                                    {{-- Show correct answer highlight after wrong pick --}}
                                    <template x-if="pickedCode !== opt.code && revealCorrect && opt.code === question.answer">
                                        <span class="ml-2 inline-flex items-center justify-center">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                    </template>
                                </button>
                            </template>
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
        function flagGuess(init) {
            return {
                meId: parseInt(init.me_id || '0', 10),
                scope: init.scope || 'global',

                puzzle: init.puzzle,
                question: init.question,

                currentIdx: parseInt(init.state.current_idx || '0', 10),
                wrong: parseInt(init.state.wrong || '0', 10),

                isSolved: !!init.run.solved,
                isFailed: !!init.run.failed,
                started: !!(init.run.solved || init.run.failed),

                startedMs: parseInt(init.run.started_ms || '0', 10),
                finalTime: init.run.final_time || null,

                // leaderboard
                leaderboardReady: false,
                leaderboardRows: (init.leaderboard && Array.isArray(init.leaderboard.rows)) ? init.leaderboard.rows : [],
                myRank: init.leaderboard ? init.leaderboard.my_rank : null,

                // ui
                timerText: '00:00',
                _timerId: null,
                submitting: false,

                // button feedback
                pickedCode: null,
                pickedOk: null,
                revealActive: false,
                revealCorrect: false,
                shaking: false,
                _revealT: null,

                init() {
                    this.leaderboardReady = true;
                    this.$watch('started',  v => { if (v && !window.__gameFinished) window.__gameStarted = true; });
                    this.$watch('isSolved', v => { if (v) window.__gameFinished = true; });
                    this.$watch('isFailed', v => { if (v) window.__gameFinished = true; });
                    if (this.isSolved || this.isFailed) {
                        this.startTimer();
                        this.stopTimer();
                    }
                },

                progressPercent() {
                    const done = Math.min(this.currentIdx, this.puzzle.total);
                    return Math.round((done / Math.max(1, this.puzzle.total)) * 100);
                },

                startTimer() {
                    this.stopTimer();
                    const pad = (n) => String(n).padStart(2, '0');

                    if (this.isSolved) {
                        this.timerText = this.finalTime || '00:00';
                        return;
                    }
                    if (this.isFailed) return;

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

                startGame() {
                    this.started = true;
                    this.startedMs = Date.now();
                    fetch('{{ route("games.mark-started") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ game_key: 'flag-guess' })
                    });
                    this.startTimer();
                },

                optionClass(code) {
                    const base = 'bg-white border-[#564D4A]/6 text-[#564D4A] hover:border-[#5B2333]/40 hover:bg-[#F7F4F3] active:scale-[0.99]';

                    if (this.pickedCode === code && this.pickedOk === true) {
                        return 'bg-[#8E936D] border-[#8E936D] text-white scale-[1.01]';
                    }

                    if (this.pickedCode === code && this.pickedOk === false) {
                        return 'bg-[#CE796B] border-[#CE796B] text-white';
                    }

                    // Show correct answer in green after a wrong pick
                    if (this.revealCorrect && code === this.question.answer && this.pickedCode !== code) {
                        return 'bg-[#8E936D] border-[#8E936D] text-white';
                    }

                    return base;
                },

                shakeClass(code) {
                    return (this.pickedCode === code && this.pickedOk === false && this.shaking) ? 'fg-shake' : '';
                },

                async pick(code) {
                    if (this.submitting || this.isSolved || this.isFailed || this.revealActive) return;

                    this.submitting = true;
                    this.pickedCode = code;
                    this.pickedOk = null;
                    this.revealCorrect = false;
                    this.shaking = false;

                    try {
                        const url = init.routes.answer + '?scope=' + encodeURIComponent(this.scope || 'global');

                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': init.csrf,
                            },
                            body: JSON.stringify({
                                idx: this.question.idx,
                                choice: code,
                            })
                        });

                        const data = await res.json();
                        if (!data?.ok) {
                            this.pickedCode = null;
                            this.pickedOk = null;
                            this.revealActive = false;
                            return;
                        }

                        const ok = !!data.correct;

                        this.pickedOk = ok;
                        this.revealActive = true;

                        if (!ok) {
                            this.shaking = true;
                            this.revealCorrect = true;
                            setTimeout(() => { this.shaking = false; }, 380);
                        }

                        // streak live update
                        if (data?.streak) {
                            window.dispatchEvent(new CustomEvent('cf:streak', { detail: data.streak }));
                        }

                        this.currentIdx = parseInt(data.current_idx || this.currentIdx, 10);
                        this.wrong = parseInt(data.wrong || this.wrong, 10);

                        clearTimeout(this._revealT);
                        const delay = ok ? 420 : 820;

                        this._revealT = setTimeout(() => {

                            if (data.finished) {
                                this.isSolved = !!data.solved;
                                this.isFailed = !!data.failed;

                                this.finalTime = data.final_time || this.finalTime;
                                if (this.isSolved && this.finalTime) this.timerText = this.finalTime;

                                this.stopTimer();

                                if (data?.leaderboard?.rows) {
                                    this.leaderboardRows = data.leaderboard.rows;
                                    this.myRank = data.leaderboard.my_rank ?? this.myRank;
                                    this.scope = data.leaderboard.scope || this.scope;
                                }

                                // confetti
                                if (this.isSolved && window.confetti && document.getElementById('mainConfettiCanvas')) {
                                    const cannon = window.confetti.create(document.getElementById('mainConfettiCanvas'), { useWorker: true, resize: true });

                                    cannon({ particleCount: 110, angle: 60, spread: 55, startVelocity: 55, gravity: 1.0, ticks: 320, origin: { x: 0.02, y: 1 } });
                                    cannon({ particleCount: 110, angle: 120, spread: 55, startVelocity: 55, gravity: 1.0, ticks: 320, origin: { x: 0.98, y: 1 } });
                                }

                                return;
                            }

                            // Next flag
                            if (data?.question) {
                                this.question = {
                                    idx:      data.question.idx,
                                    code:     data.question.code,
                                    flag_url: data.question.flag_url,
                                    options:  data.question.options || [],
                                    answer:   data.question.answer,
                                };
                            }

                            this.pickedCode = null;
                            this.pickedOk = null;
                            this.revealActive = false;
                            this.revealCorrect = false;

                        }, delay);

                    } catch (e) {
                        this.pickedCode = null;
                        this.pickedOk = null;
                        this.revealActive = false;
                        this.revealCorrect = false;
                    } finally {
                        this.submitting = false;
                    }
                },
            }
        }
    </script>
</x-layouts.dashboard>
