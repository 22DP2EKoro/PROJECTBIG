import { ref } from 'vue'

const STORAGE_KEY = 'exploreriga-favorites'

function load() {
  try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [] }
  catch { return [] }
}

const favorites = ref(load())

function persist() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(favorites.value))
}

export function useFavorites() {
  function isFavorite(key) {
    return favorites.value.some(f => f.key === key)
  }

  function toggleFavorite(place) {
    const idx = favorites.value.findIndex(f => f.key === place.key)
    if (idx === -1) {
      favorites.value.push(place)
    } else {
      favorites.value.splice(idx, 1)
    }
    persist()
  }

  function removeFavorite(key) {
    const idx = favorites.value.findIndex(f => f.key === key)
    if (idx !== -1) favorites.value.splice(idx, 1)
    persist()
  }

  return { favorites, isFavorite, toggleFavorite, removeFavorite }
}
