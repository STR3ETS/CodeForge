{{-- resources/views/games/iq-test.blade.php --}}
<x-layouts.dashboard :title="'IQ Test'" active="iqtest">
    @php
        $iqInit = [
            'simpleQuestions' => $simpleQuestions,
            'extendedQuestions' => $extendedQuestions,
        ];
    @endphp

    <style>
        [x-cloak]{display:none!important;}

        .iq-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border: 2px solid #D1CBC8;
            border-radius: 12px;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s, transform 0.1s;
            font-size: 1rem;
            color: #564D4A;
            user-select: none;
        }
        .iq-option:hover {
            border-color: #5B2333;
            background: #faf6f5;
        }
        .iq-option.selected {
            border-color: #5B2333;
            background: #f3eceb;
        }
        .iq-option:active {
            transform: scale(0.98);
        }
        .iq-option .iq-letter {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #F0ECEA;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            color: #564D4A;
            flex-shrink: 0;
            transition: background 0.15s, color 0.15s;
        }
        .iq-option.selected .iq-letter {
            background: #5B2333;
            color: #fff;
        }
        .iq-option.correct {
            border-color: #22C55E;
            background: #f0fdf4;
        }
        .iq-option.correct .iq-letter {
            background: #22C55E;
            color: #fff;
        }
        .iq-option.wrong {
            border-color: #EF4444;
            background: #fef2f2;
        }
        .iq-option.wrong .iq-letter {
            background: #EF4444;
            color: #fff;
        }
        .iq-option.disabled {
            pointer-events: none;
            opacity: 0.6;
        }

        .iq-progress-bar {
            height: 6px;
            border-radius: 3px;
            background: #E8E2DF;
            overflow: hidden;
        }
        .iq-progress-fill {
            height: 100%;
            background: #5B2333;
            border-radius: 3px;
            transition: width 0.4s ease;
        }

        .iq-category-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .iq-cat-reeksen { background: #DBEAFE; color: #1d4ed8; }
        .iq-cat-logica { background: #FEF3C7; color: #b45309; }
        .iq-cat-wiskunde { background: #DCFCE7; color: #15803d; }
        .iq-cat-analogieen { background: #F3E8FF; color: #7c3aed; }
        .iq-cat-patronen { background: #FFE4E6; color: #be123c; }

        .iq-result-bar {
            height: 10px;
            border-radius: 5px;
            background: #E8E2DF;
            overflow: hidden;
        }
        .iq-result-fill {
            height: 100%;
            border-radius: 5px;
            transition: width 0.8s ease;
        }

        @keyframes iq-fade-in {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .iq-fade-in {
            animation: iq-fade-in 0.35s ease-out;
        }

        @keyframes iq-score-count {
            from { transform: scale(0.5); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .iq-score-pop {
            animation: iq-score-count 0.5s cubic-bezier(.16,1,.3,1);
        }

        .mode-card {
            border: 2px solid #E8E2DF;
            border-radius: 16px;
            padding: 24px;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
        }
        .mode-card:hover {
            border-color: #5B2333;
            box-shadow: 0 4px 20px rgba(91, 35, 51, 0.08);
            transform: translateY(-2px);
        }
    </style>

    <div x-data="iqTest()" x-cloak class="w-full max-w-2xl mx-auto px-4 py-6">

        {{-- === INTRO SCREEN === --}}
        <template x-if="screen === 'intro'">
            <div class="iq-fade-in text-center">
                <div class="w-20 h-20 rounded-2xl bg-[#5B2333] flex items-center justify-center mx-auto mb-5">
                    <i class="fa-solid fa-brain text-3xl text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-[#564D4A] mb-2">IQ Test</h1>
                <p class="text-[#8A817C] mb-8 max-w-md mx-auto">
                    Test je cognitieve vaardigheden met vragen over reeksen, logica, wiskunde, analogieën en ruimtelijk inzicht.
                </p>

                <h2 class="text-lg font-bold text-[#564D4A] mb-4">Kies je modus</h2>

                <div class="grid sm:grid-cols-2 gap-4 max-w-lg mx-auto mb-4">
                    {{-- Eenvoudig --}}
                    <div class="mode-card" @click="selectMode('simple')">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                                <i class="fa-solid fa-bolt text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-[#564D4A]">Eenvoudig</h3>
                                <p class="text-xs text-[#8A817C]">Snelle test</p>
                            </div>
                        </div>
                        <ul class="space-y-1.5 text-sm text-[#8A817C]">
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check text-blue-500 text-xs"></i>
                                <span>10 vragen</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check text-blue-500 text-xs"></i>
                                <span>5 categorieën</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-clock text-blue-500 text-xs"></i>
                                <span>~5 minuten</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Uitgebreid --}}
                    <div class="mode-card" @click="selectMode('extended')">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                                <i class="fa-solid fa-flask text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-[#564D4A]">Uitgebreid</h3>
                                <p class="text-xs text-[#8A817C]">Volledige test</p>
                            </div>
                        </div>
                        <ul class="space-y-1.5 text-sm text-[#8A817C]">
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check text-purple-500 text-xs"></i>
                                <span>30 vragen</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check text-purple-500 text-xs"></i>
                                <span>5 categorieën</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-clock text-purple-500 text-xs"></i>
                                <span>~15 minuten</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-[#E8E2DF] p-5 mb-6 text-left max-w-lg mx-auto">
                    <div class="flex items-center gap-3 mb-3">
                        <i class="fa-solid fa-circle-info text-[#5B2333]"></i>
                        <span class="font-semibold text-[#564D4A]">Hoe werkt het?</span>
                    </div>
                    <ul class="space-y-2 text-sm text-[#8A817C]">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-[#5B2333] mt-0.5"></i>
                            <span>Meerkeuzevragen, 4 opties per vraag</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-[#5B2333] mt-0.5"></i>
                            <span>Inclusief patronen, kubussen en ruimtelijk inzicht</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-[#5B2333] mt-0.5"></i>
                            <span>Geen tijdslimiet — neem de tijd</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-[#5B2333] mt-0.5"></i>
                            <span>Resultaat met IQ-schatting en uitsplitsing per categorie</span>
                        </li>
                    </ul>
                </div>
            </div>
        </template>

        {{-- === QUESTION SCREEN === --}}
        <template x-if="screen === 'question'">
            <div class="iq-fade-in" :key="'q-' + currentIndex">
                {{-- Top bar --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold text-[#564D4A]"
                            x-text="(currentIndex + 1) + ' / ' + totalQuestions"></span>
                        <span class="iq-category-badge"
                            :class="categoryClass(currentQuestion().category)"
                            x-text="currentQuestion().category"></span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold"
                            :class="mode === 'simple' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600'"
                            x-text="mode === 'simple' ? 'Eenvoudig' : 'Uitgebreid'"></span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-[#8A817C]">
                        <i class="fa-solid fa-clock"></i>
                        <span x-text="timerDisplay"></span>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="iq-progress-bar mb-6">
                    <div class="iq-progress-fill"
                        :style="'width:' + ((currentIndex / totalQuestions) * 100) + '%'"></div>
                </div>

                {{-- Question --}}
                <div class="bg-white rounded-2xl border border-[#E8E2DF] p-6 mb-5">
                    <p class="text-lg font-semibold text-[#564D4A] leading-relaxed"
                        x-text="currentQuestion().question"></p>

                    {{-- Visual (SVG) for spatial questions --}}
                    <template x-if="currentQuestion().visual">
                        <div class="mt-5 flex justify-center" x-html="getVisual(currentQuestion().visual)"></div>
                    </template>
                </div>

                {{-- Options --}}
                <div class="space-y-3 mb-6">
                    <template x-for="(opt, oi) in currentQuestion().options" :key="oi">
                        <button @click="selectOption(oi)"
                            class="iq-option w-full text-left"
                            :class="{
                                'selected': selectedOption === oi && !answered,
                                'correct': answered && oi === currentQuestion().answer,
                                'wrong': answered && selectedOption === oi && oi !== currentQuestion().answer,
                                'disabled': answered && oi !== selectedOption && oi !== currentQuestion().answer,
                            }">
                            <span class="iq-letter" x-text="['A','B','C','D'][oi]"></span>
                            <span x-text="opt"></span>
                        </button>
                    </template>
                </div>

                {{-- Confirm / Next --}}
                <div class="flex justify-center">
                    <template x-if="!answered">
                        <button @click="confirmAnswer()"
                            :disabled="selectedOption === null"
                            class="px-8 py-3 rounded-xl bg-[#5B2333] text-white font-semibold transition hover:opacity-90 disabled:opacity-40 disabled:cursor-not-allowed">
                            Bevestigen
                        </button>
                    </template>
                    <template x-if="answered">
                        <button @click="nextQuestion()"
                            class="px-8 py-3 rounded-xl bg-[#5B2333] text-white font-semibold transition hover:opacity-90">
                            <span x-text="currentIndex < totalQuestions - 1 ? 'Volgende vraag' : 'Bekijk resultaat'"></span>
                            <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                        </button>
                    </template>
                </div>
            </div>
        </template>

        {{-- === RESULT SCREEN === --}}
        <template x-if="screen === 'result'">
            <div class="iq-fade-in text-center">
                <div class="w-20 h-20 rounded-2xl bg-[#5B2333] flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-chart-line text-3xl text-white"></i>
                </div>

                <h1 class="text-2xl font-bold text-[#564D4A] mb-1">Jouw resultaat</h1>
                <p class="text-sm text-[#8A817C] mb-1" x-text="'Modus: ' + (mode === 'simple' ? 'Eenvoudig' : 'Uitgebreid')"></p>
                <p class="text-sm text-[#8A817C] mb-6" x-text="'Tijd: ' + timerDisplay + ' — ' + correctCount + '/' + totalQuestions + ' goed'"></p>

                {{-- IQ Score --}}
                <div class="bg-white rounded-2xl border border-[#E8E2DF] p-6 mb-6">
                    <p class="text-sm text-[#8A817C] mb-2">Geschat IQ</p>
                    <div class="iq-score-pop text-5xl font-bold text-[#5B2333] mb-2" x-text="iqScore"></div>
                    <p class="text-sm font-medium" :class="iqLabelColor" x-text="iqLabel"></p>

                    <template x-if="mode === 'simple'">
                        <p class="text-xs text-[#B0A9A5] mt-2">Doe de uitgebreide test voor een nauwkeuriger resultaat</p>
                    </template>

                    <div class="mt-4 max-w-xs mx-auto">
                        <div class="iq-result-bar">
                            <div class="iq-result-fill bg-[#5B2333]"
                                :style="'width:' + Math.min(100, Math.max(0, ((iqScore - 55) / 95) * 100)) + '%'"></div>
                        </div>
                        <div class="flex justify-between text-xs text-[#B0A9A5] mt-1">
                            <span>55</span>
                            <span>100</span>
                            <span>150</span>
                        </div>
                    </div>
                </div>

                {{-- Category breakdown --}}
                <div class="bg-white rounded-2xl border border-[#E8E2DF] p-5 mb-6 text-left">
                    <h3 class="font-semibold text-[#564D4A] mb-4">Scores per categorie</h3>
                    <div class="space-y-6">
                        <template x-for="cat in categoryResults" :key="cat.name">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="iq-category-badge text-xs"
                                        :class="categoryClass(cat.name)"
                                        x-text="cat.name"></span>
                                    <span class="text-sm font-semibold text-[#564D4A]"
                                        x-text="cat.correct + '/' + cat.total"></span>
                                </div>
                                <div class="iq-result-bar">
                                    <div class="iq-result-fill"
                                        :class="cat.correct === cat.total ? 'bg-[#22C55E]' : (cat.correct >= cat.total / 2 ? 'bg-[#5B2333]' : 'bg-[#EF4444]')"
                                        :style="'width:' + (cat.total > 0 ? (cat.correct / cat.total) * 100 : 0) + '%'"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <button @click="resetTest()"
                        class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-[#5B2333] text-white font-semibold transition hover:opacity-90">
                        <i class="fa-solid fa-rotate-right text-sm"></i>
                        Opnieuw proberen
                    </button>

                    <template x-if="mode === 'simple'">
                        <button @click="mode = 'extended'; resetTest();"
                            class="inline-flex items-center gap-2 px-8 py-3 rounded-xl border-2 border-[#5B2333] text-[#5B2333] font-semibold transition hover:bg-[#5B2333]/5">
                            <i class="fa-solid fa-flask text-sm"></i>
                            Probeer uitgebreid
                        </button>
                    </template>
                </div>

                <div class="mt-4">
                    <a href="{{ route('dashboard') }}"
                        class="text-sm text-[#8A817C] hover:text-[#5B2333] transition">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Terug naar dashboard
                    </a>
                </div>
            </div>
        </template>
    </div>

    <script>
        function iqTest() {
            const cfg = @json($iqInit);

            return {
                screen: 'intro',
                mode: null,
                questions: [],
                totalQuestions: 0,
                currentIndex: 0,
                selectedOption: null,
                answered: false,
                answers: [],
                correctCount: 0,
                iqScore: 0,
                iqLabel: '',
                iqLabelColor: '',
                categoryResults: [],

                // Timer
                timerStart: null,
                timerElapsed: 0,
                timerDisplay: '00:00',
                timerInterval: null,

                selectMode(mode) {
                    this.mode = mode;
                    this.questions = mode === 'simple'
                        ? [...cfg.simpleQuestions]
                        : [...cfg.extendedQuestions];
                    this.totalQuestions = this.questions.length;
                    this.startTest();
                },

                startTest() {
                    this.screen = 'question';
                    this.currentIndex = 0;
                    this.selectedOption = null;
                    this.answered = false;
                    this.answers = [];
                    this.correctCount = 0;
                    this.timerStart = Date.now();
                    this.timerInterval = setInterval(() => this.updateTimer(), 1000);
                    this.shuffleQuestions();
                },

                shuffleQuestions() {
                    for (let i = this.questions.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [this.questions[i], this.questions[j]] = [this.questions[j], this.questions[i]];
                    }
                },

                updateTimer() {
                    this.timerElapsed = Math.floor((Date.now() - this.timerStart) / 1000);
                    const mm = String(Math.floor(this.timerElapsed / 60)).padStart(2, '0');
                    const ss = String(this.timerElapsed % 60).padStart(2, '0');
                    this.timerDisplay = mm + ':' + ss;
                },

                currentQuestion() {
                    return this.questions[this.currentIndex] || {};
                },

                categoryClass(cat) {
                    const map = {
                        'Reeksen':     'iq-cat-reeksen',
                        'Logica':      'iq-cat-logica',
                        'Wiskunde':    'iq-cat-wiskunde',
                        'Analogieën':  'iq-cat-analogieen',
                        'Patronen':    'iq-cat-patronen',
                    };
                    return map[cat] || 'iq-cat-reeksen';
                },

                selectOption(idx) {
                    if (this.answered) return;
                    this.selectedOption = idx;
                },

                confirmAnswer() {
                    if (this.selectedOption === null) return;
                    this.answered = true;
                    const q = this.currentQuestion();
                    const isCorrect = this.selectedOption === q.answer;
                    this.answers.push({
                        questionIndex: this.currentIndex,
                        selected: this.selectedOption,
                        correct: q.answer,
                        isCorrect: isCorrect,
                        category: q.category,
                    });
                    if (isCorrect) this.correctCount++;
                },

                nextQuestion() {
                    if (this.currentIndex < this.totalQuestions - 1) {
                        this.currentIndex++;
                        this.selectedOption = null;
                        this.answered = false;
                    } else {
                        this.finishTest();
                    }
                },

                finishTest() {
                    clearInterval(this.timerInterval);
                    this.updateTimer();
                    this.calculateScore();
                    this.screen = 'result';
                },

                calculateScore() {
                    const pct = this.correctCount / this.totalQuestions;
                    this.iqScore = Math.round(55 + (pct * 90));

                    if (this.iqScore >= 130) {
                        this.iqLabel = 'Uitzonderlijk';
                        this.iqLabelColor = 'text-[#15803d]';
                    } else if (this.iqScore >= 115) {
                        this.iqLabel = 'Bovengemiddeld';
                        this.iqLabelColor = 'text-[#1d4ed8]';
                    } else if (this.iqScore >= 85) {
                        this.iqLabel = 'Gemiddeld';
                        this.iqLabelColor = 'text-[#564D4A]';
                    } else if (this.iqScore >= 70) {
                        this.iqLabel = 'Benedengemiddeld';
                        this.iqLabelColor = 'text-[#b45309]';
                    } else {
                        this.iqLabel = 'Laag';
                        this.iqLabelColor = 'text-[#be123c]';
                    }

                    const cats = {};
                    this.answers.forEach(a => {
                        if (!cats[a.category]) cats[a.category] = { name: a.category, correct: 0, total: 0 };
                        cats[a.category].total++;
                        if (a.isCorrect) cats[a.category].correct++;
                    });
                    this.categoryResults = Object.values(cats);
                },

                resetTest() {
                    this.screen = 'intro';
                    this.currentIndex = 0;
                    this.selectedOption = null;
                    this.answered = false;
                    this.answers = [];
                    this.correctCount = 0;
                    this.timerElapsed = 0;
                    this.timerDisplay = '00:00';
                    clearInterval(this.timerInterval);
                },

                getVisual(type) {
                    const visuals = {
                        // Cross-shaped unfolded cube
                        'cube-unfold-cross': `
                            <svg viewBox="0 0 200 250" width="200" class="drop-shadow-sm">
                                <rect x="65" y="5" width="55" height="55" rx="4" fill="#F3E8FF" stroke="#7c3aed" stroke-width="2"/>
                                <rect x="5" y="65" width="55" height="55" rx="4" fill="#DBEAFE" stroke="#3b82f6" stroke-width="2"/>
                                <rect x="65" y="65" width="55" height="55" rx="4" fill="#DCFCE7" stroke="#22c55e" stroke-width="2"/>
                                <rect x="125" y="65" width="55" height="55" rx="4" fill="#FEF3C7" stroke="#f59e0b" stroke-width="2"/>
                                <rect x="65" y="125" width="55" height="55" rx="4" fill="#FFE4E6" stroke="#f43f5e" stroke-width="2"/>
                                <rect x="65" y="185" width="55" height="55" rx="4" fill="#E0E7FF" stroke="#6366f1" stroke-width="2"/>
                                <text x="92" y="38" text-anchor="middle" font-size="11" fill="#7c3aed" font-weight="600">1</text>
                                <text x="32" y="98" text-anchor="middle" font-size="11" fill="#3b82f6" font-weight="600">2</text>
                                <text x="92" y="98" text-anchor="middle" font-size="11" fill="#22c55e" font-weight="600">3</text>
                                <text x="152" y="98" text-anchor="middle" font-size="11" fill="#f59e0b" font-weight="600">4</text>
                                <text x="92" y="158" text-anchor="middle" font-size="11" fill="#f43f5e" font-weight="600">5</text>
                                <text x="92" y="218" text-anchor="middle" font-size="11" fill="#6366f1" font-weight="600">6</text>
                            </svg>`,

                        // Pyramid from front
                        'pyramid-front': `
                            <svg viewBox="0 0 200 180" width="200" class="drop-shadow-sm">
                                <!-- 3D pyramid -->
                                <polygon points="100,15 30,150 170,150" fill="#FEF3C7" stroke="#b45309" stroke-width="2" stroke-linejoin="round"/>
                                <polygon points="100,15 170,150 140,165" fill="#FDE68A" stroke="#b45309" stroke-width="2" stroke-linejoin="round"/>
                                <line x1="100" y1="15" x2="100" y2="150" stroke="#b45309" stroke-width="1" stroke-dasharray="4,4" opacity="0.4"/>
                                <!-- base indication -->
                                <path d="M30,150 Q100,175 170,150" fill="none" stroke="#b45309" stroke-width="1.5" stroke-dasharray="4,4" opacity="0.5"/>
                                <text x="100" y="175" text-anchor="middle" font-size="10" fill="#b45309" font-weight="500">vierkante basis</text>
                                <!-- eye icon -->
                                <circle cx="16" cy="85" r="8" fill="#fff" stroke="#564D4A" stroke-width="1.5"/>
                                <circle cx="16" cy="85" r="3" fill="#564D4A"/>
                                <line x1="28" y1="85" x2="45" y2="85" stroke="#564D4A" stroke-width="1.5" marker-end="url(#arrowP)"/>
                                <defs><marker id="arrowP" markerWidth="6" markerHeight="6" refX="5" refY="3" orient="auto"><path d="M0,0 L6,3 L0,6" fill="#564D4A"/></marker></defs>
                            </svg>`,

                        // Cube with symbols, rotate question
                        'cube-symbols-rotate': `
                            <svg viewBox="0 0 280 200" width="280" class="drop-shadow-sm">
                                <!-- Unfolded cube in cross shape with symbols -->
                                <rect x="90" y="5" width="55" height="55" rx="4" fill="#fff" stroke="#564D4A" stroke-width="2"/>
                                <circle cx="117" cy="32" r="12" fill="none" stroke="#5B2333" stroke-width="2.5"/>
                                <text x="117" y="58" text-anchor="middle" font-size="8" fill="#8A817C">boven</text>

                                <rect x="30" y="65" width="55" height="55" rx="4" fill="#fff" stroke="#564D4A" stroke-width="2"/>
                                <polygon points="57,78 45,105 69,105" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linejoin="round"/>
                                <text x="57" y="118" text-anchor="middle" font-size="8" fill="#8A817C">links</text>

                                <rect x="90" y="65" width="55" height="55" rx="4" fill="#DBEAFE" stroke="#564D4A" stroke-width="2"/>
                                <polygon points="117,75 107,100 112,100 112,108 122,108 122,100 127,100" fill="#f59e0b" stroke="#f59e0b" stroke-width="1.5" stroke-linejoin="round"/>
                                <text x="117" y="118" text-anchor="middle" font-size="8" fill="#1d4ed8" font-weight="700">voor</text>

                                <rect x="150" y="65" width="55" height="55" rx="4" fill="#fff" stroke="#564D4A" stroke-width="2"/>
                                <rect x="165" y="78" width="25" height="25" rx="2" fill="none" stroke="#22c55e" stroke-width="2.5"/>
                                <text x="177" y="118" text-anchor="middle" font-size="8" fill="#8A817C">rechts</text>

                                <rect x="210" y="65" width="55" height="55" rx="4" fill="#fff" stroke="#564D4A" stroke-width="2"/>
                                <text x="237" y="98" text-anchor="middle" font-size="18" fill="#8A817C">?</text>
                                <text x="237" y="118" text-anchor="middle" font-size="8" fill="#8A817C">achter</text>

                                <rect x="90" y="125" width="55" height="55" rx="4" fill="#fff" stroke="#564D4A" stroke-width="2"/>
                                <line x1="105" y1="140" x2="129" y2="164" stroke="#be123c" stroke-width="2.5"/>
                                <line x1="129" y1="140" x2="105" y2="164" stroke="#be123c" stroke-width="2.5"/>
                                <text x="117" y="178" text-anchor="middle" font-size="8" fill="#8A817C">onder</text>

                                <!-- Arrow indicating 90° rotation -->
                                <path d="M90,195 Q140,210 190,195" fill="none" stroke="#5B2333" stroke-width="2" marker-end="url(#arrowR)"/>
                                <text x="140" y="210" text-anchor="middle" font-size="10" fill="#5B2333" font-weight="600">90° naar rechts</text>
                                <defs><marker id="arrowR" markerWidth="8" markerHeight="8" refX="6" refY="4" orient="auto"><path d="M0,0 L8,4 L0,8" fill="#5B2333"/></marker></defs>
                            </svg>`,

                        // T-shaped unfolded cube with dark center
                        'cube-t-shape': `
                            <svg viewBox="0 0 240 220" width="240" class="drop-shadow-sm">
                                <!-- T-shape: top row of 4, then 1 below second, then 1 below that -->
                                <rect x="5" y="5" width="50" height="50" rx="4" fill="#fff" stroke="#564D4A" stroke-width="2"/>
                                <text x="30" y="35" text-anchor="middle" font-size="12" fill="#564D4A" font-weight="600">1</text>

                                <rect x="60" y="5" width="50" height="50" rx="4" fill="#fff" stroke="#564D4A" stroke-width="2"/>
                                <text x="85" y="35" text-anchor="middle" font-size="12" fill="#564D4A" font-weight="600">2</text>

                                <rect x="115" y="5" width="50" height="50" rx="4" fill="#5B2333" stroke="#5B2333" stroke-width="2"/>
                                <text x="140" y="35" text-anchor="middle" font-size="12" fill="#fff" font-weight="600">3</text>

                                <rect x="170" y="5" width="50" height="50" rx="4" fill="#fff" stroke="#564D4A" stroke-width="2"/>
                                <text x="195" y="35" text-anchor="middle" font-size="12" fill="#564D4A" font-weight="600">4</text>

                                <rect x="115" y="60" width="50" height="50" rx="4" fill="#fff" stroke="#564D4A" stroke-width="2"/>
                                <text x="140" y="90" text-anchor="middle" font-size="12" fill="#564D4A" font-weight="600">5</text>

                                <rect x="115" y="115" width="50" height="50" rx="4" fill="#fff" stroke="#564D4A" stroke-width="2"/>
                                <text x="140" y="145" text-anchor="middle" font-size="12" fill="#564D4A" font-weight="600">6</text>

                                <!-- Legend -->
                                <rect x="5" y="185" width="14" height="14" rx="3" fill="#5B2333"/>
                                <text x="25" y="196" font-size="10" fill="#564D4A">= donker vlak (3)</text>
                                <text x="5" y="215" font-size="10" fill="#8A817C">Welk vlak zit er tegenover?</text>
                            </svg>`,

                        // Cylinder on cube - top view
                        'cylinder-on-cube': `
                            <svg viewBox="0 0 260 180" width="260" class="drop-shadow-sm">
                                <!-- 3D side view -->
                                <text x="65" y="14" text-anchor="middle" font-size="10" fill="#8A817C" font-weight="600">Zijaanzicht</text>
                                <!-- Cube -->
                                <rect x="25" y="90" width="80" height="70" rx="2" fill="#DBEAFE" stroke="#3b82f6" stroke-width="2"/>
                                <!-- Cylinder on top -->
                                <rect x="40" y="40" width="50" height="50" rx="0" fill="#FFE4E6" stroke="#f43f5e" stroke-width="2"/>
                                <ellipse cx="65" cy="40" rx="25" ry="8" fill="#FFE4E6" stroke="#f43f5e" stroke-width="2"/>
                                <ellipse cx="65" cy="90" rx="25" ry="8" fill="#DBEAFE" stroke="#f43f5e" stroke-width="1" opacity="0.3"/>

                                <!-- Arrow -->
                                <path d="M145,90 L165,90" stroke="#564D4A" stroke-width="2" marker-end="url(#arrowC)"/>
                                <text x="155" y="82" text-anchor="middle" font-size="9" fill="#564D4A">bovenaanzicht</text>

                                <!-- Top view -->
                                <text x="210" y="14" text-anchor="middle" font-size="10" fill="#8A817C" font-weight="600">Bovenaanzicht</text>
                                <rect x="170" y="50" width="80" height="80" rx="2" fill="#DBEAFE" stroke="#3b82f6" stroke-width="2"/>
                                <circle cx="210" cy="90" r="25" fill="#FFE4E6" stroke="#f43f5e" stroke-width="2"/>
                                <text x="210" y="150" text-anchor="middle" font-size="10" fill="#564D4A" font-weight="500">Wat zie je?</text>
                                <defs><marker id="arrowC" markerWidth="8" markerHeight="8" refX="6" refY="4" orient="auto"><path d="M0,0 L8,4 L0,8" fill="#564D4A"/></marker></defs>
                            </svg>`,

                        // Cross unfolded cube with numbers 1-6
                        'cube-numbers': `
                            <svg viewBox="0 0 200 260" width="180" class="drop-shadow-sm">
                                <!-- Cross shape with numbers -->
                                <rect x="65" y="5" width="55" height="55" rx="4" fill="#E0E7FF" stroke="#6366f1" stroke-width="2"/>
                                <text x="92" y="40" text-anchor="middle" font-size="22" fill="#6366f1" font-weight="700">1</text>

                                <rect x="5" y="65" width="55" height="55" rx="4" fill="#FEF3C7" stroke="#f59e0b" stroke-width="2"/>
                                <text x="32" y="100" text-anchor="middle" font-size="22" fill="#f59e0b" font-weight="700">4</text>

                                <rect x="65" y="65" width="55" height="55" rx="4" fill="#DBEAFE" stroke="#3b82f6" stroke-width="2"/>
                                <text x="92" y="100" text-anchor="middle" font-size="22" fill="#3b82f6" font-weight="700">2</text>

                                <rect x="125" y="65" width="55" height="55" rx="4" fill="#DCFCE7" stroke="#22c55e" stroke-width="2"/>
                                <text x="152" y="100" text-anchor="middle" font-size="22" fill="#22c55e" font-weight="700">5</text>

                                <rect x="65" y="125" width="55" height="55" rx="4" fill="#FFE4E6" stroke="#f43f5e" stroke-width="2"/>
                                <text x="92" y="160" text-anchor="middle" font-size="22" fill="#f43f5e" font-weight="700">3</text>

                                <rect x="65" y="185" width="55" height="55" rx="4" fill="#F3E8FF" stroke="#7c3aed" stroke-width="2"/>
                                <text x="92" y="220" text-anchor="middle" font-size="22" fill="#7c3aed" font-weight="700">6</text>

                                <text x="92" y="255" text-anchor="middle" font-size="10" fill="#8A817C">Welk cijfer zit tegenover 2?</text>
                            </svg>`,

                        // Paper fold and cut
                        'paper-fold': `
                            <svg viewBox="0 0 340 130" width="340" class="drop-shadow-sm">
                                <!-- Step 1: Full paper -->
                                <rect x="5" y="15" width="60" height="60" rx="2" fill="#DBEAFE" stroke="#3b82f6" stroke-width="1.5"/>
                                <line x1="35" y1="15" x2="35" y2="75" stroke="#3b82f6" stroke-width="1" stroke-dasharray="3,3" opacity="0.5"/>
                                <line x1="5" y1="45" x2="65" y2="45" stroke="#3b82f6" stroke-width="1" stroke-dasharray="3,3" opacity="0.5"/>
                                <text x="35" y="95" text-anchor="middle" font-size="8" fill="#8A817C">Stap 1</text>
                                <text x="35" y="105" text-anchor="middle" font-size="7" fill="#8A817C">origineel</text>

                                <!-- Arrow -->
                                <path d="M72,45 L88,45" stroke="#564D4A" stroke-width="1.5" marker-end="url(#arrowF)"/>

                                <!-- Step 2: Folded once (half) -->
                                <rect x="95" y="15" width="30" height="60" rx="2" fill="#DBEAFE" stroke="#3b82f6" stroke-width="1.5"/>
                                <rect x="95" y="15" width="30" height="60" rx="2" fill="#93C5FD" stroke="#3b82f6" stroke-width="1.5" opacity="0.5"/>
                                <path d="M125,15 L125,75" stroke="#3b82f6" stroke-width="2"/>
                                <text x="110" y="95" text-anchor="middle" font-size="8" fill="#8A817C">Stap 2</text>
                                <text x="110" y="105" text-anchor="middle" font-size="7" fill="#8A817C">1x vouwen</text>

                                <!-- Arrow -->
                                <path d="M132,45 L148,45" stroke="#564D4A" stroke-width="1.5" marker-end="url(#arrowF)"/>

                                <!-- Step 3: Folded twice (quarter) -->
                                <rect x="155" y="30" width="30" height="30" rx="2" fill="#DBEAFE" stroke="#3b82f6" stroke-width="1.5"/>
                                <rect x="155" y="30" width="30" height="30" rx="2" fill="#93C5FD" stroke="#3b82f6" stroke-width="1.5" opacity="0.5"/>
                                <path d="M155,60 L185,60" stroke="#3b82f6" stroke-width="2"/>
                                <path d="M185,30 L185,60" stroke="#3b82f6" stroke-width="2"/>
                                <text x="170" y="95" text-anchor="middle" font-size="8" fill="#8A817C">Stap 3</text>
                                <text x="170" y="105" text-anchor="middle" font-size="7" fill="#8A817C">2x vouwen</text>

                                <!-- Arrow -->
                                <path d="M192,45 L208,45" stroke="#564D4A" stroke-width="1.5" marker-end="url(#arrowF)"/>

                                <!-- Step 4: Cut corner -->
                                <rect x="215" y="30" width="30" height="30" rx="2" fill="#DBEAFE" stroke="#3b82f6" stroke-width="1.5"/>
                                <polygon points="232,30 245,30 245,43" fill="#EF4444" stroke="#EF4444" stroke-width="1" opacity="0.7"/>
                                <line x1="232" y1="30" x2="245" y2="43" stroke="#EF4444" stroke-width="2"/>
                                <path d="M245,30 L245,60" stroke="#3b82f6" stroke-width="2"/>
                                <path d="M215,60 L245,60" stroke="#3b82f6" stroke-width="2"/>
                                <text x="230" y="95" text-anchor="middle" font-size="8" fill="#8A817C">Stap 4</text>
                                <text x="230" y="105" text-anchor="middle" font-size="7" fill="#EF4444" font-weight="600">knippen!</text>

                                <!-- Arrow -->
                                <path d="M252,45 L268,45" stroke="#564D4A" stroke-width="1.5" marker-end="url(#arrowF)"/>

                                <!-- Step 5: Unfolded result with ? -->
                                <rect x="275" y="15" width="60" height="60" rx="2" fill="#F0FDF4" stroke="#22c55e" stroke-width="1.5"/>
                                <text x="305" y="52" text-anchor="middle" font-size="22" fill="#22c55e" font-weight="700">?</text>
                                <text x="305" y="95" text-anchor="middle" font-size="8" fill="#22c55e" font-weight="600">Opengevouwen</text>
                                <text x="305" y="105" text-anchor="middle" font-size="7" fill="#8A817C">hoeveel gaten?</text>

                                <defs><marker id="arrowF" markerWidth="6" markerHeight="6" refX="5" refY="3" orient="auto"><path d="M0,0 L6,3 L0,6" fill="#564D4A"/></marker></defs>
                            </svg>`,

                        // Pattern matrix (3x3 grid with shapes)
                        'pattern-matrix': `
                            <svg viewBox="0 0 210 210" width="210" class="drop-shadow-sm">
                                <!-- Grid lines -->
                                <rect x="5" y="5" width="195" height="195" rx="8" fill="#FAFAF9" stroke="#E8E2DF" stroke-width="1.5"/>
                                <line x1="70" y1="5" x2="70" y2="200" stroke="#E8E2DF" stroke-width="1"/>
                                <line x1="135" y1="5" x2="135" y2="200" stroke="#E8E2DF" stroke-width="1"/>
                                <line x1="5" y1="70" x2="200" y2="70" stroke="#E8E2DF" stroke-width="1"/>
                                <line x1="5" y1="135" x2="200" y2="135" stroke="#E8E2DF" stroke-width="1"/>

                                <!-- Row 1: filled circle, filled square, filled triangle -->
                                <circle cx="37" cy="37" r="16" fill="#564D4A"/>
                                <rect x="86" y="21" width="32" height="32" rx="2" fill="#564D4A"/>
                                <polygon points="167,21 151,53 183,53" fill="#564D4A" stroke-linejoin="round"/>

                                <!-- Row 2: empty circle, empty square, empty triangle -->
                                <circle cx="37" cy="102" r="16" fill="none" stroke="#564D4A" stroke-width="2.5"/>
                                <rect x="86" y="86" width="32" height="32" rx="2" fill="none" stroke="#564D4A" stroke-width="2.5"/>
                                <polygon points="167,86 151,118 183,118" fill="none" stroke="#564D4A" stroke-width="2.5" stroke-linejoin="round"/>

                                <!-- Row 3: filled circle, filled square, ??? -->
                                <circle cx="37" cy="167" r="16" fill="#564D4A"/>
                                <rect x="86" y="151" width="32" height="32" rx="2" fill="#564D4A"/>

                                <!-- Question mark cell -->
                                <rect x="136" y="136" width="63" height="63" rx="0" fill="#FEF3C7" opacity="0.5"/>
                                <text x="167" y="175" text-anchor="middle" font-size="28" fill="#b45309" font-weight="700">?</text>

                                <!-- Pattern hint -->
                                <text x="37" y="210" text-anchor="middle" font-size="8" fill="#8A817C">gevuld</text>
                                <text x="102" y="210" text-anchor="middle" font-size="8" fill="#8A817C">gevuld</text>
                            </svg>`,
                    };
                    return visuals[type] || '';
                },
            };
        }
    </script>
</x-layouts.dashboard>
