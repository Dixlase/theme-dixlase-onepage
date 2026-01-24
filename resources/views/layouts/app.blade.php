<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
    class="scroll-smooth"
    x-data="appearanceTheme('{{ $themeSettings->appearance_mode ?? '0' }}')"
    x-init="init()"
    :class="{ 'dark': isDark, 'light': !isDark }"
>
<head>
    @include('themes::partials.head')
    
    {{-- Font Awesome for SNS Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-col min-h-screen">
    {{-- 管理バー（管理者ログイン時のみ表示） --}}
    <x-ui.admin-bar />
    
    @include('themes::partials.header')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('themes::partials.footer')

    {{-- Scripts --}}
    @if(app()->environment('local') && file_exists(public_path('hot')))
        @vite([
            'resources/src/common/js/app.js',
            'themes/DixlaseDefaultTheme/resources/src/js/app.js'
        ])
    @else
        {!! load_front_assets([], ['js/app.js']) !!}
    @endif
    
    @stack('scripts')

</body>
</html>
