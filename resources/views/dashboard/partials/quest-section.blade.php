{{-- resources/views/dashboard/partials/quest-section.blade.php --}}
@php
    $questList   = collect($questList ?? [])->values()->all();
    $doneCount   = collect($questList)->filter(fn($q) => !empty($q['is_done']))->count();
    $totalCount  = count($questList);
    $pct         = (int) round(($doneCount / max(1, $totalCount)) * 100);

    $diffOrder = ['Easy', 'Medium', 'Hard', 'Extreme'];
    $groupedQuests = collect($questList)->groupBy(fn($q) => ucfirst(strtolower($q['tag'] ?? 'Other')));
    $tagStyleMap = [
        'easy'    => 'bg-[#8E936D]/15 text-[#6b7052]',
        'medium'  => 'bg-[#F4A261]/15 text-[#b8712d]',
        'hard'    => 'bg-[#CE796B]/15 text-[#a04f43]',
        'extreme' => 'bg-[#5B2333]/15 text-[#5B2333]',
    ];
@endphp

<div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/10">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">{{ $sectionTitle }}</h2>
            <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">{{ $sectionDesc }}</p>
        </div>
        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold shrink-0">
            <i class="fa-solid fa-rotate"></i>
            {{ $resetLabel }}
        </span>
    </div>

    {{-- Progress bar --}}
    <div class="mt-5 rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-4">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white border border-[#564D4A]/10 flex items-center justify-center">
                    <i class="fa-solid fa-bolt text-[#5B2333] text-sm"></i>
                </div>
                <p class="text-xs font-semibold text-[#564D4A]/60">
                    <span class="font-extrabold text-[#564D4A]">{{ $doneCount }}</span> / {{ $totalCount }} completed
                </p>
            </div>
            <span class="text-xs font-bold text-[#5B2333]">{{ $pct }}%</span>
        </div>
        <div class="mt-3 w-full h-[7px] rounded-full bg-[#564D4A]/10 overflow-hidden">
            <div class="h-full rounded-full bg-[#5B2333] transition-all" style="width: {{ $pct }}%"></div>
        </div>
    </div>

    {{-- Quest cards grouped by difficulty in one continuous grid --}}
    <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
        @if(empty($questList))
            <div class="col-span-3 rounded-2xl border border-[#564D4A]/10 bg-[#F7F4F3] p-5">
                <p class="text-sm font-semibold text-[#564D4A]/60">No quests configured.</p>
            </div>
        @else
            @foreach($diffOrder as $diffLabel)
                @if($groupedQuests->has($diffLabel))
                    @php $questsInGroup = $groupedQuests->get($diffLabel); @endphp

                    @foreach($questsInGroup as $q)
                        @php
                            $isDone   = !empty($q['is_done']);
                            $claimed  = !empty($q['claimed']);
                            $progress = (int)($q['progress'] ?? 0);
                            $goal     = max(1, (int)($q['goal'] ?? 1));
                            $percent  = (int) round(min(100, ($progress / $goal) * 100));
                            $tag      = strtolower((string)($q['tag'] ?? ''));
                            $tagStyle = $tagStyleMap[$tag] ?? 'bg-[#564D4A]/5 text-[#564D4A]/50';
                        @endphp

                        <div class="rounded-2xl border border-[#564D4A]/10 bg-white p-5 flex flex-col">

                            {{-- Top: icon + title + tag --}}
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex flex-col gap-3 min-w-0">
                                    <div class="w-11 h-11 shrink-0 rounded-2xl bg-[#5B2333]/10 flex items-center justify-center">
                                        <i class="{{ $q['icon'] ?? 'fa-solid fa-bolt' }} text-[#5B2333] text-[16px]"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-extrabold text-[#564D4A] leading-tight">{{ $q['title'] ?? 'Quest' }}</p>
                                        <p class="mt-0.5 text-[11px] font-semibold text-[#564D4A]/55 leading-snug">{{ $q['desc'] ?? '' }}</p>
                                    </div>
                                </div>
                                @if($claimed)
                                    <span class="shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-[#8E936D]/15 text-[#6b7052]">
                                        <i class="fa-solid fa-check text-[9px]"></i> Claimed
                                    </span>
                                @elseif($isDone)
                                    <span class="shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-[#5B2333]/10 text-[#5B2333]">
                                        <i class="fa-solid fa-check text-[9px]"></i> Done
                                    </span>
                                @else
                                    <span class="shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold {{ $tagStyle }}">
                                        <i class="fa-solid fa-signal text-[9px]"></i> {{ ucfirst($tag) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Progress --}}
                            <div class="mt-4 flex items-center justify-between text-[11px] font-semibold text-[#564D4A]/55">
                                <span>Progress</span>
                                <span class="font-bold text-[#564D4A]">{{ $progress }} / {{ $goal }}</span>
                            </div>
                            <div class="mt-1.5 w-full h-[6px] rounded-full bg-[#564D4A]/10 overflow-hidden">
                                <div class="h-full rounded-full {{ $isDone ? 'bg-[#5B2333]' : 'bg-[#564D4A]/25' }}"
                                     style="width: {{ $percent }}%"></div>
                            </div>

                            {{-- Reward --}}
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-[#564D4A]/55 flex items-center gap-1.5">
                                    <i class="fa-solid fa-coins text-[#564D4A]/35"></i> Reward
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold
                                    {{ $isDone ? 'bg-[#5B2333]/10 text-[#5B2333]' : 'bg-[#F7F4F3] text-[#564D4A]/60' }}">
                                    {{ $q['reward'] ?? '+0 XP' }}
                                </span>
                            </div>

                            {{-- Claim button --}}
                            <div class="mt-4 pt-3 border-t border-[#564D4A]/8">
                                @if($claimed)
                                    <button disabled
                                        class="w-full inline-flex items-center justify-center rounded-xl py-2.5 text-xs font-semibold bg-[#8E936D]/15 text-[#6b7052] cursor-not-allowed">
                                        <i class="fa-solid fa-check mr-2"></i> Claimed
                                    </button>
                                @elseif($isDone)
                                    <form method="POST" action="{{ route('dashboard.daily.quests.claim.single') }}">
                                        @csrf
                                        <input type="hidden" name="quest_key"  value="{{ $q['key'] }}">
                                        <input type="hidden" name="quest_type" value="{{ $q['quest_type'] ?? 'daily' }}">
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center rounded-xl py-2.5 text-xs font-semibold bg-[#5B2333] hover:bg-[#5B2333]/85 text-white transition">
                                            <i class="fa-solid fa-gift mr-2"></i> Claim reward
                                        </button>
                                    </form>
                                @else
                                    <button disabled
                                        class="w-full inline-flex items-center justify-center rounded-xl py-2.5 text-xs font-semibold bg-white border border-[#564D4A]/10 text-[#564D4A]/40 cursor-not-allowed">
                                        <i class="fa-solid fa-lock mr-2"></i> Not completed
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            @endforeach
        @endif
    </div>
</div>
