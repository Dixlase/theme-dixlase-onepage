{{--
This file is part of Dixlase OnePage.

Copyright (C) 2026 exc-D inc. and Dixlase contributors
https://exc-d.com

Dual-licensed under the GNU General Public License v3 or later, or
a commercial license from exc-D inc. See LICENSE for details.
--}}

{{--
    404 — branded version. Resolved by core `errors/404.blade.php`
    via `View::exists('themes::errors.404')`. See the matching
    docblock in `errors/_page.blade.php` for the shared layout.
--}}
@include('themes::errors._page', [
    'code' => '404',
    'title' => app()->getLocale() === 'ja' ? 'ページが見つかりません' : 'Page Not Found',
    'message' => app()->getLocale() === 'ja'
        ? 'お探しのページは存在しないか、移動した可能性があります。'
        : 'The page you were looking for does not exist or may have been moved.',
])
