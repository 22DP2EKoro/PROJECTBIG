<template>
  <div :class="{ 'dark-mode': isDarkMode }">
    <div class="res-container">
      <nav>
        <a href="/" class="brand">ExploreRiga</a>
        <div class="nav-links" :style="{ right: navVisible ? '0' : '-240px' }">
          <i class="fa fa-times" @click="hideMenu"></i>
          <ul>
            <li class="dropdown">
              <a href="#" class="dropbtn" @click="toggleDropdown($event)">{{ t('nav.aboutUs') }}</a>
              <div id="myDropdown" class="dropdown-content" :class="{ show: dropdownVisible }">
                <a href="/contacts">{{ t('nav.contacts') }}</a>
                <a href="/team">{{ t('nav.team') }}</a>
                <a href="/information">{{ t('nav.information') }}</a>
              </div>
            </li>
            <li><router-link to="/">{{ t('nav.home') }}</router-link></li>
            <li><router-link to="/museums">{{ t('nav.museums') }}</router-link></li>
            <li><router-link to="/restaurants">{{ t('nav.restaurants') }}</router-link></li>
            <li><router-link to="/culture">{{ t('nav.culture') }}</router-link></li>
            <li><router-link to="/favorites">Izlase</router-link></li>
            <li v-if="auth"><router-link to="/todo">{{ t('nav.todo') }}</router-link></li>
            <li v-else><router-link to="/signin">{{ t('nav.signIn') }}</router-link></li>
            <li v-if="auth">
              <a href="#" @click.prevent="handleSignOut">{{ t('nav.signOut') }}</a>
            </li>
            <li><button class="dark-mode-o" @click="toggleDarkMode"><i :class="isDarkMode ? 'fa fa-sun' : 'fa fa-moon'"></i></button></li>
          </ul>
        </div>
        <div class="lang-switcher">
          <button v-for="l in ['lv','en','et','lt']" :key="l" @click="setLang(l)" :class="{ active: currentLang === l }">{{ l.toUpperCase() }}</button>
        </div>
        <i class="fa fa-bars" @click="showMenu"></i>
      </nav>

      <div class="row">
        <div class="col">
          <h1>{{ t('restaurants.heroTitle') }}</h1>
          <p>{{ t('restaurants.heroSub') }}</p>
        </div>
        <div class="col"></div>
      </div>
    </div>

    <div class="locations">
      <h1>{{ t('restaurants.locationsTitle') }}</h1>
      <p>{{ t('restaurants.locationsSub') }}</p>
      <div id="restaurant-map" class="restaurant-map"></div>
    </div>
    <section class="search-section">
      <input type="text" v-model="searchQuery" :placeholder="t('restaurants.searchPlaceholder')" class="search-bar" />
    </section>

    <section class="places">
      <h1>{{ t('restaurants.placesTitle') }}</h1>
      <div class="sort-options">
        <label for="sortOrder">{{ t('restaurants.sortLabel') }}</label>
        <select id="sortOrder" v-model="sortOrder">
          <option value="asc">{{ t('restaurants.sortAZ') }}</option>
          <option value="desc">{{ t('restaurants.sortZA') }}</option>
        </select>
      </div>

      <div class="rowww" v-for="(row, index) in filteredRestaurantRows" :key="index">
        <div class="places-col1" v-for="place in row" :key="place.title">
          <img :src="place.image" :alt="place.title" />
          <h3>{{ place.title }}</h3>
          <p>{{ place.description }}</p>
          <p>{{ place.address }}</p>
          <p>{{ place.avgprice }}</p>
          <div class="place-actions">
            <a :href="place.link" target="_blank" class="restaurant-link-button">{{ t('restaurants.visitWebsite') }}</a>
            <button
              @click="toggleFavorite({ key: 'restaurant-' + place.id, id: place.id, name: place.title, type: 'restaurant', image: place.image, address: place.address, link: place.link, description: place.description })"
              class="fav-btn"
              :class="{ active: isFavorite('restaurant-' + place.id) }"
            >
              {{ isFavorite('restaurant-' + place.id) ? t('restaurants.favRemove') : t('restaurants.favAdd') }}
              <i :class="isFavorite('restaurant-' + place.id) ? 'fa fa-heart' : 'fa fa-heart-o'"></i>
            </button>
            <label v-if="auth" class="route-checkbox-label">
              <input type="checkbox" :value="place.id" v-model="selectedForRoute" class="route-checkbox" />
              {{ t('restaurants.addToRoute') }}
            </label>
          </div>
        </div>
      </div>
    </section>

    <div v-if="auth" class="route-section">
      <div class="route-card">
        <div class="route-card-icon">
          <i class="fa fa-map-marker"></i>
        </div>
        <div class="route-card-body">
          <h3 class="route-title">Maršruta plānotājs</h3>
          <p class="route-hint" v-if="!selectedForRoute.length">{{ t('restaurants.routeHint') }}</p>
          <p class="route-count" v-else>
            Izvēlēti <strong>{{ selectedForRoute.length }}</strong> restorāni — atzīmē vismaz 2
          </p>
        </div>
        <button class="route-btn" @click="buildRoute" :disabled="selectedForRoute.length < 2">
          <i class="fa fa-road"></i> {{ t('restaurants.routeBtn') }}
        </button>
      </div>
    </div>

    <section class="review-section-wrap">

      <div class="write-review-card" v-if="auth">
        <div class="review-card-header">
          <span class="review-header-icon">★</span>
          <div>
            <h3>Atstājiet savu atsauksmi</h3>
            <p>Dalieties ar savu pieredzi par kādu no restorāniem</p>
          </div>
        </div>

        <div class="review-fields">
          <div class="field-group">
            <label>{{ t('restaurants.reviewSelectLabel') }}</label>
            <select v-model="selectedRestaurantId" class="styled-select">
              <option disabled value="">{{ t('restaurants.rateDefault') }}</option>
              <option v-for="r in filteredRestaurants" :key="r.id" :value="r.id">{{ r.title }}</option>
            </select>
          </div>

          <div class="field-group">
            <label>{{ t('restaurants.reviewRating') }}</label>
            <div class="star-rating">
              <button
                v-for="n in 5" :key="n"
                type="button"
                @click="newRating = n"
                @mouseenter="hoverRating = n"
                @mouseleave="hoverRating = 0"
                :class="{ active: n <= (hoverRating || newRating) }"
                class="star-btn">★</button>
              <span class="rating-label" v-if="newRating">{{ newRating }} / 5</span>
            </div>
          </div>

          <div class="field-group">
            <label>{{ t('restaurants.reviewComment') }}</label>
            <textarea v-model="newComment" :placeholder="t('restaurants.commentPlaceholder')" class="styled-textarea"></textarea>
          </div>

          <p v-if="reviewError" class="review-error">{{ reviewError }}</p>
          <button @click="submitReview" class="submit-review-btn">
            Nosūtīt atsauksmi <span>→</span>
          </button>
        </div>
      </div>

      <div class="reviews-list" v-if="Object.keys(restaurantReviews).length">
        <h3 class="reviews-list-title">{{ t('restaurants.reviewsLabel') }}</h3>
        <div v-for="(reviewList, restaurantId) in restaurantReviews" :key="restaurantId" class="review-group">
          <h4 class="review-place-name">{{ getRestaurantTitleById(restaurantId) }}</h4>
          <div v-for="(review, idx) in reviewList" :key="idx" class="review-item">
            <div class="review-avatar">{{ review.rating }}★</div>
            <div class="review-content">
              <div class="review-stars">
                <span v-for="s in 5" :key="s" :class="{ filled: s <= review.rating }">★</span>
              </div>
              <p class="review-text">{{ review.comment }}</p>
            </div>
          </div>
        </div>
      </div>

    </section>

    <footer>
      <div class="footer-content">
        <p>&copy; 2024 ExploreRiga. All rights reserved.</p>
        <div class="social-links">
          <a href="https://www.facebook.com" target="_blank">Facebook</a> |
          <a href="https://www.twitter.com" target="_blank">Twitter</a> |
          <a href="https://www.instagram.com" target="_blank">Instagram</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import L from 'leaflet'
