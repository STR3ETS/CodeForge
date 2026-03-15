<x-layouts.marketing>
    <x-slot:title>{{ __('pages.cookies.meta_title') }}</x-slot:title>
    <x-slot:description>{{ __('pages.cookies.meta_description') }}</x-slot:description>

    {{-- Page header --}}
    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-cookie-bite text-[10px]"></i> {{ __('pages.cookies.badge') }}
            </span>
            <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                {{ __('pages.cookies.title') }}
            </h1>
            <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                {{ __('pages.cookies.description') }}
            </p>
        </div>
    </section>

    {{-- Content --}}
    <section class="max-w-3xl mx-auto px-6 py-20">
        <p class="text-xs text-[#564D4A]/40 font-medium mb-10">{{ __('pages.common.last_updated') }}</p>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.cookies.section_1_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.cookies.section_1_content') }}
        </p>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.cookies.section_2_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.cookies.section_2_intro') }}
        </p>

        <h3 class="text-base font-bold text-[#564D4A] mb-3 mt-6">{{ __('pages.cookies.section_2_functional_title') }}</h3>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.cookies.section_2_functional_desc') }}
        </p>
        <ul class="list-disc list-inside text-sm text-[#564D4A]/70 leading-relaxed mb-4 space-y-1">
            <li><strong>{{ __('pages.cookies.section_2_functional_label_1') }}</strong> {{ __('pages.cookies.section_2_functional_item_1') }}</li>
            <li><strong>{{ __('pages.cookies.section_2_functional_label_2') }}</strong> {{ __('pages.cookies.section_2_functional_item_2') }}</li>
            <li><strong>{{ __('pages.cookies.section_2_functional_label_3') }}</strong> {{ __('pages.cookies.section_2_functional_item_3') }}</li>
        </ul>

        <h3 class="text-base font-bold text-[#564D4A] mb-3 mt-6">{{ __('pages.cookies.section_2_analytical_title') }}</h3>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.cookies.section_2_analytical_desc') }}
        </p>
        <ul class="list-disc list-inside text-sm text-[#564D4A]/70 leading-relaxed mb-4 space-y-1">
            <li><strong>{{ __('pages.cookies.section_2_analytical_label_1') }}</strong> {{ __('pages.cookies.section_2_analytical_item_1') }}</li>
            <li><strong>{{ __('pages.cookies.section_2_analytical_label_2') }}</strong> {{ __('pages.cookies.section_2_analytical_item_2') }}</li>
        </ul>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.cookies.section_2_no_tracking') }}
        </p>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.cookies.section_3_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.cookies.section_3_intro') }}
        </p>
        <ul class="list-disc list-inside text-sm text-[#564D4A]/70 leading-relaxed mb-4 space-y-1">
            <li><strong>{{ __('pages.cookies.section_3_label_1') }}</strong> {{ __('pages.cookies.section_3_item_1') }}</li>
            <li><strong>{{ __('pages.cookies.section_3_label_2') }}</strong> {{ __('pages.cookies.section_3_item_2') }}</li>
        </ul>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.cookies.section_3_browser_intro') }}
        </p>
        <ul class="list-disc list-inside text-sm text-[#564D4A]/70 leading-relaxed mb-4 space-y-1">
            <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener noreferrer" class="text-[#5B2333] underline hover:no-underline">Google Chrome</a></li>
            <li><a href="https://support.mozilla.org/nl/kb/cookies-verwijderen-gegevens-wissen" target="_blank" rel="noopener noreferrer" class="text-[#5B2333] underline hover:no-underline">Mozilla Firefox</a></li>
            <li><a href="https://support.apple.com/nl-nl/guide/safari/sfri11471/mac" target="_blank" rel="noopener noreferrer" class="text-[#5B2333] underline hover:no-underline">Safari</a></li>
            <li><a href="https://support.microsoft.com/nl-nl/microsoft-edge/cookies-verwijderen-in-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" rel="noopener noreferrer" class="text-[#5B2333] underline hover:no-underline">Microsoft Edge</a></li>
        </ul>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.cookies.section_4_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.cookies.section_4_content') }}
        </p>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.cookies.section_5_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.cookies.section_5_content') }}
        </p>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            <strong>{{ __('pages.common.contact_email_label') }}</strong> {{ __('pages.common.contact_email') }}
        </p>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.cookies.section_5_also_see') }} <a href="/privacybeleid" class="text-[#5B2333] underline hover:no-underline">{{ __('pages.cookies.section_5_privacy_link') }}</a> {{ __('pages.cookies.section_5_also_see_suffix') }}
        </p>
    </section>

</x-layouts.marketing>
