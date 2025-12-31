<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Dixlase') }} @yield('title')</title>

{{-- Favicon --}}
@if($themeSettings->favicon && $themeSettings->faviconPath)
    <link rel="icon" type="{{ $themeSettings->favicon->type }}" href="{{ asset('storage/' . $themeSettings->faviconPath) }}">
@endif

{{-- Fonts --}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

{{-- Styles --}}
@if(app()->environment('local') && file_exists(public_path('hot')))
    {{-- Vite開発サーバーが起動している場合 --}}
    @vite([
        'resources/src/front/scss/style.scss',
        'themes/DixlaseDefaultTheme/resources/src/front/scss/style.scss',
        'themes/DixlaseDefaultTheme/resources/assets/js/app.js'
    ])
@else
    {{-- 本番/ステージング環境: シンボリックリンク経由でアセットを読み込む --}}
    {!! load_front_assets(
        ['scss/style.scss'],
        ['css/variables.css', 'css/style.css']
    ) !!}
@endif

{{-- Alpine.js x-cloak style --}}
<style @cspNonce>
    [x-cloak] { display: none !important; }
</style>

@stack('styles')
