{{-- resources/views/dashboard/index.blade.php --}}
<x-layouts.dashboard :title="'Dashboard'" active="dashboard">
    @php
        $u = auth()->user();

        $bannerUrl = $u->profile_banner ? asset('storage/' . $u->profile_banner) : null;
        $avatarUrl = $u->profile_picture ? asset('storage/' . $u->profile_picture) : null;

        // ✅ LEVEL META (real)
        $xpMeta = $u?->levelMeta() ?? ['xp' => 0, 'level' => 1, 'nextXp' => 5000, 'percent' => 0];

        $thresholds = (array) config('levels.thresholds', []);
        ksort($thresholds);

        $currentLevel = (int)($xpMeta['level'] ?? 1);
        $nextLevel = $currentLevel + 1;

        $currentXp = (int)($xpMeta['xp'] ?? 0);
        $nextXp = (int)($xpMeta['nextXp'] ?? 0);

        $maxKey = !empty($thresholds) ? (int) max(array_keys($thresholds)) : 1;
        $prevThreshold = ($currentLevel <= 1) ? 0 : (int) ($thresholds[$currentLevel - 1] ?? 0);

        $remainingXp = max(0, $nextXp - $currentXp);

        // ✅ percent inside the current level band (so it’s not “xp/next”)
        if ($currentLevel > $maxKey) {
            $percent = 100;
        } else {
            $band = max(1, ($nextXp - $prevThreshold));
            $percent = (int) round(min(100, max(0, (($currentXp - $prevThreshold) / $band) * 100)));
        }

        // ✅ Daily challenges progress (real)
        $limit = $limit ?? (($u->plan === 'pro') ? null : 5);
        $done = (int) ($u->daily_challenges_done ?? 0);
        $remaining = $remaining ?? (is_null($limit) ? null : max(0, $limit - $done));
        $dailyPercent = is_null($limit) ? 100 : (int) round(min(100, ($done / max(1, $limit)) * 100));

        // ✅ Daily quests (real, from controller; fallback empty)
        $quests = collect($quests ?? [])->values();
        $questsDoneCount = $quests->filter(fn($q) => !empty($q['is_done']))->count();
        $questsTotalCount = $quests->count();
        $questsPercent = (int) round(($questsDoneCount / max(1, $questsTotalCount)) * 100);

        $questsAllDone = (bool)($questsAllDone ?? false);
        $questsAllClaimed = (bool)($questsAllClaimed ?? false);
        $canClaim = $questsAllDone && !$questsAllClaimed;

        $questsRemaining = max(0, $questsTotalCount - $questsDoneCount);

        $avgXpPerQuest = (int) round(
            $quests->pluck('reward_xp')->filter()->avg() ?: 250
        );
        $estimatedQuests = (int) ceil($remainingXp / max(1, $avgXpPerQuest));

        // ✅ stats (real where possible)
        $stats = (array)($stats ?? []);
        $gamesPlayedTotal = (int)($stats['games_played_total'] ?? 0);
        $gamesPlayedWeek = (int)($stats['games_played_week'] ?? 0);
        $bestRank = (int)($stats['best_rank'] ?? 14);
    @endphp

    <div class="flex flex-col gap-8">
        <div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/10">
            <div class="relative w-full h-[300px] rounded-2xl overflow-hidden flex items-end p-8">

                {{-- Banner background --}}
                @if ($bannerUrl)
                    <img src="{{ $bannerUrl }}" class="absolute inset-0 w-full h-full object-cover" alt="">
                @else
                    <div class="absolute inset-0 bg-[#5B2333]"></div>
                    <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-30" alt="">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#5B2333]/95 via-[#5B2333]/70 to-transparent"></div>
                @endif

                {{-- Banner upload --}}
                <form id="bannerForm" method="POST" action="{{ route('profile.media') }}" enctype="multipart/form-data" class="absolute inset-0 z-[5]">
                    @csrf
                    <input id="bannerUpload" name="profile_banner" type="file" accept="image/*" class="hidden">

                    <label for="bannerUpload" class="group absolute inset-0 cursor-pointer">
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition bg-black/20"></div>

                        <div class="absolute right-6 top-6 opacity-0 group-hover:opacity-100 transition">
                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/90 text-[#564D4A] text-xs font-semibold">
                                <i class="fa-solid fa-camera"></i>
                                Change banner
                            </span>
                        </div>
                    </label>
                </form>

                {{-- Avatar upload --}}
                <form id="avatarForm" method="POST" action="{{ route('profile.media') }}" enctype="multipart/form-data" class="relative z-[6]">
                    @csrf
                    <input id="avatarUpload" name="profile_picture" type="file" accept="image/*" class="hidden">

                    <label for="avatarUpload" class="group cursor-pointer block">
                        <div class="relative w-26 h-26 rounded-full overflow-hidden border-4 border-white bg-[#F7F4F3]">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" class="w-full h-full object-cover" alt="">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                    <span class="text-[#564D4A] font-black text-lg">
                                        {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif

                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition bg-black/35 flex items-center justify-center">
                                <div class="w-9 h-9 rounded-xl bg-white/90 flex items-center justify-center">
                                    <i class="fa-solid fa-camera text-[#564D4A] text-[14px]"></i>
                                </div>
                            </div>
                        </div>
                    </label>
                </form>

            </div>

            @if ($errors->has('media'))
                <p class="mt-3 text-xs font-semibold text-red-600">{{ $errors->first('media') }}</p>
            @endif

            <div class="flex flex-col">
                <div class="flex items-center gap-2 mt-4">
                    <i class="fa-solid fa-badge-check text-[13px] text-cyan-500"></i>

                    @if(($u->plan ?? 'free') === 'pro')
                        <i class="fa-solid fa-rectangle-pro text-[#F46036] text-[18px]"></i>
                    @else
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#564D4A]/5 text-[#564D4A]/60">FREE</span>
                    @endif
                </div>

                <h1 class="text-[1.5rem] font-black text-[#564D4A] flex items-center gap-2">
                    {{ $u->name }}
                    <span class="py-0.5 px-2 text-[11px] text-white bg-[#5B2333] rounded-md font-semibold">
                        Level {{ (int)$xpMeta['level'] }}
                    </span>
                </h1>

                <div class="text-[11px] font-semibold text-[#564D4A]/60">
                    {{ $u->email }}
                </div>

                {{-- friends blijft hardcoded --}}
                <a href="#" class="w-fit text-xs text-[#5B2333] mt-4 leading-[1.3] font-semibold hover:underline">
                    4 Friends
                </a>

                <div class="flex items-center gap-2">
                    <a href="#" class="mt-2 w-fit inline-flex items-center justify-center rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 transition text-white text-xs font-semibold py-2.5 px-6">
                        Edit my profile
                    </a>
                    <a href="#" class="mt-2 w-fit inline-flex items-center justify-center rounded-xl bg-[#564D4A] hover:bg-[#564D4A]/85 transition text-white text-xs font-semibold py-2.5 px-6">
                        See all friends
                    </a>
                </div>
            </div>
        </div>

        {{-- ✅ STATISTICS (partly real) --}}
        <div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/10">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Statistics</h2>
                    <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                        Your progress overview.
                    </p>
                </div>

                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold">
                    <i class="fa-solid fa-chart-simple"></i>
                    This week
                </span>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Games played (real) --}}
                <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                    <div class="flex items-start justify-between">
                        <div class="w-10 h-10 rounded-xl bg-white border border-[#564D4A]/10 flex items-center justify-center">
                            <i class="fa-solid fa-gamepad text-[#5B2333]"></i>
                        </div>

                        <span class="text-[11px] font-bold text-[#564D4A]/50">
                            {{ $gamesPlayedWeek }} this week
                        </span>
                    </div>

                    <div class="mt-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">
                            Games Played
                        </p>
                        <p class="mt-1 text-[1.8rem] leading-none font-black text-[#564D4A]">
                            {{ $gamesPlayedTotal }}
                        </p>
                        <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">
                            Total games completed
                        </p>
                    </div>
                </div>

                {{-- Longest streak (uses user field; if 0 it shows 0) --}}
                <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                    <div class="flex items-start justify-between">
                        <div class="w-10 h-10 rounded-xl bg-white border border-[#564D4A]/10 flex items-center justify-center">
                            <i class="fa-solid fa-fire-flame-curved text-[#5B2333]"></i>
                        </div>

                        <span class="text-[11px] font-bold text-[#564D4A]/50">
                            Best
                        </span>
                    </div>

                    <div class="mt-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">
                            Longest Streak
                        </p>
                        <p class="mt-1 text-[1.8rem] leading-none font-black text-[#564D4A]">
                            {{ (int)($u->streak ?? 0) }}
                            <span class="text-sm font-black text-[#564D4A]/50">days</span>
                        </p>
                        <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">
                            Keep the streak alive
                        </p>
                    </div>
                </div>

                {{-- Achievements (still hardcoded) --}}
                <div class="rounded-2xl border border-[#564D4A]/10 bg-gradient-to-br from-[#5B2333]/15 to-[#F7F4F3] p-5">
                    <div class="flex items-start justify-between">
                        <div class="w-10 h-10 rounded-xl bg-white border border-[#564D4A]/10 flex items-center justify-center">
                            <i class="fa-solid fa-trophy text-[#5B2333]"></i>
                        </div>

                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/80 border border-[#564D4A]/10 text-[11px] font-bold text-[#5B2333]">
                            <i class="fa-solid fa-star text-[10px]"></i>
                            New
                        </span>
                    </div>

                    <div class="mt-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">
                            Achievements
                        </p>
                        <p class="mt-1 text-[1.8rem] leading-none font-black text-[#564D4A]">
                            7
                            <span class="text-sm font-black text-[#564D4A]/50">/ 24</span>
                        </p>
                        <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">
                            Unlocked badges
                        </p>
                    </div>
                </div>
            </div>

            {{-- ✅ Extra row (Daily Challenges now real + Best rank real) --}}
            <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 rounded-2xl border border-[#564D4A]/10 bg-white p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#5B2333]/10 flex items-center justify-center">
                                <i class="fa-solid fa-bolt text-[#5B2333]"></i>
                            </div>
                            <div>
                                <p class="text-sm font-extrabold text-[#564D4A] leading-tight">Daily Challenges</p>
                                <p class="text-xs font-semibold text-[#564D4A]/55">
                                    {{ is_null($limit) ? 'Unlimited (Pro)' : 'Free limit: ' . $limit . ' / day' }}
                                </p>
                            </div>
                        </div>

                        <span class="text-xs font-bold text-[#5B2333]">
                            {{ $done }}{{ is_null($limit) ? '' : ' / ' . $limit }}
                        </span>
                    </div>

                    <div>
                        <div class="w-full h-[8px] rounded-full bg-[#564D4A]/10 overflow-hidden">
                            <div class="h-full rounded-full bg-[#5B2333]" style="width: {{ $dailyPercent }}%"></div>
                        </div>

                        <div class="mt-3 flex items-center justify-between text-[11px] font-semibold text-[#564D4A]/55">
                            <span>{{ is_null($limit) ? 'Unlimited challenges' : ($remaining . ' challenges remaining') }}</span>
                            <span>Reset at 00:00</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[#564D4A]/45">
                        Best Rank
                    </p>
                    <p class="mt-2 text-[1.8rem] leading-none font-black text-[#564D4A]">
                        #{{ $bestRank }}
                    </p>
                    <p class="mt-2 text-xs font-semibold text-[#564D4A]/55">
                        Top 10 soon 👀
                    </p>

                    <a href="{{ route('leaderboard') }}"
                       class="mt-4 inline-flex items-center justify-center w-full rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 transition text-white text-xs font-semibold py-2.5">
                        View leaderboard
                    </a>
                </div>
            </div>
        </div>

        {{-- Badges blijven hardcoded (zoals je al had) --}}
        <div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/10">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Badges</h2>
                    <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                        Your unlocked milestones.
                    </p>
                </div>

                <a href="#"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-[#564D4A]/10 hover:border-[#564D4A]/20 transition text-xs font-semibold text-[#564D4A]">
                    <i class="fa-solid fa-grid"></i>
                    View all
                </a>
            </div>

            {{-- (jouw badges blok 그대로 laten) --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- GOLD --}}
                <div class="rounded-2xl border border-[#564D4A]/10 bg-gradient-to-br from-[#D6B05E]/25 to-white p-5 flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-white border border-[#564D4A]/10 flex items-center justify-center">
                            <i class="fa-solid fa-medal text-[#B88B2A] text-[18px]"></i>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/80 border border-[#564D4A]/10 text-[#B88B2A]">
                            GOLD
                        </span>
                    </div>

                    <div>
                        <p class="text-sm font-extrabold text-[#564D4A] leading-tight">365 Day Streak</p>
                        <p class="mt-1 text-xs font-semibold text-[#564D4A]/55 leading-[1.35]">
                            Logged in & played daily for a full year.
                        </p>

                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-[#564D4A]/50">Unlocked</span>
                            <span class="text-[11px] font-bold text-[#564D4A]">Jan 12, 2026</span>
                        </div>
                    </div>
                </div>

                {{-- SILVER --}}
                <div class="rounded-2xl border border-[#564D4A]/10 bg-gradient-to-br from-[#BFC6D1]/35 to-white p-5 flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-white border border-[#564D4A]/10 flex items-center justify-center">
                            <i class="fa-solid fa-shield-halved text-[#6B7280] text-[18px]"></i>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/80 border border-[#564D4A]/10 text-[#6B7280]">
                            SILVER
                        </span>
                    </div>

                    <div>
                        <p class="text-sm font-extrabold text-[#564D4A] leading-tight">No Miss Week</p>
                        <p class="mt-1 text-xs font-semibold text-[#564D4A]/55 leading-[1.35]">
                            Completed all daily challenges for 7 days straight.
                        </p>

                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-[#564D4A]/50">Unlocked</span>
                            <span class="text-[11px] font-bold text-[#564D4A]">Feb 03, 2026</span>
                        </div>
                    </div>
                </div>

                {{-- BRONZE --}}
                <div class="rounded-2xl border border-[#564D4A]/10 bg-gradient-to-br from-[#C48A5A]/25 to-white p-5 flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-white border border-[#564D4A]/10 flex items-center justify-center">
                            <i class="fa-solid fa-ranking-star text-[#9A5A2E] text-[18px]"></i>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/80 border border-[#564D4A]/10 text-[#9A5A2E]">
                            BRONZE
                        </span>
                    </div>

                    <div>
                        <p class="text-sm font-extrabold text-[#564D4A] leading-tight">First Win</p>
                        <p class="mt-1 text-xs font-semibold text-[#564D4A]/55 leading-[1.35]">
                            Won your first game.
                        </p>

                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-[#564D4A]/50">Unlocked</span>
                            <span class="text-[11px] font-bold text-[#564D4A]">Dec 22, 2025</span>
                        </div>
                    </div>
                </div>

                {{-- LOCKED --}}
                <div class="rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-white border border-[#564D4A]/10 flex items-center justify-center">
                            <i class="fa-solid fa-lock text-[#564D4A]/50 text-[18px]"></i>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-white border border-[#564D4A]/10 text-[#564D4A]/55">
                            LOCKED
                        </span>
                    </div>

                    <div class="mt-4">
                        <p class="text-sm font-extrabold text-[#564D4A] leading-tight">100 Wins</p>
                        <p class="mt-1 text-xs font-semibold text-[#564D4A]/55 leading-[1.35]">
                            Win 100 games to unlock this badge.
                        </p>

                        <div class="mt-4">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-[#564D4A]/55">
                                <span>Progress</span>
                                <span>62%</span>
                            </div>
                            <div class="mt-2 w-full h-[6px] rounded-full bg-[#564D4A]/10 overflow-hidden">
                                <div class="h-full rounded-full bg-[#5B2333]" style="width: 62%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 rounded-2xl border border-[#564D4A]/10 bg-white p-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold">
                        <i class="fa-solid fa-fire-flame-curved"></i> 30-day streak
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#564D4A]/5 text-[#564D4A] text-xs font-semibold">
                        <i class="fa-solid fa-star"></i> First achievement
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#564D4A]/5 text-[#564D4A] text-xs font-semibold">
                        <i class="fa-solid fa-bolt"></i> 10 challenges done
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#564D4A]/5 text-[#564D4A] text-xs font-semibold">
                        <i class="fa-solid fa-user-group"></i> 5 friends added
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const banner = document.getElementById('bannerUpload');
            const avatar = document.getElementById('avatarUpload');

            if (banner) {
                banner.addEventListener('change', () => {
                    if (banner.files && banner.files.length > 0) {
                        document.getElementById('bannerForm').submit();
                    }
                });
            }

            if (avatar) {
                avatar.addEventListener('change', () => {
                    if (avatar.files && avatar.files.length > 0) {
                        document.getElementById('avatarForm').submit();
                    }
                });
            }
        });
    </script>
</x-layouts.dashboard>