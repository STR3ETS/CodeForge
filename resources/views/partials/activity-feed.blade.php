{{-- Activity feed — shows shared score posts --}}
{{-- Required: $scorePosts (Collection of ScorePost), $feedUser (User model) --}}
@php
    $gameIcons = [
        'find-the-emoji' => 'fa-solid fa-face-smile-wink',
        'word-forge'     => 'fa-solid fa-font',
        'sequence-rush'  => 'fa-solid fa-arrow-up-1-9',
        'flag-guess'     => 'fa-solid fa-flag',
        'block-drop'     => 'fa-solid fa-cube',
        'sudoku'         => 'fa-solid fa-table-cells-large',
        'memory-grid'    => 'fa-solid fa-brain',
        'color-match'    => 'fa-solid fa-palette',
        'reaction-time'  => 'fa-solid fa-bolt',
        'maze-runner'    => 'fa-solid fa-route',
        'color-sort'     => 'fa-solid fa-layer-group',
    ];

    $gameColors = [
        'find-the-emoji' => ['bg' => 'bg-[#FBE2D8]', 'text' => 'text-[#c0705a]'],
        'word-forge'     => ['bg' => 'bg-[#D6E4F0]', 'text' => 'text-[#4a7fa5]'],
        'sequence-rush'  => ['bg' => 'bg-[#D9EAD3]', 'text' => 'text-[#5a8a4e]'],
        'flag-guess'     => ['bg' => 'bg-[#FFF3CD]', 'text' => 'text-[#9a7a20]'],
        'block-drop'     => ['bg' => 'bg-[#E8D5F0]', 'text' => 'text-[#7a4fa0]'],
        'sudoku'         => ['bg' => 'bg-[#D0EAE8]', 'text' => 'text-[#3a8a85]'],
        'memory-grid'    => ['bg' => 'bg-[#E8D5F0]', 'text' => 'text-[#7a4fa0]'],
        'color-match'    => ['bg' => 'bg-[#FFE4E6]', 'text' => 'text-[#be123c]'],
        'reaction-time'  => ['bg' => 'bg-[#FEF9C3]', 'text' => 'text-[#a16207]'],
        'maze-runner'    => ['bg' => 'bg-[#DBEAFE]', 'text' => 'text-[#1d4ed8]'],
        'color-sort'     => ['bg' => 'bg-[#FEF3C7]', 'text' => 'text-[#b45309]'],
    ];

    $feedAvatar = $feedUser->profile_picture ? asset('storage/' . $feedUser->profile_picture) : null;
@endphp

<div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/6">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Activiteit</h2>
            <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                Gedeelde scores en resultaten.
            </p>
        </div>
        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#564D4A]/8 text-[#564D4A]/60 text-xs font-semibold">
            <i class="fa-solid fa-clock-rotate-left"></i> {{ $scorePosts->count() }}
        </span>
    </div>

    @if($scorePosts->isEmpty())
        <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-10 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-white flex items-center justify-center mb-4">
                <i class="fa-solid fa-share-nodes text-[#564D4A]/20 text-2xl"></i>
            </div>
            <p class="text-sm font-extrabold text-[#564D4A]">Nog geen activiteit</p>
            <p class="mt-1 text-xs font-semibold text-[#564D4A]/40">
                {{ isset($isMe) && $isMe ? 'Deel je scores na het oplossen van een game!' : $feedUser->name . ' heeft nog geen scores gedeeld.' }}
            </p>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($scorePosts as $post)
                @php
                    $icon = $gameIcons[$post->game_key] ?? 'fa-solid fa-gamepad';
                    $colors = $gameColors[$post->game_key] ?? ['bg' => 'bg-[#F7F4F3]', 'text' => 'text-[#564D4A]'];
                    $timeAgo = $post->created_at->diffForHumans();
                @endphp
                <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-5">
                    {{-- Header: avatar + name + time --}}
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-full overflow-hidden border border-[#564D4A]/8 bg-[#F7F4F3] shrink-0">
                            @if($feedAvatar)
                                <img src="{{ $feedAvatar }}" class="w-full h-full object-cover" alt="{{ $feedUser->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-[#564D4A]/30 font-bold text-xs">
                                    {{ strtoupper(mb_substr($feedUser->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-extrabold text-[#564D4A] truncate">{{ $feedUser->name }}</p>
                            <p class="text-[10px] font-semibold text-[#564D4A]/35">{{ $timeAgo }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl {{ $colors['bg'] }} flex items-center justify-center shrink-0">
                            <i class="{{ $icon }} {{ $colors['text'] }} text-xs"></i>
                        </div>
                    </div>

                    {{-- Message --}}
                    @if($post->message)
                        <p class="text-sm text-[#564D4A] font-medium leading-relaxed mb-3 [&_a]:text-[#5B2333] [&_a]:underline [&_a]:hover:opacity-70">{!! preg_replace('~(https?://[^\s<]+)~', '<a href="$1">$1</a>', e($post->message)) !!}</p>
                    @endif

                    {{-- Stats bar --}}
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-[#F7F4F3] text-[11px] font-semibold text-[#564D4A]/70">
                            <i class="fa-solid fa-gamepad text-[9px]"></i> {{ $post->game_name }}
                        </span>
                        @if($post->formatted_time)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-[#F7F4F3] text-[11px] font-semibold text-[#564D4A]/70">
                                <i class="fa-solid fa-stopwatch text-[9px]"></i> {{ $post->formatted_time }}
                            </span>
                        @endif
                        @if($post->attempts)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-[#F7F4F3] text-[11px] font-semibold text-[#564D4A]/70">
                                <i class="fa-solid fa-arrows-rotate text-[9px]"></i> {{ $post->attempts }} {{ $post->attempts === 1 ? 'zet' : 'zetten' }}
                            </span>
                        @endif
                        @if($post->percentile)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-green-500/10 text-[11px] font-bold text-green-600">
                                <i class="fa-solid fa-trophy text-[9px]"></i> Top {{ 100 - $post->percentile }}%
                            </span>
                        @endif
                        <span class="text-[10px] font-semibold text-[#564D4A]/30 ml-auto">
                            {{ $post->puzzle_date->format('d M Y') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
