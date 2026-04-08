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

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Dixlase') }} @yield('title')</title>

{{-- SEOメタタグ・OGP・JSON-LD（DixlaseSEOプラグインから注入） --}}
{!! $seoHeadMeta ?? '' !!}

{{-- Favicon --}}
@if(!empty($themeSettings->faviconPath))
    <link rel="icon" href="{{ asset('storage/' . $themeSettings->faviconPath) }}">
@endif

{{-- Fonts --}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

{{-- Styles --}}
@if(app()->environment('local') && file_exists(public_path('hot')))
    {{-- Vite開発サーバーが起動している場合 --}}
    @vite([
        'themes/DixlaseOnePage/resources/src/front/css/tailwind.css',
        'resources/src/front/scss/style.scss',
        'themes/DixlaseOnePage/resources/src/front/scss/style.scss',
        'themes/DixlaseOnePage/resources/src/front/js/app.js'
    ])
@else
    {{-- 本番/ステージング環境: シンボリックリンク経由でアセットを読み込む --}}
    {!! load_front_assets(
        ['scss/style.scss'],
        ['css/tailwind.css', 'scss/style.scss']
    ) !!}
@endif

{{-- Alpine.js x-cloak style --}}
<style @cspNonce>
    [x-cloak] { display: none !important; }
</style>

@stack('styles')
