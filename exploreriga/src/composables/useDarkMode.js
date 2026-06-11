import { ref, watch } from 'vue'

const isDark = ref(localStorage.getItem('darkMode') === 'true')

watch(isDark, (val) => {
  localStorage.setItem('darkMode', val)
})

export function useDarkMode() {
  return {
    isDark,
    toggleDarkMode: () => { isDark.value = !isDark.value },
  }
}
