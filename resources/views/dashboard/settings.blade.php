{{-- resources/views/dashboard/settings.blade.php --}}
<x-layouts.dashboard :title="'Instellingen'" active="settings">
    @php $u = auth()->user(); @endphp

    <div x-data="{
        name: {{ Js::from($u->name) }},
        email: {{ Js::from($u->email) }},
        currentPassword: '',
        newPassword: '',
        newPasswordConfirm: '',
        saving: false,
        savingPw: false,
        message: '',
        msgType: '',
        pwMessage: '',
        pwMsgType: '',

        flash(msg, type, target = 'profile') {
            if (target === 'password') {
                this.pwMessage = msg;
                this.pwMsgType = type;
                setTimeout(() => { this.pwMessage = ''; }, 4000);
            } else {
                this.message = msg;
                this.msgType = type;
                setTimeout(() => { this.message = ''; }, 4000);
            }
        },

        async saveProfile() {
            if (this.saving) return;
            if (!this.name.trim() || !this.email.trim()) {
                this.flash('Vul alle velden in.', 'error');
                return;
            }
            this.saving = true;
            try {
                const res = await fetch('{{ route('settings.profile') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name: this.name.trim(), email: this.email.trim() }),
                });
                const data = await res.json();
                if (data.ok) {
                    this.flash('Profiel bijgewerkt!', 'success');
                } else {
                    this.flash(data.error || 'Er ging iets mis.', 'error');
                }
            } catch(e) {
                this.flash('Er ging iets mis.', 'error');
            }
            this.saving = false;
        },

        async savePassword() {
            if (this.savingPw) return;
            if (!this.currentPassword || !this.newPassword) {
                this.flash('Vul alle velden in.', 'error', 'password');
                return;
            }
            if (this.newPassword.length < 8) {
                this.flash('Nieuw wachtwoord moet minimaal 8 tekens zijn.', 'error', 'password');
                return;
            }
            if (this.newPassword !== this.newPasswordConfirm) {
                this.flash('Wachtwoorden komen niet overeen.', 'error', 'password');
                return;
            }
            this.savingPw = true;
            try {
                const res = await fetch('{{ route('settings.password') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        current_password: this.currentPassword,
                        password: this.newPassword,
                        password_confirmation: this.newPasswordConfirm,
                    }),
                });
                const data = await res.json();
                if (data.ok) {
                    this.currentPassword = '';
                    this.newPassword = '';
                    this.newPasswordConfirm = '';
                    this.flash('Wachtwoord gewijzigd!', 'success', 'password');
                } else {
                    this.flash(data.error || 'Er ging iets mis.', 'error', 'password');
                }
            } catch(e) {
                this.flash('Er ging iets mis.', 'error', 'password');
            }
            this.savingPw = false;
        },
    }">
        {{-- Page header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-black text-[#564D4A]">Instellingen</h1>
            <p class="mt-1 text-sm font-semibold text-[#564D4A]/50">Beheer je account gegevens.</p>
        </div>

        <div class="grid gap-6">
            {{-- Profile info card --}}
            <div class="bg-white rounded-2xl border border-[#564D4A]/6 p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-[#5B2333]/10 flex items-center justify-center">
                        <i class="fa-solid fa-user-pen text-[#5B2333]"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-[#564D4A]">Profiel gegevens</h2>
                        <p class="text-[11px] font-semibold text-[#564D4A]/40">Wijzig je naam en e-mailadres</p>
                    </div>
                </div>

                {{-- Flash --}}
                <div x-show="message" x-cloak x-transition
                    class="mb-4 px-4 py-3 rounded-xl text-sm font-semibold"
                    :class="msgType === 'success' ? 'bg-green-500/10 text-green-600' : 'bg-red-500/10 text-red-600'"
                    x-text="message"></div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[#564D4A]/60 mb-1.5">Naam</label>
                        <input type="text" x-model="name" maxlength="50"
                            class="w-full rounded-xl border-2 border-[#E8E2DF] bg-[#F7F4F3] px-4 py-3 text-sm text-[#564D4A] font-medium placeholder-[#564D4A]/30 focus:border-[#5B2333] focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#564D4A]/60 mb-1.5">E-mailadres</label>
                        <input type="email" x-model="email" maxlength="100"
                            class="w-full rounded-xl border-2 border-[#E8E2DF] bg-[#F7F4F3] px-4 py-3 text-sm text-[#564D4A] font-medium placeholder-[#564D4A]/30 focus:border-[#5B2333] focus:outline-none transition">
                    </div>
                </div>

                <button @click="saveProfile()" :disabled="saving"
                    class="mt-5 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 text-white text-sm font-semibold transition cursor-pointer disabled:opacity-40">
                    <i class="fa-solid fa-check text-xs" x-show="!saving"></i>
                    <i class="fa-solid fa-spinner fa-spin text-xs" x-show="saving" x-cloak></i>
                    <span x-text="saving ? 'Opslaan...' : 'Opslaan'"></span>
                </button>
            </div>

            {{-- Password card --}}
            <div class="bg-white rounded-2xl border border-[#564D4A]/6 p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-[#5B2333]/10 flex items-center justify-center">
                        <i class="fa-solid fa-lock text-[#5B2333]"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-[#564D4A]">Wachtwoord wijzigen</h2>
                        <p class="text-[11px] font-semibold text-[#564D4A]/40">Kies een sterk wachtwoord van minimaal 8 tekens</p>
                    </div>
                </div>

                {{-- Flash --}}
                <div x-show="pwMessage" x-cloak x-transition
                    class="mb-4 px-4 py-3 rounded-xl text-sm font-semibold"
                    :class="pwMsgType === 'success' ? 'bg-green-500/10 text-green-600' : 'bg-red-500/10 text-red-600'"
                    x-text="pwMessage"></div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[#564D4A]/60 mb-1.5">Huidig wachtwoord</label>
                        <input type="password" x-model="currentPassword"
                            class="w-full rounded-xl border-2 border-[#E8E2DF] bg-[#F7F4F3] px-4 py-3 text-sm text-[#564D4A] font-medium placeholder-[#564D4A]/30 focus:border-[#5B2333] focus:outline-none transition"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#564D4A]/60 mb-1.5">Nieuw wachtwoord</label>
                        <input type="password" x-model="newPassword"
                            class="w-full rounded-xl border-2 border-[#E8E2DF] bg-[#F7F4F3] px-4 py-3 text-sm text-[#564D4A] font-medium placeholder-[#564D4A]/30 focus:border-[#5B2333] focus:outline-none transition"
                            placeholder="Minimaal 8 tekens">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#564D4A]/60 mb-1.5">Bevestig nieuw wachtwoord</label>
                        <input type="password" x-model="newPasswordConfirm"
                            class="w-full rounded-xl border-2 border-[#E8E2DF] bg-[#F7F4F3] px-4 py-3 text-sm text-[#564D4A] font-medium placeholder-[#564D4A]/30 focus:border-[#5B2333] focus:outline-none transition"
                            placeholder="Herhaal nieuw wachtwoord">
                    </div>
                </div>

                <button @click="savePassword()" :disabled="savingPw"
                    class="mt-5 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#5B2333] hover:bg-[#5B2333]/85 text-white text-sm font-semibold transition cursor-pointer disabled:opacity-40">
                    <i class="fa-solid fa-key text-xs" x-show="!savingPw"></i>
                    <i class="fa-solid fa-spinner fa-spin text-xs" x-show="savingPw" x-cloak></i>
                    <span x-text="savingPw ? 'Wijzigen...' : 'Wachtwoord wijzigen'"></span>
                </button>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
