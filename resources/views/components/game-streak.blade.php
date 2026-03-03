{{-- resources/views/components/game-streak.blade.php --}}
@props(['streak'])

<div
    x-data="gameStreak(@js($streak))"
    x-init="init()"
    @cf:streak.window="apply($event.detail)"
    class="w-full rounded-2xl border border-[#564D4A]/10 overflow-hidden"
>
    {{-- 🔥 Header (fire vibe) --}}
    <div class="relative p-6 bg-gradient-to-br from-[#F97316]/25 via-[#F59E0B]/15 to-white">
        {{-- decorative blobs --}}
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-[#F97316]/20 blur-2xl"></div>
        <div class="absolute -bottom-14 -left-10 w-56 h-56 rounded-full bg-[#EF4444]/10 blur-2xl"></div>

        <div class="relative flex items-start justify-between gap-4">
            <div>
                <p class="text-[1.15rem] font-black text-[#564D4A] leading-tight">
                    You've got a streak of
                    <span class="text-[#F97316]" x-text="current"></span>
                    <span class="text-[#F97316]">days</span>
                </p>

                <p class="mt-1 text-xs font-semibold text-[#564D4A]/60">
                    Play daily to keep your streak going.
                </p>

            </div>
            
            <div class="shrink-0">
                <div class="w-12 h-12 rounded-2xl bg-white/75 border border-white/60 ring-4 ring-[#F97316]/20 flex items-center justify-center">
                    <i class="fa-solid fa-fire text-[#F97316] text-[18px]"></i>
                </div>
            </div>
        </div>

        <div class="mt-5 flex w-full justify-between items-center gap-2">
            <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#F97316]">
                <i class="fa-solid fa-fire text-[#F97316]/40"></i>
                Best Streak: <span class="font-black -ml-1 -mr-1" x-text="best"></span> <span class="font-black">days(s)</span>
            </span>

            <span class="inline-flex items-center gap-2 text-xs font-semibold text-amber-500">
                <i class="fa-solid fa-wand-magic-sparkles text-amber-500/40"></i>
                Remaining Skips: <span class="font-black -ml-1" x-text="jokers"></span>
            </span>
        </div>

        {{-- 7-day row --}}
        <div class="relative mt-3 rounded-2xl bg-white/70 border border-white/60 p-4">
            <div class="flex items-center justify-between">
                <template x-for="(d, idx) in days" :key="idx">
                    <div class="flex flex-col items-center gap-2">
                        <div class="text-[10px] font-black uppercase tracking-wider"
                             :class="d.is_today ? 'text-[#B45309]' : 'text-[#564D4A]/50'"
                             x-text="d.dow"></div>

                        <div class="w-10 h-10 rounded-full border flex items-center justify-center shadow-sm"
                             :class="dotClass(d)">
                            <template x-if="!!d.solved">
                                <i class="fa-solid fa-check text-[12px]"></i>
                            </template>
                            <template x-if="!d.solved">
                                <span class="text-[14px] font-black leading-none">•</span>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- 🏅 Milestones (scrollable) --}}
    <div class="bg-white p-6">
        <div class="flex items-center justify-between gap-3">
            <p class="text-sm font-extrabold text-[#564D4A]">Badges</p>
            <p class="text-[11px] font-semibold text-[#564D4A]/55">Scroll →</p>
        </div>

        <div class="mt-3 -mx-6 px-6 overflow-x-auto no-scrollbar">
            <div class="flex gap-3 min-w-max pr-2">
                <template x-for="m in milestones" :key="m.days">
                    <div class="w-[170px] shrink-0 rounded-2xl border border-[#564D4A]/10 p-4"
                         :class="milestoneCardClass(m)">
                        <div class="flex items-start justify-between gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-white border border-[#564D4A]/10 ring-4 flex items-center justify-center"
                                 :class="tone(m.tone).ring">
                                <i class="text-[16px]" :class="m.icon + ' ' + tone(m.tone).icon"></i>
                            </div>

                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black border"
                                  :class="tone(m.tone).pill">
                                <span x-text="m.days"></span>&nbsp;days
                            </span>
                        </div>

                        <p class="mt-3 text-sm font-black text-[#564D4A] leading-tight" x-text="m.label"></p>

                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-[11px] font-semibold"
                                  :class="isUnlocked(m) ? 'text-[#8E936D]' : 'text-[#564D4A]/45'"
                                  x-text="isUnlocked(m) ? 'Unlocked' : 'Locked'"></span>

                            <template x-if="isUnlocked(m)">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-extrabold text-[#F97316]">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                            </template>

                            <template x-if="!isUnlocked(m)">
                                <span class="text-[11px] font-semibold text-[#564D4A]/45">
                                    <span x-text="Math.max(0, m.days - best)"></span> to go
                                </span>
                            </template>
                        </div>

                        <div class="mt-3">
                            <div class="w-full h-[6px] rounded-full bg-[#564D4A]/10 overflow-hidden">
                                <div class="h-full rounded-full bg-[#F97316]"
                                     :style="'width:' + pct(m.days) + '%'"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    /* Chrome / Safari / Edge (Chromium) */
    .no-scrollbar::-webkit-scrollbar { width: 0px; height: 0px; }
    .no-scrollbar::-webkit-scrollbar-thumb { background: transparent; }

    /* Firefox */
    .no-scrollbar { scrollbar-width: none; -ms-overflow-style: none; }
</style>

<script>
    // ✅ Global helper voor Alpine x-data
    if (!window.gameStreak) {
        window.gameStreak = function (initial) {
            const milestones = [
                { days: 1,   label: 'Warm-up',      icon: 'fa-solid fa-fire-flame-curved', tone: 'amber' },
                { days: 7,   label: 'Week Warrior', icon: 'fa-solid fa-bolt',              tone: 'amber' },
                { days: 14,  label: 'Two Weeks',    icon: 'fa-solid fa-shield-halved',     tone: 'orange' },
                { days: 21,  label: 'Habit Mode',   icon: 'fa-solid fa-seedling',          tone: 'orange' },

                { days: 30,  label: 'Month Streak', icon: 'fa-solid fa-medal',             tone: 'orange' },
                { days: 45,  label: 'Unstoppable',  icon: 'fa-solid fa-rocket',            tone: 'orange' },
                { days: 60,  label: 'Blazing',      icon: 'fa-solid fa-fire',              tone: 'red' },
                { days: 75,  label: 'Inferno',      icon: 'fa-solid fa-meteor',            tone: 'red' },

                { days: 90,  label: 'Hall of Fame', icon: 'fa-solid fa-trophy',            tone: 'red' },
                { days: 120, label: 'Champion',     icon: 'fa-solid fa-crown',             tone: 'gold' },
                { days: 150, label: 'Legend',       icon: 'fa-solid fa-star',              tone: 'gold' },
                { days: 200, label: 'Mythic',       icon: 'fa-solid fa-gem',               tone: 'gold' },

                { days: 250, label: 'Immortal',     icon: 'fa-solid fa-wand-magic-sparkles', tone: 'gold' },
                { days: 300, label: 'Titan',        icon: 'fa-solid fa-mountain',          tone: 'gold' },
                { days: 365, label: '1 Year Flame', icon: 'fa-solid fa-crown',             tone: 'gold' },
            ];

            return {
                current: parseInt(initial?.current ?? 0, 10),
                best: parseInt(initial?.best ?? 0, 10),
                jokers: parseInt(initial?.jokers ?? 0, 10),
                days: Array.isArray(initial?.days) ? initial.days : [],
                milestones,

                init() {},

                // ✅ dit is de live update (komt van jouw fetch solve/guess)
                apply(payload) {
                    if (!payload) return;
                    this.current = parseInt(payload.current ?? this.current, 10);
                    this.best    = parseInt(payload.best ?? this.best, 10);
                    this.jokers  = parseInt(payload.jokers ?? this.jokers, 10);
                    this.days    = Array.isArray(payload.days) ? payload.days : this.days;
                },

                isUnlocked(m) {
                    return this.best >= parseInt(m.days, 10);
                },

                pct(goalDays) {
                    const g = Math.max(1, parseInt(goalDays, 10));
                    return Math.round(Math.min(100, (this.best / g) * 100));
                },

                dotClass(d) {
                    const solved = !!d?.solved;
                    const isToday = !!d?.is_today;

                    if (solved) return 'bg-[#F97316] border-[#F97316] text-white';
                    if (isToday) return 'bg-white border-[#F97316] text-[#F97316]';
                    return 'bg-white border-[#564D4A]/15 text-[#564D4A]/35';
                },

                milestoneCardClass(m) {
                    return this.isUnlocked(m) ? 'bg-[#F7F4F3]' : 'bg-white opacity-70';
                },

                tone(tone) {
                    switch (tone) {
                        case 'amber':
                            return {
                                pill: 'bg-[#F59E0B]/15 text-[#B45309] border-[#F59E0B]/20',
                                icon: 'text-[#F59E0B]',
                                ring: 'ring-[#F59E0B]/25',
                            };
                        case 'orange':
                            return {
                                pill: 'bg-[#F97316]/15 text-[#C2410C] border-[#F97316]/20',
                                icon: 'text-[#F97316]',
                                ring: 'ring-[#F97316]/25',
                            };
                        case 'red':
                            return {
                                pill: 'bg-[#EF4444]/12 text-[#B91C1C] border-[#EF4444]/20',
                                icon: 'text-[#EF4444]',
                                ring: 'ring-[#EF4444]/25',
                            };
                        case 'gold':
                            return {
                                pill: 'bg-[#D6B05E]/18 text-[#B88B2A] border-[#D6B05E]/25',
                                icon: 'text-[#B88B2A]',
                                ring: 'ring-[#D6B05E]/30',
                            };
                        default:
                            return {
                                pill: 'bg-[#564D4A]/5 text-[#564D4A]/70 border-[#564D4A]/10',
                                icon: 'text-[#564D4A]/60',
                                ring: 'ring-[#564D4A]/15',
                            };
                    }
                },
            };
        };
    }
</script>