<x-layouts.marketing>
    <x-slot:title>{{ __('pages.category.logic.meta_title') }}</x-slot:title>
    <x-slot:description>{{ __('pages.category.logic.meta_description') }}</x-slot:description>

    {{-- Page header --}}
    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <a href="{{ route('pages.games') }}" class="inline-flex items-center gap-1.5 text-sm text-[#564D4A]/50 hover:text-[#5B2333] transition mb-6">
                <i class="fa-solid fa-arrow-left text-xs"></i> {{ __('pages.common.all_games') }}
            </a>
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-chess text-[10px]"></i> {{ __('pages.category.logic.badge') }}
            </span>
            <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                {{ __('pages.category.logic.title') }}
            </h1>
            <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                {{ __('pages.category.logic.description') }}
            </p>
        </div>
    </section>

    {{-- SEO content section --}}
    <section class="max-w-6xl mx-auto px-6 py-16">
        <div data-animate="fade-up" class="max-w-3xl">
            <h2 class="text-2xl font-black text-[#564D4A] tracking-tight mb-6">{{ __('pages.category.logic.seo_title') }}</h2>
            <div class="space-y-4 text-[#564D4A]/60 leading-relaxed">
                <p>
                    {{ __('pages.category.logic.seo_paragraph_1') }}
                </p>
                <p>
                    {{ __('pages.category.logic.seo_paragraph_2') }}
                </p>
                <p>
                    {{ __('pages.category.logic.seo_paragraph_3') }}
                </p>
            </div>
        </div>
    </section>

    {{-- Featured games grid --}}
    <section class="bg-white border-y border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6 py-20">
            <div class="text-center mb-14">
                <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                    <i class="fa-solid fa-gamepad text-[10px]"></i> {{ __('pages.common.games_badge') }}
                </span>
                <h2 data-animate="fade-up" data-animate-delay="1" class="text-2xl sm:text-3xl font-black text-[#564D4A] tracking-tight">{{ __('pages.category.logic.games_title') }}</h2>
                <p data-animate="fade-up" data-animate-delay="2" class="mt-3 text-[#564D4A]/50 max-w-lg mx-auto">{{ __('pages.category.logic.games_subtitle') }}</p>
            </div>

            @php
                $games = [
                    [
                        'name' => __('pages.category.logic.game_sudoku_name'),
                        'icon' => 'fa-solid fa-table-cells-large',
                        'bg' => 'bg-[#D0EAE8]',
                        'color' => 'text-[#3a8a85]',
                        'tagBg' => 'bg-[#D0EAE8]/50',
                        'tags' => [__('pages.category.logic.game_sudoku_tag_1'), __('pages.category.logic.game_sudoku_tag_2')],
                        'description' => __('pages.category.logic.game_sudoku_desc'),
                    ],
                    [
                        'name' => __('pages.category.logic.game_word_name'),
                        'icon' => 'fa-solid fa-font',
                        'bg' => 'bg-[#D6E4F0]',
                        'color' => 'text-[#4a7fa5]',
                        'tagBg' => 'bg-[#D6E4F0]/50',
                        'tags' => [__('pages.category.logic.game_word_tag_1'), __('pages.category.logic.game_word_tag_2')],
                        'description' => __('pages.category.logic.game_word_desc'),
                    ],
                    [
                        'name' => __('pages.category.logic.game_color_name'),
                        'icon' => 'fa-solid fa-layer-group',
                        'bg' => 'bg-[#FEF3C7]',
                        'color' => 'text-[#b45309]',
                        'tagBg' => 'bg-[#FEF3C7]/50',
                        'tags' => [__('pages.category.logic.game_color_tag_1'), __('pages.category.logic.game_color_tag_2')],
                        'description' => __('pages.category.logic.game_color_desc'),
                    ],
                    [
                        'name' => __('pages.category.logic.game_block_name'),
                        'icon' => 'fa-solid fa-cube',
                        'bg' => 'bg-[#E8D5F0]',
                        'color' => 'text-[#7a4fa0]',
                        'tagBg' => 'bg-[#E8D5F0]/50',
                        'tags' => [__('pages.category.logic.game_block_tag_1'), __('pages.category.logic.game_block_tag_2')],
                        'description' => __('pages.category.logic.game_block_desc'),
                    ],
                ];
            @endphp

            <div class="grid gap-6">
                @foreach($games as $game)
                    <div data-animate="fade-up" class="bg-white rounded-2xl border border-[#564D4A]/6 p-8 hover:shadow-md hover:shadow-black/3 transition-all group">
                        <div class="flex flex-col md:flex-row md:items-start gap-6">
                            <div class="w-16 h-16 rounded-2xl {{ $game['bg'] }} flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <i class="{{ $game['icon'] }} {{ $game['color'] }} text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <h3 class="font-black text-xl text-[#564D4A]">{{ $game['name'] }}</h3>
                                    @foreach($game['tags'] as $i => $tag)
                                        <span class="px-2.5 py-0.5 rounded-md {{ $i === 0 ? $game['tagBg'] . ' ' . $game['color'] : 'bg-[#564D4A]/5 text-[#564D4A]/40' }} text-[10px] font-bold">{{ $tag }}</span>
                                    @endforeach
                                </div>
                                <p class="text-[#564D4A]/60 leading-relaxed">{{ $game['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="hero-gradient rounded-3xl p-12 sm:p-16 text-center relative overflow-hidden">
            <img src="/assets/stacked-waves-haikei.png" class="absolute inset-0 w-full h-full object-cover opacity-15 pointer-events-none" alt="">
            <div class="relative z-10">
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ __('pages.category.logic.cta_title') }}</h2>
                <p class="mt-3 text-white/50 max-w-md mx-auto">{{ __('pages.category.logic.cta_description') }}</p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-[#5B2333] font-bold text-sm px-8 py-4 rounded-2xl hover:bg-white/90 transition shadow-lg shadow-black/10 mt-8">
                    <i class="fa-solid fa-bolt"></i> {{ __('pages.common.free_start') }}
                </a>
            </div>
        </div>
    </section>

</x-layouts.marketing>
