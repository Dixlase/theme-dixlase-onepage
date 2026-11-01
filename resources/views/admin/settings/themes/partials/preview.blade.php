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

{{-- Theme settings preview.

     The frame below is the theme itself, rendered by the theme's own
     partials through the `themes::` namespace, so a site that overrides a
     partial in `custom/` sees its override here exactly as on the live page.

     It used to be a hand-built facsimile of the header, hero, contact
     section and footer, with a content-only iframe in the middle (#79). A
     second implementation of the theme's appearance cannot help drifting
     from the first: it read the raw primary-locale settings instead of the
     translated ones, so the hero previewed in the wrong language; it never
     resolved media ids, so the hero background was missing; and controls
     added to the real partials — the appearance switcher of #68 / #70 —
     simply did not exist in it.

     `src` renders the stored settings, so the first paint needs no
     JavaScript. The script below re-renders the frame with the values
     currently in the form, which have not been saved, by posting the form
     into the frame. Both go to the same view the front page editor's
     preview uses, with `bareContent` false.

     The frame's height comes from the device preset, never from the
     document inside it — see the note on the iframe, and #121. --}}

<x-admin.theme-preview-container
    :title="__('themes::admin.settings.editor.preview_title')"
    :appearanceMode="$settings->appearance_mode ?? '0'"
>
    {{-- The frame is a window of the device the operator picked, and the page
         scrolls inside it — the same shape core's front page preview uses.

         The height has to come from the device rather than from the document,
         and that is the whole fix for #121: the page inside is sized in
         viewport units, and in an iframe the viewport is the frame, so a
         height measured from the content fed straight back into the thing
         that decides the content's size. Fixed here, nothing can inflate.

         The numbers are the ones the container's own device buttons name in
         their tooltips — 375x667, 768x1024, 1440x900. It tracks only the
         widths (`DEVICE_WIDTHS`), so the heights are written out here; a free
         width keeps the desktop shape. They would sit better in the
         container, next to the widths and the tooltips that already quote
         them, which is worth raising against core.

         Width comes from the wrapper this sits in, and the scale is a
         transform on that wrapper, so this is the unscaled device height. --}}
    <iframe id="dls-theme-preview-frame"
            name="dls-theme-preview-frame"
            src="{{ route('admin.settings.themes.settings.preview-frame') }}"
            class="w-full block border-0"
            :style="'height: ' + (previewDevice === 'mobile' ? 667 : previewDevice === 'tablet' ? 1024 : 900) + 'px; background: transparent;'"
            sandbox="allow-scripts allow-same-origin allow-forms"
            title="{{ __('themes::admin.settings.editor.preview_title') }}"></iframe>
</x-admin.theme-preview-container>

<script @cspNonce>
(function () {
    var FRAME_ID = 'dls-theme-preview-frame';
    var FORM_ID = 'theme-settings-form';
    var ACTION = @json(route('admin.settings.themes.settings.preview'));

    // How often the form is sampled, and how long it must stay unchanged
    // before the frame is re-rendered. Sampling rather than listening for
    // `input` events because several fields are hidden inputs written by
    // Alpine or by the media picker, and a value set as a property fires no
    // event. Sampling catches every field the form will actually submit,
    // whoever wrote it.
    var SAMPLE_MS = 300;
    var SETTLE_MS = 600;

    var frame = document.getElementById(FRAME_ID);
    var form = document.getElementById(FORM_ID);
    if (!frame || !form) {
        return;
    }

    // --- height ------------------------------------------------------
    // There is none here, deliberately. The frame used to measure the
    // document it contains and write the result back as its own height,
    // which cannot converge: the page inside is sized in viewport units
    // (`min-h-screen` on the body, the hero at `h-screen`), and inside an
    // iframe the viewport *is* the frame. Growing the frame grew the page,
    // which grew the measurement, which grew the frame — the runaway in
    // #121. The height now comes from the device preset, above.

    // --- re-render with unsaved values -------------------------------
    function snapshot() {
        try {
            return new URLSearchParams(new FormData(form)).toString();
        } catch (e) {
            return null;
        }
    }

    // The frame starts on the stored settings, which is what the form shows
    // before the operator touches it, so the first sample is already
    // rendered and must not trigger a post.
    var rendered = snapshot();
    var seen = rendered;
    var settledAt = 0;

    function render() {
        var data;
        try {
            data = new FormData(form);
        } catch (e) {
            return;
        }

        // A detached form, so submitting it navigates the frame without
        // touching the real form's action or its submit button.
        var carrier = document.createElement('form');
        carrier.method = 'POST';
        carrier.action = ACTION;
        carrier.target = FRAME_ID;
        carrier.style.display = 'none';

        data.forEach(function (value, key) {
            if (typeof File !== 'undefined' && value instanceof File) {
                // File inputs are not theme settings; the media picker
                // stores an id in a hidden field instead.
                return;
            }
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            carrier.appendChild(input);
        });

        document.body.appendChild(carrier);
        carrier.submit();
        document.body.removeChild(carrier);
    }

    window.setInterval(function () {
        var current = snapshot();
        if (current === null) {
            return;
        }

        if (current !== seen) {
            seen = current;
            settledAt = Date.now();
            return;
        }

        if (current !== rendered && Date.now() - settledAt >= SETTLE_MS) {
            rendered = current;
            render();
        }
    }, SAMPLE_MS);
})();
</script>
