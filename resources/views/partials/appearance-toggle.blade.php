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

{{-- Appearance switcher: auto / light / dark.

     Rendered only when the operator enabled it (`appearance_toggle_enabled`).
     The buttons call setTheme() on the `appearanceTheme` Alpine data that
     layouts/app.blade.php puts on <html>, so this partial carries no state of
     its own and works wherever it is included.

     `$variant` is 'desktop' (header) or 'mobile' (hamburger menu); it only
     changes the surrounding layout classes, not the behaviour.
--}}
@php
    $variant = $variant ?? 'desktop';
    $appearanceOptions = [
        '0' => ['label' => __('themes::theme.appearance.auto'),  'icon' => 'fa-circle-half-stroke'],
        '1' => ['label' => __('themes::theme.appearance.light'), 'icon' => 'fa-sun'],
        '2' => ['label' => __('themes::theme.appearance.dark'),  'icon' => 'fa-moon'],
    ];
@endphp

<div
    class="dls-appearance {{ $variant === 'mobile' ? 'dls-appearance--mobile' : 'dls-appearance--desktop' }}"
    role="group"
    aria-label="{{ __('themes::theme.appearance.label') }}"
>
    @if ($variant === 'mobile')
        <span class="dls-appearance__label">{{ __('themes::theme.appearance.label') }}</span>
    @endif

    <div class="dls-appearance__options">
        @foreach ($appearanceOptions as $value => $option)
            <button
                type="button"
                @click="setTheme('{{ $value }}')"
                :class="optionClass('{{ $value }}')"
                :aria-pressed="optionPressed('{{ $value }}')"
                class="dls-appearance-option"
                title="{{ $option['label'] }}"
            >
                <i class="fa-solid {{ $option['icon'] }}" aria-hidden="true"></i>
                <span class="sr-only">{{ $option['label'] }}</span>
            </button>
        @endforeach
    </div>
</div>
