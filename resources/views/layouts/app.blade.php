<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="scroll-smooth"
    x-data="appearanceTheme('{{ $themeSettings->appearance_mode ?? '0' }}')"
    x-init="init()"
    :class="{ 'dark': isDark, 'light': !isDark }"
>
<head>
    {{-- FOUC防止: Alpine.js初期化前にダークモードクラスを即時適用 --}}
    <script @cspNonce>
    (function(){
        var m = '{{ $themeSettings->appearance_mode ?? '0' }}';
        if (m === '2' || (m === '0' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        } else {
            document.documentElement.classList.add('light');
            document.documentElement.classList.remove('dark');
        }
    })();
    </script>
    @include('themes::partials.head')

    {{-- Font Awesome for SNS Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-col min-h-screen">
    {{-- 管理バー・メンテナンスバナー（管理者ログイン時のみ表示） --}}
    <div class="sticky top-0 z-[9999]">
        {{-- メンテナンスバナー（メンテナンス中のみ表示） --}}
        <x-ui-maintenance-banner />
        {{-- 管理バー --}}
        <x-ui-admin-bar />
    </div>
    
    @include('themes::partials.header')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('themes::partials.footer')

    {{-- Scripts --}}
    @if(app()->environment('local') && file_exists(public_path('hot')))
        @vite([
            'resources/src/common/js/app.js',
            'themes/DixlaseOnePage/resources/src/js/app.js'
        ])
    @else
        {!! load_front_assets([], ['js/app.js']) !!}
    @endif
    
    @stack('scripts')

</body>
</html>
