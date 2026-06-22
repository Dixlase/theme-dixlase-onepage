{{--
    Hero text block (title + sub-title + 2 buttons), shared between
    "with foreground image" and "without foreground image" branches
    of partials/hero.blade.php. Pulled out so the parent can decide
    layout (flex column with image-fills-rest vs. centred block)
    without duplicating ~30 lines of markup.
--}}
{{-- `text-wrap: balance` via inline style: Tailwind v4 doesn't emit
     the `text-balance` utility in our dev pipeline, so we apply
     the CSS property directly. It tells the browser to redistribute
     glyphs across wrapped lines so the visible break falls at a
     punctuation pause (e.g. ja: "、" "。") rather than mid-phrase
     greedy wrapping. --}}
<h1 class="text-2xl md:text-6xl lg:text-7xl font-bold mb-4 md:mb-6 leading-tight text-gray-900 dark:text-white whitespace-pre-line"
    style="text-wrap: balance;">
    {{ $heroMainTitle }}
</h1>

@if($heroSubTitle)
    <p class="text-base md:text-lg text-gray-600 dark:text-gray-300 mb-6 md:mb-8 max-w-lg mx-auto whitespace-pre-line"
       style="text-wrap: balance;">
        {{ $heroSubTitle }}
    </p>
@endif

<div class="flex flex-col sm:flex-row justify-center gap-4">
    @if($heroButtonEnabled && $heroButtonText)
        <a href="{{ $heroButtonLink }}"
           target="{{ $heroButtonTarget }}"
           @if($heroButtonTarget === '_blank') rel="noopener noreferrer" @endif
           class="inline-flex items-center justify-center gap-2 h-11 rounded-xl text-white px-8 py-6 transition-opacity hover:opacity-90"
           style="background-color: {{ $primaryColor }}">
            {{ $heroButtonText }}
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-5 w-5">
                <path d="M5 12h14"></path>
                <path d="m12 5 7 7-7 7"></path>
            </svg>
        </a>
    @endif

    @if($heroButtonSecondaryEnabled && $heroButtonSecondaryText)
        {{-- Secondary button: a translucent white surface gives the
             border something to sit against, which it lacked against
             the primary-coloured radial gradient in light mode —
             `border-gray-300` alone read as nearly invisible there.
             `bg-white/50 + backdrop-blur-sm` is the same glass-recipe
             the drawer / cookie banner / header use, so the button
             feels at home in the site's translucent-overlay vocabulary.
             Border bumped a tier (gray-300 → gray-400) for additional
             contrast on the white-glass background. --}}
        <a href="{{ $heroButtonSecondaryLink }}"
           target="{{ $heroButtonSecondaryTarget }}"
           @if($heroButtonSecondaryTarget === '_blank') rel="noopener noreferrer" @endif
           class="hero-btn-secondary inline-flex items-center justify-center gap-2 h-11 rounded-xl px-8 border border-gray-400 dark:border-gray-600 bg-white/50 dark:bg-white/5 backdrop-blur-sm text-gray-700 dark:text-white hover:bg-white/70 dark:hover:bg-white/10 py-6 transition-colors">
            {{ $heroButtonSecondaryText }}
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-5 w-5">
                <path d="M7 7h10v10"></path>
                <path d="M7 17 17 7"></path>
            </svg>
        </a>
    @endif
</div>
