import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { initializeAuth } from './auth' // updated

import axios from 'axios';

axios.defaults.baseURL = 'http://localhost:3000'; // or your backend port

initializeAuth() // properly set up auth state

const app = createApp(App)
app.use(router)
app.mount('#app')
