// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { isAuthenticated } from '../auth'

// Import views

import HomeView from '@/views/HomeView.vue'
import MuseumsView from '@/views/MuseumsView.vue'
import RestaurantsView from '@/views/RestaurantsView.vue'
import CultureView from '@/views/CultureView.vue'
import TodoView from '@/views/TodoView.vue'
import SignInView from '@/views/SignInView.vue'
import AdminDashboard from '@/views/AdminDashboard.vue'; 
import ContactsView from '@/views/ContactsView.vue'
import TeamView from '@/views/TeamView.vue'
import InformationView from '@/views/InformationView.vue'
const routes = [
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, isAdmin: true }
  },
  { path: '/', name: 'Home', component: HomeView ,meta: { requiresAuth: true } }, // ✅ Mark route as protected
  { path: '/museums', name: 'Museums', component: MuseumsView, meta: { requiresAuth: true } }, // ✅ Mark route as protected
  
  
  { path: '/restaurants', name: 'Restaurants', component: RestaurantsView, meta: { requiresAuth: true } }, // ✅ Mark route as protected
  { path: '/culture', name: 'Culture', component: CultureView, meta: { requiresAuth: true } }, // ✅ Mark route as protected
  {
    path: '/contacts',
    name: 'Contacts',
    component: ContactsView,
  },
  {
   path: '/team',
   name: 'Team',
   component: TeamView,
  },
  {
    path: '/information',
    name: 'Information',
    component: InformationView,
  },

  {
    path: '/todo',
    name: 'Todo',
    component: TodoView,
    meta: { requiresAuth: true }, // ✅ Mark route as protected
  },
  { path: '/signin', name: 'SignIn', component: SignInView },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// ✅ Global navigation guard for auth check
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !isAuthenticated.value) {
    next({ path: '/signin', query: { redirect: to.fullPath } })
  } else {
    next()
  }
})

export default router
