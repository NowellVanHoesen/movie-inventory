import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import vue from "@vitejs/plugin-vue";
import path from "path";
import vueDevTools from "vite-plugin-vue-devtools";

export default defineConfig({
    plugins: [
        tailwindcss(),
        vueDevTools(),

        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),

        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            "~fontawesome": path.resolve(__dirname, "node_modules/@fortawesome/fontawesome-free"),
            "@": path.resolve(__dirname, "resources/js"),
        },
    },
});
