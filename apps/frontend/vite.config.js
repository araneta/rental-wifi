import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  plugins: [vue()],
  build: {
    outDir: path.resolve(__dirname, '../public/assets'),
    emptyOutDir: true,
    manifest: true,
    assetsDir: '.',
    rollupOptions: {
      input: './src/main.js'
    }
  }
});
