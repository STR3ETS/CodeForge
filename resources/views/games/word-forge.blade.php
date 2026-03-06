{{-- resources/views/games/word-forge.blade.php --}}
<x-layouts.dashboard :title="'Woord Raden'" active="daily">
    @php
        $pattern = (string)($state['pattern'] ?? '');
        $letters = str_split($pattern);

        $maxAttempts = (int)($state['max_attempts'] ?? 5);
        $attemptRows = (array)($state['attempts'] ?? []);

        $isSolved = (bool)($run->solved ?? false);
        $attemptsUsed = count($attemptRows);
        $attemptsLeft = max(0, $maxAttempts - $attemptsUsed);

        $isFailed = (!$isSolved && ($run->finished_at && $attemptsLeft <= 0));

        $startMs = (int)($state['started_ms'] ?? (
            $run->started_at ? $run->started_at->getTimestampMs() : now()->getTimestampMs()
        ));

        $finalTime = null;
        $fmtMs = function ($ms) {
            if ($ms === null) return '--:--';
            $sec = (int) round($ms / 1000);
            $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
            $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);
            return $mm . ':' . $ss;
        };

        if ($isSolved && $run->duration_ms !== null) {
            $finalTime = $fmtMs($run->duration_ms);
        }

        $meId = (int) auth()->id();

        $wfInit = [
            'me_id' => $meId,
            'scope' => (string)($scope ?? 'global'),

            'puzzle' => [
                'number'   => (int)($puzzle['number'] ?? 0),
                'date'     => (string)($puzzle['date'] ?? date('Y-m-d')),
                'category' => (string)($puzzle['category'] ?? ''),
                'length'   => (int)($puzzle['length'] ?? 0),
                'first'    => (string)($puzzle['first'] ?? ''),
            ],
            'state' => [
                'pattern'  => $pattern,
                'attempts' => $attemptRows,
                'max'      => $maxAttempts,
            ],
            'run' => [
                'solved'     => $isSolved,
                'failed'     => $isFailed,
                'started_ms' => $startMs,
                'final_time' => $finalTime,
            ],
            'leaderboard' => [
                'rows' => collect($topTimes ?? collect())->map(fn($row) => $row)->values()->all(),
                'my_rank' => $myRank ?? null,
            ],
            'answer' => ($isFailed || $isSolved) ? (string)($puzzle['word'] ?? '') : null,
            'routes' => [
                'guess' => route('games.wordforge.guess'),
            ],
            'csrf' => csrf_token(),
        ];
    @endphp

    <style>[x-cloak]{display:none!important;}</style>

    <script>
        window.__WF_INIT__ = @json($wfInit);
    </script>
    <script>
        window.__gameFinished = @json($isSolved || $isFailed);
        window.__gameStarted  = false;
        window.addEventListener('beforeunload', function () {
            if (window.__gameFinished || !window.__gameStarted) return;
            navigator.sendBeacon(
                '{{ route("games.abandon") }}',
                new Blob([JSON.stringify({ game_key: 'word-forge', _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );
        });
    </script>

    <div x-data="wordForge(window.__WF_INIT__)" x-init="init()" class="flex flex-col gap-8 max-w-3xl mx-auto relative overflow-hidden">

        {{-- HERO HEADER --}}
        <div class="relative z-[1] overflow-hidden rounded-2xl border border-[#564D4A]/10 bg-[#5B2333]">
            <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-20" alt="">
            <div class="absolute inset-0 bg-gradient-to-r from-[#5B2333]/95 via-[#5B2333]/80 to-transparent"></div>

            <div class="relative p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 text-white text-xs font-semibold w-fit">
                            <i class="fa-solid fa-font"></i>
                            Dagelijks spel
                        </div>

                        <h1 class="mt-3 text-[1.5rem] md:text-[1.8rem] font-black text-white tracking-tight leading-tight">
                            <template x-if="!isSolved">
                                <span>
                                    Woord Raden
                                    <span class="text-white/70">
                                        #<span x-text="puzzle.number">{{ (int)$puzzle['number'] }}</span>
                                    </span>
                                </span>
                            </template>
                            <template x-if="isSolved"><span>Goed gedaan! Je hebt het 🎉</span></template>
                        </h1>

                        <p class="mt-2 text-xs md:text-sm font-semibold text-white/80 leading-[1.3] italic">
                            <template x-if="!isSolved">
                                <span>
                                    Psstt... ik geef je een kleine hint,<br>
                                    dit Woord Raden gaat over
                                    <span class="font-black text-white" x-text="puzzle.category">{{ $puzzle['category'] }}</span>
                                </span>
                            </template>

                            <template x-if="isSolved">
                                <span>
                                    Je hebt het woord correct geraden.<br>
                                    Kom morgen terug voor een nieuw woord.
                                </span>
                            </template>
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span x-show="!isSolved" x-cloak class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 text-white text-xs font-semibold">
                            <template x-if="isFailed">
                                <span class="inline-flex items-center gap-2">
                                    <i class="fa-solid fa-xmark"></i>
                                    <span>Mislukt</span>
                                </span>
                            </template>

                            <template x-if="!isFailed">
                                <span class="inline-flex items-center gap-2">
                                    <i class="fa-solid fa-stopwatch"></i>
                                    <span x-text="timerText">00:00</span>
                                </span>
                            </template>
                        </span>

                        <span x-show="!isSolved" x-cloak class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 text-white text-xs font-semibold">
                            <i class="fa-solid fa-bullseye-arrow"></i>
                            Pogingen: <span x-text="attemptsUsed">{{ $attemptsUsed }}</span> / <span x-text="state.max">{{ $maxAttempts }}</span>
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
            <div x-show="(isSolved || isFailed)" x-cloak>
                <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <!-- ✅ ICON: groen check bij win / rood kruis bij fail -->
                            <template x-if="!isFailed">
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
                                <p class="text-sm font-extrabold text-[#564D4A] leading-tight"
                                    x-text="isFailed ? ‘Mislukt’ : ‘Voltooid’">Voltooid</p>
                                <p class="text-xs font-semibold text-[#564D4A]/55 leading-[1.3]"
                                    x-text="isFailed ? ‘Je hebt alle pogingen gebruikt.’ : ‘Je hebt het woord correct geraden.’">
                                    Je hebt het woord correct geraden.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-[#564D4A]/10 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45"
                                x-text="isFailed ? 'VOLGENDE KEER BETER' : 'TIJD'"></p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]"
                                x-text="isFailed ? ‘Mislukt!’ : (finalTime || timerText)">00:00</p>
                            <p class="mt-2 text-xs font-semibold text-[#564D4A]/55"
                                x-text="isFailed ? ‘Kom morgen terug voor een nieuw woord.’ : ‘Opgeslagen als resultaat van vandaag.’"></p>
                        </div>

                        <div class="rounded-2xl border border-[#564D4A]/10 bg-white p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Woord</p>
                            <p class="mt-2 text-[1.8rem] leading-none font-black tracking-wider uppercase text-[#564D4A]" x-text="answerWord">
                                {{ $isSolved ? $puzzle['word'] : '' }}
                            </p>
                            <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">Kom morgen terug voor een nieuw woord.</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('dashboard.daily') }}"
                           class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-white border border-[#564D4A]/10 hover:border-[#564D4A]/20 transition text-xs font-semibold text-[#564D4A]">
                            <i class="fa-solid fa-arrow-left"></i>
                            Terug naar Dagelijkse Uitdagingen
                        </a>
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
                                            <p class="text-[11px] font-semibold text-[#564D4A]/55">Level {{ (int)($p->level ?? 1) }}</p>
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
            <div x-show="!(isSolved || isFailed)" x-cloak>
                <div x-show="!started" x-cloak class="text-center py-10">
                    <p class="text-xl font-extrabold text-[#564D4A]">Ben je er klaar voor?</p>
                    <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">Raad het woord in zo min mogelijk pogingen.</p>
                    <button @click="startGame()"
                        class="mt-6 inline-flex items-center gap-2 px-8 py-3 rounded-2xl bg-[#5B2333] text-white text-sm font-bold hover:bg-[#5B2333]/90 transition active:scale-[0.98]">
                        <i class="fa-solid fa-play"></i>
                        Spel starten
                    </button>
                </div>

                <div x-show="started" x-cloak>
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <div>
                        <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Raad het woord</h2>
                        @php $wordLength = (int)($puzzle[‘length’] ?? 0); @endphp
                        <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                            Typ een woord van <span x-text="puzzle.length">{{ $wordLength }}</span> letters. We onthullen letters die op de juiste positie staan.
                        </p>
                    </div>

                    @if($attemptsLeft <= 0)
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#564D4A]/5 text-[#564D4A]/70 text-xs font-semibold">
                            <i class="fa-solid fa-xmark"></i>
                            Geen pogingen meer
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold">
                            <i class="fa-solid fa-lightbulb"></i>
                            Categorie: <span x-text="puzzle.category">{{ $puzzle['category'] }}</span>
                        </span>
                    @endif
                </div>

                {{-- Reveal answer on fail --}}
                <div x-show="isFailed" x-cloak class="mt-5 rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white border border-[#564D4A]/10 flex items-center justify-center">
                                <i class="fa-solid fa-eye text-[#5B2333]"></i>
                            </div>
                            <div>
                                <p class="text-sm font-extrabold text-[#564D4A] leading-tight">Het woord was</p>
                                <p class="mt-1 text-[1.8rem] leading-none font-black text-[#564D4A] tracking-wider uppercase"
                                   x-text="answerWord">{{ $isFailed ? $puzzle['word'] : '' }}</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-[#564D4A]/10 text-xs font-semibold text-[#564D4A]/70">
                            Kom morgen terug
                        </span>
                    </div>
                </div>

                {{-- Pattern row --}}
                <div class="mt-6 rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-6">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Huidige poging</p>

                    {{-- Typen in blokjes (letter voor letter) --}}
                    <div class="mt-3">
                        <label class="block text-[11px] font-semibold text-[#564D4A]/55 mb-2">
                            Typ je gok in de vakjes (begint met <span class="font-black" x-text="puzzle.first"></span>)
                        </label>

                        <div class="relative" @click="focusEntry()">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(ch, idx) in entrySlots" :key="idx">
                                    <div
                                        class="w-11 h-11 rounded-xl border flex items-center justify-center font-black text-lg select-none"
                                        :class="slotClass(idx)"
                                    >
                                        <span x-text="ch ? ch : '•'"></span>
                                    </div>
                                </template>
                            </div>

                            {{-- Onzichtbare input die het toetsenbord opent (ook mobiel) --}}
                            <input
                                x-ref="entryInput"
                                type="text"
                                inputmode="text"
                                autocomplete="off"
                                autocapitalize="characters"
                                spellcheck="false"
                                class="absolute inset-0 opacity-0"
                                @keydown="onEntryKeydown($event)"
                                @input="onEntryInput($event)"
                                @paste.prevent="onEntryPaste($event)"
                                :disabled="submitting"
                            >
                        </div>

                        <p x-show="guessError" x-cloak class="mt-3 text-xs font-semibold text-red-600" x-text="guessError"></p>
                    </div>
                </div>

                <hr class="border-[#5B2333]/10 my-8">

                {{-- How to play (collapsed) --}}
                <div>
                    <details x-data="{ open:false }" @toggle="open = $el.open" class="rounded-2xl border border-[#564D4A]/10 bg-white p-5">
                        <summary class="cursor-pointer list-none flex items-center justify-between">
                            <span class="text-sm font-extrabold text-[#564D4A]">Hoe te spelen</span>
                            <i class="fa-solid fa-chevron-down text-[#564D4A]/50 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </summary>

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-4">
                                <p class="text-xs font-extrabold text-[#564D4A]">1) Gebruik de categorie</p>
                                <p class="mt-1 text-[11px] font-semibold text-[#564D4A]/55 leading-[1.35]">
                                    Je krijgt altijd een categorie + de eerste letter.
                                </p>
                            </div>

                            <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-4">
                                <p class="text-xs font-extrabold text-[#564D4A]">2) Raad tot {{ $maxAttempts }} keer</p>
                                <p class="mt-1 text-[11px] font-semibold text-[#564D4A]/55 leading-[1.35]">
                                    Na elke gok worden letters op de juiste positie zichtbaar.
                                </p>
                            </div>
                        </div>
                    </details>
                </div>
                </div>
            </div>
        </div>

        <div x-show="isSolved || isFailed" x-cloak class="relative z-[1]">
            <x-game-streak :streak="$streak" />
        </div>
    </div>

    <script>
        function wordForge(init) {
            return {
                meId: parseInt(init.me_id || '0', 10),
                scope: init.scope || 'global',

                puzzle: init.puzzle,
                state: {
                    pattern: init.state.pattern || '',
                    max: parseInt(init.state.max || '5', 10),
                },

                isSolved: !!init.run.solved,
                isFailed: !!init.run.failed,
                started: !!(init.run.solved || init.run.failed),
                answerWord: init.answer,

                startedMs: parseInt(init.run.started_ms || '0', 10),
                finalTime: init.run.final_time || null,

                // ✅ live leaderboard
                leaderboardReady: false,
                leaderboardRows: (init.leaderboard && Array.isArray(init.leaderboard.rows)) ? init.leaderboard.rows : [],
                myRank: init.leaderboard ? init.leaderboard.my_rank : null,

                guessError: '',
                submitting: false,

                attempts: Array.isArray(init.state.attempts) ? init.state.attempts : [],
                timerText: '00:00',
                _timerId: null,

                // ✅ “blok input”
                entry: [],          // array met letters per positie
                activeIdx: 0,       // huidige cursor positie
                entryFocused: false,

                get attemptsUsed() { return this.attempts.length; },
                get noAttemptsLeft() { return (this.state.max - this.attempts.length) <= 0; },

                // blokjes renderen
                get entrySlots() { return this.entry; },

                // ===== init =====
                init() {
                    this.leaderboardReady = true;
                    this.$watch('started',  v => { if (v && !window.__gameFinished) window.__gameStarted = true; });
                    this.$watch('isSolved', v => { if (v) window.__gameFinished = true; });
                    this.$watch('isFailed', v => { if (v) window.__gameFinished = true; });
                    if (this.isSolved || this.isFailed) {
                        this.startTimer();
                        return;
                    }
                    this.initEntryFromPattern();
                },

                // ===== timer =====
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
                        body: JSON.stringify({ game_key: 'word-forge' })
                    });
                    this.startTimer();
                    this.$nextTick(() => this.focusEntry());
                },

                // ===== pattern/locks =====
                isLocked(idx) {
                    const pat = String(this.state.pattern || '');
                    const ch = pat[idx] || '_';
                    return ch !== '_' && ch !== '•';
                },

                initEntryFromPattern() {
                    const len = parseInt(this.puzzle.length || 0, 10);
                    const first = String(this.puzzle.first || '').toUpperCase();
                    let pat = String(this.state.pattern || '');

                    // fallback pattern als die ontbreekt
                    if (!pat || pat.length !== len) {
                        pat = first + '_'.repeat(Math.max(0, len - 1));
                        this.state.pattern = pat;
                    }

                    this.entry = [];
                    for (let i = 0; i < len; i++) {
                        const pch = (pat[i] || '_');
                        this.entry[i] = (pch !== '_' ? String(pch).toUpperCase() : '');
                    }

                    // zorg dat eerste letter altijd vast staat
                    this.entry[0] = first;

                    // zet cursor op eerstvolgende “lege, niet-locked” plek
                    this.activeIdx = this.nextEditableIndex(0);
                },

                nextEditableIndex(from) {
                    const len = this.entry.length;
                    for (let i = Math.max(0, from); i < len; i++) {
                        if (!this.isLocked(i)) return i;
                    }
                    return len - 1;
                },

                prevEditableIndex(from) {
                    for (let i = Math.min(from, this.entry.length - 1); i >= 0; i--) {
                        if (!this.isLocked(i)) return i;
                    }
                    return 0;
                },

                // ===== UI / focus =====
                focusEntry() {
                    const el = this.$refs.entryInput;
                    if (!el) return;
                    el.focus({ preventScroll: true });
                    this.entryFocused = true;
                },

                slotClass(idx) {
                    const locked = this.isLocked(idx);
                    const active = this.entryFocused && idx === this.activeIdx && !locked;

                    if (locked) {
                        return 'bg-[#6E9075] border-[#6E9075] text-white';
                    }

                    if (active) {
                        return 'bg-white border-[#5B2333] text-[#564D4A]';
                    }

                    if (this.entry[idx]) {
                        return 'bg-white border-[#564D4A]/20 text-[#564D4A]';
                    }

                    return 'bg-white/60 border-[#564D4A]/10 text-[#564D4A]/35';
                },

                // ===== input events =====
                onEntryKeydown(e) {
                    this.guessError = '';

                    // focus state
                    this.entryFocused = true;

                    const key = e.key;

                    // Enter submit
                    if (key === 'Enter') {
                        e.preventDefault();
                        this.submitGuess();
                        return;
                    }

                    // arrows
                    if (key === 'ArrowLeft') {
                        e.preventDefault();
                        this.activeIdx = this.prevEditableIndex(this.activeIdx - 1);
                        return;
                    }
                    if (key === 'ArrowRight') {
                        e.preventDefault();
                        this.activeIdx = this.nextEditableIndex(this.activeIdx + 1);
                        return;
                    }

                    // backspace delete
                    if (key === 'Backspace') {
                        e.preventDefault();

                        const i = this.activeIdx;

                        // als huidige slot leeg is -> ga terug en wis vorige
                        if (!this.entry[i]) {
                            const prev = this.prevEditableIndex(i - 1);
                            if (!this.isLocked(prev)) {
                                this.entry[prev] = '';
                                this.activeIdx = prev;
                            }
                            return;
                        }

                        // anders wis huidige
                        if (!this.isLocked(i)) {
                            this.entry[i] = '';
                        }
                        return;
                    }

                    // Voor letters op desktop komt @input daarna binnen.
                    // Op sommige browsers kun je ook hier letters afvangen, maar we laten @input het doen.
                },

                onEntryInput(e) {
                    // Hier komt (mobiel/desktop) de getypte letter(s) binnen
                    const raw = String(e.target.value || '');
                    const letters = raw.toUpperCase().replace(/[^A-Z]/g, '');
                    if (!letters) {
                        e.target.value = '';
                        return;
                    }

                    for (const ch of letters) {
                        this.insertChar(ch);
                    }

                    // input leeg houden (zodat backspace logisch blijft)
                    e.target.value = '';
                },

                onEntryPaste(e) {
                    const text = (e.clipboardData?.getData('text') || '').toUpperCase().replace(/[^A-Z]/g, '');
                    if (!text) return;
                    for (const ch of text) this.insertChar(ch);
                },

                maybeAutoSubmit() {
                    if (this.submitting || this.isSolved || this.isFailed) return;

                    const guess = this.buildGuessString();
                    if (guess) {
                        this.submitGuess();
                    }
                },

                insertChar(ch) {
                    if (!ch || ch.length !== 1) return;
                    if (this.submitting || this.isSolved || this.isFailed) return;

                    let i = this.activeIdx;

                    // als current locked is, spring door
                    if (this.isLocked(i)) {
                        i = this.nextEditableIndex(i + 1);
                    }

                    if (this.isLocked(i)) return;

                    this.entry[i] = ch;

                    // naar volgende editable
                    this.activeIdx = this.nextEditableIndex(i + 1);

                    // ✅ als alle blokjes gevuld zijn -> automatisch versturen
                    this.maybeAutoSubmit();
                },

                buildGuessString() {
                    // alles moet gevuld zijn (locked letters zitten al in entry)
                    for (let i = 0; i < this.entry.length; i++) {
                        const ch = this.entry[i];
                        if (!ch || !/^[A-Z]$/.test(ch)) return null;
                    }

                    // extra veiligheid: starts with first
                    const first = String(this.puzzle.first || '').toUpperCase();
                    if (this.entry[0] !== first) this.entry[0] = first;

                    return this.entry.join('');
                },

                async submitGuess() {
                    this.guessError = '';
                    if (this.submitting) return;

                    const guess = this.buildGuessString();
                    if (!guess) {
                        this.guessError = 'Fill all letters first.';
                        this.$nextTick(() => this.focusEntry());
                        return;
                    }

                    this.submitting = true;

                    try {
                        const url = init.routes.guess + '?scope=' + encodeURIComponent(this.scope || 'global');

                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': init.csrf,
                            },
                            body: JSON.stringify({ guess })
                        });

                        if (res.status === 422) {
                            const data = await res.json();
                            this.guessError = data?.errors?.guess?.[0] || 'Invalid guess.';
                            this.$nextTick(() => this.focusEntry());
                            return;
                        }

                        const data = await res.json();
                        if (!data?.ok) {
                            this.guessError = data?.message || 'Something went wrong.';
                            this.$nextTick(() => this.focusEntry());
                            return;
                        }

                        // update state from server
                        this.state.pattern = data.pattern || this.state.pattern;
                        this.attempts = data.attempts || this.attempts;

                        this.isSolved = !!data.solved;
                        this.isFailed = !!data.failed;

                        // ✅ streak UI live updaten
                        if (data?.streak) {
                            window.dispatchEvent(new CustomEvent('cf:streak', { detail: data.streak }));
                        }

                        if (data.answer) this.answerWord = data.answer;
                        if (this.isSolved && data.final_time) this.finalTime = data.final_time;

                        if (this.isSolved && this.finalTime) {
                            this.timerText = this.finalTime;
                        }

                        // confetti only on solved
                        if (this.isSolved && window.confetti && document.getElementById('mainConfettiCanvas')) {
                            const cannon = window.confetti.create(document.getElementById('mainConfettiCanvas'), { useWorker: true, resize: true });

                            cannon({ particleCount: 110, angle: 60, spread: 55, startVelocity: 55, gravity: 1.0, ticks: 320, origin: { x: 0.02, y: 1 } });
                            cannon({ particleCount: 110, angle: 120, spread: 55, startVelocity: 55, gravity: 1.0, ticks: 320, origin: { x: 0.98, y: 1 } });
                        }

                        // leaderboard update (only when solved)
                        if (data?.leaderboard?.rows) {
                            this.leaderboardRows = data.leaderboard.rows;
                            this.myRank = data.leaderboard.my_rank ?? this.myRank;
                            this.scope = data.leaderboard.scope || this.scope;
                        }

                        if (this.isSolved || this.isFailed) {
                            this.stopTimer();
                            return;
                        }

                        // ✅ nieuwe gok: direct klaar om volgende letters te typen
                        this.initEntryFromPattern();
                        this.$nextTick(() => this.focusEntry());

                    } catch (e) {
                        this.guessError = 'Network error. Please try again.';
                        this.$nextTick(() => this.focusEntry());
                    } finally {
                        this.submitting = false;
                    }
                },
            }
        }
    </script>
</x-layouts.dashboard>