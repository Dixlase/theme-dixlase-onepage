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

{{-- Appearance switcher, fixed in the bottom-right corner of the viewport.

     Reachable at any scroll position, unlike the footer placement which is a
     full page away on a long front page. Rendered from layouts/app.blade.php
     rather than from the footer partial: a `custom/` override replaces a view
     wholesale, so a site that has forked partials/footer.blade.php would never
     receive a control added inside it.

     Two forms, split by a media query in front/scss/style.scss:

       below 768px — one trigger showing the icon of the mode in effect; the
                     three options appear when it is tapped
       768px and up — the three options are always visible and the trigger is
                     display:none

     The Alpine state only adds `is-open`; the stylesheet decides what that
     means per breakpoint. Nothing writes an inline `display`, so resizing
     across the breakpoint cannot leave the control in a state the media query
     cannot undo. `floatOpen` lives on the `appearanceTheme` data that
     layouts/app.blade.php puts on <html>, next to the mode itself.

     The left-hand corner is where DixlaseCookie puts its persistent trigger
     (position: fixed; bottom: 1rem; left: 1rem; z-index: 40), so this is the
     mirrored position at the same stacking level — below the header (z-50) and
     the mobile drawer (z-[60]/z-[70]), which must stay on top.
--}}
@php
    $appearanceOptions = dls_onepage_appearance_options();
@endphp

<div
    class="dls-appearance-float"
    :class="floatOpenClass()"
    x-cloak
    @click.outside="closeFloat()"
    @keydown.escape.window="closeFloat()"
>
    {{-- Collapsed trigger. Every icon is rendered and the one for the mode in
         effect is shown, so the icon list stays in the shared helper instead
         of being resolved in JavaScript. --}}
    <button
        type="button"
        class="dls-appearance-float__trigger"
        @click="toggleFloat()"
        :aria-expanded="floatExpanded()"
        aria-controls="dls-appearance-float-options"
        title="{{ __('themes::theme.appearance.label') }}"
    >
        @foreach ($appearanceOptions as $value => $option)
            <i class="fa-solid {{ $option['icon'] }}" x-show="isCurrent('{{ $value }}')" aria-hidden="true"></i>
        @endforeach
        <span class="sr-only">{{ __('themes::theme.appearance.label') }}</span>
    </button>

    <div id="dls-appearance-float-options" class="dls-appearance-float__options">
        @include('themes::partials.appearance-toggle', ['variant' => 'float'])
    </div>
</div>
