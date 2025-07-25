import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  plugins: [vue()],
  build: {
	  outDir: 'dist', // Output directory for production build
    
    emptyOutDir: true,
    manifest: true,
    
    assetsDir: '.',
    rollupOptions: {
       input: ['src/main.js', './index.html']
    }
  }
});
