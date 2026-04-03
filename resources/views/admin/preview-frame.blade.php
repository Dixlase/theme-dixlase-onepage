{{--
This file is part of Dixlase.

Copyright (C) 2026 exc-D inc.
https://exc-d.com

iframe用プレビューフレーム。
管理画面のフロントページ編集画面でiframeとして読み込まれ、
テーマの実際のレイアウトでコンテンツをリアルタイムプレビューする。
postMessage でコンテンツ・カスタムCSSの更新を受け取る。
軽量プレビューレイアウト（layouts.preview）を使用し、JSバンドルを読み込まない。
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
@if(function_exists('dls_inquiry_enabled') && dls_inquiry_enabled())
<section class="inquiry-section py-16 bg-gray-100 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            @php $inquirySettings = function_exists('dls_inquiry_settings') ? dls_inquiry_settings() : null; @endphp
            @if(!empty($inquirySettings->form_heading))
                <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-3">{{ $inquirySettings->form_heading }}</h2>
            @endif
            @if(!empty($inquirySettings->form_description))
                <p class="text-center text-gray-600 dark:text-gray-400 mb-8 max-w-lg mx-auto">{{ $inquirySettings->form_description }}</p>
            @endif
            {!! dls_inquiry_form() !!}
        </div>
    </div>
</section>
@endif
@endsection

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
