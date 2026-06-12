<template>
  <div :class="{ 'dark-mode': isDark }">
    <div class="plc-container">
      <nav>
        <a href="/" class="brand">ExploreRiga</a>
        <div class="nav-links" id="navLinks">
          <i class="fa fa-times" @click="hideMenu"></i>
          <ul>
            <li class="dropdown">
              <a href="#" class="dropbtn" @click="toggleDropdown($event)">{{ t('nav.aboutUs') }}</a>
              <div id="myDropdown" class="dropdown-content" :class="{ show: dropdownOpen }">
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
            <li v-if="isAuth"><router-link to="/todo">{{ t('nav.todo') }}</router-link></li>
            <li v-else><router-link to="/signin">{{ t('nav.signIn') }}</router-link></li>
            <li v-if="isAuth">
              <a href="#" @click.prevent="handleSignOut">{{ t('nav.signOut') }}</a>
            </li>
            <li><button class="dark-mode-o" @click="toggleDarkMode"><i :class="isDark ? 'fa fa-sun' : 'fa fa-moon'"></i></button></li>
          </ul>
        </div>
        <div class="lang-switcher">
          <button v-for="l in ['lv', 'en', 'et', 'lt']" :key="l" @click="setLang(l)"
            :class="{ active: currentLang === l }">{{ l.toUpperCase() }}</button>
        </div>
        <i class="fa fa-bars" @click="showMenu"></i>
      </nav>

      <div class="row">
        <div class="col">
          <h1>{{ t('culture.heroTitle') }}</h1>
          <p>{{ t('culture.heroSub') }}</p>
        </div>
        <div class="col"></div>
      </div>
    </div>

    <div class="search-bar">
      <input type="text" v-model="searchQuery" :placeholder="t('culture.searchPlaceholder')" class="search-input" />
    </div>

    <div class="sort-dropdown">
      <label for="sortOptions">{{ t('culture.sortLabel') }}</label>
      <select v-model="sortOption" @change="applySort" id="sortOptions">
        <option value="alphabetical">{{ t('culture.sortAZ') }}</option>
        <option value="reverse-alphabetical">{{ t('culture.sortZA') }}</option>
      </select>
    </div>
    <div class="map-section">
      <div id="culture-map" class="culture-map"></div>
    </div>

    <div class="places">
      <div class="flex-container" v-for="place in filteredPlaces" :key="place.id">
        <div class="places-col">
          <img :src="place.image" :alt="place.name" />
          <a :href="place.link" class="visit-button" target="_blank">{{ t('culture.visitWebsite') }}</a>
        </div>
        <div class="text-box">
          <h3>{{ place.name }}</h3>
          <p>{{ place.description }}</p>
          <p>{{ place.address }}</p>
          <p>{{ place.facts }}</p>
          <div class="place-actions">
            <label v-if="isAuth" class="route-checkbox-label">
              <input type="checkbox" :value="place.id" v-model="selectedForRoute" class="route-checkbox" />
              {{ t('culture.addToRoute') }}
            </label>
            <button
              @click="toggleFavorite({ key: 'culture-' + place.id, id: place.id, name: place.name, type: 'culture', image: place.image, address: place.address, link: place.link, description: place.description })"
              class="fav-btn"
              :class="{ active: isFavorite('culture-' + place.id) }"
            >
              {{ isFavorite('culture-' + place.id) ? t('culture.favRemove') : t('culture.favAdd') }}
              <i :class="isFavorite('culture-' + place.id) ? 'fa fa-heart' : 'fa fa-heart-o'"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isAuth" class="route-section">
      <div class="route-card">
        <div class="route-card-icon">
          <i class="fa fa-map-marker"></i>
        </div>
        <div class="route-card-body">
          <h3 class="route-title">Maršruta plānotājs</h3>
          <p class="route-hint" v-if="!selectedForRoute.length">{{ t('culture.routeHint') }}</p>
          <p class="route-count" v-else>
            Izvēlētas <strong>{{ selectedForRoute.length }}</strong> vietas — atzīmē vismaz 2
          </p>
        </div>
        <button class="route-btn" @click="buildRoute" :disabled="selectedForRoute.length < 2">
          <i class="fa fa-road"></i> {{ t('culture.routeBtn') }}
        </button>
      </div>
    </div>

    <section class="review-section-wrap">

      <div class="write-review-card" v-if="isAuth">
        <div class="review-card-header">
          <span class="review-header-icon">★</span>
          <div>
            <h3>Atstājiet savu atsauksmi</h3>
            <p>Dalieties ar savu pieredzi par kādu no kultūras vietām</p>
          </div>
        </div>

        <div class="review-fields">
          <div class="field-group">
            <label>{{ t('culture.selectLabel') }}</label>
            <select v-model="selectedPlaceId" class="styled-select">
              <option disabled value="">{{ t('culture.selectDefault') }}</option>
              <option v-for="place in filteredPlaces" :key="place.id" :value="place.id">{{ place.name }}</option>
            </select>
          </div>

          <div class="field-group">
            <label>{{ t('culture.reviewRating') }}</label>
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
            <label>{{ t('culture.reviewComment') }}</label>
            <textarea v-model="newComment" :placeholder="t('culture.commentPlaceholder')" class="styled-textarea"></textarea>
          </div>

          <p v-if="reviewError" class="review-error">{{ reviewError }}</p>
          <button @click="submitPlaceReview" class="submit-review-btn">
            Nosūtīt atsauksmi <span>→</span>
          </button>
        </div>
      </div>

      <div class="reviews-list" v-if="Object.keys(placeReviews).length">
        <h3 class="reviews-list-title">{{ t('culture.allReviews') }}</h3>
        <div v-for="(reviewList, placeId) in placeReviews" :key="placeId" class="review-group">
          <h4 class="review-place-name">{{ getPlaceTitleById(placeId) }}</h4>
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
        <p>&copy; 2024 ExploreRiga. Visas tiesības aizsargātas.</p>
        <div class="social-links">
          <a href="https://www.facebook.com" target="_blank">Facebook</a> |
          <a href="https://www.twitter.com" target="_blank">Twitter</a> |
          <a href="https://www.instagram.com" target="_blank">Instagram</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import L from 'leaflet'
