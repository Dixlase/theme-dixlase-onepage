{{--
This file is part of Dixlase OnePage.

Copyright (C) 2026 exc-D inc.
https://exc-d.com

Dual-licensed under the GNU General Public License v3 or later, or
a commercial license from exc-D inc. See LICENSE for details.
--}}

{{-- 419 — branded version. See `errors/_page.blade.php`. --}}
@include('themes::errors._page', [
    'code' => '419',
    'title' => app()->getLocale() === 'ja' ? 'ページの有効期限が切れました' : 'Page Expired',
    'message' => app()->getLocale() === 'ja'
        ? 'セッションの有効期限が切れました。ページを再読み込みしてもう一度お試しください。'
        : 'Your session has expired. Please refresh the page and try again.',
])
