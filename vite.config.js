import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
    build: {
        // ビルド出力先（コア側がシンボリックリンクで public/assets/themes/DixlaseOnePage として公開する）
        outDir: resolve(__dirname, 'resources/assets'),
        // ビルドごとに出力先をクリアして残骸ファイルを排除する
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
