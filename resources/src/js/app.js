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

// Alpine.js をインポート
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Alpine.js プラグインを登録
Alpine.plugin(collapse);

// グローバルに Alpine を公開（デバッグ用）
window.Alpine = Alpine;



// =============================================================================
// Alpine.js 外観モード関数
// =============================================================================

/**
 * 外観モード（ライト/ダーク）を制御する Alpine.js 関数
 * テーマ設定の値に基づいて自動的にテーマを切り替える
 * 
 * @param {string} defaultValue - テーマ設定の値 ('0': 自動, '1': ライト, '2': ダーク)
 */
window.appearanceTheme = function (defaultValue) {
    return {
        theme: defaultValue, // テーマ設定の値を使用
        isDark: false,

        applyTheme() {
            // テーマ設定に基づいてダークモードを判定
            this.isDark = this.theme === '2' ||
                (this.theme === '0' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', this.isDark);
            document.documentElement.classList.toggle('light', !this.isDark);
        },

        init() {
            this.applyTheme();

            // 自動モードの場合、システム設定変更を監視
            if (this.theme === '0') {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                    this.applyTheme();
                });
            }
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

// Alpine.js を起動
Alpine.start();
