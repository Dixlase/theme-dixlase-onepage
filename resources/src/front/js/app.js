/**
 * This file is part of Dixlase.
 *
 * Copyright (C) 2025 exc-D inc.
 * Website: https://exc-d.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

// =============================================================================
// Dixlase Default Theme - Main JavaScript Entry Point
// =============================================================================

// Alpine.js はコア（common/js/app.js）で読み込み済み
// テーマではグローバルの Alpine を使用する
const Alpine = window.Alpine;



// =============================================================================
// Alpine.js 外観モード関数
// =============================================================================

/**
 * Appearance mode for the front end.
 *
 * `defaultValue` is the theme setting ('0' auto / '1' light / '2' dark) and
 * acts as the default. When the front-end switcher is enabled
 * (`toggleEnabled`), a visitor can override it; that choice is kept in
 * localStorage under STORAGE_KEY and wins on every later page. While the
 * switcher is disabled the stored choice is ignored, so turning the switcher
 * off returns every visitor to the operator's mode. A stored choice is a
 * fixed value: picking the mode that matches today's setting does not follow
 * a later change of that setting.
 *
 * The same precedence is duplicated in the FOUC guard in layouts/app.blade.php,
 * which runs before Alpine and has to reach the same answer; keep the two in
 * step.
 */
window.APPEARANCE_STORAGE_KEY = 'dls-appearance-mode';

// How long the surface colours cross-fade when the appearance mode
// changes. Keep in step with `--dls-theme-fade` in style.scss.
window.APPEARANCE_FADE_MS = 300;

window.appearanceTheme = function (defaultValue, toggleEnabled = false) {
    return {
        theme: defaultValue,
        isDark: false,

        // The visitor's stored choice while the switcher is enabled, otherwise
        // the theme setting.
        resolveInitialTheme() {
            if (!toggleEnabled) {
                return defaultValue;
            }
            try {
                const stored = window.localStorage.getItem(window.APPEARANCE_STORAGE_KEY);
                if (stored === '0' || stored === '1' || stored === '2') {
                    return stored;
                }
            } catch (e) {
                // Private mode or blocked storage: fall back to the setting.
            }

            return defaultValue;
        },

        applyTheme() {
            this.isDark = this.theme === '2' ||
                (this.theme === '0' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', this.isDark);
            document.documentElement.classList.toggle('light', !this.isDark);
        },

        // Timer that takes the cross-fade class off again.
        fadeTimer: null,

        // Turn the colour transition on for the length of one switch only.
        // The surface colours come from Tailwind utilities spread over every
        // section, so the universal selector is the only way to reach them
        // all — and a permanent `* { transition: color }` would slow down
        // every hover and fade the whole page in on first paint. Scoping it
        // to a class that lives for APPEARANCE_FADE_MS keeps it to the
        // switch itself.
        beginFade() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            const root = document.documentElement;
            root.classList.add('dls-theme-switching');

            if (this.fadeTimer !== null) {
                window.clearTimeout(this.fadeTimer);
            }

            this.fadeTimer = window.setTimeout(() => {
                root.classList.remove('dls-theme-switching');
                this.fadeTimer = null;
            }, window.APPEARANCE_FADE_MS);
        },

        // Class string for one option button. Returned from a method rather
        // than composed in the directive, so the markup stays within what the
        // @alpinejs/csp build allows (no expressions in attributes).
        optionClass(value) {
            return this.theme === value
                ? 'dls-appearance-option is-active'
                : 'dls-appearance-option';
        },

        // aria-pressed wants the string 'true'/'false'.
        optionPressed(value) {
            return this.theme === value ? 'true' : 'false';
        },

        // Called by the footer switcher.
        setTheme(value) {
            if (value !== '0' && value !== '1' && value !== '2') {
                return;
            }

            this.theme = value;

            try {
                window.localStorage.setItem(window.APPEARANCE_STORAGE_KEY, value);
            } catch (e) {
                // Storage unavailable: the choice still applies to this page.
            }

            this.beginFade();
            this.applyTheme();
        },

        init() {
            this.theme = this.resolveInitialTheme();
            this.applyTheme();

            // Follow the OS only while on auto. The listener stays attached for
            // the life of the page because the visitor can switch back to auto.
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (this.theme === '0') {
                    this.beginFade();
                    this.applyTheme();
                }
            });
        }
    }
};


/**
 * スクロールトップボタン
 */
function initScrollToTop() {
    const scrollTopBtn = document.querySelector('[data-scroll-top]');

    if (scrollTopBtn) {
        // スクロール位置に応じて表示/非表示
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.remove('hidden');
            } else {
                scrollTopBtn.classList.add('hidden');
            }
        });

        // クリックでトップへスクロール
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

/**
 * 外部リンクに target="_blank" を自動追加
 */
function initExternalLinks() {
    const links = document.querySelectorAll('a[href^="http"]');

    links.forEach(link => {
        const url = new URL(link.href);

        // 外部リンクの場合
        if (url.hostname !== window.location.hostname) {
            link.setAttribute('target', '_blank');
            link.setAttribute('rel', 'noopener noreferrer');
        }
    });
}

/**
 * 画像の遅延読み込み
 */
function initLazyLoading() {
    if ('loading' in HTMLImageElement.prototype) {
        // ネイティブ lazy loading をサポート
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
    } else {
        // Intersection Observer でフォールバック
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });

        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => imageObserver.observe(img));
    }
}

/**
 * ページ読み込み時の初期化
 */
document.addEventListener('DOMContentLoaded', () => {
    initScrollToTop();
    initExternalLinks();
    initLazyLoading();

    console.log('Dixlase Default Theme Loaded');
});

// Alpine.js はコア側で既に起動済みのため、ここでは起動しない
// （common/js/app.js で Alpine.start() が呼ばれている）

// Sentinel marker for theme update verification. The sandbox greps for this
// string in the built resources/assets/js/app.js to confirm that a theme
// update actually replaces the built front-end assets, not just theme.json
// and the source tree.
//
// v0.1.4 (the baseline) ships no marker at all, so the assertion pair is
// "absent before the update, present after it" — and rolling back restores
// the v0.1.4 build, which makes the marker disappear again rather than
// revert to an older value. Harmless in production.
window.DixlaseOnePage = window.DixlaseOnePage || {};
window.DixlaseOnePage.buildMarker = 'DIXLASE_ONEPAGE_BUILD_MARKER:0.1.5';
