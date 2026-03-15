<x-layouts.auth :title="'Login - ' . config('app.name')">
    <div class="w-full max-w-[420px]">
        <div class="mb-6">
            <h1 class="text-2xl font-black text-[#564D4A] tracking-tight">Welkom terug</h1>
            <p class="text-sm text-[#564D4A]/40 mt-1">Log in om verder te spelen.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl bg-red-50 border border-red-100 p-4 mb-5">
                <div class="flex items-center gap-2 mb-1.5">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-xs"></i>
                    <p class="text-xs font-bold text-red-700">Er ging iets mis</p>
                </div>
                <ul class="text-xs text-red-600 space-y-0.5 ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <a href="{{ route('auth.google') }}"
            class="flex items-center justify-center gap-2.5 w-full bg-white border border-[#564D4A]/8 hover:border-[#564D4A]/20 hover:shadow-sm transition-all duration-200 rounded-xl py-3 px-4 text-sm text-[#564D4A] font-semibold cursor-pointer">
            <svg class="w-4 h-4" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Inloggen met Google
        </a>

        <div class="flex items-center gap-3 my-6">
            <div class="flex-1 h-px bg-[#564D4A]/8"></div>
            <span class="text-[10px] uppercase text-[#564D4A]/30 font-bold tracking-wider">of met e-mail</span>
            <div class="flex-1 h-px bg-[#564D4A]/8"></div>
        </div>

        <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label class="text-xs font-semibold text-[#564D4A]/60 mb-1.5 block">E-mailadres</label>
                <div class="relative">
                    <input name="email" value="{{ old('email') }}" type="email"
                        class="w-full bg-white rounded-xl py-3 px-4 pl-10 text-sm text-[#564D4A] font-medium border border-[#564D4A]/8 focus:border-[#5B2333]/40 focus:ring-2 focus:ring-[#5B2333]/5 outline-none transition"
                        placeholder="naam@voorbeeld.nl" required autocomplete="email">
                    <i class="fa-solid fa-envelope text-[#564D4A]/25 absolute left-3.5 top-1/2 -translate-y-1/2 text-xs"></i>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-[#564D4A]/60 mb-1.5 block">Wachtwoord</label>
                <div class="relative" x-data="{ show: false }">
                    <input name="password" :type="show ? 'text' : 'password'"
                        class="w-full bg-white rounded-xl py-3 px-4 pl-10 pr-10 text-sm text-[#564D4A] font-medium border border-[#564D4A]/8 focus:border-[#5B2333]/40 focus:ring-2 focus:ring-[#5B2333]/5 outline-none transition"
                        placeholder="Jouw wachtwoord" required autocomplete="current-password">
                    <i class="fa-solid fa-lock text-[#564D4A]/25 absolute left-3.5 top-1/2 -translate-y-1/2 text-xs"></i>
                    <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#564D4A]/25 hover:text-[#564D4A]/50 transition cursor-pointer">
                        <i class="fa-solid text-xs" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <label class="flex items-center gap-2.5 select-none cursor-pointer">
                <span class="relative flex items-center justify-center">
                    <input name="remember" value="1" type="checkbox"
                        class="peer h-[18px] w-[18px] appearance-none rounded-md border border-[#564D4A]/15 bg-white checked:bg-[#5B2333] checked:border-[#5B2333] focus:outline-none transition cursor-pointer"/>
                    <i class="fa-solid fa-check text-[8px] absolute text-white pointer-events-none opacity-0 peer-checked:opacity-100"></i>
                </span>
                <span class="text-xs text-[#564D4A]/60 font-medium">Ingelogd blijven</span>
            </label>

            <button type="submit"
                class="cursor-pointer w-full bg-[#5B2333] hover:bg-[#5B2333]/85 active:scale-[0.98] transition-all duration-200 rounded-xl py-3.5 text-sm text-white font-bold shadow-sm shadow-[#5B2333]/15 mt-2">
                Inloggen <i class="fa-solid fa-arrow-right text-xs ml-1"></i>
            </button>
        </form>

        <p class="text-center mt-6 text-xs text-[#564D4A]/40">
            Nog geen account?
            <a href="{{ route('register') }}" class="text-[#5B2333] font-semibold hover:underline">Gratis registreren</a>
        </p>
    </div>
</x-layouts.auth>
