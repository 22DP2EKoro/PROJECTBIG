<template>
  <div :class="{ 'dark-mode': isDark }">
    <div class="mus-container">
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
          <h1>{{ t('museums.heroTitle') }}</h1>
          <p>{{ t('museums.heroSub') }}</p>
        </div>
        <div class="col"></div>
      </div>
    </div>

    <div class="search-bar">
      <input type="text" v-model="searchQuery" :placeholder="t('museums.searchPlaceholder')" class="search-input" />
    </div>

    <div class="sort-dropdown">
      <label for="sortOptions">{{ t('museums.sortLabel') }}</label>
      <select v-model="sortOption" @change="applySort" id="sortOptions">
        <option value="alphabetical">{{ t('museums.sortAZ') }}</option>
        <option value="reverse-alphabetical">{{ t('museums.sortZA') }}</option>
      </select>
    </div>

    <div class="map-section">
      <div id="museum-map" class="museum-map"></div>
    </div>

    <div class="museums">
      <div class="flex-container" v-for="(museum, index) in filteredMuseums" :key="museum.id">
        <div class="places-col" v-if="index % 2 === 0">
          <img :src="museum.image" :alt="museum.name" />
          <a :href="museum.link" class="visit-button" target="_blank">{{ t('museums.visitWebsite') }}</a>
        </div>
        <div class="text-box">
          <h3>{{ museum.name }}</h3>
          <p>{{ museum.description }}</p>
          <p>{{ museum.address }}</p>
          <p>{{ museum.openingHours }}</p>
          <div class="place-actions">
            <label v-if="isAuth" class="route-checkbox-label">
              <input
                type="checkbox"
                :value="museum.id"
                v-model="selectedForRoute"
                class="route-checkbox"
              />
              {{ t('museums.addToRoute') }}
            </label>
            <button
              @click="toggleFavorite({ key: 'museum-' + museum.id, id: museum.id, name: museum.name, type: 'museum', image: museum.image, address: museum.address, link: museum.link, description: museum.description })"
              class="fav-btn"
              :class="{ active: isFavorite('museum-' + museum.id) }"
            >
              {{ isFavorite('museum-' + museum.id) ? t('museums.favRemove') : t('museums.favAdd') }}
              <i :class="isFavorite('museum-' + museum.id) ? 'fa fa-heart' : 'fa fa-heart-o'"></i>
            </button>
          </div>
        </div>
        <div class="places-col" v-if="index % 2 !== 0">
          <img :src="museum.image" :alt="museum.name" />
          <a :href="museum.link" class="visit-button" target="_blank">{{ t('museums.visitWebsite') }}</a>
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
          <p class="route-hint" v-if="!selectedForRoute.length">{{ t('museums.routeHint') }}</p>
          <p class="route-count" v-else>
            Izvēlētas <strong>{{ selectedForRoute.length }}</strong> vietas — atzīmē vismaz 2
          </p>
        </div>
        <button class="route-btn" @click="buildRoute" :disabled="selectedForRoute.length < 2">
          <i class="fa fa-road"></i> {{ t('museums.routeBtn') }}
        </button>
      </div>
    </div>

    <section class="review-section-wrap">

      <!-- Write review card -->
      <div class="write-review-card" v-if="isAuth">
        <div class="review-card-header">
          <span class="review-header-icon">★</span>
          <div>
            <h3>Atstājiet savu atsauksmi</h3>
            <p>Dalieties ar savu pieredzi par kādu no muzejiem</p>
          </div>
        </div>

        <div class="review-fields">
          <div class="field-group">
            <label>{{ t('museums.reviewSelectLabel') }}</label>
            <select v-model="selectedMuseumId" class="styled-select">
              <option disabled value="">{{ t('museums.selectDefault') }}</option>
              <option v-for="museum in filteredMuseums" :key="museum.id" :value="museum.id">
                {{ museum.name }}
              </option>
            </select>
          </div>

          <div class="field-group">
            <label>{{ t('museums.reviewRating') }}</label>
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
            <label>{{ t('museums.reviewComment') }}</label>
            <textarea
              v-model="newComment"
              :placeholder="t('museums.commentPlaceholder')"
              class="styled-textarea"></textarea>
          </div>

          <p v-if="reviewError" class="review-error">{{ reviewError }}</p>
          <button @click="submitMuseumReview" class="submit-review-btn">
            Nosūtīt atsauksmi <span>→</span>
          </button>
        </div>
      </div>

      <!-- Reviews display -->
      <div class="reviews-list" v-if="Object.keys(museumReviews).length">
        <h3 class="reviews-list-title">{{ t('museums.allReviews') }}</h3>
        <div v-for="(reviewList, museumId) in museumReviews" :key="museumId" class="review-group">
          <h4 class="review-museum-name">{{ getMuseumTitleById(museumId) }}</h4>
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
import { isAuthenticated, signOut } from '@/auth'
import { useI18n } from '@/i18n'
import { useRouter } from 'vue-router'
import { useFavorites } from '@/composables/useFavorites'
import { useDarkMode } from '@/composables/useDarkMode'
import { useReviews } from '@/composables/useReviews'