import { useFavorites } from '@/composables/useFavorites'
import { useReviews } from '@/composables/useReviews'

const searchQuery = ref('')
const sortOrder = ref('asc')
const { restaurantReviews } = useReviews()
const selectedRestaurantId = ref('')
const newComment = ref('')
const newRating = ref(0)
const hoverRating = ref(0)
const reviewError = ref('')
const userId = ref(3)
const selectedForRoute = ref([])
import { isAuthenticated, signOut } from '@/auth'
import { useI18n } from '@/i18n'
import { useRouter } from 'vue-router'
import { useDarkMode } from '@/composables/useDarkMode'

const { t, currentLang, setLang } = useI18n()
const auth = isAuthenticated
const { isFavorite, toggleFavorite } = useFavorites()
const { isDark: isDarkMode, toggleDarkMode } = useDarkMode()

const router = useRouter()

function handleSignOut() {
  signOut()
  router.push('/signin')
}

const submitReview = async () => {
  reviewError.value = ''
  if (!selectedRestaurantId.value && !newComment.value.trim() && !newRating.value) {
    reviewError.value = 'Lūdzu izvēlies restorānu, novērtē ar zvaigznēm un uzraksti komentāru.'
    return
  }
  if (!selectedRestaurantId.value) { reviewError.value = 'Lūdzu izvēlies restorānu.'; return }
  if (!newRating.value) { reviewError.value = 'Lūdzu novērtē ar zvaigznēm.'; return }
  if (!newComment.value.trim()) { reviewError.value = 'Lūdzu uzraksti komentāru.'; return }

  const id = selectedRestaurantId.value
  const comment = newComment.value.trim()
  const rating = newRating.value

  try {
    const res = await fetch('/api/submit_review.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ type: 'restaurant', objectId: id, comment, rating }),
    })
    const json = await res.json()
    if (json.error) { reviewError.value = json.error; return }

    restaurantReviews.value = {
      ...restaurantReviews.value,
      [id]: [...(restaurantReviews.value[id] || []), { comment, rating }],
    }

    newComment.value = ''
    newRating.value = 0
    selectedRestaurantId.value = ''
    reviewError.value = ''
  } catch (error) {
    reviewError.value = 'Neizdevās nosūtīt atsauksmi. Mēģini vēlreiz.'
  }
}

