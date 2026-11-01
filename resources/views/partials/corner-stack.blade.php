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

{{-- The controls that float in the bottom-right corner of the viewport.

     Both are optional and independent, and the one that is shown must land in
     the same corner whichever it is. So the position lives here, once, and the
     controls are laid out by this stack rather than fixing themselves: the
     appearance switcher's height changes with the breakpoint and again when it
     opens on a phone, so a back-to-top button offset by a hardcoded amount
     would overlap it at some width.

     Order is bottom-up in importance: the appearance switcher keeps the corner
     it already had, and back-to-top sits above it. With the switcher off,
     back-to-top takes the corner itself and the page looks as though the stack
     were never there.

     Resolved from `$themeSettings` here rather than taken as arguments, so the
     live layout and the admin preview frame cannot disagree about which
     controls are on.

     Included from the layouts, not from partials/footer.blade.php: a `custom/`
     override replaces a view wholesale, so a site that has forked the footer
     would never receive a control added inside it.

     The left-hand corner is DixlaseCookie's (fixed, bottom-left, z-index 40);
     this is the mirrored position at the same stacking level — below the
     header (z-50) and the mobile drawer (z-[60]/z-[70]). --}}
@php
    $cornerBackToTop = (string) ($themeSettings->back_to_top_enabled ?? '0') === '1';
    $cornerAppearance = (string) ($themeSettings->appearance_toggle_float ?? '0') === '1';
@endphp

@if ($cornerBackToTop || $cornerAppearance)
    <div class="dls-corner-stack">
        @if ($cornerBackToTop)
            @include('themes::partials.back-to-top')
        @endif

        @if ($cornerAppearance)
            @include('themes::partials.appearance-float')
        @endif
    </div>
@endif