export default {
  setup() {
    const { t, currentLang, setLang } = useI18n()
    const { isFavorite, toggleFavorite } = useFavorites()
    const { isDark, toggleDarkMode } = useDarkMode()
    const { museumReviews } = useReviews()
    return { t, currentLang, setLang, isFavorite, toggleFavorite, isDark, toggleDarkMode, museumReviews }
  },
  data() {
    return {
      hoverRating: 0,

      searchQuery: '',
      dropdownOpen: false,
      sortOption: 'alphabetical',
      selectedMuseumId: '',
      newComment: '',
      newRating: '',
      reviewError: '',
      selectedForRoute: [],
      museums: [
        { id: '1',  name: 'Mākslas muzejs', description: 'Mākslas muzejs piedāvā plašu izstāžu klāstu. Apmeklē un atklāj jaunas mākslas pasaules!', address: 'Jaņa Rozentāla laukums 1, Rīga, LV-1010', openingHours: 'Darba laiks: P. slēgts, O.–Sv. 11:00–18:00', image: '/museums/makslasmuzeijs.jpg', link: 'https://www.lnmm.lv/', lat: 56.9541, lng: 24.1170 },
        { id: '2',  name: 'Kara muzejs', description: 'Kara muzejs piedāvā plašu izstāžu klāstu. Apmeklē un atklāj jaunas mākslas pasaules!', address: 'Smilšu iela 20, Rīga, LV-1050', openingHours: 'Darba laiks: Katru dienu 10:00–17:00', image: '/museums/karamuzejs.jpg', link: 'https://www.karamuzejs.lv/', lat: 56.9494, lng: 24.1116 },
        { id: '3',  name: 'Dabas muzejs', description: 'Dabas muzejs piedāvā plašu izstāžu klāstu. Apmeklē un atklāj jaunas mākslas pasaules!', address: 'Krišjāņa Barona iela 4, Rīga, LV-1050', openingHours: 'Darba laiks: P. slēgts, O.–S. 10:00–17:00, Sv. 11:00–17:00', image: '/museums/dabasmuzejs.jpg', link: 'https://www.dabasmuzejs.gov.lv/lv', lat: 56.9519, lng: 24.1136 },
        { id: '4',  name: 'Paula Stradiņa Medicīnas vēstures muzejs', description: 'Paula Stradiņa Medicīnas vēstures muzejs ir unikāls muzejs, kas piedāvā plašu izstāžu klāstu par medicīnas vēsturi un attīstību.', address: 'Antonijas iela 1, Rīga, LV-1010', openingHours: 'Darba laiks: P. slēgts, O.–Sv. 10:00–17:00', image: '/museums/stradini.jpg', link: 'https://www.stradini.lv/', lat: 56.9527, lng: 24.1131 },
        { id: '5',  name: 'Dekoratīvās mākslas un dizaina muzejs', description: 'Muzejs iepazīstina sabiedrību ar aktuālākajām dizaina un dekoratīvās mākslas tendencēm Latvijā un pasaulē un rosina diskusiju par jomas attīstību.', address: 'Skārņu iela 10/20, Rīga, LV-1050', openingHours: 'Darba laiks: O.–Sv. 11:00–18:00, P. slēgts', image: '/museums/dmdmuzejs.jpg', link: 'https://lnmm.gov.lv/dekorativas-makslas-un-dizaina-muzejs', lat: 56.9479, lng: 24.1072 },
        { id: '6',  name: 'Rīgas Motormuzejs', description: 'Rīgas Motormuzejs ir unikāls muzejs, kas piedāvā plašu izstāžu klāstu par automobiļu vēsturi un attīstību.', address: 'Sergeja Eizenšteina iela 8, Rīga, LV-1079', openingHours: 'Darba laiks: Katru dienu 10:00–18:00', image: '/museums/motormuzejs.jpg', link: 'https://www.motormuzejs.lv/', lat: 56.9210, lng: 24.1570 },
        { id: '7', name: 'Latvijas Nacionālais mākslas muzejs', description: 'Latvijas Nacionālais mākslas muzejs ir lielākā profesionālās mākslas krātuve Latvijā ar plašu latviešu mākslas kolekciju.', address: 'Jaņa Rozentāla laukums 1, Rīga, LV-1010', openingHours: 'Darba laiks: O.–Sv. 10:00–18:00, P. slēgts', image: '/museums/lnmm.jpg', link: 'https://www.lnmm.lv/', lat: 56.9541, lng: 24.1170 },
        { id: '8', name: 'Latvijas Etnogrāfiskais brīvdabas muzejs', description: 'Viens no vecākajiem brīvdabas muzejiem Eiropā, kas parāda tradicionālo Latvijas lauku dzīvi.', address: 'Brīvības gatve 440, Rīga, LV-1024', openingHours: 'Darba laiks: Katru dienu 10:00–17:00', image: '/museums/brivdabas.jpg', link: 'https://brivdabasmuzejs.lv/', lat: 56.9796, lng: 24.2112 },
        { id: '9', name: 'Rīgas Biržas mākslas muzejs', description: 'Muzejs ar bagātīgu pasaules mākslas kolekciju – Āzijas, Eiropas un antīkās mākslas darbi.', address: 'Doma laukums 6, Rīga, LV-1050', openingHours: 'Darba laiks: O.–Sv. 10:00–17:00, P. slēgts', image: '/museums/birza.jpg', link: 'https://www.lnmm.lv/birzas-nams', lat: 56.9487, lng: 24.1050 },
        { id: '10', name: 'Rīgas vēstures un kuģniecības muzejs', description: 'Viens no vecākajiem muzejiem Eiropā, kas atspoguļo Rīgas un Latvijas vēsturi.', address: 'Palasta iela 4, Rīga, LV-1050', openingHours: 'Darba laiks: O.–Sv. 10:00–17:00, P. slēgts', image: '/museums/vesturesmuzejs.jpg', link: 'https://www.rigamuz.lv/rhvm/', lat: 56.9481, lng: 24.1066 },
        { id: '11', name: 'Latvijas Okupācijas muzejs', description: 'Muzejs par Latvijas okupācijas vēsturi 20. gadsimtā.', address: 'Latviešu strēlnieku laukums 1, Rīga, LV-1050', openingHours: 'Darba laiks: Katru dienu 10:00–18:00', image: '/museums/okupacijas.jpg', link: 'https://okupacijasmuzejs.lv/', lat: 56.9477, lng: 24.1082 },
        { id: '12', name: 'Zuzeum mākslas centrs', description: 'Mūsdienu mākslas centrs ar Latvijas un starptautisko laikmetīgo mākslu.', address: 'Lāčplēša iela 101, Rīga, LV-1011', openingHours: 'Darba laiks: Tre.–Sv. 11:00–19:00, P.–O. slēgts', image: '/museums/zuzeum.jpg', link: 'https://www.zuzeum.com/', lat: 56.9450, lng: 24.1260 },
      ],
    }
  },

  computed: {
    isAuth() {
      return isAuthenticated.value
    },
    filteredMuseums() {
      let filtered = this.museums.filter(museum =>
        museum.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      )
      if (this.sortOption === 'alphabetical') {
        return filtered.sort((a, b) => a.name.localeCompare(b.name))
      } else if (this.sortOption === 'reverse-alphabetical') {
        return filtered.sort((a, b) => b.name.localeCompare(a.name))
      }
      return filtered
    },
  },

  mounted() {
    fetch('/api/get_reviews.php?type=museum')
      .then(r => r.json())
      .then(data => { this.museumReviews = data })
      .catch(() => {})

    const map = L.map('museum-map').setView([56.952, 24.113], 13)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map)

    this.museums.forEach(m => {
      if (!m.lat || !m.lng) return
      L.marker([m.lat, m.lng], {
        icon: L.divIcon({
          className: '',
          html: `<div style="background:#991499;color:#fff;border-radius:50%;width:38px;height:38px;display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 2px 8px rgba(0,0,0,0.35);border:2px solid #fff"><i class="fa fa-landmark"></i></div>`,
          iconSize: [38, 38],
          iconAnchor: [19, 19],
          popupAnchor: [0, -22]
        })
      })
        .addTo(map)
        .bindPopup(`<b>${m.name}</b><br><small>${m.address}</small><br><a href="${m.link}" target="_blank" style="color:#991499">Apmeklēt →</a>`)
    })
  },

  methods: {
    showMenu() {
      const navLinks = document.getElementById('navLinks')
      if (navLinks) navLinks.style.right = '0'
    },
    hideMenu() {
      const navLinks = document.getElementById('navLinks')
      if (navLinks) navLinks.style.right = '-200px'
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
    submitMuseumReview() {
      this.reviewError = ''
      if (!this.selectedMuseumId && !this.newComment.trim() && !this.newRating) {
        this.reviewError = 'Lūdzu izvēlies muzeju, novērtē ar zvaigznēm un uzraksti komentāru.'
        return
      }
      if (!this.selectedMuseumId) { this.reviewError = 'Lūdzu izvēlies muzeju.'; return }
      if (!this.newRating) { this.reviewError = 'Lūdzu novērtē ar zvaigznēm.'; return }
      if (!this.newComment.trim()) { this.reviewError = 'Lūdzu uzraksti komentāru.'; return }

      const id = this.selectedMuseumId
      const comment = this.newComment.trim()
      const rating = parseInt(this.newRating)

      fetch('/api/submit_review.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: 'museum', objectId: id, comment, rating }),
      })
        .then(r => r.json())
        .then(() => {
          this.museumReviews = {
            ...this.museumReviews,
            [id]: [...(this.museumReviews[id] || []), { comment, rating }],
          }
          this.newComment = ''
          this.newRating = ''
          this.selectedMuseumId = ''
          this.reviewError = ''
        })
        .catch(() => { this.reviewError = 'Neizdevās nosūtīt atsauksmi. Mēģini vēlreiz.' })
    },
    getMuseumTitleById(id) {
      const museum = this.museums.find(m => m.id === id)
      return museum ? museum.name : 'Unknown Museum'
    },
    applySort() {},

    buildRoute() {
      const points = this.selectedForRoute
        .map(id => this.museums.find(m => m.id === id))
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
.mus-container {
  width: 100%;
  height: 100vh;
  background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)),
    url("https://assets.vogue.com/photos/5cf02219747ce17f10fb866a/master/w_2560%2Cc_limit/00-story-image-the-10-most-popular-museums-in-the-world.jpg");
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
  letter-spacing: -0.2px;
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

.museum-map {
  width: 100%;
  height: 420px;
  border-radius: 12px;
  box-shadow: 0 6px 28px rgba(0,0,0,0.1);
  z-index: 0;
}

/* ── Museum cards ── */
.museums {
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
  align-self: stretch;
  overflow: hidden;
  position: relative;
}

.places-col img {
  width: 100%;
  height: 100%;
  min-height: 280px;
  object-fit: cover;
  display: block;
  transition: transform 0.4s;
}

.flex-container:hover .places-col img {
  transform: scale(1.04);
}

.visit-button {
  display: block;
  margin: 0;
  padding: 10px 20px;
  background: #991499;
  color: #fff;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.5px;
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

.route-card-body {
  flex: 1;
}

.route-title {
  font-size: 18px;
  color: #1a1a2e;
  margin: 0 0 4px;
}

.route-hint {
  color: #888;
  font-size: 14px;
  margin: 0;
}

.route-count {
  color: #991499;
  font-size: 14px;
  margin: 0;
}

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

.route-btn:hover:not(:disabled) {
  opacity: 0.88;
  transform: translateY(-2px);
}

.route-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

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

.route-checkbox {
  accent-color: #991499;
  width: 16px;
  height: 16px;
  cursor: pointer;
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
.fav-btn:hover, .fav-btn.active {
  background: #991499; color: #fff;
}
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

.review-header-icon {
  font-size: 2.4rem;
  line-height: 1;
  flex-shrink: 0;
}

.review-card-header h3 {
  font-size: 22px;
  margin: 0 0 4px;
  color: #fff;
}

.review-card-header p {
  font-size: 14px;
  margin: 0;
  color: rgba(255, 255, 255, 0.82);
  max-width: none;
}

.review-fields {
  padding: 28px 32px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.field-group {
  display: flex;
  flex-direction: column;
  gap: 7px;
}

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

.styled-select:focus {
  border-color: #991499;
}

.star-rating {
  display: flex;
  align-items: center;
  gap: 6px;
}

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

.star-btn:hover,
.star-btn.active {
  color: #f5b800;
  transform: scale(1.2);
}

.rating-label {
  font-size: 14px;
  font-weight: 700;
  color: #991499;
  margin-left: 8px;
}

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

.styled-textarea:focus {
  border-color: #991499;
}

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

.submit-review-btn:hover {
  opacity: 0.88;
  transform: translateY(-2px);
}

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
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
  padding: 28px 32px;
}

.reviews-list-title {
  font-size: 22px;
  color: #1a1a2e;
  margin-bottom: 24px;
  padding-bottom: 14px;
  border-bottom: 2px solid #f0e6ff;
}

.review-museum-name {
  font-size: 16px;
  color: #991499;
  font-weight: 700;
  margin: 18px 0 10px;
}

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

.review-content {
  flex: 1;
}

.review-stars {
  display: flex;
  gap: 2px;
  margin-bottom: 6px;
}

.review-stars span {
  color: #ddd;
  font-size: 16px;
}

.review-stars span.filled {
  color: #f5b800;
}

.review-text {
  font-size: 14px;
  color: #555;
  margin: 0;
  line-height: 1.6;
}

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

.dark-mode .search-input {
  background: #1e1e1e;
  color: #eee;
  border-color: #6b0487;
}
.dark-mode .sort-dropdown label { color: #ccc; }
.dark-mode .sort-dropdown select {
  background: #1e1e1e;
  color: #eee;
  border-color: #6b0487;
}

.dark-mode .museums { background: #0d0d0d; }
.dark-mode .flex-container { background: #1e1e1e; box-shadow: 0 4px 20px rgba(0,0,0,0.4); }
.dark-mode .text-box h3 { color: #f0e6ff; }
.dark-mode .text-box p { color: #bbb; }

.dark-mode .route-section { background: #0d0d0d; }
.dark-mode .route-card { background: #1e1e1e; border-left-color: #7b0d9b; box-shadow: 0 4px 24px rgba(0,0,0,0.4); }
.dark-mode .route-title { color: #f0e6ff; }
.dark-mode .route-hint { color: #777; }
.dark-mode .route-count { color: #c084e0; }

.dark-mode .fav-btn { border-color: #c084e0; color: #c084e0; }
.dark-mode .fav-btn:hover,
.dark-mode .fav-btn.active { background: #7b0d9b; color: #fff; border-color: #7b0d9b; }

.dark-mode .write-review-card { background: #1e1e1e; }
.dark-mode .reviews-list { background: #1e1e1e; }
.dark-mode .reviews-list-title { color: #f0e6ff; border-bottom-color: #3a1a4a; }
.dark-mode .review-museum-name { color: #c084e0; }
.dark-mode .review-item { background: #2a2a2a; }
.dark-mode .review-text { color: #bbb; }
.dark-mode .field-group label { color: #aaa; }
.dark-mode .styled-select {
  background: #2a2a2a;
  color: #eee;
  border-color: #4a2a5a;
}
.dark-mode .styled-textarea {
  background: #2a2a2a;
  color: #eee;
  border-color: #4a2a5a;
}

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
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px 12px;
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
  letter-spacing: 0.5px;
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
  }

  .places-col img {
    min-height: 220px;
  }

  .text-box {
    padding: 20px;
  }

  h1 {
    font-size: 46px;
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
}
</style>