function getRestaurantTitleById(id) {
  const all = restaurantRows.flat()
  const r = all.find(r => r.id === id)
  return r ? r.title : 'Unknown'
}

const restaurantRows = [
  [
    {
      id: "13",
      title: 'Riviera',
      image: 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=640&h=400',
      description: 'Restorāns Riviera radīts pēc Vidusjūras virtuves labākajām tradīcijām. Īpaša vieta ar patīkamu atmosfēru un autentiskiem Vidusjūras ēdieniem.',
      address: 'Dzirnavu iela 31, Rīga, LV-1010',
      avgprice: 'Vidējā cena: 25€',
      link: 'http://rivierarestorans.lv/en/',
      lat: 56.9490, lng: 24.1055,
    },
    {
      id: "14",
      title: 'Max Cekot Kitchen',
      image: 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=640&h=400',
      description: 'Restorāns piedāvā aizraujošu ceļojumu cauri virtuves pasaulei ar radošiem, sezonāliem ēdieniem un izcilu šefpavāra meistarību.',
      address: 'Jelgavas iela 42, Rīga, LV-1004',
      avgprice: 'Vidējā cena: 18€',
      link: 'https://maxcekot.com/en/',
      lat: 56.9335, lng: 24.0748,
    },
    {
      id: "15",
      title: 'Tails',
      image: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=640&h=400',
      description: 'Īpaša vieta, kas iekārtota mierīgās krāsās ar akmens mozaīkām. Eleganta atmosfēra un izsmalcināta virtuve katram īpašam brīdim.',
      address: 'Antonijas iela 6a, Rīga, LV-1010',
      avgprice: 'Vidējā cena: 30€',
      link: 'https://tails.lv/',
      lat: 56.9527, lng: 24.1131,
    },
  ],
  [
    {
      id: "16",
      title: 'The Catch',
      image: 'https://images.unsplash.com/photo-1553621042-f6e147245754?auto=format&fit=crop&w=640&h=400',
      description: 'The Catch apvieno tradīcijas un inovācijas, piedāvājot autentiskas japāņu garšas — robatayaki grilētus ēdienus, sushi un tempuru.',
      address: 'Antonijas iela 12-19, Rīga, LV-1010',
      avgprice: 'Vidējā cena: 35€',
      link: 'https://thecatch.lv/',
      lat: 56.9515, lng: 24.1150,
    },
    {
      id: "17",
      title: 'Le Dome',
      image: 'https://images.unsplash.com/photo-1432139509613-5c4255815697?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mzl8fGZvb2R8ZW58MHx8MHx8fDA%3D',
      description: 'Le Dome piedāvā franču kulinārijas eleganci Rīgas vecpilsētas sirdī ar klasisku interjeru un izsmalcinātu ēdienu karti.',
      address: 'Miesnieku iela 4, Rīga, LV-1050',
      avgprice: 'Vidējā cena: 40€',
      link: 'https://www.domehotel.lv/restorans',
      lat: 56.9480, lng: 24.1055,
    },
    {
      id: "18",
      title: 'Entresol',
      image: 'https://images.unsplash.com/photo-1432139555190-58524dae6a55?q=80&w=1176&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
      description: 'Entresol ir pazīstams ar savu unikālo pieeju nelielu, bet bagātīgu ēdienu pasniegšanā — degustācijas ēdienkarte no labākajiem vietējiem produktiem.',
      address: 'Elizabetes iela 22, Rīga, LV-1010',
      avgprice: 'Vidējā cena: 28€',
      link: 'https://guide.michelin.com/lv/en/riga-region/riga_1991577/restaurant/entresol',
      lat: 56.9524, lng: 24.1162,
    },
  ],
  [
    {
      id: "19",
      title: 'Trīs pavāru restorāns',
      image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=640&h=400',
      description: 'Gastronomiska pieredze, kur ēdiens tiek gatavots viesu acu priekšā. Trīs šefpavāri apvieno Latvijas produktus ar pasaules virtuves tendencēm.',
      address: 'Torņa iela 4, Rīga, LV-1050',
      avgprice: 'Vidējā cena: 50€',
      link: 'https://www.3pavari.lv/',
      lat: 56.9502, lng: 24.1058,
    },
    {
      id: "20",
      title: 'Barents',
      image: 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
      description: 'Barents piedāvā ziemeļu kulinārijas piedzīvojumu ar minimālistisku, taču elegantu interjeru un uzsvaru uz svaigiem jūras produktiem.',
      address: 'Smilšu iela 3, Rīga, LV-1050',
      avgprice: 'Vidējā cena: 32€',
      link: 'https://www.barents.lv/',
      lat: 56.9494, lng: 24.1090,
    },
    {
      id: "21",
      title: 'Lido',
      image: 'https://images.unsplash.com/photo-1504754524776-8f4f37790ca0?auto=format&fit=crop&w=640&h=400',
      description: 'Rīgas iemīļotākais latviskās pašapkalpošanās restorāns ar plašu tradicionālo ēdienu izvēli, piemērots visai ģimenei.',
      address: 'Krasta iela 76, Rīga, LV-1019',
      avgprice: 'Vidējā cena: 15€',
      link: 'https://www.lido.lv/lv/',
      lat: 56.9420, lng: 24.1030,
    },
  ],
  [
    {
      id: "34",
      title: 'Vincents',
      image: 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=640&h=400',
      description: 'Viens no Latvijas prestižākajiem restorāniem ar Michelin ieteikumu. Šefpavārs Mārtiņš Rītiņš piedāvā izsmalcinātu ēdienu karti, kas balstīta uz vietējiem sezonālajiem produktiem.',
      address: 'Elizabetes iela 19, Rīga, LV-1010',
      avgprice: 'Vidējā cena: 60€',
      link: 'https://www.vincents.lv/',
      lat: 56.9524, lng: 24.1162,
    },
    {
      id: "35",
      title: 'Folkklubs Ala pagrabs',
      image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=640&h=400',
      description: 'Autentisks latviešu virtuves restorāns vēsturiskā pagrabā Rīgas vecpilsētā. Piedāvā tradicionālus latviešu ēdienus, vietējos alus šķirnes un dzīvo tautas mūziku.',
      address: 'Peldu iela 19, Rīga, LV-1050',
      avgprice: 'Vidējā cena: 18€',
      link: 'https://www.folkklubs.lv/',
      lat: 56.9459, lng: 24.1050,
    },
    {
      id: "36",
      title: 'Kaļķu vārti',
      image: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=640&h=400',
      description: 'Rīgas vecpilsētas sirds restorāns ar latviskās un starptautiskās virtuves apvienojumu. Gaumīgs interjers un plaša ēdienu izvēle gan pusdienām, gan vakariņām.',
      address: 'Kaļķu iela 11, Rīga, LV-1050',
      avgprice: 'Vidējā cena: 22€',
      link: 'https://kalkuvartis.lv/',
      lat: 56.9477, lng: 24.1082,
    },
  ],
]