import { useFavorites } from '@/composables/useFavorites'
import { isAuthenticated, signOut } from '@/auth'
import { useI18n } from '@/i18n'
import { useRouter } from 'vue-router'
import { useDarkMode } from '@/composables/useDarkMode'
import { useReviews } from '@/composables/useReviews'

export default {
  setup() {
    const { t, currentLang, setLang } = useI18n()
    const { isFavorite, toggleFavorite } = useFavorites()
    const { isDark, toggleDarkMode } = useDarkMode()
    const { placeReviews } = useReviews()
    return { t, currentLang, setLang, isFavorite, toggleFavorite, isDark, toggleDarkMode, placeReviews }
  },

  data() {
    return {
      hoverRating: 0,
      searchQuery: '',
      dropdownOpen: false,
      sortOption: 'alphabetical',
      selectedPlaceId: '',
      newComment: '',
      newRating: 0,
      reviewError: '',
      selectedForRoute: [],

      places: [
        {
          id: '22',
          name: 'Latvijas Nacionālais teātris',
          description: 'Latvijas izcilākais dramatiskais teātris, dibināts 1919. gadā. Atrodas neogotikas stilā celtā ēkā Kronvalda bulvārī un ir viens no Rīgas kultūras simboliem.',
          address: 'Kronvalda bulvāris 3, Rīga',
          facts: 'Šeit 1918. gada 18. novembrī tika pasludināta Latvijas Republika.',
          image: '/culture/nationalteatris.jpg',
          link: 'https://teatris.lv/',
          lat: 56.9505,
          lng: 24.1080,
        },
        {
          id: '23',
          name: 'Latvijas Nacionālā Opera un Balets',
          description: 'Viena no Eiropas prestižākajām operas namām, dibināta 1919. gadā. Neoklasicisma ēka Aspazijas bulvārī ir viena no skaistākajām operas ēkām Baltijā.',
          address: 'Aspazijas bulvāris 3, Rīga',
          facts: 'Katru gadu sniedz vairāk nekā 200 izrādes ar ap 200 000 apmeklētāju.',
          image: '/culture/opera.jpg',
          link: 'https://www.opera.lv/lv/',
          lat: 56.9493,
          lng: 24.1143,
        },
        {
          id: '24',
          name: 'Latvijas Nacionālā bibliotēka',
          description: 'Modernā "Gaismas pils" ēka celta 2014. gadā pēc arhitekta Gunāra Birkerta projekta. Glabā vairāk nekā 5 miljonus grāmatu un dokumentu.',
          address: 'Mūkusalas iela 3, Rīga',
          facts: 'Ēkas forma simbolizē latviešu tautas dziesmu "Saule, Māra, Laima".',
          image: '/culture/biblioteka.jpg',
          link: 'https://lnb.gov.lv/',
          lat: 56.9411,
          lng: 24.0962,
        },
        {
          id: '25',
          name: 'Dailes teātris',
          description: 'Lielākais dramatiskais teātris Baltijā pēc skatītāju skaita, dibināts 1920. gadā. Šobrīd mītne modernisma ēkā Brīvības ielā ar trim skatuvēm.',
          address: 'Brīvības iela 75, Rīga',
          facts: 'Galvenā zāle var uzņemt vairāk nekā 1000 skatītāju vienlaicīgi.',
          image: '/culture/dailes.jpg',
          link: 'https://www.dailes.lv/',
          lat: 56.9595,
          lng: 24.1269,
        },
        {
          id: '26',
          name: 'Latvijas Leļļu teātris',
          description: 'Viens no vecākajiem leļļu teātriem Eiropā, dibināts 1944. gadā. Piedāvā izrādes gan bērniem, gan pieaugušajiem ar dažādiem leļļu stiliem.',
          address: 'Barona iela 16/18, Rīga',
          facts: 'Katru gadu teātri apmeklē ap 100 000 skatītāju no Latvijas un ārzemēm.',
          image: '/culture/lellu.jpg',
          link: 'https://www.lelluteatris.lv/lv/',
          lat: 56.9473,
          lng: 24.1185,
        },
        {
          id: '27',
          name: 'Rīgas Krievu teātris',
          description: 'Viens no vecākajiem krievu profesionālajiem teātriem ārpus Krievijas, dibināts 1883. gadā. Plašs klasisko un mūsdienu lugu repertuārs.',
          address: 'Kalķu iela 16, Rīga',
          facts: 'Teātris regulāri uzaicina viesrežisorus no Eiropas un piedalās starptautiskos festivālos.',
          image: '/culture/krievu.jpg',
          link: 'https://www.mct.lv/',
          lat: 56.9490,
          lng: 24.1095,
        },
        {
          id: '28',
          name: 'Jaunais Rīgas teātris',
          description: 'Eksperimentāls teātris, dibināts 1992. gadā. Pazīstams ar avangarda izrādēm un starptautiskiem projektiem. Mākslinieciskais vadītājs — Alvis Hermanis.',
          address: 'Lāčplēša iela 25, Rīga',
          facts: 'Alvis Hermanis ir strādājis Vīnes, Berlīnes un Parīzes vadošajos teātros.',
          image: '/culture/jaunaisriga.jpg',
          link: 'https://www.jrt.lv/',
          lat: 56.9600,
          lng: 24.1150,
        },
        {
          id: '29',
          name: 'Rīgas Cirks',
          description: 'Vienīgais pastāvīgais cirks Baltijā, dibināts 1888. gadā. Vēsturiskā apaļā ēka Merķeļa ielā ir izcils arhitektūras piemineklis.',
          address: 'Merķeļa iela 4, Rīga',
          facts: 'Ēka ir viena no retajām saglabājušajām vēsturiskajām cirka ēkām visā Eiropā.',
          image: '/culture/cirks.jpg',
          link: 'https://www.cirks.lv/',
          lat: 56.9450,
          lng: 24.1100,
        },
        {
          id: '30',
          name: 'Latvijas Filharmonija',
          description: 'Latvijas vadošā simfoniskās mūzikas institūcija, dibināta 1926. gadā. Koncertzāle atrodas vēsturiskā ēkā Vecpilsētā ar izcilu akustiku.',
          address: 'Amatu iela 6, Rīga',
          facts: 'Šeit regulāri uzstājas Latvijas Nacionālais simfoniskais orķestris un Latvijas Radio koris.',
          image: '/culture/filharmonija.jpg',
          link: 'https://www.lnso.lv/',
          lat: 56.9490,
          lng: 24.1050,
        },
        {
          id: '31',
          name: 'Ģertrūdes ielas teātris',
          description: 'Mūsdienīgs neatkarīgs Rīgas teātris ar daudzveidīgu repertuāru — no klasikas līdz eksperimentālajiem iestudējumiem.',
          address: 'Ģertrūdes iela 101, Rīga, LV-1009',
          facts: 'Teātris ir ieguvis vairākas Latvijas teātra gada balvas par inovatīviem iestudējumiem.',
          image: 'https://images.unsplash.com/photo-1539964604210-db87088e0c2c?auto=format&fit=crop&w=640&h=400',
          link: 'https://git.lv/',
          lat: 56.9576,
          lng: 24.1321,
        },
        {
          id: '32',
          name: 'Dirty Deal Teatro',
          description: 'Neatkarīgs mūsdienu mākslas teātris, kas specializējas eksperimentālās un vizuālās teātra formās.',
          address: 'Hanzas iela 28k1, Rīga, LV-1046',
          facts: 'Viens no Latvijas aktīvākajiem neatkarīgajiem teātriem, regulāri piedaloties starptautiskos festivālos.',
          image: 'https://images.unsplash.com/photo-1565798846807-2af22c843402?auto=format&fit=crop&w=640&h=400',
          link: 'https://www.ddt.lv/',
          lat: 56.9536,
          lng: 24.0900,
        },
        {
          id: '33',
          name: 'Latvijas Jaunatnes teātris',
          description: 'Teātris, kas veltīts jauno skatītāju un jauniešu radošajai attīstībai ar mūsdienīgiem iestudējumiem.',
          address: 'Dzirnavu iela 84/86, Rīga, LV-1011',
          facts: 'Katru sezonu piedāvā vairāk nekā 10 jauniestudējumu, aptverot gan klasiku, gan mūsdienu dramaturģiju.',
          image: 'https://images.unsplash.com/photo-1722321974501-059dff03e970?auto=format&fit=crop&w=640&h=400',
          link: 'https://www.facebook.com/jaunatnesteatris/',
          lat: 56.9520,
          lng: 24.1186,
        },
      ],
    }
  },

  computed: {
    isAuth() {
      return isAuthenticated.value
    },

    filteredPlaces() {
      let filtered = this.places.filter(p =>
        p.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      )

      if (this.sortOption === 'alphabetical') {
        return filtered.sort((a, b) => a.name.localeCompare(b.name))
      }

      if (this.sortOption === 'reverse-alphabetical') {
        return filtered.sort((a, b) => b.name.localeCompare(a.name))
      }

      return filtered
    },
  },

  mounted() {
    fetch('/api/get_reviews.php?type=culture')
      .then(r => r.json())
      .then(data => { this.placeReviews = data })
      .catch(() => {})

    const map = L.map('culture-map').setView([56.946, 24.105], 13)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map)

    this.places.forEach(p => {
      if (!p.lat || !p.lng) return
      L.marker([p.lat, p.lng], {
        icon: L.divIcon({
          className: '',
          html: `<div style="background:#991499;color:#fff;border-radius:50%;width:38px;height:38px;display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 2px 8px rgba(0,0,0,0.35);border:2px solid #fff"><i class="fa fa-masks-theater"></i></div>`,
          iconSize: [38, 38],
          iconAnchor: [19, 19],
          popupAnchor: [0, -22]
        })
      })
        .addTo(map)
        .bindPopup(`<b>${p.name}</b><br><small>${p.address}</small><br><a href="${p.link}" target="_blank" style="color:#991499">Apmeklēt →</a>`)
    })
  },

  methods: {
    showMenu() {
      const navLinks = document.getElementById('navLinks')
      if (navLinks) navLinks.style.right = '0'
    },

    hideMenu() {
      const navLinks = document.getElementById('navLinks')
      if (navLinks) navLinks.style.right = '-240px'
    },

    toggleDropdown(event) {
      event.preventDefault()
      this.dropdownOpen = !this.dropdownOpen
    },

    handleSignOut() {
      signOut()
      const router = useRouter()
      router.push('/')
    },

    buildRoute() {
      const points = this.selectedForRoute
        .map(id => this.places.find(p => p.id === id))
        .filter(Boolean)
      const ordered = this.sortByNearest(points)
      const waypoints = ordered.map(p => `${p.lat},${p.lng}`).join('/')
      window.open(`https://www.google.com/maps/dir/${waypoints}`, '_blank')
    },

    sortByNearest(points) {
      if (!points.length) return []
      const result = []
      const remaining = [...points]
      let current = remaining.splice(0, 1)[0]
      result.push(current)
      while (remaining.length) {
        let nearestIdx = 0
        let nearestDist = Infinity
        remaining.forEach((p, i) => {
          const d = this.getDistance(current, p)
          if (d < nearestDist) { nearestDist = d; nearestIdx = i }
        })
        current = remaining.splice(nearestIdx, 1)[0]
        result.push(current)
      }
      return result
    },

    getDistance(a, b) {
      const dx = a.lat - b.lat
      const dy = a.lng - b.lng
      return dx * dx + dy * dy
    },

    submitPlaceReview() {
      this.reviewError = ''
      if (!this.selectedPlaceId && !this.newComment.trim() && !this.newRating) {
        this.reviewError = 'Lūdzu izvēlies vietu, novērtē ar zvaigznēm un uzraksti komentāru.'
        return
      }
      if (!this.selectedPlaceId) { this.reviewError = 'Lūdzu izvēlies vietu.'; return }
      if (!this.newRating) { this.reviewError = 'Lūdzu novērtē ar zvaigznēm.'; return }
      if (!this.newComment.trim()) { this.reviewError = 'Lūdzu uzraksti komentāru.'; return }

      const id = this.selectedPlaceId
      const comment = this.newComment.trim()
      const rating = parseInt(this.newRating)

      fetch('/api/submit_review.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: 'culture', objectId: id, comment, rating }),
      })
        .then(r => r.json())
        .then(() => {
          this.placeReviews = {
            ...this.placeReviews,
            [id]: [...(this.placeReviews[id] || []), { comment, rating }],
          }
          this.newComment = ''
          this.newRating = ''
          this.selectedPlaceId = ''
          this.reviewError = ''
        })
        .catch(() => { this.reviewError = 'Neizdevās nosūtīt atsauksmi. Mēģini vēlreiz.' })
    },

    getPlaceTitleById(id) {
      const place = this.places.find(p => p.id === id)
      return place ? place.name : 'Unknown'
    },
  },
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  font-family: 'Inter', sans-serif;
  box-sizing: border-box;
}

