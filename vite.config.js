import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import dotenv from 'dotenv';

// Carrega as variáveis do .env
dotenv.config();
import path from 'path';  // Adicionando a importação do 'path'
export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js","resources/css/ti.css"],
            refresh: true,
        }),
    ],

    server: {
        host: process.env.VITE_SERVER_HOST || '0.0.0.0', // Usa a variável de ambiente ou um valor padrão
        port: process.env.VITE_SERVER_PORT || 5173, // Porta configurável
        strictPort: true,
        cors: true,
        hmr: {
            host: process.env.VITE_HMR_HOST || 'localhost', // Configurável via variável de ambiente
            port: process.env.VITE_HMR_PORT || 5173,
        },
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },
});
