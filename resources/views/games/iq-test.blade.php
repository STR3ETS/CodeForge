{{-- resources/views/games/iq-test.blade.php --}}
<x-layouts.dashboard :title="'IQ Test'" active="iqtest">
    @php
        $iqInit = [
            'questions' => $questions,
            'totalQuestions' => count($questions),
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
    </style>

    <div x-data="iqTest()" x-cloak class="w-full max-w-2xl mx-auto px-4 py-6">

        {{-- === INTRO SCREEN === --}}
        <template x-if="screen === 'intro'">
            <div class="iq-fade-in text-center">
                <div class="w-20 h-20 rounded-2xl bg-[#5B2333] flex items-center justify-center mx-auto mb-5">
                    <i class="fa-solid fa-brain text-3xl text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-[#564D4A] mb-2">IQ Test</h1>
                <p class="text-[#8A817C] mb-6 max-w-md mx-auto">
                    Test je cognitieve vaardigheden met 30 vragen over reeksen, logica, wiskunde, analogieën en patronen.
                </p>

                <div class="bg-white rounded-2xl border border-[#E8E2DF] p-5 mb-6 text-left max-w-sm mx-auto">
                    <div class="flex items-center gap-3 mb-3">
                        <i class="fa-solid fa-circle-info text-[#5B2333]"></i>
                        <span class="font-semibold text-[#564D4A]">Hoe werkt het?</span>
                    </div>
                    <ul class="space-y-2 text-sm text-[#8A817C]">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-[#5B2333] mt-0.5"></i>
                            <span>30 meerkeuzevragen, 4 opties per vraag</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-[#5B2333] mt-0.5"></i>
                            <span>5 categorieën: reeksen, logica, wiskunde, analogieën, patronen</span>
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

                <button @click="startTest()"
                    class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-[#5B2333] text-white font-semibold text-base hover:opacity-90 transition">
                    <i class="fa-solid fa-play text-sm"></i>
                    Start de test
                </button>
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
                <p class="text-sm text-[#8A817C] mb-6" x-text="'Tijd: ' + timerDisplay + ' — ' + correctCount + '/' + totalQuestions + ' goed'"></p>

                {{-- IQ Score --}}
                <div class="bg-white rounded-2xl border border-[#E8E2DF] p-6 mb-6">
                    <p class="text-sm text-[#8A817C] mb-2">Geschat IQ</p>
                    <div class="iq-score-pop text-5xl font-bold text-[#5B2333] mb-2" x-text="iqScore"></div>
                    <p class="text-sm font-medium" :class="iqLabelColor" x-text="iqLabel"></p>

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

                {{-- Retry --}}
                <button @click="resetTest()"
                    class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-[#5B2333] text-white font-semibold transition hover:opacity-90">
                    <i class="fa-solid fa-rotate-right text-sm"></i>
                    Opnieuw proberen
                </button>

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
                questions: cfg.questions,
                totalQuestions: cfg.totalQuestions,
                currentIndex: 0,
                selectedOption: null,
                answered: false,
                answers: [],          // user's selected option per question
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
                    // Shuffle question order for variety
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
                    // IQ estimation based on percentage correct
                    // Mapping: 0/30 => ~55, 15/30 => ~100, 30/30 => ~145
                    const pct = this.correctCount / this.totalQuestions;
                    this.iqScore = Math.round(55 + (pct * 90));

                    // Label
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

                    // Category breakdown
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
            };
        }
    </script>
</x-layouts.dashboard>