h1,
h2,
h3,
h4 {
  font-family: 'Playfair Display', serif;
}

/* ── Hero ── */
.plc-container {
  width: 100%;
  height: 100vh;
  background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)),
    url("https://tse4.mm.bing.net/th/id/OIP.KNf0dD3--emDErBb7jNcrgHaFN?r=0&rs=1&pid=ImgDetMain");
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

.nav-links {
  flex: 1;
  text-align: right;
}

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

.nav-links ul li:hover::after {
  width: 100%;
}

nav .fa-bars {
  display: none;
}
.nav-links .fa-times { display: none; }

.dark-mode-o {
  background: rgba(255, 255, 255, 0.12);
  color: white;
  padding: 7px 14px;
  font-size: 14px;
  cursor: pointer;
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 8px;
  transition: background 0.3s;
}

.dark-mode-o:hover {
  background: rgba(255, 255, 255, 0.22);
}

/* ── Hero text ── */
.row {
  display: flex;
  height: calc(100% - 80px);
  align-items: center;
}

.col {
  flex-basis: 50%;
  padding: 20px;
}

h1 {
  color: #fff;
  font-size: 76px;
  margin-bottom: 20px;
  line-height: 1.1;
}

p {
  color: rgba(255, 255, 255, 0.88);
  font-size: 18px;
  line-height: 1.65;
  max-width: 560px;
}

