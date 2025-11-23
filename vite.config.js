import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // Permite conexões de qualquer IP (incluindo túneis)
        port: 5173,
        strictPort: false,
        hmr: {
            host: 'localhost', // Para desenvolvimento local
            // Se estiver usando túnel, descomente e configure:
            // host: process.env.VITE_HMR_HOST || 'localhost',
        },
        cors: {
            origin: '*', // Permite CORS para desenvolvimento com túneis
            credentials: true,
        },
    },
});
