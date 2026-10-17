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

     The frame is same-origin, so its height is read from its own document
     rather than through a postMessage round-trip, and the container's
     ResizeObserver picks the change up to rescale. --}}

<x-admin.theme-preview-container
    :title="__('themes::admin.settings.editor.preview_title')"
    :appearanceMode="$settings->appearance_mode ?? '0'"
>
    <iframe id="dls-theme-preview-frame"
            name="dls-theme-preview-frame"
            src="{{ route('admin.settings.themes.settings.preview-frame') }}"
            class="w-full block border-0"
            style="height: 600px; background: transparent;"
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
    // Same-origin, so the document can be measured directly. Observing the
    // body covers content that arrives after load (web fonts, images), and
    // fonts.ready covers the reflow when a heading face swaps in.
    function syncHeight() {
        try {
            var doc = frame.contentDocument;
            if (!doc) {
                return;
            }
            var height = Math.max(
                doc.documentElement ? doc.documentElement.scrollHeight : 0,
                doc.body ? doc.body.scrollHeight : 0
            );
            if (height > 0 && Math.abs(frame.clientHeight - height) > 1) {
                frame.style.height = height + 'px';
            }
        } catch (e) {
            // Cross-origin or a frame mid-navigation: keep the last height.
        }
    }

    frame.addEventListener('load', function () {
        syncHeight();
        try {
            var doc = frame.contentDocument;
            if (doc && doc.body && typeof ResizeObserver !== 'undefined') {
                new ResizeObserver(syncHeight).observe(doc.body);
            }
            if (doc && doc.fonts && doc.fonts.ready) {
                doc.fonts.ready.then(syncHeight);
            }
        } catch (e) {
            // As above.
        }
    });

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
