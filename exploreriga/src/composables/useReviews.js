import { ref } from 'vue'

const museumReviews = ref({})
const restaurantReviews = ref({})
const placeReviews = ref({})

export function useReviews() {
  return { museumReviews, restaurantReviews, placeReviews }
}
