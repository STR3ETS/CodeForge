{{-- resources/views/games/geo-guess.blade.php --}}
<x-layouts.dashboard :title="'Geo Gok'" active="daily">
    @php
        $isSolved = (bool)($run->solved ?? false);
        $isFailed = (bool)(!$isSolved && !empty($run->finished_at));

        $startMs = (int)($state['started_ms'] ?? (
            $run->started_at ? $run->started_at->getTimestampMs() : now()->getTimestampMs()
        ));

        $finalDistance = $isSolved ? ($state['distance_m'] ?? $run->duration_ms) : null;
        $finalDistanceFmt = null;
        if ($finalDistance !== null) {
            $finalDistanceFmt = $finalDistance < 1000
                ? round($finalDistance) . ' m'
                : number_format($finalDistance / 1000, 1, ',', '.') . ' km';
        }

        $lbRows = collect($topTimes ?? collect())->values()->all();
        $meId = (int) auth()->id();

        $ggInit = [
            'me_id' => $meId,
            'scope' => (string)($scope ?? 'global'),
            'puzzle' => [
                'number' => (int)($puzzle['number'] ?? 0),
                'date' => (string)($puzzle['date'] ?? date('Y-m-d')),
                'lat' => (float)($puzzle['lat'] ?? 0),
                'lng' => (float)($puzzle['lng'] ?? 0),
            ],
            'run' => [
                'solved' => $isSolved,
                'failed' => $isFailed,
                'started_ms' => $startMs,
                'distance_m' => $finalDistance,
                'distance' => $finalDistanceFmt,
            ],
            'target' => $target ?? ($isSolved ? [
                'lat' => (float)($state['target_lat'] ?? 0),
                'lng' => (float)($state['target_lng'] ?? 0),
                'country' => $state['country'] ?? null,
            ] : null),
            'guess' => $isSolved ? [
                'lat' => (float)($state['guess_lat'] ?? 0),
                'lng' => (float)($state['guess_lng'] ?? 0),
            ] : null,
            'leaderboard' => [
                'rows' => collect($lbRows)->values()->all(),
                'my_rank' => $myRank ?? null,
            ],
            'routes' => [
                'solve' => route('games.geoguess.solve'),
            ],
            'csrf' => csrf_token(),
            'mapsApiKey' => $mapsApiKey ?? '',
        ];
    @endphp

    <style>
        [x-cloak]{display:none!important;}

        .gg-sv-container {
            width: 100%;
            height: 350px;
            border-radius: 1rem;
            overflow: hidden;
        }
        @media (min-width: 640px) {
            .gg-sv-container { height: 420px; }
        }

        .gg-map-container {
            width: 100%;
            height: 280px;
            border-radius: 1rem;
            overflow: hidden;
            border: 2px solid rgba(86, 77, 74, 0.1);
            transition: height 0.3s ease;
        }
        .gg-map-container.expanded {
            height: 420px;
        }

        .gg-map-container-result {
            width: 100%;
            height: 350px;
            border-radius: 1rem;
            overflow: hidden;
        }

        @keyframes gg-pin-drop {
            0% { transform: translateY(-20px) scale(1.2); opacity: 0; }
            60% { transform: translateY(2px) scale(0.95); opacity: 1; }
            100% { transform: translateY(0) scale(1); opacity: 1; }
        }
        .anim-pin-drop { animation: gg-pin-drop 0.4s cubic-bezier(.16,1,.3,1); }

        @keyframes gg-pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.08); opacity: 0.8; }
        }
        .anim-pulse { animation: gg-pulse 1.5s ease-in-out infinite; }
    </style>

    <script>
        window.__GG_INIT__ = @json($ggInit);
        window.__gameFinished = @json($isSolved || $isFailed);
        window.__gameStarted  = false;
        window.addEventListener('beforeunload', function () {
            if (window.__gameFinished || !window.__gameStarted) return;
            navigator.sendBeacon(
                '{{ route("games.abandon") }}',
                new Blob([JSON.stringify({ game_key: 'geo-guess', _token: '{{ csrf_token() }}' })], { type: 'application/json' })
            );
        });
    </script>

    <div x-data="geoGok(window.__GG_INIT__)" x-init="init()" class="flex flex-col gap-10 max-w-4xl mx-auto">

        {{-- HEADER --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-[1.5rem] md:text-[1.8rem] font-black text-[#564D4A] tracking-tight leading-tight">
                        <template x-if="!isSolved && !isFailed">
                            <span>Geo Gok <span class="text-[#564D4A]/40">#<span x-text="puzzle.number"></span></span></span>
                        </template>
                        <template x-if="isSolved"><span>Goed gegokt! 🌍</span></template>
                        <template x-if="isFailed"><span>Niet afgemaakt 😅</span></template>
                    </h1>
                    <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
                        <template x-if="!isSolved && !isFailed">
                            <span>Kijk rond in Street View en gok waar je bent. Plaats een pin op de kaart en bevestig je gok!</span>
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
                            <p class="text-xs font-semibold text-[#564D4A]/55 leading-[1.3]">
                                Je gok was <strong x-text="resultDistance"></strong> van de echte locatie.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-4 text-center">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Afstand</p>
                            <p class="mt-2 text-[1.5rem] leading-none font-black" :class="resultDistanceM < 500000 ? 'text-[#8E936D]' : (resultDistanceM < 2000000 ? 'text-[#EAB308]' : 'text-[#CE796B]')" x-text="resultDistance">--</p>
                        </div>
                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-4 text-center">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">Land</p>
                            <p class="mt-2 text-[1.25rem] leading-none font-black text-[#564D4A]" x-text="targetCountry || '—'">—</p>
                        </div>
                    </div>

                    {{-- Result map --}}
                    <div class="mt-4">
                        <div id="resultMap" class="gg-map-container-result"></div>
                    </div>

                    @include('games.partials.share-score-button', ['gameKey' => 'geo-guess'])

                    {{-- Leaderboard --}}
                    <div class="mt-6 rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <h2 class="text-[1.05rem] font-extrabold text-[#564D4A]">Scorebord</h2>
                                <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                                    Dichtstbij wint. Laagste afstand bovenaan.
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
                            <template x-for="(row, idx) in leaderboardRows" :key="row.user.id + ':' + row.distance_m">
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
                                        <i class="fa-solid fa-location-dot text-[#0D9488]"></i>
                                        <span x-text="row.distance"></span>
                                    </span>
                                </div>
                            </template>
                            <template x-if="leaderboardRows.length === 0">
                                <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
                                    <p class="text-sm font-semibold text-[#564D4A]/60">Nog geen gokken.</p>
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
                    <div class="w-16 h-16 rounded-2xl bg-[#0D9488]/10 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-earth-europe text-[#0D9488] text-2xl"></i>
                    </div>
                    <p class="text-xl font-extrabold text-[#564D4A]">Waar ter wereld ben je?</p>
                    <p class="mt-2 text-xs font-semibold text-[#564D4A]/55 max-w-md mx-auto">
                        Je wordt ergens op de wereld gedropt in Google Street View.
                        Kijk rond, zoek aanwijzingen en plaats een pin op de kaart.
                        Hoe dichter bij de echte locatie, hoe beter je score!
                    </p>
                    <div class="mt-4 inline-flex items-center gap-4 px-5 py-3 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-xs font-semibold text-[#564D4A]/60">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-street-view text-[#0D9488]"></i> Kijk rond
                        </span>
                        <i class="fa-solid fa-arrow-right text-[#564D4A]/20"></i>
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-map-pin text-[#CE796B]"></i> Plaats pin
                        </span>
                        <i class="fa-solid fa-arrow-right text-[#564D4A]/20"></i>
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-[#8E936D]"></i> Bevestig
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
                    {{-- Street View --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#564D4A]/45">
                                <i class="fa-solid fa-street-view mr-1"></i> Street View
                            </span>
                            <span class="text-[11px] font-bold text-[#564D4A]/45">Kijk rond met slepen of pijltjestoetsen</span>
                        </div>
                        <div id="streetView" class="gg-sv-container"></div>
                    </div>

                    {{-- Guess Map --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#564D4A]/45">
                                <i class="fa-solid fa-map mr-1"></i> Jouw gok
                            </span>
                            <button @click="mapExpanded = !mapExpanded" class="text-[11px] font-bold text-[#5B2333] hover:text-[#5B2333]/70 transition cursor-pointer">
                                <i class="fa-solid" :class="mapExpanded ? 'fa-compress' : 'fa-expand'"></i>
                                <span x-text="mapExpanded ? 'Verkleinen' : 'Vergroten'"></span>
                            </button>
                        </div>
                        <div id="guessMap" class="gg-map-container" :class="mapExpanded && 'expanded'"></div>
                    </div>

                    {{-- Pin status + Confirm --}}
                    <div class="flex items-center justify-between gap-4">
                        <div class="text-xs font-semibold text-[#564D4A]/50">
                            <template x-if="!hasGuess">
                                <span class="anim-pulse inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-map-pin text-[#CE796B]"></i>
                                    Klik op de kaart om je pin te plaatsen
                                </span>
                            </template>
                            <template x-if="hasGuess">
                                <span class="inline-flex items-center gap-1.5 text-[#8E936D]">
                                    <i class="fa-solid fa-check-circle"></i>
                                    Pin geplaatst — klaar om te bevestigen!
                                </span>
                            </template>
                        </div>
                        <button @click="confirmGuess()"
                            :disabled="!hasGuess || submitting"
                            class="cursor-pointer inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-sm font-bold transition active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="hasGuess ? 'bg-[#5B2333] text-white hover:bg-[#5B2333]/90' : 'bg-[#564D4A]/10 text-[#564D4A]/40'">
                            <i class="fa-solid fa-check" x-show="!submitting"></i>
                            <i class="fa-solid fa-spinner fa-spin" x-show="submitting" x-cloak></i>
                            Bevestig gok
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- STREAK --}}
        <div x-show="isSolved || isFailed" x-cloak>
            <x-game-streak :streak="$streak" />
        </div>
    </div>

    {{-- Google Maps API --}}
    <script>
        function geoGok(init) {
            return {
                meId: parseInt(init.me_id || '0', 10),
                scope: init.scope || 'global',
                puzzle: init.puzzle,

                isSolved: !!init.run.solved,
                isFailed: !!(init.run && init.run.failed),
                started: !!(init.run.solved || init.run.failed),
                startedMs: parseInt(init.run.started_ms || '0', 10),

                timerText: '00:00',
                _timerId: null,

                // Game state
                hasGuess: false,
                guessLat: 0,
                guessLng: 0,
                submitting: false,
                mapExpanded: false,

                // Maps
                _sv: null,
                _map: null,
                _marker: null,
                _resultMap: null,

                // Result
                resultDistance: init.run.distance || null,
                resultDistanceM: init.run.distance_m || 0,
                targetCountry: init.target?.country || null,

                // Leaderboard
                leaderboardRows: (init.leaderboard && Array.isArray(init.leaderboard.rows)) ? init.leaderboard.rows : [],
                myRank: init.leaderboard ? init.leaderboard.my_rank : null,

                init() {
                    this.$watch('started', v => { if (v && !window.__gameFinished) window.__gameStarted = true; });
                    this.$watch('isSolved', v => { if (v) window.__gameFinished = true; });
                    this.$watch('isFailed', v => { if (v) window.__gameFinished = true; });

                    if (this.isSolved && init.target && init.guess) {
                        this.loadMaps(() => {
                            // Poll until the result map container is visible and has dimensions
                            const waitForEl = () => {
                                const el = document.getElementById('resultMap');
                                if (el && el.offsetHeight > 0) {
                                    this.showResultMap(init.target, init.guess);
                                } else {
                                    requestAnimationFrame(waitForEl);
                                }
                            };
                            waitForEl();
                        });
                    }
                },

                startGame() {
                    this.started = true;
                    this.startedMs = Date.now();

                    fetch('{{ route("games.mark-started") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ game_key: 'geo-guess' })
                    });

                    this.startTimer();
                    this.loadMaps();
                },

                loadMaps(onReady) {
                    // Load Google Maps script if not already loaded
                    if (window.google && window.google.maps) {
                        if (onReady) onReady(); else this.initMaps();
                        return;
                    }

                    const script = document.createElement('script');
                    script.src = `https://maps.googleapis.com/maps/api/js?key=${init.mapsApiKey}&callback=__ggMapsReady`;
                    script.async = true;
                    script.defer = true;

                    window.__ggMapsReady = () => {
                        if (onReady) onReady(); else this.initMaps();
                    };

                    document.head.appendChild(script);
                },

                initMaps() {
                    const pos = { lat: this.puzzle.lat, lng: this.puzzle.lng };

                    // Street View — find nearest outdoor/road panorama first
                    const svService = new google.maps.StreetViewService();
                    svService.getPanorama({
                        location: pos,
                        radius: 1000,
                        preference: google.maps.StreetViewPreference.NEAREST,
                        source: google.maps.StreetViewSource.OUTDOOR,
                    }, (data, status) => {
                        const svPos = (status === 'OK' && data?.location?.latLng)
                            ? data.location.latLng
                            : pos;

                        this._sv = new google.maps.StreetViewPanorama(
                            document.getElementById('streetView'),
                            {
                                position: svPos,
                                pov: { heading: Math.random() * 360, pitch: 0 },
                                zoom: 1,
                                addressControl: false,
                                showRoadLabels: false,
                                linksControl: true,
                                panControl: true,
                                zoomControl: true,
                                fullscreenControl: false,
                                motionTracking: false,
                                motionTrackingControl: false,
                                source: google.maps.StreetViewSource.OUTDOOR,
                            }
                        );
                    });

                    // Guess map
                    this._map = new google.maps.Map(
                        document.getElementById('guessMap'),
                        {
                            center: { lat: 20, lng: 0 },
                            zoom: 2,
                            mapTypeId: 'roadmap',
                            disableDefaultUI: true,
                            zoomControl: true,
                            gestureHandling: 'greedy',
                            clickableIcons: false,
                            styles: [
                                { featureType: 'poi', stylers: [{ visibility: 'off' }] },
                                { featureType: 'transit', stylers: [{ visibility: 'off' }] },
                            ],
                        }
                    );

                    // Click to place guess
                    this._map.addListener('click', (e) => {
                        this.guessLat = e.latLng.lat();
                        this.guessLng = e.latLng.lng();
                        this.hasGuess = true;

                        if (this._marker) {
                            this._marker.setPosition(e.latLng);
                        } else {
                            this._marker = new google.maps.Marker({
                                position: e.latLng,
                                map: this._map,
                                icon: {
                                    path: google.maps.SymbolPath.CIRCLE,
                                    scale: 10,
                                    fillColor: '#5B2333',
                                    fillOpacity: 1,
                                    strokeColor: '#fff',
                                    strokeWeight: 3,
                                },
                                draggable: true,
                            });

                            this._marker.addListener('dragend', (ev) => {
                                this.guessLat = ev.latLng.lat();
                                this.guessLng = ev.latLng.lng();
                            });
                        }
                    });
                },

                async confirmGuess() {
                    if (!this.hasGuess || this.submitting) return;
                    this.submitting = true;

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
                                guess_lat: this.guessLat,
                                guess_lng: this.guessLng,
                                duration_ms: durationMs,
                            })
                        });

                        const data = await res.json();
                        if (!data?.ok) { this.submitting = false; return; }

                        this.isSolved = true;
                        this.resultDistance = data.distance || '—';
                        this.resultDistanceM = data.distance_m || 0;
                        this.targetCountry = data.target?.country || '—';

                        if (data?.leaderboard?.rows) {
                            this.leaderboardRows = data.leaderboard.rows;
                            this.myRank = data.leaderboard.my_rank ?? this.myRank;
                            this.scope = data.leaderboard.scope || this.scope;
                        }

                        if (data?.streak) {
                            window.dispatchEvent(new CustomEvent('cf:streak', { detail: data.streak }));
                        }

                        // Show result map
                        if (data.target) {
                            this.$nextTick(() => {
                                this.showResultMap(data.target, { lat: this.guessLat, lng: this.guessLng });
                            });
                        }

                        // Confetti for close guesses (< 100km)
                        if (data.distance_m < 100000 && window.confetti && document.getElementById('mainConfettiCanvas')) {
                            const cannon = window.confetti.create(document.getElementById('mainConfettiCanvas'), { useWorker: true, resize: true });
                            cannon({ particleCount: 110, angle: 60, spread: 55, startVelocity: 55, gravity: 1.0, ticks: 320, origin: { x: 0.02, y: 1 } });
                            cannon({ particleCount: 110, angle: 120, spread: 55, startVelocity: 55, gravity: 1.0, ticks: 320, origin: { x: 0.98, y: 1 } });
                        }
                    } catch (e) {
                        this.submitting = false;
                    }
                },

                showResultMap(target, guess) {
                    const el = document.getElementById('resultMap');
                    if (!el || !window.google) return;

                    const targetPos = { lat: target.lat, lng: target.lng };
                    const guessPos = { lat: guess.lat, lng: guess.lng };

                    const map = new google.maps.Map(el, {
                        center: targetPos,
                        zoom: 4,
                        mapTypeId: 'roadmap',
                        disableDefaultUI: true,
                        zoomControl: true,
                        gestureHandling: 'greedy',
                    });

                    // Target marker (green)
                    new google.maps.Marker({
                        position: targetPos,
                        map: map,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 12,
                            fillColor: '#22C55E',
                            fillOpacity: 1,
                            strokeColor: '#fff',
                            strokeWeight: 3,
                        },
                        title: 'Echte locatie',
                    });

                    // Guess marker (accent)
                    new google.maps.Marker({
                        position: guessPos,
                        map: map,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 10,
                            fillColor: '#5B2333',
                            fillOpacity: 1,
                            strokeColor: '#fff',
                            strokeWeight: 3,
                        },
                        title: 'Jouw gok',
                    });

                    // Line between
                    new google.maps.Polyline({
                        path: [targetPos, guessPos],
                        geodesic: true,
                        strokeColor: '#5B2333',
                        strokeOpacity: 0.6,
                        strokeWeight: 2,
                        map: map,
                    });

                    // Fit bounds
                    const bounds = new google.maps.LatLngBounds();
                    bounds.extend(targetPos);
                    bounds.extend(guessPos);
                    map.fitBounds(bounds, 60);
                },

                // Timer
                startTimer() {
                    this.stopTimer();
                    const pad = (n) => String(n).padStart(2, '0');

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
            }
        }
    </script>
</x-layouts.dashboard>
