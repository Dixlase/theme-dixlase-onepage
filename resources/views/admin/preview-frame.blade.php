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

@extends('themes::layouts.preview')

@section('title', __('Preview'))

{{-- The body a plugin supplies, when the theme setting names one.

     Only for the theme settings preview. In bare mode this view is the front
     page editor's preview, where the body being edited *is* core's front page
     content and the parent window live-replaces it through postMessage — a
     provider's body there would be showing the operator something other than
     what they are editing.

     The surface says "preview", so a provider can tell this render from the
     live page. The helper reads the setting through
     dls_onepage_primary_setting(), which prefers a value posted by the
     settings form over the stored one, so changing the chooser re-renders the
     frame with the new body before anything is saved. --}}
@php($previewProviderBody = ($bareContent ?? false)
    ? null
    : dls_onepage_front_page_body(DLS_ONEPAGE_FRONT_PAGE_SURFACE_PREVIEW))
@php($hasPreviewProviderBody = $previewProviderBody !== null && ! $previewProviderBody->isEmpty())

@if($hasPreviewProviderBody && $previewProviderBody->styleUrls !== [])
@push('styles')
@foreach($previewProviderBody->styleUrls as $styleUrl)
<link rel="stylesheet" href="{{ $styleUrl }}">
@endforeach
@endpush
@endif

@if($hasPreviewProviderBody && $previewProviderBody->scriptUrls !== [])
@push('scripts')
@foreach($previewProviderBody->scriptUrls as $scriptUrl)
<script @cspNonce src="{{ $scriptUrl }}"></script>
@endforeach
@endpush
@endif

@if(!empty($hasCustomCss))
@push('styles')
<link rel="stylesheet" id="preview-custom-css-link" href="{{ route('front.custom-style') }}?v={{ $customAssetVersion }}">
@endpush
@endif

@if(!empty($hasCustomJs))
@push('scripts')
<script @cspNonce src="{{ route('front.custom-script') }}?v={{ $customAssetVersion }}"></script>
@endpush
@endif

@section('content')
@unless($bareContent ?? false)
{{-- Hero Section, under the same setting the live page reads, so the
     frame shows a page with no hero as a page with no hero. --}}
@if(dls_onepage_hero_enabled())
@include('themes::partials.hero')
@endif
@endunless

{{-- Front Page Body — a plugin's, or core's front page content.

     The wrappers differ exactly as they do on the live page
     (resources/views/index.blade.php): core's content gets `max-w-4xl` and
     `prose`, a provider's body gets the container width and no `prose`,
     because it brings its own layout.

     The provider branch carries neither `preview-content-area` nor
     `preview-content-section`. Those ids are the front page editor's
     live-update targets, and they belong to the element holding the content
     being edited — which, in the provider branch, is not what is on screen.
     Bare mode never takes this branch, so the editor always has them. --}}
@if($hasPreviewProviderBody)
<section class="front-page-body pb-16">
    <div class="container mx-auto px-4">
        {!! $previewProviderBody->html() !!}
    </div>
</section>
@else
{{-- Bare mode keeps the same wrapping classes as the live front so the
     embedding iframe shows the content centered at the same width. --}}
<section class="front-content {{ ($bareContent ?? false) ? 'py-0' : 'pb-16' }}" id="preview-content-section">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto prose prose-lg dark:prose-invert" id="preview-content-area">
            {!! $initialRenderedContent ?? '' !!}
        </div>
    </div>
</section>
@endif

@unless($bareContent ?? false)
{{-- Contact Form Section --}}
@if(($themeSettings->show_inquiry_form ?? '0') === '1' && function_exists('dls_inquiry_section'))
    {!! dls_inquiry_section() !!}
@endif
@endunless
@endsection

@push('styles')
<style @cspNonce>
/* CAPTCHAウィジェットを非表示 */
.captcha-container,
.g-recaptcha,
.grecaptcha-badge,
.cf-turnstile,
#recaptcha-container,
iframe[src*="recaptcha"],
iframe[src*="turnstile"] {
    display: none !important;
}
</style>
@endpush

@push('scripts')
<script @cspNonce>
/**
 * プレビューフレーム postMessage リスナー
 * 親ウィンドウ（管理画面エディタ）からのメッセージを受信し、コンテンツを更新する。
 */
(function() {
    var contentArea = document.getElementById('preview-content-area');
    var contentSection = document.getElementById('preview-content-section');

    window.addEventListener('message', function(event) {
        if (event.origin !== window.location.origin) return;
        if (!event.data || event.data.type !== 'dixlase-preview-update') return;

        switch (event.data.action) {
            case 'updateContent':
                if (contentArea) {
                    contentArea.innerHTML = event.data.html || '';
                }
                if (contentSection) {
                    contentSection.style.display = (event.data.html) ? '' : 'none';
                }
                break;

            case 'updateCustomCss':
                // Disable the saved-state <link> so live edits fully replace it;
                // otherwise rules removed by the editor would still apply.
                var linkEl = document.getElementById('preview-custom-css-link');
                if (linkEl) {
                    linkEl.disabled = true;
                }
                var styleEl = document.getElementById('preview-custom-css');
                if (!styleEl) {
                    styleEl = document.createElement('style');
                    styleEl.id = 'preview-custom-css';
                    document.head.appendChild(styleEl);
                }
                styleEl.textContent = event.data.css || '';
                break;
        }
    });

    // 親ウィンドウに準備完了を通知
    if (window.parent !== window) {
        window.parent.postMessage({ type: 'dixlase-preview-ready' }, window.location.origin);
    }

    // Notify parent of content height so embeddings (e.g., theme settings preview)
    // can size the iframe without scroll. Front-page-master uses fixed device
    // heights and simply ignores these messages.
    // Use '*' as targetOrigin: the parent verifies event.origin on receive,
    // and matching origins explicitly here is fragile inside sandboxed iframes.
    var lastReportedHeight = -1;
    function postHeight() {
        if (window.parent === window) return;
        var h = Math.max(
            document.documentElement.scrollHeight || 0,
            document.body ? document.body.scrollHeight : 0,
            document.body ? document.body.offsetHeight : 0
        );
        if (h === lastReportedHeight) return;
        lastReportedHeight = h;
        window.parent.postMessage({ type: 'dixlase-preview-height', height: h }, '*');
    }
    if (typeof ResizeObserver !== 'undefined' && document.body) {
        new ResizeObserver(postHeight).observe(document.body);
    }
    window.addEventListener('load', postHeight);
    document.addEventListener('DOMContentLoaded', postHeight);
    // Also re-measure after web fonts / images that load late.
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(postHeight);
    }
    postHeight();
})();
</script>
@endpush
