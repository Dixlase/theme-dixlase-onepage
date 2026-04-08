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

@extends('themes::layouts.app')

@section('content')
<div class="frontpage-builder">
    {{-- フロントページビルダーコンテンツがここに動的に挿入されます --}}
    @yield('builder-content')
    
    {{-- ビルダーコンテンツがない場合のデフォルト表示 --}}
    @if(!View::hasSection('builder-content'))
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Welcome to {{ config('app.name', 'Dixlase') }}
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 mb-8">
                    フロントページビルダーでこのページをカスタマイズできます
                </p>
                <x-front.button variant="primary" size="lg" href="/admin">
                    管理画面へ
                </x-front.button>
            </div>
        </div>
    @endif
</div>
@endsection
