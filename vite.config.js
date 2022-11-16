import {defineConfig} from "vite";
import laravel from "laravel-vite-plugin";
import reload from "vite-plugin-full-reload";
import sri from "vite-plugin-manifest-sri";

export default defineConfig({
    plugins: [
        laravel([
            "resources/css/app.css",
            "resources/js/app.js"
        ]),
        reload(["resources/views/**/*.blade.php"]),
        sri()
    ]
});
