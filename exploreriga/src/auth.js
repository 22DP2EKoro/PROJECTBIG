// src/auth.js
import { ref } from 'vue'

export const isAuthenticated = ref(false)

export function initializeAuth() {
  isAuthenticated.value = !!localStorage.getItem('user')
}

export function signIn(user) {
  localStorage.setItem('user', JSON.stringify(user))
  isAuthenticated.value = true
}

export function signOut() {
  localStorage.removeItem('user')
  isAuthenticated.value = false
}
