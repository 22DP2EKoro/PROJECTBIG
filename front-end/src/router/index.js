// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

// Import your views
import HomeView from '@/views/HomeView.vue'
import MuseumsView from '@/views/MuseumsView.vue'
import RestaurantsView from '@/views/RestaurantsView.vue'
import CultureView from '@/views/CultureView.vue'
import TodoView from '@/views/TodoView.vue'
import SignInView from '@/views/SignInView.vue'

const routes = [
  { path: '/', name: 'Home', component: HomeView },
  { path: '/museums', name: 'Museums', component: MuseumsView },
  { path: '/restaurants', name: 'Restaurants', component: RestaurantsView },
  { path: '/culture', name: 'Culture', component: CultureView },
  { path: '/todo', name: 'Todo', component: TodoView },
  { path: '/signin', name: 'SignIn', component: SignInView },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
