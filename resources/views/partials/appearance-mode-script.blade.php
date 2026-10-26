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

{{-- Apply the dark/light class before anything paints, so the page is never
     shown in the wrong theme for a frame. Every layout that renders a <head>
     includes this, so the rule lives in one place.

     `$honourStoredChoice` decides whether a visitor's stored preference
     outranks the theme setting. The live layout passes its switcher check: a
     stored choice counts only while a switcher is on screen to have made it.
     The admin preview frame passes nothing and gets the default, false — it
     shows the operator the mode they configured, not whatever the browser
     they happen to be sitting at chose last.

     The precedence mirrors appearanceTheme() in
     resources/src/front/js/app.js. Keep the two in step. --}}
@php($honourStoredChoice = $honourStoredChoice ?? false)
<script @cspNonce>
(function(){
    var m = '{{ $themeSettings->appearance_mode ?? '0' }}';
@if ($honourStoredChoice)
    try {
        var stored = window.localStorage.getItem('dls-appearance-mode');
        if (stored === '0' || stored === '1' || stored === '2') {
            m = stored;
        }
    } catch (e) {
        // Private mode or blocked storage: keep the theme setting.
    }
@endif
    if (m === '2' || (m === '0' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
    } else {
        document.documentElement.classList.add('light');
        document.documentElement.classList.remove('dark');
    }
})();
</script>
