{{-- resources/views/dashboard/partials/quest-section.blade.php --}}
@php
    $questList   = collect($questList ?? [])->values()->all();

    $diffOrder = ['Easy', 'Medium', 'Hard', 'Extreme'];
    $diffLabelsNl = ['easy' => 'Makkelijk', 'medium' => 'Gemiddeld', 'hard' => 'Moeilijk', 'extreme' => 'Extreem'];
    $groupedQuests = collect($questList)->groupBy(fn($q) => ucfirst(strtolower($q['tag'] ?? 'Other')));
    $tagStyleMap = [
        'easy'    => 'bg-[#8E936D]/15 text-[#6b7052]',
        'medium'  => 'bg-[#F4A261]/15 text-[#b8712d]',
        'hard'    => 'bg-[#CE796B]/15 text-[#a04f43]',
        'extreme' => 'bg-[#5B2333]/15 text-[#5B2333]',
    ];
@endphp

<div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">{{ $sectionTitle }}</h2>
            <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">{{ $sectionDesc }}</p>
        </div>
        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#564D4A]/8 text-[#564D4A]/50 text-xs font-semibold shrink-0">
            <i class="fa-solid fa-rotate"></i>
            {{ $resetLabel }}
        </span>
    </div>

    {{-- Quest cards grouped by difficulty in one continuous grid --}}
    <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
        @if(empty($questList))
            <div class="col-span-3 rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-5">
                <p class="text-sm font-semibold text-[#564D4A]/60">Geen quests geconfigureerd.</p>
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

                        <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5 flex flex-col"
                             x-data="{ claimed: @js($claimed), loading: false, error: false }">

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
                                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold {{ $tagStyle }}">
                                    <i class="fa-solid fa-signal text-[9px]"></i> {{ $diffLabelsNl[$tag] ?? ucfirst($tag) }}
                                </span>
                            </div>

                            {{-- Progress --}}
                            <div class="mt-4 flex items-center justify-between text-[11px] font-semibold text-[#564D4A]/55">
                                <span>Voortgang</span>
                                <span class="font-bold text-[#564D4A]">{{ $progress }} / {{ $goal }}</span>
                            </div>
                            <div class="mt-1.5 w-full h-[6px] rounded-full bg-[#564D4A]/10 overflow-hidden">
                                <div class="h-full rounded-full {{ $isDone ? 'bg-[#8E936D]' : 'bg-[#564D4A]/25' }}"
                                     style="width: {{ $percent }}%"></div>
                            </div>

                            {{-- Reward --}}
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-[#564D4A]/55 flex items-center gap-1.5">
                                    <i class="fa-solid fa-coins text-[#564D4A]/35"></i> Beloning
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold
                                    {{ $isDone ? 'bg-[#5B2333]/10 text-[#5B2333]' : 'bg-[#F7F4F3] text-[#564D4A]/60' }}">
                                    {{ $q['reward'] ?? '+0 XP' }}
                                </span>
                            </div>

                            {{-- Claim button --}}
                            <div class="mt-4 pt-3 border-t border-[#564D4A]/8">
                                @if(!$isDone)
                                    <button disabled
                                        class="w-full inline-flex items-center justify-center rounded-xl py-2.5 text-xs font-semibold bg-white border border-[#564D4A]/6 text-[#564D4A]/40 cursor-not-allowed">
                                        <i class="fa-solid fa-lock mr-2"></i> Nog niet voltooid
                                    </button>
                                @else
                                    {{-- Claimed state --}}
                                    <button x-show="claimed" disabled
                                        class="w-full inline-flex items-center justify-center rounded-xl py-2.5 text-xs font-semibold bg-[#8E936D]/15 text-[#6b7052] cursor-not-allowed">
                                        <i class="fa-solid fa-check mr-2"></i> Geclaimd
                                    </button>

                                    {{-- Claimable state --}}
                                    <button x-show="!claimed" :disabled="loading"
                                        @click.prevent="(async () => {
                                            loading = true;
                                            try {
                                                const r = await fetch('{{ route('dashboard.daily.quests.claim.single') }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'X-Requested-With': 'XMLHttpRequest',
                                                    },
                                                    body: JSON.stringify({ quest_key: '{{ addslashes($q['key']) }}', quest_type: '{{ addslashes($q['quest_type'] ?? 'daily') }}' }),
                                                });
                                                const data = await r.json();
                                                if (data.ok) { claimed = true; error = false; }
                                                else { error = true; }
                                            } catch (e) { error = true; }
                                            loading = false;
                                        })()"
                                        :class="loading ? 'opacity-60 cursor-wait' : 'hover:bg-[#5B2333]/85 cursor-pointer'"
                                        class="w-full inline-flex items-center justify-center rounded-xl py-2.5 text-xs font-semibold bg-[#5B2333] text-white transition">
                                        <i class="fa-solid fa-gift mr-2"></i>
                                        <span x-text="loading ? 'Bezig...' : 'Beloning claimen'"></span>
                                    </button>

                                    {{-- Error feedback --}}
                                    <p x-show="error" x-cloak class="mt-2 text-[11px] font-semibold text-red-500 text-center">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Claimen mislukt. Probeer het opnieuw.
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            @endforeach
        @endif
    </div>
</div>