/* ── Search & Sort ── */
.search-bar {
  text-align: center;
  margin: 40px 0 20px;
}

.search-input {
  padding: 13px 22px;
  width: 80%;
  max-width: 520px;
  border: 2px solid #991499;
  border-radius: 50px;
  font-size: 15px;
  outline: none;
  transition: box-shadow 0.3s;
}

.search-input:focus {
  box-shadow: 0 0 0 4px rgba(153, 20, 153, 0.15);
}

.sort-dropdown {
  text-align: center;
  margin: 16px 0 10px;
}

.sort-dropdown select {
  padding: 10px 18px;
  font-size: 14px;
  border: 1px solid #991499;
  border-radius: 50px;
  cursor: pointer;
  outline: none;
}

/* ── Map ── */
.map-section {
  padding: 40px 5% 0;
  background: #faf7fc;
}

.culture-map {
  width: 100%;
  height: 420px;
  border-radius: 12px;
  box-shadow: 0 6px 28px rgba(0,0,0,0.1);
  z-index: 0;
}

/* ── Place cards ── */
.places {
  padding: 20px 5% 60px;
  background: #faf7fc;
}

.flex-container {
  display: flex;
  align-items: center;
  gap: 32px;
  margin-bottom: 40px;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  transition: box-shadow 0.3s, transform 0.3s;
}

