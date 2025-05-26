import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    build: {
        // Force HTTPS for asset URLs in production
        rollupOptions: {
            external: [],
        },
    },
    server: {
        // For local development
        https: false,
        host: true,
    },
    // Force base URL to use HTTPS in production
    base: process.env.APP_ENV === "production" ? process.env.APP_URL : "/",
});