function buildRoute() {
  const allRestaurants = restaurantRows.flat()
  const points = selectedForRoute.value
    .map(id => allRestaurants.find(r => r.id === id))
    .filter(Boolean)
  const ordered = sortByNearest(points)
  const waypoints = ordered.map(p => `${p.lat},${p.lng}`).join('/')
  window.open(`https://www.google.com/maps/dir/${waypoints}`, '_blank')
}

function sortByNearest(points) {
  if (!points.length) return []
  const result = []
  const remaining = [...points]
  let current = remaining.splice(0, 1)[0]
  result.push(current)
  while (remaining.length) {
    let nearestIdx = 0
    let nearestDist = Infinity
    remaining.forEach((p, i) => {
      const d = getDistance(current, p)
      if (d < nearestDist) { nearestDist = d; nearestIdx = i }
    })
    current = remaining.splice(nearestIdx, 1)[0]
    result.push(current)
  }
  return result
}

function getDistance(a, b) {
  const dx = a.lat - b.lat
  const dy = a.lng - b.lng
  return dx * dx + dy * dy
}
const filteredRestaurants = computed(() => {
  const query = searchQuery.value.toLowerCase()
  const allRestaurants = restaurantRows.flat()
  let restaurants = allRestaurants.filter(
    (r) =>
      r.title.toLowerCase().includes(query) ||
      r.description.toLowerCase().includes(query)
  )
  if (sortOrder.value === 'asc') {
    restaurants.sort((a, b) => a.title.localeCompare(b.title))
  } else if (sortOrder.value === 'desc') {
    restaurants.sort((a, b) => b.title.localeCompare(a.title))
  }
  return restaurants
})


