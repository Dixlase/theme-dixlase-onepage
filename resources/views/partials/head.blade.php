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
    {{-- Vite開発サーバーが起動している場合。コア共通 Tailwind
         (`resources/src/common/css/tailwind.css`) を必ず含めること。
         これが無いと、フロントページがコア由来のユーティリティクラス
         無しでレンダリングされてレイアウトが崩壊する。
         non-Vite 経路 (`load_front_assets` → `load_core_assets`) は
         AssetHelper 側で自動 prepend されるが、こちらの直接 @vite
         呼び出しでは明示する必要がある。 --}}
    @vite([
        'resources/src/common/css/tailwind.css',
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
