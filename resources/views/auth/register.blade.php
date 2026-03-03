<x-layouts.auth :title="'Register • ' . config('app.name')">
    <div class="bg-white max-w-[400px] w-full p-8 rounded-2xl flex flex-col shadow-[0_10px_30px_rgba(0,0,0,0.06)]">
        <div class="w-12 h-12 rounded-xl bg-[#5B2333] flex items-center justify-center">
            <img src="/assets/logo-wit.png" class="max-h-6" alt="Logo">
        </div>

        <h1 class="text-2xl font-black text-[#564D4A] my-3">Create your account</h1>

        @if ($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-100 p-3 mb-4">
                <p class="text-[12px] font-semibold text-red-700">Er ging iets mis:</p>
                <ul class="mt-2 text-[12px] text-red-700 list-disc ml-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="cursor-pointer w-full bg-[#564D4A]/10 hover:bg-[#564D4A]/25 transition duration-200 rounded-md py-2.5 px-4 text-center text-xs text-[#564D4A] font-semibold">
            <i class="fa-brands fa-google"></i> <span class="ml-2">Sign up with Google</span>
        </div>

        <div class="grid grid-cols-5 gap-2 items-center my-5">
            <div class="col-span-2 h-[1px] w-full bg-[#564D4A]/10"></div>
            <p class="text-center text-[11px] uppercase text-[#564D4A]/50 font-bold">OR</p>
            <div class="col-span-2 h-[1px] w-full bg-[#564D4A]/10"></div>
        </div>

        <form method="POST" action="{{ route('register.submit') }}" class="grid gap-3">
            @csrf

            <div class="relative w-full">
                <input name="name" value="{{ old('name') }}" type="text"
                    class="w-full outline-none rounded-md py-2.5 px-4 pl-10 text-xs text-[#564D4A] font-semibold border border-[#564D4A]/10 focus:border-[#5B2333]/50 transition duration-200"
                    placeholder="Your Name" required autocomplete="name">
                <i class="fa-solid fa-user text-[#564D4A]/50 absolute left-4 top-1/2 -translate-y-1/2 text-xs"></i>
            </div>

            <div class="relative w-full">
                <input name="email" value="{{ old('email') }}" type="email"
                    class="w-full outline-none rounded-md py-2.5 px-4 pl-10 text-xs text-[#564D4A] font-semibold border border-[#564D4A]/10 focus:border-[#5B2333]/50 transition duration-200"
                    placeholder="Your Email" required autocomplete="email">
                <i class="fa-solid fa-at text-[#564D4A]/50 absolute left-4 top-1/2 -translate-y-1/2 text-xs"></i>
            </div>

            <div class="grid gap-2">
                <div class="relative w-full">
                    <input name="password" type="password"
                        class="w-full outline-none rounded-md py-2.5 px-10 text-xs text-[#564D4A] font-semibold border border-[#564D4A]/10 focus:border-[#5B2333]/50 transition duration-200"
                        placeholder="Your Password" required autocomplete="new-password">
                    <i class="fa-solid fa-key text-[#564D4A]/50 absolute left-4 top-1/2 -translate-y-1/2 text-xs"></i>
                    <i class="fa-solid fa-eye text-[#564D4A]/30 absolute right-4 top-1/2 -translate-y-1/2 text-xs"></i>
                </div>

                <div class="relative w-full">
                    <input name="password_confirmation" type="password"
                        class="w-full outline-none rounded-md py-2.5 px-10 text-xs text-[#564D4A] font-semibold border border-[#564D4A]/10 focus:border-[#5B2333]/50 transition duration-200"
                        placeholder="Confirm Password" required autocomplete="new-password">
                    <i class="fa-solid fa-lock text-[#564D4A]/50 absolute left-4 top-1/2 -translate-y-1/2 text-xs"></i>
                </div>

                <p class="text-[11px] text-[#564D4A] font-medium italic mt-1 opacity-40">
                    Password must be at least 8 characters.
                </p>
            </div>

            <label class="flex items-center gap-2 mt-2 select-none">
                <span class="relative flex items-center justify-center">
                    <input name="terms" value="1" type="checkbox" required
                        class="peer h-4 w-4 appearance-none rounded border border-[#564D4A]/25 bg-white checked:bg-[#5B2333] checked:border-[#5B2333] focus:outline-none transition duration-200 cursor-pointer"/>
                    <i class="fa-solid fa-check text-[8px] absolute text-white pointer-events-none opacity-0 peer-checked:opacity-100"></i>
                </span>
                <span class="text-xs text-[#564D4A] font-semibold">
                    I agree to the <a href="#" class="text-[#5B2333] underline">Terms & Conditions</a>
                </span>
            </label>

            <button type="submit"
                class="cursor-pointer w-full bg-[#5B2333] hover:bg-[#5B2333]/80 transition duration-200 rounded-md py-3.5 px-4 text-center text-xs text-white font-semibold mt-6">
                Complete Account Registration
            </button>
        </form>

        <a href="{{ route('login') }}" class="text-xs italic opacity-50 font-medium text-center mt-4 underline">
            I already have an account
        </a>
    </div>
</x-layouts.auth>