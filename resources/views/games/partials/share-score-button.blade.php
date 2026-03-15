{{-- Share score modal — include inside solved section --}}
{{-- Required: $gameKey (string) --}}
<div x-data="{
    showModal: false,
    shared: false,
    sentTo: [],
    sharing: false,
    loading: false,
    message: '',
    defaultMessage: '',
    tab: 'profile',
    friends: [],
    loadingFriends: false,
    sendingTo: null,

    async openShare() {
        this.showModal = true;
        this.tab = 'profile';
        this.loading = true;
        try {
            const res = await fetch('{{ route('games.share-preview') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ game_key: '{{ $gameKey }}' }),
            });
            const data = await res.json();
            if (data.ok) {
                this.defaultMessage = data.default_message;
                this.message = data.default_message;
            }
        } catch(e) {}
        this.loading = false;
        this.$nextTick(() => { this.$refs.msgInput?.focus(); });
    },

    async submitShare() {
        this.sharing = true;
        try {
            const res = await fetch('{{ route('games.share-score') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ game_key: '{{ $gameKey }}', message: this.message }),
            });
            const data = await res.json();
            if (data.ok) {
                this.shared = true;
                this.showModal = false;
            }
        } catch(e) {}
        this.sharing = false;
    },

    async switchToSend() {
        this.tab = 'send';
        if (this.friends.length === 0) {
            this.loadingFriends = true;
            try {
                const res = await fetch('{{ route('chat.conversations') }}', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if (data.ok) this.friends = data.conversations.map(c => c.user);
            } catch(e) {}
            this.loadingFriends = false;
        }
    },

    async sendToFriend(friendId) {
        this.sendingTo = friendId;
        try {
            const res = await fetch('{{ route('chat.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ receiver_id: friendId, body: this.message }),
            });
            const data = await res.json();
            if (data.ok) this.sentTo.push(friendId);
        } catch(e) {}
        this.sendingTo = null;
    },
}" class="mt-4">
    {{-- Trigger button --}}
    <button
        x-show="!shared"
        @click="openShare()"
        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 text-white text-sm font-semibold transition cursor-pointer">
        <i class="fa-solid fa-share-nodes text-xs"></i>
        Deel je score
    </button>

    {{-- Success state (profile share) --}}
    <div x-show="shared" x-cloak
        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-green-500/10 text-green-600 text-sm font-semibold">
        <i class="fa-solid fa-check text-xs"></i>
        Gedeeld op je profiel!
    </div>

    {{-- Modal --}}
    <template x-teleport="body">
        <div x-show="showModal" x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            @keydown.escape.window="showModal = false">
            <div class="absolute inset-0 bg-[#564D4A]/60 backdrop-blur-md" @click="showModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden" @click.stop>

                {{-- Header --}}
                <div class="p-6 pb-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#5B2333]/10 flex items-center justify-center">
                                <i class="fa-solid fa-share-nodes text-[#5B2333]"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-[#564D4A]">Deel je score</h3>
                                <p class="text-[11px] font-semibold text-[#564D4A]/40" x-text="tab === 'profile' ? 'Post op je profiel' : 'Stuur naar een vriend'"></p>
                            </div>
                        </div>
                        <button @click="showModal = false"
                            class="w-8 h-8 rounded-full bg-[#564D4A]/5 hover:bg-[#564D4A]/10 flex items-center justify-center transition cursor-pointer">
                            <i class="fa-solid fa-xmark text-[#564D4A]/50 text-sm"></i>
                        </button>
                    </div>

                    {{-- Tabs --}}
                    <div class="flex gap-1 mt-4 bg-[#F7F4F3] rounded-xl p-1">
                        <button @click="tab = 'profile'"
                            :class="tab === 'profile' ? 'bg-white shadow-sm text-[#5B2333]' : 'text-[#564D4A]/50 hover:text-[#564D4A]'"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition cursor-pointer">
                            <i class="fa-solid fa-user text-[10px]"></i> Profiel
                        </button>
                        <button @click="switchToSend()"
                            :class="tab === 'send' ? 'bg-white shadow-sm text-[#5B2333]' : 'text-[#564D4A]/50 hover:text-[#564D4A]'"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition cursor-pointer">
                            <i class="fa-solid fa-paper-plane text-[10px]"></i> Stuur naar vriend
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-6">
                    {{-- Loading --}}
                    <div x-show="loading" class="flex items-center justify-center py-8">
                        <i class="fa-solid fa-spinner fa-spin text-[#5B2333] text-xl"></i>
                    </div>

                    <div x-show="!loading" x-cloak>
                        {{-- Message editor (shared between tabs) --}}
                        <label class="block text-xs font-bold text-[#564D4A]/60 mb-2">Je bericht</label>
                        <textarea
                            x-ref="msgInput"
                            x-model="message"
                            maxlength="500"
                            rows="3"
                            class="w-full rounded-xl border-2 border-[#E8E2DF] bg-[#F7F4F3] px-4 py-3 text-sm text-[#564D4A] font-medium placeholder-[#564D4A]/30 focus:border-[#5B2333] focus:outline-none transition resize-none"
                            placeholder="Schrijf iets..."
                        ></textarea>
                        <div class="flex items-center justify-between mt-2">
                            <button @click="message = defaultMessage"
                                class="text-[11px] font-semibold text-[#5B2333] hover:text-[#5B2333]/70 transition cursor-pointer">
                                <i class="fa-solid fa-rotate-right text-[9px] mr-0.5"></i> Herstel standaardtekst
                            </button>
                            <span class="text-[11px] font-semibold text-[#564D4A]/30"
                                x-text="message.length + '/500'"></span>
                        </div>

                        {{-- TAB: Profile --}}
                        <div x-show="tab === 'profile'" class="mt-5">
                            <button @click="submitShare()"
                                :disabled="sharing || !message.trim()"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 text-white text-sm font-semibold transition disabled:opacity-40 cursor-pointer">
                                <i class="fa-solid fa-user text-xs" x-show="!sharing"></i>
                                <i class="fa-solid fa-spinner fa-spin text-xs" x-show="sharing" x-cloak></i>
                                <span x-text="sharing ? 'Delen...' : 'Post op je profiel'"></span>
                            </button>
                        </div>

                        {{-- TAB: Send to friend --}}
                        <div x-show="tab === 'send'" class="mt-4">
                            <div x-show="loadingFriends" class="flex items-center justify-center py-6">
                                <i class="fa-solid fa-spinner fa-spin text-[#5B2333]"></i>
                            </div>

                            <div x-show="!loadingFriends && friends.length === 0" class="text-center py-6">
                                <p class="text-sm font-semibold text-[#564D4A]/40">Geen vrienden gevonden.</p>
                            </div>

                            <div x-show="!loadingFriends && friends.length > 0" class="max-h-[200px] overflow-y-auto -mx-1 px-1 space-y-1.5">
                                <template x-for="f in friends" :key="f.id">
                                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-[#564D4A]/6 bg-white">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-9 h-9 rounded-full overflow-hidden bg-[#F7F4F3] border border-[#564D4A]/6 shrink-0">
                                                <template x-if="f.profile_picture_url">
                                                    <img :src="f.profile_picture_url" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!f.profile_picture_url">
                                                    <div class="w-full h-full flex items-center justify-center text-[#564D4A]/30 font-bold text-[10px]"
                                                        x-text="f.initials"></div>
                                                </template>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-[#564D4A] truncate" x-text="f.name"></p>
                                                <p class="text-[10px] font-semibold text-[#564D4A]/35" x-text="'Level ' + f.level"></p>
                                            </div>
                                        </div>

                                        {{-- Sent state --}}
                                        <template x-if="sentTo.includes(f.id)">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-green-500/10 text-green-600 text-[11px] font-semibold shrink-0">
                                                <i class="fa-solid fa-check text-[9px]"></i> Verstuurd
                                            </span>
                                        </template>

                                        {{-- Send button --}}
                                        <template x-if="!sentTo.includes(f.id)">
                                            <button @click="sendToFriend(f.id)"
                                                :disabled="sendingTo === f.id || !message.trim()"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-[#5B2333] hover:bg-[#5B2333]/85 text-white text-[11px] font-semibold transition cursor-pointer disabled:opacity-40 shrink-0">
                                                <i class="fa-solid fa-paper-plane text-[9px]" x-show="sendingTo !== f.id"></i>
                                                <i class="fa-solid fa-spinner fa-spin text-[9px]" x-show="sendingTo === f.id" x-cloak></i>
                                                Stuur
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