const filteredRestaurantRows = computed(() => {
  const chunkSize = 3
  const result = []
  for (let i = 0; i < filteredRestaurants.value.length; i += chunkSize) {
    result.push(filteredRestaurants.value.slice(i, i + chunkSize))
  }
  return result
})

const navVisible = ref(false)
const dropdownVisible = ref(false)

const showMenu = () => { navVisible.value = true }
const hideMenu = () => { navVisible.value = false }

const toggleNav = (show) => {
  navVisible.value = show
}

const toggleDropdown = (event) => {
  event.preventDefault()
  dropdownVisible.value = !dropdownVisible.value
}


onMounted(() => {
  fetch('/api/get_reviews.php?type=restaurant')
    .then(r => r.json())
    .then(data => { restaurantReviews.value = data })
    .catch(() => {})

  window.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown')) {
      dropdownVisible.value = false
    }
  })

  const map = L.map('restaurant-map').setView([56.949, 24.105], 13)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map)

  restaurantRows.flat().forEach(r => {
    if (!r.lat || !r.lng) return
    L.marker([r.lat, r.lng], {
      icon: L.divIcon({
        className: '',
        html: `<div style="background:#991499;color:#fff;border-radius:50%;width:38px;height:38px;display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 2px 8px rgba(0,0,0,0.35);border:2px solid #fff"><i class="fa fa-utensils"></i></div>`,
        iconSize: [38, 38],
        iconAnchor: [19, 19],
        popupAnchor: [0, -22]
      })
    })
      .addTo(map)
      .bindPopup(`<b>${r.title}</b><br><small>${r.address}</small><br><a href="${r.link}" target="_blank" style="color:#991499">Apmeklēt →</a>`)
  })
})


</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  font-family: 'Inter', sans-serif;
  box-sizing: border-box;
}

h1, h2, h3, h4 { font-family: 'Playfair Display', serif; }

