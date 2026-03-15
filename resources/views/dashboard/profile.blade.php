{{-- resources/views/dashboard/profile.blade.php --}}
<x-layouts.dashboard :title="'Mijn Profiel'" active="profile">
    <style>
        @keyframes sparkle1 { 0%,100%{opacity:1;transform:scale(1) rotate(0)} 50%{opacity:.4;transform:scale(.6) rotate(20deg)} }
        @keyframes sparkle2 { 0%,100%{opacity:.5;transform:scale(.7) rotate(0)} 50%{opacity:1;transform:scale(1.1) rotate(-15deg)} }
        .animate-sparkle-1 { animation: sparkle1 2s ease-in-out infinite; }
        .animate-sparkle-2 { animation: sparkle2 2.4s ease-in-out infinite .6s; }
        @keyframes rainbow-spin { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }
        .animate-rainbow-border { background:linear-gradient(90deg,#ff6b6b,#ffd93d,#6bcb77,#4d96ff,#9b59b6,#ff6b6b); background-size:200% 200%; animation:rainbow-spin 3s linear infinite; -webkit-mask:radial-gradient(circle,transparent 62%,black 65%); mask:radial-gradient(circle,transparent 62%,black 65%); }
        @keyframes fire-ring-spin { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }
        .animate-fire-ring { background:linear-gradient(90deg,#ff6b35,#ff2d2d,#ff8c00,#ff4500,#ff6b35); background-size:200% 200%; animation:fire-ring-spin 2s linear infinite; -webkit-mask:radial-gradient(circle,transparent 62%,black 65%); mask:radial-gradient(circle,transparent 62%,black 65%); }
        @keyframes galaxy-spin { 0%{transform:rotate(0);background-position:0% 50%} 100%{transform:rotate(360deg);background-position:200% 50%} }
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
        $u = auth()->user();

        $bannerUrl = $u->profile_banner ? asset('storage/' . $u->profile_banner) : null;
        $avatarUrl = $u->profile_picture ? asset('storage/' . $u->profile_picture) : null;

        $xpMeta = $u?->levelMeta() ?? ['xp' => 0, 'level' => 1, 'nextXp' => 5000, 'percent' => 0];

        $thresholds = (array) config('levels.thresholds', []);
        ksort($thresholds);

        $currentLevel = (int)($xpMeta['level'] ?? 1);
        $currentXp = (int)($xpMeta['xp'] ?? 0);
        $nextXp = (int)($xpMeta['nextXp'] ?? 0);
        $maxKey = !empty($thresholds) ? (int) max(array_keys($thresholds)) : 1;
        $prevThreshold = ($currentLevel <= 1) ? 0 : (int) ($thresholds[$currentLevel - 1] ?? 0);
        $remainingXp = max(0, $nextXp - $currentXp);

        if ($currentLevel > $maxKey) {
            $percent = 100;
        } else {
            $band = max(1, ($nextXp - $prevThreshold));
            $percent = (int) round(min(100, max(0, (($currentXp - $prevThreshold) / $band) * 100)));
        }

        $limit = $limit ?? (($u->plan === 'pro') ? null : 5);
        $done = (int) ($u->daily_challenges_done ?? 0);
        $remaining = $remaining ?? (is_null($limit) ? null : max(0, $limit - $done));
        $dailyPercent = is_null($limit) ? 100 : (int) round(min(100, ($done / max(1, $limit)) * 100));

        $stats = (array)($stats ?? []);
        $gamesPlayedTotal = (int)($stats['games_played_total'] ?? 0);
        $gamesPlayedWeek = (int)($stats['games_played_week'] ?? 0);
        $bestRank = (int)($stats['best_rank'] ?? 14);
    @endphp

    @php
        // Equipped cosmetics
        $equipped = $u->equippedCosmetics()->get()->keyBy('type');
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

        $isPro = ($u->plan ?? 'free') === 'pro';
        $acceptTypes = $isPro ? 'image/jpeg,image/png,image/webp,image/gif' : 'image/jpeg,image/png,image/webp';
        $recentAvatars = collect(\Illuminate\Support\Facades\File::files(storage_path('app/public/profile/avatars')))
            ->filter(fn($f) => in_array(strtolower($f->getExtension()), ['jpg','jpeg','png','webp','gif']))
            ->sortByDesc(fn($f) => $f->getMTime())
            ->take(6)
            ->map(fn($f) => asset('storage/profile/avatars/' . $f->getFilename()))
            ->values()
            ->all();
    @endphp

    <div class="flex flex-col gap-10"
         x-data="{
            showUpgrade: false,
            mediaModal: false,
            mediaTarget: '',
            gifSearch: '',
            gifResults: [],
            gifLoading: false,
            gifMode: false,
            uploading: false,

            openMediaPicker(target) {
                this.mediaTarget = target;
                this.mediaModal = true;
                this.gifMode = false;
                this.gifSearch = '';
                this.gifResults = [];
            },

            triggerFileUpload() {
                const inputId = this.mediaTarget === 'banner' ? 'bannerUpload' : 'avatarUpload';
                document.getElementById(inputId).click();
            },

            async searchGifs() {
                if (this.gifSearch.length < 2) return;
                this.gifLoading = true;
                try {
                    const key = '{{ config('services.tenor.key', 'AIzaSyA3bPswMBl3Rqh0sDk1AkHHMzhhXMjqjXg') }}';
                    const res = await fetch(`https://tenor.googleapis.com/v2/search?q=${encodeURIComponent(this.gifSearch)}&key=${key}&limit=8&media_filter=tinygif,gif`);
                    const data = await res.json();
                    this.gifResults = (data.results || []).map(r => ({
                        preview: r.media_formats?.tinygif?.url || r.media_formats?.gif?.url,
                        full: r.media_formats?.gif?.url || r.media_formats?.tinygif?.url,
                    }));
                } catch(e) { this.gifResults = []; }
                this.gifLoading = false;
            },

            async selectGif(url) {
                this.uploading = true;
                try {
                    const resp = await fetch(url);
                    const blob = await resp.blob();
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    const fieldName = this.mediaTarget === 'banner' ? 'profile_banner' : 'profile_picture';
                    formData.append(fieldName, blob, 'gif-' + Date.now() + '.gif');
                    const r = await fetch('{{ route('profile.media') }}', { method: 'POST', body: formData });
                    if (r.ok) window.location.reload();
                } catch(e) {}
                this.uploading = false;
            },

            selectRecent(url) {
                this.uploading = true;
                (async () => {
                    try {
                        const resp = await fetch(url);
                        const blob = await resp.blob();
                        const ext = url.split('.').pop().split('?')[0] || 'png';
                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        const fieldName = this.mediaTarget === 'banner' ? 'profile_banner' : 'profile_picture';
                        formData.append(fieldName, blob, 'recent-' + Date.now() + '.' + ext);
                        const r = await fetch('{{ route('profile.media') }}', { method: 'POST', body: formData });
                        if (r.ok) window.location.reload();
                    } catch(e) {}
                    this.uploading = false;
                })();
            }
         }">

        {{-- CLEAN TEXT HEADER --}}
        <div>
            <h1 class="text-[1.5rem] md:text-2xl font-black text-[#564D4A] tracking-tight leading-tight">
                Mijn Profiel
            </h1>
            <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
                Pas je profiel aan, bekijk statistieken en badges.
            </p>
        </div>

        {{-- PROFILE HEADER --}}
        <div class="w-full bg-white rounded-2xl p-8 border border-[#564D4A]/6">
            <div class="relative w-full h-[300px] rounded-2xl overflow-hidden flex items-end p-8" data-upload="banner">

                @if ($bannerUrl)
                    <img src="{{ $bannerUrl }}" class="absolute inset-0 w-full h-full object-cover" alt="">
                @else
                    <div class="absolute inset-0 bg-[#5B2333]"></div>
                    <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-30" alt="">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#5B2333]/95 via-[#5B2333]/70 to-transparent"></div>
                @endif

                <form id="bannerForm" method="POST" action="{{ route('profile.media') }}" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input id="bannerUpload" name="profile_banner" type="file" accept="{{ $acceptTypes }}">
                </form>

                <div class="absolute inset-0 z-[5] group cursor-pointer" @click="openMediaPicker('banner')">
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition bg-black/20"></div>
                    <div class="absolute right-6 top-6 opacity-0 group-hover:opacity-100 transition">
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/90 text-[#564D4A] text-xs font-semibold">
                            <i class="fa-solid fa-camera"></i> Banner wijzigen
                        </span>
                    </div>
                </div>

                <form id="avatarForm" method="POST" action="{{ route('profile.media') }}" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input id="avatarUpload" name="profile_picture" type="file" accept="{{ $acceptTypes }}">
                </form>

                <div class="relative z-[6] cursor-pointer group" @click.stop="openMediaPicker('avatar')">
                    {{-- Hat emoji above avatar --}}
                    @if ($eqHat)
                        <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-3xl z-20 drop-shadow-sm pointer-events-none">
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

                    <div class="relative w-26 h-26 rounded-full overflow-hidden bg-[#F7F4F3]
                        {{ $eqBorder ? $eqBorder->css_class : 'border-4 border-white' }}" data-upload="avatar">
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
                </div>
            </div>

            @if ($errors->has('media'))
                <p class="mt-3 text-xs font-semibold text-red-600">{{ $errors->first('media') }}</p>
            @endif

            <div class="flex flex-col">
                <div class="flex items-center gap-2 mt-4">
                    <span class="py-1 px-2 text-[10px] text-[#5B2333] bg-[#5B2333]/8 rounded-full font-semibold">
                        Level {{ (int)$xpMeta['level'] }}
                    </span>
                    @if($isPro)
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-yellow-300/20 text-yellow-300"><i class="fa-solid fa-crown text-yellow-300 text-[9px] mr-1"></i>PRO</span>
                    @else
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#564D4A]/5 text-[#564D4A]/60">FREE</span>
                    @endif
                </div>

                <div class="flex items-center gap-3 mt-2">
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
                    <h2 class="text-[1.5rem] font-black flex items-center gap-2 flex-wrap
                        {{ $eqNameColor ? $eqNameColor->css_class : 'text-[#564D4A]' }}">
                        {{ $u->name }}
                    </h2>
                </div>

                @php $friendCount = $u->friends()->count(); @endphp
                <a href="{{ route('friends.index') }}" class="w-fit text-xs text-[#5B2333] mt-4 leading-[1.3] font-semibold hover:underline">
                    {{ $friendCount }} {{ $friendCount === 1 ? 'Vriend' : 'Vrienden' }}
                </a>
            </div>
        </div>

        {{-- ACTIVITY FEED --}}
        @include('partials.activity-feed', ['scorePosts' => $scorePosts, 'feedUser' => $user, 'isMe' => true])

        {{-- MEDIA PICKER MODAL --}}
        <template x-if="mediaModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak
                 role="dialog" aria-modal="true" aria-labelledby="mediaPickerTitle"
                 @keydown.escape.window="mediaModal = false">
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="mediaModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 overflow-hidden" @click.stop>
                    <button @click="mediaModal = false" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-[#564D4A]/5 hover:bg-[#564D4A]/10 flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-[#564D4A]/50 text-sm"></i>
                    </button>

                    <h3 id="mediaPickerTitle" class="text-lg font-black text-[#564D4A]">Selecteer een afbeelding</h3>

                    <div x-show="uploading" x-cloak class="absolute inset-0 z-50 bg-white/90 flex items-center justify-center rounded-3xl">
                        <div class="flex items-center gap-3 text-[#564D4A] font-semibold">
                            <i class="fa-solid fa-spinner fa-spin text-[#5B2333] text-lg"></i> Uploaden...
                        </div>
                    </div>

                    <div x-show="!gifMode" class="mt-5">
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="triggerFileUpload()"
                                class="rounded-2xl border-2 border-dashed border-[#564D4A]/15 bg-[#F7F4F3] hover:bg-[#564D4A]/8 transition p-8 flex flex-col items-center justify-center gap-3 cursor-pointer">
                                <div class="w-12 h-12 rounded-xl bg-[#5B2333]/10 flex items-center justify-center">
                                    <i class="fa-solid fa-cloud-arrow-up text-[#5B2333] text-xl"></i>
                                </div>
                                <span class="text-sm font-semibold text-[#564D4A]">Afbeelding uploaden</span>
                                <span class="text-[10px] text-[#564D4A]/40 font-medium">JPG, PNG, WEBP{{ $isPro ? ', GIF' : '' }}</span>
                            </button>

                            @if($isPro)
                                <button @click="gifMode = true"
                                    class="rounded-2xl border border-[#5B2333]/15 bg-gradient-to-br from-[#5B2333]/8 to-[#7a3349]/8 hover:from-[#5B2333]/15 hover:to-[#7a3349]/15 transition p-8 flex flex-col items-center justify-center gap-3 cursor-pointer relative overflow-hidden">
                                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/10 flex items-center justify-center">
                                        <i class="fa-solid fa-gif text-[#5B2333] text-xl"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-[#564D4A]">Gifje kiezen</span>
                                    <span class="text-[10px] text-[#564D4A]/40 font-medium">Zoek via Tenor</span>
                                </button>
                            @else
                                <button @click="mediaModal = false; $nextTick(() => showUpgrade = true)"
                                    class="rounded-2xl border border-[#564D4A]/6 overflow-hidden relative cursor-pointer hover:scale-[1.02] transition-transform w-full">
                                    <div class="absolute inset-0 grid grid-cols-2 grid-rows-2">
                                        <img src="{{ asset('assets/gifs/200w.gif') }}" alt="" class="w-full h-full object-cover">
                                        <img src="{{ asset('assets/gifs/shannon-sharpe-undisputed.gif') }}" alt="" class="w-full h-full object-cover">
                                        <img src="{{ asset('assets/gifs/speed-ishowspeed.gif') }}" alt="" class="w-full h-full object-cover">
                                        <img src="{{ asset('assets/gifs/source.gif') }}" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <div class="absolute inset-0 bg-black/50"></div>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-2">
                                        <div class="absolute top-3 right-3">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-[#5B2333] text-white text-[9px] font-bold">
                                                <i class="fa-solid fa-crown text-[8px]"></i> PRO
                                            </span>
                                        </div>
                                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                            <i class="fa-solid fa-gif text-white text-xl"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-white">Gifje kiezen</span>
                                        <span class="text-[10px] text-white/60 font-medium">Alleen voor Pro</span>
                                    </div>
                                </button>
                            @endif
                        </div>

                        @if(count($recentAvatars) > 0)
                            <div class="mt-6 pt-5 border-t border-[#564D4A]/8">
                                <p class="text-sm font-bold text-[#564D4A]">Recente avatars</p>
                                <p class="text-[11px] text-[#564D4A]/40 font-medium mt-0.5">Toegang tot je {{ count($recentAvatars) }} recentste avataruploads.</p>
                                <div class="mt-3 flex items-center gap-3 flex-wrap">
                                    @foreach($recentAvatars as $recent)
                                        <button @click="selectRecent('{{ $recent }}')"
                                            class="w-14 h-14 rounded-full overflow-hidden border-2 border-[#564D4A]/10 hover:border-[#5B2333]/40 transition cursor-pointer shrink-0">
                                            <img src="{{ $recent }}" class="w-full h-full object-cover" alt="Recent avatar">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($isPro)
                    <div x-show="gifMode" x-cloak class="mt-5">
                        <button @click="gifMode = false" class="inline-flex items-center gap-1.5 text-[11px] text-[#564D4A]/40 hover:text-[#564D4A]/70 font-semibold transition mb-3 cursor-pointer">
                            <i class="fa-solid fa-arrow-left text-[9px]"></i> Terug
                        </button>

                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-[#564D4A]/30 text-sm"></i>
                            <input type="text" x-model="gifSearch" @input.debounce.500ms="searchGifs()"
                                placeholder="Zoek een GIF..."
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-[#F7F4F3] border border-[#564D4A]/6 text-sm font-medium text-[#564D4A] placeholder:text-[#564D4A]/30 focus:outline-none focus:ring-2 focus:ring-[#5B2333]/20 focus:border-[#5B2333]/30 transition">
                        </div>

                        <div x-show="gifLoading" class="mt-4 text-center py-6">
                            <i class="fa-solid fa-spinner fa-spin text-[#5B2333]"></i>
                        </div>

                        <div x-show="gifResults.length > 0 && !gifLoading" class="mt-3 grid grid-cols-2 gap-2 max-h-[300px] overflow-y-auto rounded-xl">
                            <template x-for="(gif, idx) in gifResults" :key="idx">
                                <button @click="selectGif(gif.full)"
                                    class="rounded-xl overflow-hidden aspect-square cursor-pointer hover:ring-2 hover:ring-[#5B2333]/40 transition">
                                    <img :src="gif.preview" class="w-full h-full object-cover" alt="GIF">
                                </button>
                            </template>
                        </div>

                        <div x-show="gifResults.length === 0 && !gifLoading && gifSearch.length >= 2" class="mt-4 text-center text-sm text-[#564D4A]/40 font-medium py-6">
                            Geen GIFs gevonden
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </template>

        {{-- UPGRADE MODAL --}}
        <template x-if="showUpgrade">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak
                 role="dialog" aria-modal="true" aria-labelledby="upgradeTitle"
                 @keydown.escape.window="showUpgrade = false">
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showUpgrade = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center" @click.stop>
                    <button @click="showUpgrade = false" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-[#564D4A]/5 hover:bg-[#564D4A]/10 flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-[#564D4A]/50 text-sm"></i>
                    </button>

                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#5B2333] to-[#7a3349] flex items-center justify-center mx-auto mb-5">
                        <i class="fa-solid fa-crown text-white text-2xl"></i>
                    </div>

                    <h3 id="upgradeTitle" class="text-xl font-black text-[#564D4A]">Upgrade naar Pro</h3>
                    <p class="mt-2 text-sm text-[#564D4A]/60 font-medium leading-relaxed">
                        Ontgrendel GIF-uploads en meer met <span class="font-bold text-[#5B2333]">Pro</span>!
                    </p>

                    <div class="mt-6 space-y-3">
                        <div class="flex items-center gap-3 text-left px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <i class="fa-solid fa-infinity text-[#5B2333]"></i>
                            <span class="text-sm font-semibold text-[#564D4A]">Onbeperkt spellen per dag</span>
                        </div>
                        <div class="flex items-center gap-3 text-left px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <i class="fa-solid fa-wand-magic-sparkles text-[#B88B2A]"></i>
                            <span class="text-sm font-semibold text-[#564D4A]">Custom profieluitstraling (GIF's & meer)</span>
                        </div>
                        <div class="flex items-center gap-3 text-left px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <i class="fa-solid fa-bolt text-[#E8A838]"></i>
                            <span class="text-sm font-semibold text-[#564D4A]">Exclusieve Pro badge op je profiel</span>
                        </div>
                    </div>

                    <div class="mt-7 flex flex-col gap-3">
                        <a href="{{ route('pages.pricing') }}" class="block w-full py-3.5 rounded-2xl bg-gradient-to-r from-[#5B2333] to-[#7a3349] text-white font-bold text-sm text-center hover:opacity-90 transition shadow-lg shadow-[#5B2333]/25">
                            <i class="fa-solid fa-crown mr-2"></i> Upgrade naar Pro — 1,99/maand
                        </a>
                        <button @click="showUpgrade = false" class="w-full py-3 rounded-2xl bg-[#564D4A]/5 text-[#564D4A]/60 font-semibold text-sm hover:bg-[#564D4A]/10 transition cursor-pointer">
                            Misschien later
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const banner = document.getElementById('bannerUpload');
            const avatar = document.getElementById('avatarUpload');
            const bannerArea = document.querySelector('[data-upload="banner"]');
            const avatarArea = document.querySelector('[data-upload="avatar"]');

            function showUploadingOverlay(el) {
                if (!el) return;
                const overlay = document.createElement('div');
                overlay.className = 'absolute inset-0 z-[10] bg-black/40 flex items-center justify-center';
                overlay.innerHTML = '<div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/90 text-sm font-semibold text-[#564D4A]"><i class="fa-solid fa-spinner fa-spin"></i> Uploaden...</div>';
                overlay.dataset.uploadOverlay = '1';
                el.style.position = 'relative';
                el.appendChild(overlay);
            }

            if (banner) {
                banner.addEventListener('change', () => {
                    if (banner.files && banner.files.length > 0) {
                        showUploadingOverlay(bannerArea);
                        document.getElementById('bannerForm').submit();
                    }
                });
            }

            if (avatar) {
                avatar.addEventListener('change', () => {
                    if (avatar.files && avatar.files.length > 0) {
                        showUploadingOverlay(avatarArea);
                        document.getElementById('avatarForm').submit();
                    }
                });
            }
        });
    </script>
</x-layouts.dashboard>
