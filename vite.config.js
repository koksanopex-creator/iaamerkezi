import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default ({ mode }) => {
    // 1. .env dosyasındaki değişkenleri yüklüyoruz
    const env = loadEnv(mode, process.cwd(), '');

    return defineConfig({
        // 2. DİNAMİK AYAR BURASI:
        // Eğer .env dosyasında ASSET_URL varsa (Örn: /iaa/) onu kullanır.
        // Yoksa (Lokalde olduğu gibi) '/' (kök dizin) kullanır.
        base: env.ASSET_URL || '/',

        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: [
                    'resources/routes/**',
                    'routes/**',
                    'resources/views/**',
                    'app/Http/Controllers/**', // Controller değişikliklerini izle
                    'app/Models/**',           // Model değişikliklerini izle
                    'resources/js/**',         // JS değişikliklerini izle
                    'resources/css/**'         // CSS değişikliklerini izle
                ],
            }),
        ],
        server: {
            watch: {
                ignored: [
                    '**/storage/**',
                    '**/vendor/**',
                    '**/public/**',
                    '**/.git/**',
                    '**/node_modules/**'
                ]
            }
        }
    });
};