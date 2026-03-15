<x-layouts.dashboard :title="$profileUser->name" active="friends">
    <style>
        @keyframes sparkle1 { 0%,100%{opacity:1;transform:scale(1) rotate(0)} 50%{opacity:.4;transform:scale(.6) rotate(20deg)} }
        @keyframes sparkle2 { 0%,100%{opacity:.5;transform:scale(.7) rotate(0)} 50%{opacity:1;transform:scale(1.1) rotate(-15deg)} }
        .animate-sparkle-1 { animation: sparkle1 2s ease-in-out infinite; }
        .animate-sparkle-2 { animation: sparkle2 2.4s ease-in-out infinite .6s; }
        @keyframes rainbow-spin { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }
        .animate-rainbow-border { background:linear-gradient(90deg,#ff6b6b,#ffd93d,#6bcb77,#4d96ff,#9b59b6,#ff6b6b); background-size:200% 200%; animation:rainbow-spin 3s linear infinite; -webkit-mask:radial-gradient(circle,transparent 62%,black 65%); mask:radial-gradient(circle,transparent 62%,black 65%); }
        @keyframes fire-ring-spin { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }
        .animate-fire-ring { background:linear-gradient(90deg,#ff6b35,#ff2d2d,#ff8c00,#ff4500,#ff6b35); background-size:200% 200%; animation:fire-ring-spin 2s linear infinite; -webkit-mask:radial-gradient(circle,transparent 62%,black 65%); mask:radial-gradient(circle,transparent 62%,black 65%); }
        @keyframes galaxy-spin { 0%{transform:rotate(0)} 100%{transform:rotate(360deg)} }
        .animate-galaxy { background:conic-gradient(from 0deg,#6366f1,#8b5cf6,#d946ef,#3b82f6,#6366f1); animation:galaxy-spin 6s linear infinite; -webkit-mask:radial-gradient(circle,transparent 62%,black 65%); mask:radial-gradient(circle,transparent 62%,black 65%); }
        @keyframes shadow-pulse { 0%,100%{box-shadow:0 0 8px rgba(0,0,0,.3)} 50%{box-shadow:0 0 20px rgba(0,0,0,.5)} }
        .animate-shadow-aura { background:radial-gradient(circle,transparent 60%,rgba(0,0,0,.15) 80%,transparent 100%); animation:shadow-pulse 3s ease-in-out infinite; -webkit-mask:radial-gradient(circle,transparent 62%,black 65%); mask:radial-gradient(circle,transparent 62%,black 65%); }
        @keyframes name-rainbow { 0%{color:#ff6b6b} 16%{color:#ffd93d} 33%{color:#6bcb77} 50%{color:#4d96ff} 66%{color:#9b59b6} 83%{color:#ff6b6b} 100%{color:#ff6b6b} }
        .animate-name-rainbow { animation:name-rainbow 4s linear infinite; }
        @keyframes name-fire { 0%,100%{color:#ff4500;text-shadow:0 0 6px rgba(255,69,0,.4)} 25%{color:#ff8c00;text-shadow:0 0 10px rgba(255,140,0,.5)} 50%{color:#ffd700;text-shadow:0 0 14px rgba(255,215,0,.5)} 75%{color:#ff6b35;text-shadow:0 0 10px rgba(255,107,53,.4)} }
        .animate-name-fire { animation:name-fire 2s ease-in-out infinite; }
        @keyframes name-neon-pulse { 0%,100%{color:#38bdf8;text-shadow:0 0 4px rgba(56,189,248,.3)} 50%{color:#7dd3fc;text-shadow:0 0 12px rgba(56,189,248,.6),0 0 24px rgba(56,189,248,.2)} }
        .animate-name-neon-pulse { animation:name-neon-pulse 2s ease-in-out infinite; }
        @keyframes name-glitch { 0%,100%{color:#22d3ee;text-shadow:none} 5%{color:#f43f5e;text-shadow:-2px 0 #22d3ee} 10%{color:#22d3ee;text-shadow:2px 0 #f43f5e} 15%{color:#a855f7;text-shadow:none} 20%{color:#22d3ee;text-shadow:-1px 0 #a855f7,1px 0 #f43f5e} 25%,100%{color:#22d3ee;text-shadow:none} }
        .animate-name-glitch { animation:name-glitch 3s steps(1) infinite; }
        @keyframes badge-rainbow { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }
        .animate-badge-rainbow { background:linear-gradient(90deg,#ff6b6b,#ffd93d,#6bcb77,#4d96ff,#9b59b6,#ff6b6b); background-size:200% 200%; animation:badge-rainbow 3s linear infinite; color:white; border-color:transparent; text-shadow:0 1px 2px rgba(0,0,0,.15); }
    </style>

    {{-- Tailwind safelist: bg-red-100 text-red-700 border-red-200 bg-orange-100 text-orange-700 border-orange-200 bg-yellow-100 text-yellow-700 border-yellow-200 bg-green-100 text-green-700 border-green-200 bg-emerald-100 text-emerald-700 border-emerald-200 bg-cyan-100 text-cyan-700 border-cyan-200 bg-blue-100 text-blue-700 border-blue-200 bg-indigo-100 text-indigo-700 border-indigo-200 bg-purple-100 text-purple-700 border-purple-200 bg-pink-100 text-pink-700 border-pink-200 bg-slate-100 text-slate-700 border-slate-200 --}}
    @php
        $avatarUrl = $profileUser->profile_picture ? asset('storage/' . $profileUser->profile_picture) : null;
        $bannerUrl = $profileUser->profile_banner ? asset('storage/' . $profileUser->profile_banner) : null;
        $isMe = auth()->id() === $profileUser->id;

        // Equipped cosmetics
        $equipped = $profileUser->equippedCosmetics()->get()->keyBy('type');
        $eqBorder    = $equipped->get('border');
        $eqHat       = $equipped->get('hat');
        $eqEffect    = $equipped->get('effect');
        $eqFlair     = $equipped->get('badge_flair');
        $eqNameColor = $equipped->get('name_color');

        $hatEmojis = [
            'hat-party' => '🎉', 'hat-cap' => '🧢', 'hat-beanie' => '🧶',
            'hat-wizard' => '🧙', 'hat-santa' => '🎅', 'hat-cowboy' => '🤠',
            'hat-pirate' => '🏴‍☠️', 'hat-chef' => '👨‍🍳', 'hat-crown' => '👑',
            'hat-horns' => '😈', 'hat-astronaut' => '🚀', 'hat-halo' => '😇',
        ];

        $flairMeta = [
            'flair-noob'           => ['emoji' => '🐣', 'bg' => 'bg-green-100',   'text' => 'text-green-700',   'border' => 'border-green-200'],
            'flair-oeps'           => ['emoji' => '🫣', 'bg' => 'bg-orange-100',  'text' => 'text-orange-700',  'border' => 'border-orange-200'],
            'flair-zzz'            => ['emoji' => '😴', 'bg' => 'bg-indigo-100',  'text' => 'text-indigo-700',  'border' => 'border-indigo-200'],
            'flair-sus'            => ['emoji' => '👀', 'bg' => 'bg-red-100',     'text' => 'text-red-700',     'border' => 'border-red-200'],
            'flair-skill-issue'    => ['emoji' => '💀', 'bg' => 'bg-slate-100',   'text' => 'text-slate-700',   'border' => 'border-slate-200'],
            'flair-tryhard'        => ['emoji' => '😤', 'bg' => 'bg-red-100',     'text' => 'text-red-700',     'border' => 'border-red-200'],
            'flair-touch-grass'    => ['emoji' => '🌿', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
            'flair-gg-ez'          => ['emoji' => '😎', 'bg' => 'bg-sky-100',     'text' => 'text-sky-700',     'border' => 'border-sky-200'],
            'flair-carried'        => ['emoji' => '🧳', 'bg' => 'bg-amber-100',   'text' => 'text-amber-700',   'border' => 'border-amber-200'],
            'flair-big-brain'      => ['emoji' => '🧠', 'bg' => 'bg-pink-100',    'text' => 'text-pink-700',    'border' => 'border-pink-200'],
            'flair-geen-leven'     => ['emoji' => '💻', 'bg' => 'bg-violet-100',  'text' => 'text-violet-700',  'border' => 'border-violet-200'],
            'flair-speedrunner'    => ['emoji' => '⚡', 'bg' => 'bg-yellow-100',  'text' => 'text-yellow-700',  'border' => 'border-yellow-200'],
            'flair-1iq'            => ['emoji' => '🪱', 'bg' => 'bg-orange-100',  'text' => 'text-orange-700',  'border' => 'border-orange-200'],
            'flair-custom-gold'    => ['emoji' => '✏️', 'bg' => 'bg-yellow-100',  'text' => 'text-yellow-800',  'border' => 'border-yellow-300'],
            'flair-custom-rainbow' => ['emoji' => '🌈', 'bg' => 'bg-gradient-to-r from-pink-100 via-purple-100 to-cyan-100', 'text' => 'text-purple-700', 'border' => 'border-purple-200'],
            'flair-goat'           => ['emoji' => '🐐', 'bg' => 'bg-amber-100',   'text' => 'text-amber-800',   'border' => 'border-amber-300'],
        ];
    @endphp

    {{-- Back link --}}
    <div class="mb-6">
        <a href="{{ route('friends.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[#564D4A]/50 hover:text-[#564D4A] transition">
            <i class="fa-solid fa-arrow-left text-[10px]"></i> Terug naar Vrienden
        </a>
    </div>

    {{-- PROFILE CARD --}}
    <div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/6">

        {{-- Banner + Avatar (same layout as own profile) --}}
        <div class="relative w-full h-[300px] rounded-2xl overflow-hidden flex items-end p-8">
            @if ($bannerUrl)
                <img src="{{ $bannerUrl }}" class="absolute inset-0 w-full h-full object-cover" alt="Profielbanner van {{ $profileUser->name }}">
            @else
                <div class="absolute inset-0 bg-[#5B2333]"></div>
                <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-30" alt="">
                <div class="absolute inset-0 bg-gradient-to-r from-[#5B2333]/95 via-[#5B2333]/70 to-transparent"></div>
            @endif

            {{-- Avatar with cosmetics --}}
            <div class="relative z-10">
                {{-- Hat --}}
                @if ($eqHat)
                    <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-3xl z-20 pointer-events-none">
                        {{ $hatEmojis[$eqHat->slug] ?? '🎩' }}
                    </span>
                @endif

                {{-- Effect glow --}}
                @if ($eqEffect && in_array($eqEffect->slug, ['effect-rainbow', 'effect-fire-ring', 'effect-galaxy', 'effect-shadow-aura']))
                    <div class="absolute -inset-2 rounded-full z-[5] pointer-events-none
                        {{ $eqEffect->slug === 'effect-rainbow' ? 'animate-rainbow-border opacity-70' : '' }}
                        {{ $eqEffect->slug === 'effect-fire-ring' ? 'animate-fire-ring opacity-80' : '' }}
                        {{ $eqEffect->slug === 'effect-galaxy' ? 'animate-galaxy opacity-60' : '' }}
                        {{ $eqEffect->slug === 'effect-shadow-aura' ? 'animate-shadow-aura opacity-60' : '' }}
                    "></div>
                @endif

                {{-- Effect particles --}}
                @if ($eqEffect)
                    @php
                        $particleMap = [
                            'effect-sparkle'      => ['✨', '💫'],
                            'effect-hearts'       => ['💕', '💗'],
                            'effect-snowflakes'   => ['❄️', '❄️'],
                            'effect-electric-arc' => ['⚡', '⚡'],
                            'effect-galaxy'       => ['⭐', '✨'],
                            'effect-fire-ring'    => ['🔥', '🔥'],
                        ];
                        $particles = $particleMap[$eqEffect->slug] ?? null;
                    @endphp
                    @if ($particles)
                        <span class="absolute -top-1 -right-1 text-lg z-20 pointer-events-none animate-sparkle-1">{{ $particles[0] }}</span>
                        <span class="absolute -bottom-1 -left-1 text-sm z-20 pointer-events-none animate-sparkle-2">{{ $particles[1] }}</span>
                    @endif
                @endif

                <div class="w-26 h-26 rounded-full overflow-hidden bg-[#F7F4F3]
                    {{ $eqBorder ? $eqBorder->css_class : 'border-4 border-white' }}">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" class="w-full h-full object-cover" alt="{{ $profileUser->name }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                            <span class="text-[#564D4A] font-black text-lg">
                                {{ strtoupper(mb_substr($profileUser->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div class="mt-4 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex flex-col">
                <div class="flex items-center gap-2">
                    <span class="py-1 px-2 text-[10px] text-[#5B2333] bg-[#5B2333]/8 rounded-full font-semibold">
                        Level {{ (int) $xpMeta['level'] }}
                    </span>
                    @if(($profileUser->plan ?? 'free') === 'pro')
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-yellow-300/20 text-yellow-300"><i class="fa-solid fa-crown text-yellow-300 text-[9px] mr-1"></i>PRO</span>
                    @else
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#564D4A]/5 text-[#564D4A]/60">FREE</span>
                    @endif
                </div>

                <div class="flex items-center gap-3 mt-2">
                    {{-- Badge flair --}}
                    @if ($eqFlair)
                        @php
                            $isCustomFlair = str_contains($eqFlair->slug, 'custom');
                            $customData = null;
                            if ($isCustomFlair && $eqFlair->pivot->custom_value) {
                                $customData = json_decode($eqFlair->pivot->custom_value, true);
                            }
                            if ($isCustomFlair && $customData) {
                                $cc = $customData['color'] ?? 'slate';
                                $isRainbow = ($cc === 'rainbow');
                                $fm = [
                                    'emoji' => $customData['emoji'] ?? '✦',
                                    'bg' => $isRainbow ? 'animate-badge-rainbow' : "bg-{$cc}-100",
                                    'text' => $isRainbow ? '' : "text-{$cc}-700",
                                    'border' => $isRainbow ? '' : "border-{$cc}-200",
                                ];
                                $flairLabel = $customData['text'] ?? $eqFlair->name;
                            } elseif ($isCustomFlair) {
                                $fm = $flairMeta[$eqFlair->slug] ?? ['emoji' => '✦', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200'];
                                $flairLabel = $eqFlair->pivot->custom_value ?? $eqFlair->name;
                            } else {
                                $fm = $flairMeta[$eqFlair->slug] ?? ['emoji' => '✦', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200'];
                                $flairLabel = $eqFlair->name;
                            }
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $fm['bg'] }} {{ $fm['text'] }} {{ $fm['border'] }} w-fit">
                            <span class="text-xs leading-none">{{ $fm['emoji'] }}</span> {{ $flairLabel }}
                        </span>
                    @endif
                    <h1 class="text-[1.5rem] font-black flex items-center gap-2 flex-wrap
                        {{ $eqNameColor ? $eqNameColor->css_class : 'text-[#564D4A]' }}">
                        {{ $profileUser->name }}
                    </h1>
                </div>

                <p class="mt-2 text-xs font-semibold text-[#564D4A]/50">
                    {{ $friendCount }} {{ $friendCount === 1 ? 'Vriend' : 'Vrienden' }}
                </p>
            </div>

            {{-- Action buttons --}}
            @if(!$isMe)
                <div class="shrink-0" x-data="{ status: '{{ $status }}', loading: false, actionError: false }">
                    {{-- Send request --}}
                    <template x-if="status === 'none'">
                        <button :disabled="loading" @click.prevent="(async () => {
                            loading = true;
                            actionError = false;
                            try {
                                const res = await fetch('{{ route('friends.request') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ friend_id: {{ $profileUser->id }} }),
                                });
                                const data = await res.json();
                                if (data.ok) status = 'sent';
                                else actionError = true;
                            } catch(e) { actionError = true; }
                            loading = false;
                        })()"
                            class="cursor-pointer inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 text-white text-xs font-semibold transition">
                            <i class="fa-solid fa-user-plus text-[11px]"></i>
                            <span x-text="loading ? 'Bezig...' : 'Vriend toevoegen'"></span>
                        </button>
                    </template>

                    {{-- Pending (I sent) --}}
                    <template x-if="status === 'sent'">
                        <span class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#564D4A]/8 text-[#564D4A]/50 text-xs font-semibold">
                            <i class="fa-solid fa-clock text-[11px]"></i> Verzoek verstuurd
                        </span>
                    </template>

                    {{-- Incoming (they sent to me) --}}
                    <template x-if="status === 'incoming'">
                        <div class="flex items-center gap-2">
                            <button :disabled="loading" @click.prevent="(async () => {
                                loading = true;
                                actionError = false;
                                try {
                                    const res = await fetch('/friends/{{ $friendship?->id }}/accept', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                    });
                                    const d = await res.json();
                                    if (d.ok) status = 'friends';
                                    else actionError = true;
                                } catch(e) { actionError = true; }
                                loading = false;
                            })()"
                                class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 text-white text-xs font-semibold transition">
                                <i class="fa-solid fa-check text-[11px]"></i> Accepteren
                            </button>
                            <button :disabled="loading" @click.prevent="(async () => {
                                loading = true;
                                actionError = false;
                                try {
                                    const res = await fetch('/friends/{{ $friendship?->id }}/decline', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                    });
                                    const d = await res.json();
                                    if (d.ok) status = 'declined';
                                    else actionError = true;
                                } catch(e) { actionError = true; }
                                loading = false;
                            })()"
                                class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-600 text-xs font-semibold transition">
                                <i class="fa-solid fa-xmark text-[11px]"></i> Afwijzen
                            </button>
                        </div>
                    </template>

                    {{-- Already friends --}}
                    <template x-if="status === 'friends'">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-green-500/10 text-green-600 text-xs font-semibold">
                                <i class="fa-solid fa-user-check text-[11px]"></i> Vrienden
                            </span>
                            <button @click.prevent="(async () => {
                                if (!confirm('Weet je zeker dat je {{ addslashes($profileUser->name) }} wilt verwijderen als vriend?')) return;
                                loading = true;
                                actionError = false;
                                try {
                                    const res = await fetch('/friends/{{ $profileUser->id }}', {
                                        method: 'DELETE',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                    });
                                    const d = await res.json();
                                    if (d.ok) status = 'none';
                                    else actionError = true;
                                } catch(e) { actionError = true; }
                                loading = false;
                            })()"
                                class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-600 text-xs font-semibold transition">
                                <i class="fa-solid fa-user-minus text-[10px]"></i>
                            </button>
                        </div>
                    </template>

                    {{-- Declined --}}
                    <template x-if="status === 'declined'">
                        <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#564D4A]/8 text-[#564D4A]/50 text-xs font-semibold">
                            Afgewezen
                        </span>
                    </template>

                    {{-- Error feedback --}}
                    <p x-show="actionError" x-cloak class="mt-2 text-[11px] font-semibold text-red-500">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Actie mislukt. Probeer het opnieuw.
                    </p>
                </div>
            @else
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#564D4A] hover:bg-[#564D4A]/85 text-white text-xs font-semibold transition">
                    <i class="fa-solid fa-pen text-[10px]"></i> Mijn Dashboard
                </a>
            @endif
        </div>
    </div>

    {{-- ACTIVITY FEED --}}
    <div class="mt-6">
        @include('partials.activity-feed', ['scorePosts' => $scorePosts, 'feedUser' => $profileUser, 'isMe' => (auth()->id() === $profileUser->id)])
    </div>

    {{-- FRIENDS LIST --}}
    <div id="friendsSection" class="mt-6 w-full bg-white rounded-2xl p-8 border border-[#564D4A]/6">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h2 class="text-[1.2rem] font-extrabold text-[#564D4A]">Vrienden van {{ $profileUser->name }}</h2>
                <p class="mt-1 text-xs font-semibold text-[#564D4A]/50 leading-[1.3]">
                    {{ $friendCount }} {{ $friendCount === 1 ? 'vriend' : 'vrienden' }}
                </p>
            </div>
            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#564D4A]/8 text-[#564D4A]/60 text-xs font-semibold">
                <i class="fa-solid fa-user-group"></i> {{ $friendCount }}
            </span>
        </div>

        @if($profileFriends->isEmpty())
            <div class="rounded-2xl border border-[#564D4A]/6 bg-[#F7F4F3] p-10 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-white flex items-center justify-center mb-4">
                    <i class="fa-solid fa-user-group text-[#564D4A]/20 text-2xl"></i>
                </div>
                <p class="text-sm font-extrabold text-[#564D4A]">Nog geen vrienden</p>
                <p class="mt-1 text-xs font-semibold text-[#564D4A]/40">{{ $profileUser->name }} heeft nog geen vrienden toegevoegd.</p>
            </div>
        @else
            <div class="grid gap-3">
                @foreach($profileFriends as $pf)
                    @php
                        $fu = $pf['user'];
                        $fStatus = $pf['status'];
                        $fuAvatar = $fu->profile_picture ? asset('storage/' . $fu->profile_picture) : null;
                    @endphp
                    <div class="flex items-center justify-between gap-4 p-4 rounded-2xl border border-[#564D4A]/6 bg-white hover:bg-[#F7F4F3] transition"
                         x-data="{ friendStatus: '{{ $fStatus }}', loading: false }">
                        <a href="{{ route('users.profile', $fu) }}" class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-11 h-11 rounded-full overflow-hidden border border-[#564D4A]/6 bg-[#F7F4F3] shrink-0">
                                @if($fuAvatar)
                                    <img src="{{ $fuAvatar }}" class="w-full h-full object-cover" alt="{{ $fu->name }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-[#564D4A]/30 font-bold text-sm">
                                        {{ strtoupper(mb_substr($fu->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-extrabold text-[#564D4A] truncate">{{ $fu->name }}</p>
                                    @if(($fu->plan ?? 'free') === 'pro')
                                        <i class="fa-solid fa-rectangle-pro text-[#F46036] text-[14px]"></i>
                                    @endif
                                </div>
                                <p class="text-[11px] font-semibold text-[#564D4A]/40">Level {{ (int) $fu->level }}</p>
                            </div>
                        </a>

                        <div class="shrink-0">
                            {{-- It's me --}}
                            <template x-if="friendStatus === 'me'">
                                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold">
                                    <i class="fa-solid fa-user text-[10px]"></i> Jij
                                </span>
                            </template>

                            {{-- Already friends --}}
                            <template x-if="friendStatus === 'friends'">
                                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-green-500/10 text-green-600 text-xs font-semibold">
                                    <i class="fa-solid fa-check text-[10px]"></i> Vrienden
                                </span>
                            </template>

                            {{-- Can add --}}
                            <template x-if="friendStatus === 'none'">
                                <button :disabled="loading" @click.prevent="(async () => {
                                    loading = true;
                                    try {
                                        const res = await fetch('{{ route('friends.request') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                            },
                                            body: JSON.stringify({ friend_id: {{ $fu->id }} }),
                                        });
                                        const data = await res.json();
                                        if (data.ok) friendStatus = 'sent';
                                    } catch(e) {}
                                    loading = false;
                                })()"
                                    class="cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 text-white text-xs font-semibold transition">
                                    <i class="fa-solid fa-user-plus text-[10px]"></i>
                                    <span x-text="loading ? 'Bezig...' : 'Toevoegen'"></span>
                                </button>
                            </template>

                            {{-- Request sent --}}
                            <template x-if="friendStatus === 'sent'">
                                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-[#564D4A]/8 text-[#564D4A]/50 text-xs font-semibold">
                                    <i class="fa-solid fa-clock text-[10px]"></i> Verstuurd
                                </span>
                            </template>

                            {{-- Incoming request --}}
                            <template x-if="friendStatus === 'incoming'">
                                <a href="{{ route('users.profile', $fu) }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold hover:bg-[#5B2333]/20 transition">
                                    <i class="fa-solid fa-envelope text-[10px]"></i> Bekijk verzoek
                                </a>
                            </template>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-layouts.dashboard>
