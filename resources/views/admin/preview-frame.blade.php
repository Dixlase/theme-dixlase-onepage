{{--
This file is part of Dixlase OnePage.

Copyright (C) 2026 exc-D inc.
https://exc-d.com

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

@section('title', ' - ' . __('Preview'))

@section('content')
{{-- Hero Section --}}
@include('themes::partials.hero')

{{-- Front Page Content --}}
<section class="front-content py-16" id="preview-content-section">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto prose prose-lg dark:prose-invert" id="preview-content-area">
            {!! $initialRenderedContent ?? '' !!}
        </div>
    </div>
</section>

{{-- Contact Form Section --}}
@if(function_exists('dls_inquiry_section'))
    {!! dls_inquiry_section() !!}
@endif
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
})();
</script>
@endpush
