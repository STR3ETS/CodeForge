<x-layouts.marketing>
    <x-slot:title>{{ __('pages.game_info.flag_guess.meta_title') }}</x-slot:title>
    <x-slot:description>{{ __('pages.game_info.flag_guess.meta_description') }}</x-slot:description>

    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <a href="{{ route('pages.games') }}" data-animate="fade-up" class="inline-flex items-center gap-2 text-[#564D4A]/40 hover:text-[#564D4A] text-sm font-semibold mb-6 transition">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> {{ __('pages.game_info.common.all_games') }}
            </a>
            <div class="flex items-start gap-5">
                <div data-animate="fade-up" data-animate-delay="1" class="w-16 h-16 rounded-2xl bg-[#FFF3CD] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-flag text-[#9a7a20] text-2xl"></i>
                </div>
                <div>
                    <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">{{ __('pages.game_info.flag_guess.title') }}</h1>
                    <p data-animate="fade-up" data-animate-delay="2" class="mt-2 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                        {{ __('pages.game_info.flag_guess.description') }}
                    </p>
                    <div data-animate="fade-up" data-animate-delay="3" class="flex flex-wrap gap-2 mt-4">
                        <span class="px-3 py-1 rounded-full bg-[#FFF3CD]/50 text-[#9a7a20] text-xs font-bold">{{ __('pages.game_info.flag_guess.tag_1') }}</span>
                        <span class="px-3 py-1 rounded-full bg-[#FFF3CD]/50 text-[#9a7a20] text-xs font-bold">{{ __('pages.game_info.flag_guess.tag_2') }}</span>
                        <span class="px-3 py-1 rounded-full bg-[#FFF3CD]/50 text-[#9a7a20] text-xs font-bold">{{ __('pages.game_info.flag_guess.tag_3') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="grid md:grid-cols-2 gap-12">
            <div>
                <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">{{ __('pages.game_info.flag_guess.how_title') }}</h2>
                <div class="space-y-4">
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5"><span class="text-[#5B2333] text-sm font-black">1</span></div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">{{ __('pages.game_info.flag_guess.step1_title') }}</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">{{ __('pages.game_info.flag_guess.step1_desc') }}</p>
                        </div>
                    </div>
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5"><span class="text-[#5B2333] text-sm font-black">2</span></div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">{{ __('pages.game_info.flag_guess.step2_title') }}</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">{{ __('pages.game_info.flag_guess.step2_desc') }}</p>
                        </div>
                    </div>
                    <div data-animate="fade-up" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-[#5B2333]/10 flex items-center justify-center shrink-0 mt-0.5"><span class="text-[#5B2333] text-sm font-black">3</span></div>
                        <div>
                            <h3 class="font-bold text-[#564D4A] mb-1">{{ __('pages.game_info.flag_guess.step3_title') }}</h3>
                            <p class="text-sm text-[#564D4A]/60 leading-relaxed">{{ __('pages.game_info.flag_guess.step3_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">{{ __('pages.game_info.common.difficulties') }}</h2>
                <div class="space-y-3">
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2"><span class="px-2.5 py-1 rounded-lg bg-green-100 text-green-700 text-[11px] font-bold">{{ __('pages.game_info.common.difficulty_easy') }}</span></div>
                        <p class="text-sm text-[#564D4A]/60">{{ __('pages.game_info.flag_guess.easy_desc') }}</p>
                    </div>
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2"><span class="px-2.5 py-1 rounded-lg bg-yellow-100 text-yellow-700 text-[11px] font-bold">{{ __('pages.game_info.common.difficulty_normal') }}</span></div>
                        <p class="text-sm text-[#564D4A]/60">{{ __('pages.game_info.flag_guess.normal_desc') }}</p>
                    </div>
                    <div data-animate="fade-up" class="bg-white rounded-xl border border-[#564D4A]/8 p-5">
                        <div class="flex items-center gap-3 mb-2"><span class="px-2.5 py-1 rounded-lg bg-red-100 text-red-700 text-[11px] font-bold">{{ __('pages.game_info.common.difficulty_hard') }}</span></div>
                        <p class="text-sm text-[#564D4A]/60">{{ __('pages.game_info.flag_guess.hard_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-20">
            <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight text-center mb-4">{{ __('pages.game_info.flag_guess.why_title') }}</h2>
            <p data-animate="fade-up" class="text-center text-[#564D4A]/50 mb-12 max-w-xl mx-auto">{{ __('pages.game_info.flag_guess.why_subtitle') }}</p>
            <div class="grid sm:grid-cols-3 gap-6">
                <div data-animate="fade-up" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4"><i class="fa-solid fa-globe text-[#5B2333]"></i></div>
                    <h3 class="font-bold text-[#564D4A] mb-2">{{ __('pages.game_info.flag_guess.benefit1_title') }}</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">{{ __('pages.game_info.flag_guess.benefit1_desc') }}</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="1" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4"><i class="fa-solid fa-earth-americas text-[#5B2333]"></i></div>
                    <h3 class="font-bold text-[#564D4A] mb-2">{{ __('pages.game_info.flag_guess.benefit2_title') }}</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">{{ __('pages.game_info.flag_guess.benefit2_desc') }}</p>
                </div>
                <div data-animate="fade-up" data-animate-delay="2" class="text-center p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#5B2333]/8 flex items-center justify-center mx-auto mb-4"><i class="fa-solid fa-map text-[#5B2333]"></i></div>
                    <h3 class="font-bold text-[#564D4A] mb-2">{{ __('pages.game_info.flag_guess.benefit3_title') }}</h3>
                    <p class="text-sm text-[#564D4A]/50 leading-relaxed">{{ __('pages.game_info.flag_guess.benefit3_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-6 py-20 text-center">
        <h2 data-animate="fade-up" class="text-2xl font-black text-[#564D4A] tracking-tight mb-4">{{ __('pages.game_info.common.ready_to_play') }}</h2>
        <p data-animate="fade-up" class="text-[#564D4A]/50 mb-8">{{ __('pages.game_info.flag_guess.cta_text') }}</p>
        <a href="{{ route('register') }}" data-animate="fade-up" class="inline-flex items-center gap-2 bg-[#5B2333] hover:bg-[#5B2333]/85 text-white font-bold text-sm px-8 py-4 rounded-2xl transition shadow-lg shadow-[#5B2333]/20">
            <i class="fa-solid fa-bolt"></i> {{ __('pages.game_info.common.free_start') }}
        </a>
    </section>
</x-layouts.marketing>