/* ── Dark Mode ── */
.dark-mode { background-color: #121212; color: #fff; }
.dark-mode .places-col1 { background: #1e1e1e; }
.dark-mode .places-col1 h3, .dark-mode .places-col1 p { color: #ddd; }
.dark-mode .locations { background: #181818; }
.dark-mode .locations h1 { color: #f0e6ff; }
.dark-mode .locations > p { color: #aaa; }
.dark-mode .places h1 { color: #fff; }
.dark-mode .sort-options label { color: #ccc; }
.dark-mode .sort-options select { background: #1e1e1e; color: #eee; border-color: #6b0487; }
.dark-mode .route-card { background: #1e1e1e; border-left-color: #7b0d9b; box-shadow: 0 4px 24px rgba(0,0,0,0.4); }
.dark-mode .route-title { color: #f0e6ff; }
.dark-mode .route-hint { color: #777; }
.dark-mode .route-count { color: #c084e0; }
.dark-mode footer { background: linear-gradient(135deg, #0d0d0d, #1a001a); }

/* ── Hero ── */
.res-container {
  width: 100%;
  height: 100vh;
  background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)),
    url(https://tendences.lv/wp-content/uploads/2023/10/entresol-restorans-1068x712.jpg);
  background-position: center;
  background-size: cover;
  padding: 0 6%;
}

/* ── Navigation ── */
nav {
  display: flex;
  padding: 1.8% 0;
  align-items: center;
  gap: 12px;
}

.brand {
  font-family: 'Playfair Display', serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: #fff;
  text-decoration: none;
  flex-shrink: 0;
}

.nav-links { flex: 1; text-align: right; }

.nav-links ul li {
  list-style: none;
  display: inline-block;
  padding: 8px 12px;
  position: relative;
}

.nav-links ul li a {
  color: #fff;
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  letter-spacing: 0.8px;
  text-transform: uppercase;
}

.nav-links ul li::after {
  content: '';
  width: 0%;
  height: 2px;
  background: #c847d8;
  display: block;
  margin: auto;
  transition: 0.4s;
}

.nav-links ul li:hover::after { width: 100%; }

nav .fa-bars { display: none; }
.nav-links .fa-times { display: none; }

.dark-mode-o {
  background: rgba(255,255,255,0.12);
  color: white;
  padding: 7px 14px;
  font-size: 14px;
  cursor: pointer;
  border: 1px solid rgba(255,255,255,0.25);
  border-radius: 8px;
  transition: background 0.3s;
  flex-shrink: 0;
}

.dark-mode-o:hover { background: rgba(255,255,255,0.22); }

/* ── Hero text ── */
.row {
  display: flex;
  height: calc(100% - 80px);
  align-items: center;
}

.col { flex-basis: 50%; padding: 20px; }

h1 {
  color: #fff;
  font-size: 76px;
  margin-bottom: 20px;
  line-height: 1.1;
}

.col p {
  color: rgba(255,255,255,0.88);
  font-size: 18px;
  line-height: 1.65;
  max-width: 520px;
}

/* ── Locations / map ── */
.locations {
  padding: 60px 5%;
  text-align: center;
  background: #faf7fc;
}

.locations h1 {
  font-size: 40px;
  color: #1a1a2e;
  margin-bottom: 12px;
}

.locations > p {
  color: #555;
  font-size: 16px;
  max-width: 620px;
  margin: 0 auto 28px;
  line-height: 1.6;
  text-align: center;
}

.restaurant-map {
  width: 100%;
  max-width: 1100px;
  height: 420px;
  display: block;
  margin: auto;
  border-radius: 12px;
  box-shadow: 0 6px 28px rgba(0,0,0,0.1);
  z-index: 0;
}

/* ── Search ── */
.search-section {
  text-align: center;
  margin: 36px 0 0;
}

.search-bar {
  padding: 13px 22px;
  width: 80%;
  max-width: 520px;
  font-size: 15px;
  border: 2px solid #991499;
  border-radius: 50px;
  outline: none;
  transition: box-shadow 0.3s;
}

.search-bar:focus {
  box-shadow: 0 0 0 4px rgba(153,20,153,0.15);
}

/* ── Favorite button ── */
.fav-btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 7px;
  padding: 9px 18px;
  border: 2px solid #991499; background: none; color: #991499;
  border-radius: 50px; font-size: 13px; font-weight: 600;
  cursor: pointer; transition: all 0.25s;
  white-space: nowrap; flex-shrink: 0; width: fit-content;
}
.fav-btn:hover, .fav-btn.active { background: #991499; color: #fff; }
.fav-btn .fa-heart, .fav-btn .fa-heart-o { font-size: 14px; }

/* ── Places section ── */
.places {
  max-width: 1200px;
  margin: 0 auto;
  padding: 50px 24px 60px;
  text-align: center;
}

.places h1 {
  font-size: 40px;
  color: #1a1a2e;
  margin-bottom: 12px;
}

.sort-options {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 32px;
}

.sort-options label { font-size: 14px; color: #555; }

.sort-options select {
  padding: 9px 16px;
  border-radius: 50px;
  border: 1px solid #991499;
  font-size: 14px;
  cursor: pointer;
  outline: none;
}

/* ── Restaurant card grid ── */
.rowww {
  display: flex;
  justify-content: flex-start;
  gap: 24px;
  flex-wrap: wrap;
  margin-bottom: 24px;
}

.places-col1 {
  flex-basis: calc(33.333% - 16px);
  background: #fff;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 18px rgba(0,0,0,0.08);
  transition: transform 0.35s, box-shadow 0.35s;
  display: flex;
  flex-direction: column;
}

.places-col1:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 36px rgba(153,20,153,0.14);
}

.places-col1 img {
  width: 100%;
  height: 190px;
  object-fit: cover;
  display: block;
  transition: transform 0.4s;
}

.places-col1:hover img { transform: scale(1.04); }

.places-col1 h3 {
  font-size: 18px;
  color: #1a1a2e;
  margin: 16px 16px 6px;
  text-align: left;
}

.places-col1 > p {
  font-size: 13px;
  color: #666;
  line-height: 1.5;
  margin: 0 16px 6px;
  text-align: left;
}

.places-col1 > div {
  margin-top: auto;
}

/* ── Route section ── */
.route-section {
  padding: 0 5% 48px;
}

.route-card {
  display: flex;
  align-items: center;
  gap: 20px;
  background: #fff;
  border-radius: 16px;
  padding: 22px 28px;
  box-shadow: 0 4px 24px rgba(153, 20, 153, 0.10);
  border-left: 5px solid #991499;
  max-width: 900px;
  margin: 0 auto;
}

.route-card-icon {
  width: 54px;
  height: 54px;
  border-radius: 14px;
  background: linear-gradient(135deg, #7b0d9b, #c847d8);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  flex-shrink: 0;
}

.route-card-body { flex: 1; }

.route-title { font-size: 18px; color: #1a1a2e; margin: 0 0 4px; }
.route-hint { color: #888; font-size: 14px; margin: 0; }
.route-count { color: #991499; font-size: 14px; margin: 0; }

.route-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #7b0d9b, #991499);
  color: #fff;
  border: none;
  padding: 13px 26px;
  font-size: 14px;
  font-weight: 700;
  border-radius: 50px;
  cursor: pointer;
  letter-spacing: 0.3px;
  transition: opacity 0.25s, transform 0.2s;
  white-space: nowrap;
  flex-shrink: 0;
}

.route-btn:hover:not(:disabled) { opacity: 0.88; transform: translateY(-2px); }
.route-btn:disabled { opacity: 0.4; cursor: not-allowed; }

.place-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  margin: 16px 16px 12px;
}

.route-checkbox-label {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #991499;
  font-weight: 600;
  cursor: pointer;
  user-select: none;
}

.route-checkbox { accent-color: #991499; width: 16px; height: 16px; cursor: pointer; }

/* ── Visit button ── */
.restaurant-link-button {
  display: inline-block;
  padding: 9px 20px;
  background: #991499;
  color: #fff;
  border-radius: 50px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.3s;
}

.restaurant-link-button:hover { background: #6b0487; }

.lang-switcher { display: flex; gap: 4px; }
.lang-switcher button {
  background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);
  border: 1px solid rgba(255,255,255,0.2); padding: 5px 9px;
  font-size: 11px; font-weight: 600; cursor: pointer; border-radius: 6px; transition: all 0.2s;
}
.lang-switcher button:hover { background: rgba(255,255,255,0.2); color: #fff; }
.lang-switcher button.active { background: #991499; color: #fff; border-color: #991499; }

/* ── Review section ── */
.review-section-wrap {
  max-width: 860px;
  margin: 0 auto 60px;
  padding: 0 20px;
}

.write-review-card {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 6px 32px rgba(153, 20, 153, 0.10);
  overflow: hidden;
  margin-bottom: 36px;
}

.review-card-header {
  display: flex;
  align-items: center;
  gap: 18px;
  background: linear-gradient(135deg, #7b0d9b, #c847d8);
  padding: 28px 32px;
  color: #fff;
}

.review-header-icon { font-size: 2.4rem; line-height: 1; flex-shrink: 0; }

.review-card-header h3 { font-size: 22px; margin: 0 0 4px; color: #fff; }
.review-card-header p { font-size: 14px; margin: 0; color: rgba(255,255,255,0.82); }

.review-fields {
  padding: 28px 32px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.field-group { display: flex; flex-direction: column; gap: 7px; }

.field-group label {
  font-size: 13px;
  font-weight: 700;
  color: #555;
  text-transform: uppercase;
  letter-spacing: 0.6px;
}

.styled-select {
  padding: 12px 16px;
  font-size: 15px;
  border: 2px solid #e5d6f5;
  border-radius: 10px;
  font-family: 'Inter', sans-serif;
  outline: none;
  transition: border-color 0.2s;
  background: #faf7fc;
}
.styled-select:focus { border-color: #991499; }

.star-rating { display: flex; align-items: center; gap: 6px; }

.star-btn {
  background: none;
  border: none;
  font-size: 2rem;
  color: #ddd;
  cursor: pointer;
  transition: color 0.15s, transform 0.15s;
  padding: 0;
  line-height: 1;
}
.star-btn:hover, .star-btn.active { color: #f5b800; transform: scale(1.2); }

.rating-label { font-size: 14px; font-weight: 700; color: #991499; margin-left: 8px; }

.styled-textarea {
  padding: 12px 16px;
  font-size: 15px;
  border: 2px solid #e5d6f5;
  border-radius: 10px;
  font-family: 'Inter', sans-serif;
  min-height: 110px;
  resize: vertical;
  outline: none;
  transition: border-color 0.2s;
  background: #faf7fc;
}
.styled-textarea:focus { border-color: #991499; }

.submit-review-btn {
  align-self: flex-start;
  background: linear-gradient(135deg, #7b0d9b, #991499);
  color: #fff;
  border: none;
  padding: 13px 30px;
  border-radius: 50px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: opacity 0.25s, transform 0.2s;
  letter-spacing: 0.3px;
}
.submit-review-btn:hover { opacity: 0.88; transform: translateY(-2px); }

.review-error {
  color: #c0392b;
  background: #fdecea;
  border: 1px solid #f5c6c2;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 14px;
  margin-bottom: 10px;
}

.reviews-list {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.07);
  padding: 28px 32px;
}

.reviews-list-title {
  font-size: 22px;
  color: #1a1a2e;
  margin-bottom: 24px;
  padding-bottom: 14px;
  border-bottom: 2px solid #f0e6ff;
}

.review-place-name { font-size: 16px; color: #991499; font-weight: 700; margin: 18px 0 10px; }

.review-item {
  display: flex;
  gap: 14px;
  align-items: flex-start;
  background: #faf7fc;
  border-radius: 12px;
  padding: 14px 16px;
  margin-bottom: 10px;
}

.review-avatar {
  flex-shrink: 0;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: linear-gradient(135deg, #7b0d9b, #c847d8);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
}

.review-content { flex: 1; }

.review-stars { display: flex; gap: 2px; margin-bottom: 6px; }
.review-stars span { color: #ddd; font-size: 16px; }
.review-stars span.filled { color: #f5b800; }

.review-text { font-size: 14px; color: #555; margin: 0; line-height: 1.6; }

/* ── Dark mode review ── */
.dark-mode .write-review-card { background: #1e1e1e; }
.dark-mode .reviews-list { background: #1e1e1e; }
.dark-mode .reviews-list-title { color: #f0e6ff; border-bottom-color: #3a1a4a; }
.dark-mode .review-place-name { color: #c084e0; }
.dark-mode .review-item { background: #2a2a2a; }
.dark-mode .review-text { color: #bbb; }
.dark-mode .field-group label { color: #aaa; }
.dark-mode .styled-select { background: #2a2a2a; color: #eee; border-color: #4a2a5a; }
.dark-mode .styled-textarea { background: #2a2a2a; color: #eee; border-color: #4a2a5a; }

/* ── Footer ── */
footer {
  background: linear-gradient(135deg, #1a001a 0%, #2d003a 100%);
  color: #fff;
  padding: 30px 5%;
  text-align: center;
}

.footer-content { max-width: 1200px; margin: 0 auto; }
.footer-content p { font-size: 13px; color: rgba(255,255,255,0.5); margin: 0 0 10px; }

.social-links a {
  color: rgba(255,255,255,0.7);
  text-decoration: none;
  margin: 0 10px;
  font-size: 14px;
  transition: color 0.2s;
}

.social-links a:hover { color: #e8a0e8; }

/* ── Dropdown ── */
.dropbtn {
  color: #fff;
  font-size: 13px;
  font-weight: 500;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px 12px;
  text-decoration: none;
}

.dropdown { position: relative; display: inline-block; }

.dropdown-content {
  display: none;
  position: absolute;
  background: #3b053d;
  min-width: 160px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  z-index: 10;
  border-radius: 8px;
  overflow: hidden;
  opacity: 0;
  transform: translateY(-8px);
  transition: opacity 0.25s, transform 0.25s;
}

.dropdown-content a {
  color: #fff;
  padding: 11px 16px;
  text-decoration: none;
  display: block;
  font-size: 13px;
}

.dropdown-content a:hover { background: rgba(255,255,255,0.1); }

.dropdown-content.show { display: block; opacity: 1; transform: translateY(0); }

/* ── Mobile ── */
@media (max-width: 700px) {
  h1 { font-size: 44px; }

  .nav-links {
    position: fixed;
    background: #3b053d;
    height: 100vh;
    width: 240px;
    top: 0;
    right: -240px;
    text-align: left;
    z-index: 1000;
    transition: 0.4s;
  }

  .nav-links ul li { display: block; }
  .nav-links ul { padding: 30px 20px; }

  nav .fa-bars {
    display: block;
    color: #fff;
    font-size: 22px;
    cursor: pointer;
  }

  .places-col1 { flex-basis: 100%; }
  .locations iframe { height: 280px; }
}
</style>
