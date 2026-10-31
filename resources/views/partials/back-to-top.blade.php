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

     Rendered inside partials/corner-stack.blade.php, which owns the position.
     Nothing here is fixed or offset — the stack decides whether this sits in
     the corner or above the appearance switcher. --}}
<button
    type="button"
    class="dls-back-to-top"
    x-data="backToTop"
    x-show="shown"
    x-cloak
    @click="toTop()"
    title="{{ __('themes::theme.back_to_top.label') }}"
>
    <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
    <span class="sr-only">{{ __('themes::theme.back_to_top.label') }}</span>
</button>