.flex-container:hover {
  box-shadow: 0 10px 36px rgba(153, 20, 153, 0.14);
  transform: translateY(-4px);
}

.places-col {
  flex-basis: 35%;
  min-width: 240px;
  height: 280px;
  flex-shrink: 0;
  overflow: hidden;
  position: relative;
}

.places-col img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
  transition: transform 0.4s;
}

.flex-container:hover .places-col img {
  transform: scale(1.04);
}

.visit-button {
  display: block;
  padding: 10px 20px;
  background: #991499;
  color: #fff;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  text-align: center;
  transition: background 0.3s;
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
}

.visit-button:hover {
  background: #6b0487;
}

.text-box {
  flex: 1;
  padding: 28px 28px 28px 0;
  text-align: left;
}

.text-box h3 {
  font-size: 24px;
  color: #1a1a2e;
  margin-bottom: 12px;
}

.text-box p {
  font-size: 15px;
  color: #555;
  line-height: 1.65;
  margin-bottom: 6px;
}

/* ── Route section ── */
.route-section {
  padding: 0 5% 48px;
  background: #faf7fc;
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
  gap: 12px;
  flex-wrap: wrap;
  margin-top: 12px;
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
.review-card-header p { font-size: 14px; margin: 0; color: rgba(255,255,255,0.82); max-width: none; }

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

.lang-switcher {
  display: flex;
  gap: 4px;
}

.lang-switcher button {
  background: rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 5px 9px;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s;
}

.lang-switcher button:hover {
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
}

.lang-switcher button.active {
  background: #991499;
  color: #fff;
  border-color: #991499;
}

/* ── Dark mode ── */
.dark-mode { background-color: #121212; color: #fff; }
.dark-mode .map-section { background: #1a1a1a; }
.dark-mode .places { background: #0d0d0d; }
.dark-mode .flex-container { background: #1e1e1e; box-shadow: 0 4px 20px rgba(0,0,0,0.4); }
.dark-mode .text-box h3 { color: #f0e6ff; }
.dark-mode .text-box p { color: #bbb; }
.dark-mode .route-section { background: #0d0d0d; }
.dark-mode .route-card { background: #1e1e1e; border-left-color: #7b0d9b; box-shadow: 0 4px 24px rgba(0,0,0,0.4); }
.dark-mode .route-title { color: #f0e6ff; }
.dark-mode .route-hint { color: #777; }
.dark-mode .route-count { color: #c084e0; }
.dark-mode .fav-btn { border-color: #c084e0; color: #c084e0; }
.dark-mode .fav-btn:hover, .dark-mode .fav-btn.active { background: #7b0d9b; color: #fff; border-color: #7b0d9b; }
.dark-mode .write-review-card { background: #1e1e1e; }
.dark-mode .reviews-list { background: #1e1e1e; }
.dark-mode .reviews-list-title { color: #f0e6ff; border-bottom-color: #3a1a4a; }
.dark-mode .review-place-name { color: #c084e0; }
.dark-mode .review-item { background: #2a2a2a; }
.dark-mode .review-text { color: #bbb; }
.dark-mode .field-group label { color: #aaa; }
.dark-mode .styled-select { background: #2a2a2a; color: #eee; border-color: #4a2a5a; }
.dark-mode .styled-textarea { background: #2a2a2a; color: #eee; border-color: #4a2a5a; }
.dark-mode footer { background: linear-gradient(135deg, #0d0d0d, #1a001a); }

/* ── Footer ── */
footer {
  background: linear-gradient(135deg, #1a001a 0%, #2d003a 100%);
  color: #fff;
  padding: 30px 5%;
  text-align: center;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
}

.footer-content p {
  margin: 0 0 10px;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.5);
  max-width: none;
  text-align: center;
}

.social-links a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  margin: 0 10px;
  font-size: 14px;
  transition: color 0.2s;
}

.social-links a:hover {
  color: #e8a0e8;
}

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

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background: #3b053d;
  min-width: 160px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
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

.dropdown-content a:hover {
  background: rgba(255, 255, 255, 0.1);
}

.dropdown-content.show {
  display: block;
  opacity: 1;
  transform: translateY(0);
}

/* ── Mobile ── */
@media (max-width: 700px) {
  .flex-container {
    flex-direction: column;
  }

  .places-col {
    flex-basis: 100%;
    min-width: unset;
    width: 100%;
    height: 220px;
  }

  .places-col img {
    width: 100%;
  }

  .text-box {
    padding: 20px;
  }

  h1 {
    font-size: 40px;
  }

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
    overflow-y: auto;
  }

  .nav-links ul li {
    display: block;
  }

  .nav-links ul {
    padding: 30px 20px;
  }

  nav .fa-bars {
    display: block;
    color: #fff;
    font-size: 22px;
    cursor: pointer;
  }

  .nav-links .fa-times {
    display: block;
    color: #fff;
    font-size: 22px;
    cursor: pointer;
    padding: 20px 20px 0;
  }

  .route-card {
    flex-wrap: wrap;
    gap: 14px;
  }

  .route-btn {
    width: 100%;
    justify-content: center;
  }

  .search-input {
    width: 95%;
  }

  .culture-map {
    height: 280px;
  }
}
</style>
