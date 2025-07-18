import { createApp } from 'vue';
import App from './App.vue';
import router from './router/index.js';

// Import Bootstrap CSS and JS from CDN
import 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css';
import 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';

createApp(App)
  .use(router)
  .mount('#app'); 