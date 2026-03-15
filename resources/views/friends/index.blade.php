<x-layouts.dashboard :title="'Vrienden'" active="friends">

    {{-- CLEAN TEXT HEADER --}}
    <div>
        <h1 class="text-[1.5rem] md:text-2xl font-black text-[#564D4A] tracking-tight leading-tight">
            Vrienden
        </h1>
        <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
            Zoek spelers, stuur vriendschapsverzoeken en bekijk profielen.
        </p>
    </div>

    {{-- SEARCH --}}
    <div class="mt-6" x-data="{
        q: '',
        results: [],
        loading: false,
        open: false,
        error: '',
        async search() {
            if (this.q.length < 2) { this.results = []; this.open = false; return; }
            this.loading = true;
            this.error = '';
            try {
                const res = await fetch(`{{ route('friends.search') }}?q=${encodeURIComponent(this.q)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.results = data.users || [];
                this.open = this.results.length > 0;
            } catch(e) { this.error = 'Zoeken mislukt. Probeer het opnieuw.'; }
            this.loading = false;
        },
        async sendRequest(userId) {
            this.error = '';
            try {
                const res = await fetch('{{ route('friends.request') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ friend_id: userId }),
                });
                const data = await res.json();
                if (data.ok) {
                    const u = this.results.find(r => r.id === userId);
                    if (u) u.status = 'sent';
                } else { this.error = 'Verzoek versturen mislukt.'; }
            } catch(e) { this.error = 'Verzoek versturen mislukt. Probeer het opnieuw.'; }
        }
    }">
        <div class="relative">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-[#564D4A]/30 text-sm"></i>
                <input type="text"
                    x-model="q"
                    @input.debounce.400ms="search()"
                    @focus="if (results.length) open = true"
                    @click.outside="open = false"
                    placeholder="Zoek spelers op naam..."
                    class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-[#564D4A]/6 bg-white text-sm font-semibold text-[#564D4A] placeholder:text-[#564D4A]/30 focus:outline-none focus:ring-2 focus:ring-[#5B2333]/20 focus:border-[#5B2333]/30 transition">
            </div>

            <p x-show="error" x-cloak class="mt-2 text-[11px] font-semibold text-red-500" x-text="error"></p>

            {{-- Results dropdown --}}
            <div x-show="open" x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="absolute z-30 mt-2 w-full bg-white rounded-2xl border border-[#564D4A]/6 shadow-lg overflow-hidden">

                <template x-for="user in results" :key="user.id">
                    <div class="flex items-center justify-between gap-4 px-5 py-3.5 border-b border-[#564D4A]/5 last:border-0 hover:bg-[#F7F4F3]/60 transition">
                        <a :href="'/users/' + user.id" class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-full overflow-hidden border border-[#564D4A]/6 bg-[#F7F4F3] shrink-0">
                                <template x-if="user.profile_picture">
                                    <img :src="user.profile_picture" class="w-full h-full object-cover" alt="">
                                </template>
                                <template x-if="!user.profile_picture">
                                    <div class="w-full h-full flex items-center justify-center text-[#564D4A]/30 font-bold text-sm" x-text="user.name.charAt(0).toUpperCase()"></div>
                                </template>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-[#564D4A] truncate" x-text="user.name"></p>
                                <p class="text-[11px] font-semibold text-[#564D4A]/40">Level <span x-text="user.level"></span></p>
                            </div>
                        </a>
                        <div class="shrink-0">
                            <template x-if="user.status === 'none'">
                                <button @click.prevent="sendRequest(user.id)"
                                    class="cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 text-white text-xs font-semibold transition">
                                    <i class="fa-solid fa-user-plus text-[10px]"></i> Toevoegen
                                </button>
                            </template>
                            <template x-if="user.status === 'sent'">
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#564D4A]/8 text-[#564D4A]/50 text-xs font-semibold">
                                    <i class="fa-solid fa-clock text-[10px]"></i> Verstuurd
                                </span>
                            </template>
                            <template x-if="user.status === 'friends'">
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-green-500/10 text-green-600 text-xs font-semibold">
                                    <i class="fa-solid fa-check text-[10px]"></i> Vrienden
                                </span>
                            </template>
                            <template x-if="user.status === 'incoming'">
                                <a :href="'/users/' + user.id"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#5B2333]/10 text-[#5B2333] text-xs font-semibold hover:bg-[#5B2333]/20 transition">
                                    <i class="fa-solid fa-envelope text-[10px]"></i> Bekijk verzoek
                                </a>
                            </template>
                        </div>
                    </div>
                </template>

                <div x-show="loading" class="px-5 py-4 text-center">
                    <i class="fa-solid fa-spinner fa-spin text-[#564D4A]/30"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- INCOMING REQUESTS --}}
    @if($incoming->count() > 0)
        <div class="mt-8">
            <div class="flex items-center gap-3 mb-4">
                <h2 class="text-[1.1rem] font-extrabold text-[#564D4A]">Vriendschapsverzoeken</h2>
                <span class="min-w-6 h-6 px-2 inline-flex items-center justify-center rounded-full bg-[#5B2333] text-white text-[11px] font-bold">
                    {{ $incoming->count() }}
                </span>
            </div>

            <div class="grid gap-3">
                @foreach($incoming as $req)
                    <div class="flex items-center justify-between gap-4 p-4 rounded-2xl border border-[#564D4A]/6 bg-white"
                        x-data="{ handled: false, action: '', reqError: false }">
                        <a href="{{ route('users.profile', $req->user) }}" class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-12 h-12 rounded-full overflow-hidden border border-[#564D4A]/6 bg-[#F7F4F3] shrink-0">
                                @if($req->user->profile_picture)
                                    <img src="{{ asset('storage/' . $req->user->profile_picture) }}" class="w-full h-full object-cover" alt="{{ $req->user->name }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-[#564D4A]/30 font-bold text-lg">
                                        {{ strtoupper(substr($req->user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-[#564D4A] truncate">{{ $req->user->name }}</p>
                                <p class="text-[11px] font-semibold text-[#564D4A]/40">Level {{ (int) $req->user->level }}</p>
                                <p x-show="reqError" x-cloak class="text-[10px] font-semibold text-red-500 mt-0.5">Actie mislukt. Probeer opnieuw.</p>
                            </div>
                        </a>
                        <div class="flex items-center gap-2 shrink-0" x-show="!handled">
                            <button @click.prevent="(async () => {
                                reqError = false;
                                try {
                                    const res = await fetch('{{ route('friends.accept', $req) }}', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                    });
                                    const d = await res.json();
                                    if (d.ok) { handled = true; action = 'accepted'; }
                                    else { reqError = true; }
                                } catch(e) { reqError = true; }
                            })()"
                                class="cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 text-white text-xs font-semibold transition">
                                <i class="fa-solid fa-check text-[10px]"></i> Accepteren
                            </button>
                            <button @click.prevent="(async () => {
                                reqError = false;
                                try {
                                    const res = await fetch('{{ route('friends.decline', $req) }}', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                    });
                                    const d = await res.json();
                                    if (d.ok) { handled = true; action = 'declined'; }
                                    else { reqError = true; }
                                } catch(e) { reqError = true; }
                            })()"
                                class="cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-600 text-xs font-semibold transition">
                                <i class="fa-solid fa-xmark text-[10px]"></i> Afwijzen
                            </button>
                        </div>
                        <div x-show="handled" x-cloak class="shrink-0">
                            <span x-show="action === 'accepted'" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-green-500/10 text-green-600 text-xs font-semibold">
                                <i class="fa-solid fa-check text-[10px]"></i> Geaccepteerd
                            </span>
                            <span x-show="action === 'declined'" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#564D4A]/8 text-[#564D4A]/50 text-xs font-semibold">
                                Afgewezen
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- FRIENDS LIST --}}
    <div class="mt-8">
        <h2 class="text-[1.1rem] font-extrabold text-[#564D4A] mb-4">
            Mijn Vrienden
            @if($friends->count() > 0)
                <span class="text-[#564D4A]/30 font-bold text-sm ml-1">({{ $friends->count() }})</span>
            @endif
        </h2>

        @if($friends->count() > 0)
            <div class="grid gap-3">
                @foreach($friends as $friend)
                    <div class="flex items-center justify-between gap-4 p-4 rounded-2xl border border-[#564D4A]/6 bg-white"
                        x-data="{ removed: false, removeError: false }">
                        <a href="{{ route('users.profile', $friend) }}" class="flex items-center gap-3 min-w-0 flex-1" x-show="!removed">
                            <div class="w-12 h-12 rounded-full overflow-hidden border border-[#564D4A]/6 bg-[#F7F4F3] shrink-0">
                                @if($friend->profile_picture)
                                    <img src="{{ asset('storage/' . $friend->profile_picture) }}" class="w-full h-full object-cover" alt="{{ $friend->name }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-[#564D4A]/30 font-bold text-lg">
                                        {{ strtoupper(substr($friend->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-[#564D4A] truncate">{{ $friend->name }}</p>
                                <p class="text-[11px] font-semibold text-[#564D4A]/40">Level {{ (int) $friend->level }}</p>
                                <p x-show="removeError" x-cloak class="text-[10px] font-semibold text-red-500 mt-0.5">Verwijderen mislukt.</p>
                            </div>
                        </a>
                        <div class="flex items-center gap-2 shrink-0" x-show="!removed">
                            <a href="{{ route('users.profile', $friend) }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#564D4A]/8 hover:bg-[#564D4A]/12 text-[#564D4A] text-xs font-semibold transition">
                                <i class="fa-solid fa-user text-[10px]"></i> Profiel
                            </a>
                            <button @click.prevent="(async () => {
                                if (!confirm('Weet je zeker dat je {{ addslashes($friend->name) }} wilt verwijderen als vriend?')) return;
                                removeError = false;
                                try {
                                    const res = await fetch('/friends/{{ $friend->id }}', {
                                        method: 'DELETE',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                    });
                                    const d = await res.json();
                                    if (d.ok) removed = true;
                                    else removeError = true;
                                } catch(e) { removeError = true; }
                            })()"
                                class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-600 text-xs font-semibold transition">
                                <i class="fa-solid fa-user-minus text-[10px]"></i>
                            </button>
                        </div>
                        <div x-show="removed" x-cloak class="w-full text-center py-2">
                            <span class="text-xs font-semibold text-[#564D4A]/40">Verwijderd</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-[#564D4A]/6 bg-white p-10 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-[#F7F4F3] flex items-center justify-center mb-4">
                    <i class="fa-solid fa-user-group text-[#564D4A]/20 text-2xl"></i>
                </div>
                <p class="text-sm font-extrabold text-[#564D4A]">Nog geen vrienden</p>
                <p class="mt-1 text-xs font-semibold text-[#564D4A]/40">Zoek spelers hierboven en stuur een vriendschapsverzoek.</p>
            </div>
        @endif
    </div>

    {{-- SENT REQUESTS --}}
    @if($sent->count() > 0)
        <div class="mt-8">
            <h2 class="text-[1.1rem] font-extrabold text-[#564D4A] mb-4">Verzonden verzoeken</h2>
            <div class="grid gap-3">
                @foreach($sent as $req)
                    <div class="flex items-center justify-between gap-4 p-4 rounded-2xl border border-[#564D4A]/6 bg-white">
                        <a href="{{ route('users.profile', $req->friend) }}" class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-full overflow-hidden border border-[#564D4A]/6 bg-[#F7F4F3] shrink-0">
                                @if($req->friend->profile_picture)
                                    <img src="{{ asset('storage/' . $req->friend->profile_picture) }}" class="w-full h-full object-cover" alt="{{ $req->friend->name }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-[#564D4A]/30 font-bold text-sm">
                                        {{ strtoupper(substr($req->friend->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-[#564D4A] truncate">{{ $req->friend->name }}</p>
                                <p class="text-[11px] font-semibold text-[#564D4A]/40">Level {{ (int) $req->friend->level }}</p>
                            </div>
                        </a>
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#564D4A]/8 text-[#564D4A]/50 text-xs font-semibold shrink-0">
                            <i class="fa-solid fa-clock text-[10px]"></i> Wacht op reactie
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</x-layouts.dashboard>
