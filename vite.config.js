import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
    // Static files copied verbatim to outDir on every build. Vite treats
    // the contents of this directory as "assets that ship with the theme
    // but aren't part of the build graph" — perfect fit for the hand-
    // authored theme thumbnail that would otherwise be wiped by
    // `emptyOutDir: true` below.
    //
    // Before this option existed in the theme's vite config, thumbnail.png
    // lived directly at `resources/assets/thumbnail.png` and .gitignore
    // carried a one-file exception (`!/resources/assets/thumbnail.*`) to
    // keep it tracked. Every `npm run build` on a clean checkout deleted
    // it — the core release CI hit this. `publicDir` fixes it by making
    // the source location distinct from the output location: source
    // stays under `resources/public/`, vite copies it into
    // `resources/assets/` alongside the built CSS/JS at build time, and
    // the runtime path `resources/assets/thumbnail.png` (which core's
    // ExtensionCardPresenter probes) is unchanged.
    publicDir: resolve(__dirname, 'resources/public'),

    build: {
        // ビルド出力先（コア側がシンボリックリンクで public/assets/themes/DixlaseOnePage として公開する）
        outDir: resolve(__dirname, 'resources/assets'),
        // ビルドごとに出力先をクリアして残骸ファイルを排除する
        // (thumbnail.png は `publicDir` 経由で毎ビルド copy されるので
        // wipe されても復活する)
        emptyOutDir: true,

        // ソースマップ
        sourcemap: process.env.NODE_ENV === 'development',

        // ロールアップオプション
        rollupOptions: {
            input: {
                // Tailwind CSS v4
                'tailwind': resolve(__dirname, 'resources/src/front/css/tailwind.css'),

                // SCSS (開発時)
                'style': resolve(__dirname, 'resources/src/front/scss/style.scss'),

                // JavaScript (Alpine.js含む)
                'app': resolve(__dirname, 'resources/src/front/js/app.js'),
            },
            output: {
                // 出力ファイル名のパターン
                entryFileNames: 'js/[name].js',
                chunkFileNames: 'js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    // CSSファイル
                    if (assetInfo.name.endsWith('.css')) {
                        return 'css/[name][extname]';
                    }
                    // 画像ファイル
                    if (/\.(png|jpe?g|gif|svg|webp|ico)$/.test(assetInfo.name)) {
                        return 'images/[name][extname]';
                    }
                    // フォントファイル
                    if (/\.(woff2?|eot|ttf|otf)$/.test(assetInfo.name)) {
                        return 'fonts/[name][extname]';
                    }
                    // その他
                    return 'assets/[name][extname]';
                },
            },
        },

        // マニフェストファイル生成（AssetHelperが manifest.json を参照するため明示指定）
        manifest: 'manifest.json',

        // 最小化
        minify: process.env.NODE_ENV === 'production' ? 'terser' : false,

        // Terserオプション
        terserOptions: {
            compress: {
                drop_console: process.env.NODE_ENV === 'production',
            },
        },

        // CSS の minify は lightningcss を使う。
        // @tailwindcss/typography 0.5.x は Tailwind v4 配下で空の
        // :where() を含む CSS を出力することがあり、esbuild
        // (Vite の CSS minify デフォルト) はこれを valid と認識できず
        // 毎ビルドで "Unexpected \")\"" の warning を出す。
        // lightningcss は forgiving-selector-list を許容し、圧縮率も
        // esbuild より高い (Tailwind v4 公式推奨)。
        cssMinify: 'lightningcss',

        // lightningcss に対するターゲット指定。明示しないと保守的な
        // browserslist 既定で動き、Tailwind v4 が出す oklch() / nesting
        // / `:where()` などのモダン CSS に対して fallback を展開する
        // 結果、ファイルサイズが大きく膨らむ。Tailwind v4 自身が前提と
        // するモダンブラウザ (Chrome / Edge / Safari / Firefox 直近) に
        // 揃え、不要な変換を避けることで出力サイズを抑える。
        cssTarget: ['chrome111', 'edge111', 'safari16.4', 'firefox128'],
    },

    // 開発サーバー設定
    server: {
        host: '0.0.0.0',
        port: 5174, // デフォルトテーマ専用ポート
        strictPort: false,
        hmr: {
            host: 'localhost',
        },
    },

    // プレビューサーバー設定
    preview: {
        port: 5174,
    },

    // 依存関係の最適化
    optimizeDeps: {
        include: [],
    },

    // CSS設定
    css: {
        devSourcemap: true,
        preprocessorOptions: {
            scss: {
                // Use the modern Sass JS API so Vite stops printing the
                // "legacy-js-api" deprecation warning. The legacy API will be
                // removed in Dart Sass 2.0.
                api: 'modern-compiler',
            },
        },
    },
});
