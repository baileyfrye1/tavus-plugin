import { defineConfig } from "vite";
import react, { reactCompilerPreset } from "@vitejs/plugin-react";
import babel from "@rolldown/plugin-babel";

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(), babel({ presets: [reactCompilerPreset()] })],
  base: "/wp-content/plugins/tavus-integration/dist/",
  build: {
    outDir: "dist",
    manifest: true,
    cssCodeSplit: false,
    rolldownOptions: {
      input: {
        main: "src/main.tsx",
        admin: "src/admin.tsx",
      },
    },
  },
});
