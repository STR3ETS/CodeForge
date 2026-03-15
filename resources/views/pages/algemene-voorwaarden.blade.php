<x-layouts.marketing>
    <x-slot:title>{{ __('pages.terms.meta_title') }}</x-slot:title>
    <x-slot:description>{{ __('pages.terms.meta_description') }}</x-slot:description>

    {{-- Page header --}}
    <section class="pt-28 pb-16 bg-white border-b border-[#564D4A]/6">
        <div class="max-w-6xl mx-auto px-6">
            <span data-animate="fade-up" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#5B2333]/8 text-[#5B2333] text-xs font-bold mb-4">
                <i class="fa-solid fa-file-contract text-[10px]"></i> {{ __('pages.common.legal_badge') }}
            </span>
            <h1 data-animate="fade-up" data-animate-delay="1" class="text-3xl sm:text-4xl font-black text-[#564D4A] tracking-tight">
                {{ __('pages.terms.title') }}
            </h1>
            <p data-animate="fade-up" data-animate-delay="2" class="mt-4 text-[#564D4A]/50 max-w-2xl leading-relaxed text-lg">
                {{ __('pages.terms.description') }}
            </p>
        </div>
    </section>

    {{-- Content --}}
    <section class="max-w-3xl mx-auto px-6 py-20">
        <p class="text-xs text-[#564D4A]/40 font-medium mb-10">{{ __('pages.common.last_updated') }}</p>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.terms.section_1_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_1_intro') }}
        </p>
        <ul class="list-disc list-inside text-sm text-[#564D4A]/70 leading-relaxed mb-4 space-y-1">
            <li><strong>BrainForge</strong>: {{ __('pages.terms.section_1_item_1') }}</li>
            <li><strong>Gebruiker</strong>: {{ __('pages.terms.section_1_item_2') }}</li>
            <li><strong>Account</strong>: {{ __('pages.terms.section_1_item_3') }}</li>
            <li><strong>Dienst</strong>: {{ __('pages.terms.section_1_item_4') }}</li>
            <li><strong>Abonnement</strong>: {{ __('pages.terms.section_1_item_5') }}</li>
        </ul>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.terms.section_2_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_2_content_1') }}
        </p>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_2_content_2') }}
        </p>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.terms.section_3_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_3_content_1') }}
        </p>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_3_content_2') }}
        </p>
        <ul class="list-disc list-inside text-sm text-[#564D4A]/70 leading-relaxed mb-4 space-y-1">
            <li>{{ __('pages.terms.section_3_item_1') }}</li>
            <li>{{ __('pages.terms.section_3_item_2') }}</li>
            <li>{{ __('pages.terms.section_3_item_3') }}</li>
            <li>{{ __('pages.terms.section_3_item_4') }}</li>
            <li>{{ __('pages.terms.section_3_item_5') }}</li>
        </ul>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.terms.section_4_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_4_content_1') }}
        </p>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_4_content_2') }}
        </p>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_4_content_3') }}
        </p>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.terms.section_5_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_5_content_1') }}
        </p>
        <ul class="list-disc list-inside text-sm text-[#564D4A]/70 leading-relaxed mb-4 space-y-1">
            <li>{{ __('pages.terms.section_5_item_1') }}</li>
            <li>{{ __('pages.terms.section_5_item_2') }}</li>
            <li>{{ __('pages.terms.section_5_item_3') }}</li>
            <li>{{ __('pages.terms.section_5_item_4') }}</li>
        </ul>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_5_content_2') }}
        </p>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.terms.section_6_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_6_content_1') }}
        </p>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_6_content_2') }}
        </p>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.terms.section_7_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_7_content_1') }}
        </p>
        <ul class="list-disc list-inside text-sm text-[#564D4A]/70 leading-relaxed mb-4 space-y-1">
            <li>{{ __('pages.terms.section_7_item_1') }}</li>
            <li>{{ __('pages.terms.section_7_item_2') }}</li>
            <li>{{ __('pages.terms.section_7_item_3') }}</li>
            <li>{{ __('pages.terms.section_7_item_4') }}</li>
        </ul>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_7_content_2') }}
        </p>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.terms.section_8_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_8_content') }}
        </p>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.terms.section_9_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_9_content') }}
        </p>

        <h2 class="text-xl font-black text-[#564D4A] mb-4 mt-10">{{ __('pages.terms.section_10_title') }}</h2>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            {{ __('pages.terms.section_10_content') }}
        </p>
        <p class="text-sm text-[#564D4A]/70 leading-relaxed mb-4">
            <strong>{{ __('pages.common.contact_email_label') }}</strong> {{ __('pages.common.contact_email') }}
        </p>
    </section>

</x-layouts.marketing>
