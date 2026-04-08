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

@section('title', ' - Page Not Found')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center">
        {{-- 404 Icon --}}
        <div class="mb-8">
            <svg class="mx-auto h-32 w-32 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        {{-- Error Message --}}
        <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">
            ページが見つかりません
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mb-8">
            お探しのページは存在しないか、移動した可能性があります。
        </p>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <x-front.button variant="primary" href="{{ url('/') }}">
                ホームに戻る
            </x-front.button>
            <x-front.button variant="outline" @click="history.back()">
                前のページに戻る
            </x-front.button>
        </div>
    </div>
</div>
@endsection
