{{--
This file is part of Dixlase OnePage.

Copyright (C) 2026 exc-D inc.
https://exc-d.com

Dual-licensed under the GNU General Public License v3 or later, or
a commercial license from exc-D inc. See LICENSE for details.
--}}

{{-- 403 — branded version. See `errors/_page.blade.php`. --}}
@include('themes::errors._page', [
    'code' => '403',
    'title' => app()->getLocale() === 'ja' ? 'アクセスが拒否されました' : 'Access Denied',
    'message' => app()->getLocale() === 'ja'
        ? 'このページを表示する権限がありません。'
        : 'You do not have permission to access this page.',
])
