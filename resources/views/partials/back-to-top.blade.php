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

{{-- Return to the top of the page.

     This is a one-page theme: the front page is the whole site, so a visitor
     who has reached the footer is a full page away from the navigation. The
     control appears once the page has been scrolled past the threshold in
     `backToTop` (resources/src/front/js/app.js) and does nothing before that,
     so a page shorter than the viewport never shows it.

     Hidden until Alpine resolves, like the appearance switcher beside it.
     Without JavaScript it stays hidden, which is right: it cannot work.

     It fades rather than appearing and vanishing: the control arrives while
     the visitor is reading something else, and a shape that blinks into the
     corner pulls the eye harder than one that resolves there. Opacity only —
     no movement — which is the substitute WCAG asks for in place of motion,
     so this needs no `prefers-reduced-motion` exception the way the scroll
     itself does.

     `x-transition` with modifiers carries no expression for Alpine to
     evaluate, so it is as strict-mode-safe as the rest of this file.

     Rendered inside partials/corner-stack.blade.php, which owns the position.
     Nothing here is fixed or offset — the stack decides whether this sits in
     the corner or above the appearance switcher. --}}
<button
    type="button"
    class="dls-back-to-top"
    x-data="backToTop"
    x-show="shown"
    x-transition.opacity.duration.200ms
    x-cloak
    @click="toTop()"
    title="{{ __('themes::theme.back_to_top.label') }}"
>
    {{-- An inline chevron rather than Font Awesome's `fa-chevron-up`, which
         is a filled shape with mitred ends — heavier than this control wants
         to be. Two strokes with round caps read as a hint rather than a
         button face. It is the only inline icon in the theme; the SNS row and
         the appearance switcher stay on the bundled font, which is right for
         them (brand marks, and three icons that have to agree in weight).

         `currentColor` so the light / dark rules on the button carry it, and
         `focusable="false"` so IE-era tab order cannot reach the SVG. --}}
    <svg
        class="dls-back-to-top__icon"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2.5"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
        focusable="false"
    >
        <path d="m5 15 7-7 7 7" />
    </svg>
    <span class="sr-only">{{ __('themes::theme.back_to_top.label') }}</span>
</button>
