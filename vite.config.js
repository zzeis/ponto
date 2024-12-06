import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from 'path';  // Adicionando a importação do 'path'
export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],

    server: {
        host: '0.0.0.0',  // Expõe o servidor para toda a rede
        port: 5173,        // A porta que o Vite vai escutar
     
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },
});
