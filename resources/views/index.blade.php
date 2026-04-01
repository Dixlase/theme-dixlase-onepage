@extends('themes::layouts.app')

@section('title', ' - ' . __('Home'))

@if(!empty($hasCustomCss))
@push('styles')
<link rel="stylesheet" href="{{ route('front.custom-style') }}?v={{ $customAssetVersion }}">
@endpush
@endif

@if(!empty($hasCustomJs))
@push('scripts')
<script @cspNonce src="{{ route('front.custom-script') }}?v={{ $customAssetVersion }}"></script>
@endpush
@endif

@section('content')
{{-- Hero Section --}}
@include('themes::partials.hero')

{{-- Front Page Content --}}
@if(!empty($frontContent))
<section class="front-content py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto prose prose-lg dark:prose-invert">
            @if($frontEditorType === 'blade')
                {!! shortcode_parse(\Illuminate\Support\Facades\Blade::render($frontContent)) !!}
            @elseif($frontEditorType === 'markdown')
                {!! shortcode_parse(\Illuminate\Support\Str::markdown($frontContent)) !!}
            @else
                {!! shortcode_parse($frontContent) !!}
            @endif
        </div>
    </div>
</section>
@endif

{{-- Contact Form Section --}}
@if(function_exists('dls_inquiry_enabled') && dls_inquiry_enabled())
<section class="inquiry-section py-16 bg-gray-100 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            @php $inquirySettings = function_exists('dls_inquiry_settings') ? dls_inquiry_settings() : null; @endphp
            <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-3">
                {{ !empty($inquirySettings->form_heading) ? $inquirySettings->form_heading : __('dixlase-inquiry::front.form.heading') }}
            </h2>
            @php $inquiryDesc = !empty($inquirySettings->form_description) ? $inquirySettings->form_description : __('dixlase-inquiry::front.form.default_description'); @endphp
            @if($inquiryDesc)
                <p class="text-center text-gray-600 dark:text-gray-400 mb-8 max-w-lg mx-auto">{{ $inquiryDesc }}</p>
            @endif
            {!! dls_inquiry_form() !!}
        </div>
    </div>
</section>
@endif

{{-- 
<section class="py-24 bg-gradient-to-b from-[#0F1117] to-[#12141C]">
    <div class="container mx-auto px-4">

        <div id="features" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6 hover:bg-white/10 transition-all duration-300 hover:shadow-xl hover:shadow-purple-500/5">
            <div class="text-center">
                <div class="bg-purple-500/20 rounded-lg w-12 h-12 flex items-center justify-center mb-5 text-purple-400 mx-auto">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-white">
                    {{ __('Fast & Modern') }}
                </h3>
                <p class="text-gray-400">
                    {{ __('Built with modern technologies for optimal performance') }}
                </p>
            </div>
        </div>

        <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6 hover:bg-white/10 transition-all duration-300 hover:shadow-xl hover:shadow-purple-500/5">
            <div class="text-center">
                <div class="bg-purple-500/20 rounded-lg w-12 h-12 flex items-center justify-center mb-5 text-purple-400 mx-auto">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-white">
                    {{ __('Customizable') }}
                </h3>
                <p class="text-gray-400">
                    {{ __('Easily customize with themes and plugins') }}
                </p>
            </div>
        </div>

        <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6 hover:bg-white/10 transition-all duration-300 hover:shadow-xl hover:shadow-purple-500/5">
            <div class="text-center">
                <div class="bg-purple-500/20 rounded-lg w-12 h-12 flex items-center justify-center mb-5 text-purple-400 mx-auto">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-white">
                    {{ __('Secure') }}
                </h3>
                <p class="text-gray-400">
                    {{ __('Built with security best practices in mind') }}
                </p>
            </div>
        </div>
    </div>

        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-3xl p-12 md:p-16 text-center text-white shadow-2xl">
        <h2 class="text-3xl md:text-5xl font-bold mb-6">
            {{ __('Ready to Get Started?') }}
        </h2>
        <p class="text-xl md:text-2xl mb-10 text-white/90 max-w-2xl mx-auto">
            {{ __('Create your amazing website with Dixlase today') }}
        </p>
        <a href="/admin" class="inline-flex items-center px-10 py-5 bg-white text-blue-600 font-bold text-lg rounded-full hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-xl">
            {{ __('Get Started Now') }}
            <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </a>
        </div>
    </div>
</section>
--}}
@endsection
