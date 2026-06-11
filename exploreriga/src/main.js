import 'bootstrap/dist/css/bootstrap.min.css'
import '@fortawesome/fontawesome-free/css/all.min.css'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { initializeAuth } from './auth' // updated
import './assets/global.css'
import "leaflet/dist/leaflet.css";

import axios from 'axios';

axios.defaults.baseURL = 'http://localhost:3000'; // or your backend port

initializeAuth() // properly set up auth state

const app = createApp(App)
app.use(router)
app.mount('#app')
