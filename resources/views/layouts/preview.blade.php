{{--
This file is part of Dixlase OnePage.

Copyright (C) 2026 exc-D inc. and Dixlase contributors
https://exc-d.com

Dixlase OnePage is dual-licensed. You may use this file under either:

  (a) the GNU General Public License version 3 or later, as published
      by the Free Software Foundation; or

  (b) a commercial license agreement obtained from exc-D inc.

Unless you have entered into a commercial license agreement, this
file is governed by the GPL terms below.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <https://www.gnu.org/licenses/>.
--}}

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="scroll-smooth"
>
<head>
    @include('themes::partials.appearance-mode-script')

    {{-- The same <head> the live page renders, so a tag added there reaches
         the preview with no second copy to keep in step. `stylesOnly` leaves
         the JavaScript bundle out: this frame shows the page's appearance and
         runs no front-end JS. --}}
    @include('themes::partials.head', ['stylesOnly' => true])

    {{-- The same appearance tokens the live page emits: the primary colour,
         the heading font and its per-region toggles. Shared so the frame
         shows what a visitor will get, and so a new setting reaches both
         without being added twice. --}}
    @include('themes::partials.appearance-tokens')

    {{-- Font Awesome (footer SNS icons) comes from the theme's bundled
         style.scss (FA 7.x); see layouts/app.blade.php for why the cdnjs
         6.4.0 stylesheet must not be loaded here. --}}

    <style @cspNonce>
        /* Header position: fixed at the top, since the frame has no admin bar. */
        header.fixed { top: 0 !important; }
        /* Preview only: every interactive element is disabled. */
        a, button, [role="button"],
        input, textarea, select {
            pointer-events: none !important;
            cursor: default !important;
        }
    </style>

</head>
<body
    class="text-gray-900 dark:text-gray-100 {{ ($bareContent ?? false) ? '' : 'bg-gray-50 dark:bg-gray-900 flex flex-col min-h-screen' }}"
    @if($bareContent ?? false) style="background: transparent;" @endif>
    @unless($bareContent ?? false)
        @include('themes::partials.header')
    @endunless

    <main class="{{ ($bareContent ?? false) ? '' : 'flex-grow' }}">
        @yield('content')
    </main>

    @unless($bareContent ?? false)
        @include('themes::partials.footer')

        {{-- The corner controls, included here for the same reason
             layouts/app.blade.php includes them rather than the footer
             partial: a `custom/` override replaces a view wholesale, so a
             site that has forked the footer would never receive a control
             added inside it.

             Included so this layout keeps asking for the same things the
             live one does, which is what stops the two drifting. They do not
             appear in the frame though, and an earlier version of this
             comment was wrong to say they did: this layout emits no
             JavaScript (`stylesOnly`), both controls carry `x-cloak`, and
             the shared head declares `[x-cloak] { display: none !important }`
             — so nothing removes the attribute and both stay hidden. Giving
             the frame a static rendering of them is a separate change.

             The footer placement of the appearance switcher needs nothing
             extra: it lives inside partials/footer.blade.php, which is
             included above. --}}
        @include('themes::partials.corner-stack')
    @endunless

    @stack('scripts')
</body>
</html>
