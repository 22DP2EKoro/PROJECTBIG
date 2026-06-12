<template>
  <div :class="{ 'dark-mode': isDarkMode }">
    <section class="home-header">
      <nav>
        <router-link to="/" class="brand">ExploreRiga</router-link>
        <div class="nav-links" :style="{ right: navVisible ? '0' : '-240px' }">
          <i class="fa fa-times" @click="hideMenu"></i>
          <ul>
            <li class="dropdown">
              <a href="#" class="dropbtn" @click.prevent="toggleDropdown">{{ t('nav.aboutUs') }}</a>
              <div class="dropdown-content" :class="{ show: dropdownOpen }">
                <a href="/contacts">{{ t('nav.contacts') }}</a>
                <a href="/team">{{ t('nav.team') }}</a>
                <a href="/information">{{ t('nav.information') }}</a>
              </div>
            </li>
            <li><router-link to="/">{{ t('nav.home') }}</router-link></li>
            <li><router-link to="/museums">{{ t('nav.museums') }}</router-link></li>
            <li><router-link to="/restaurants">{{ t('nav.restaurants') }}</router-link></li>
            <li><router-link to="/culture">{{ t('nav.culture') }}</router-link></li>
            <li v-if="auth"><router-link to="/todo">{{ t('nav.todo') }}</router-link></li>
            <li v-else><router-link to="/signin">{{ t('nav.signIn') }}</router-link></li>
            <li v-if="auth">
              <a href="#" @click.prevent="handleSignOut">{{ t('nav.signOut') }}</a>
            </li>
            <li><button class="dark-mode-o" @click="toggleDarkMode"><i :class="isDarkMode ? 'fa fa-sun' : 'fa fa-moon'"></i></button></li>
          </ul>
        </div>
        <div class="lang-switcher">
          <button v-for="l in langs" :key="l" @click="setLang(l)" :class="{ active: currentLang === l }">{{ l.toUpperCase() }}</button>
        </div>
        <i class="fa fa-bars" @click="showMenu"></i>
      </nav>
      <div class="text-box">
        <h1>{{ t('home.heroTitle') }}</h1>
        <p>{{ t('home.heroSub') }}</p>
        <router-link to="/museums" class="hero-btn">{{ t('home.heroBtn') }} <i class="fa fa-arrow-right"></i></router-link>
      </div>

      <div class="weather-widget" v-if="weather">
        <div class="weather-icon"><i :class="'fa ' + weather.icon"></i></div>
        <div class="weather-info">
          <span class="weather-temp">{{ weather.temp }}°C</span>
          <span class="weather-desc">{{ weather.desc }}</span>
          <span class="weather-details">
            <i class="fa fa-wind"></i> {{ weather.wind }} km/h
            &nbsp;·&nbsp;
            <i class="fa fa-droplet"></i> {{ weather.humidity }}%
          </span>
          <span class="weather-city">Rīga, Latvija</span>
        </div>
      </div>
    </section>

    <section class="karte">
      <h2>{{ t('home.aboutTitle') }}</h2>
      <p>{{ t('home.aboutText') }}</p>
      <div class="map-wrapper">
        <iframe
          src="https://maps.google.com/maps?q=Riga+Latvia&t=&z=12&ie=UTF8&iwloc=&output=embed"
          width="100%" height="420" style="border:0; border-radius: 12px;" allowfullscreen loading="lazy">
        </iframe>
      </div>
    </section>

    <!-- Weather forecast section -->
    <section class="weather-section" v-if="weatherForecast">
      <div class="weather-section-inner">
        <h2>Laikapstākļi Rīgā</h2>
        <div class="weather-main">
          <div class="weather-current" v-if="weather">
            <div class="wc-icon"><i :class="'fa ' + weather.icon"></i></div>
            <div class="wc-info">
              <div class="wc-temp">{{ weather.temp }}°C</div>
              <div class="wc-desc">{{ weather.desc }}</div>
              <div class="wc-stats">
                <span><i class="fa fa-wind"></i> {{ weather.wind }} km/h</span>
                <span><i class="fa fa-droplet"></i> {{ weather.humidity }}%</span>
              </div>
              <div class="wc-label">Tagad · Rīga</div>
            </div>
          </div>
          <div class="weather-daily">
            <div class="wd-card" v-for="day in weatherForecast.daily" :key="day.date">
              <span class="wd-dayname">{{ day.dayName }}</span>
              <i :class="'fa ' + day.icon"></i>
              <span class="wd-high">{{ day.tempMax }}°</span>
              <span class="wd-low">{{ day.tempMin }}°</span>
              <span class="wd-precip" v-if="day.precip > 0"><i class="fa fa-droplet"></i> {{ day.precip }}%</span>
              <span class="wd-precip" v-else>&nbsp;</span>
            </div>
          </div>
        </div>
        <div class="weather-hourly-wrap">
          <div class="weather-hourly">
            <div class="wh-item" v-for="hour in weatherForecast.hourly" :key="hour.time">
              <span class="wh-time">{{ formatHour(hour.time) }}</span>
              <i :class="'fa ' + (weatherDescriptions[hour.code] || { icon: 'fa-cloud' }).icon"></i>
              <span class="wh-temp">{{ Math.round(hour.temp) }}°</span>
              <span class="wh-precip" v-if="hour.precip > 0"><i class="fa fa-droplet"></i> {{ hour.precip }}%</span>
              <span class="wh-precip" v-else>&nbsp;</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="places1">
      <h2>{{ t('home.placesTitle') }}</h2>
      <p class="section-sub">{{ t('home.placesSub') }}</p>
      <div class="row">
        <div class="places-col">
          <div class="img-wrapper"><img :src="places[0].img" :alt="t('home.rest_title')" /></div>
          <div class="card-body"><h3>{{ t('home.rest_title') }}</h3><p>{{ t('home.rest_desc') }}</p></div>
        </div>
        <div class="places-col">
          <div class="img-wrapper"><img :src="places[1].img" :alt="t('home.mus_title')" /></div>
          <div class="card-body"><h3>{{ t('home.mus_title') }}</h3><p>{{ t('home.mus_desc') }}</p></div>
        </div>
        <div class="places-col">
          <div class="img-wrapper"><img :src="places[2].img" :alt="t('home.parks_title')" /></div>
          <div class="card-body"><h3>{{ t('home.parks_title') }}</h3><p>{{ t('home.parks_desc') }}</p></div>
        </div>
      </div>
    </section>

    <section class="tourism_sight">
      <h2>{{ t('home.viralTitle') }}</h2>
      <p class="section-sub">{{ t('home.viralSub') }}</p>
      <div class="video-grid" ref="videoGrid">
        <video controls muted playsinline>
          <source src="https://videos.pexels.com/video-files/8243661/8243661-uhd_2732_1440_24fps.mp4" type="video/mp4" />
        </video>
        <video controls muted playsinline>
          <source src="https://videos.pexels.com/video-files/8467767/8467767-hd_1920_1080_30fps.mp4" type="video/mp4" />
        </video>
      </div>
    </section>

    <!-- News section -->
    <section class="news-section">
      <h2>Aktuālas ziņas par Rīgu</h2>
      <p class="section-sub">Jaunākie notikumi un aktualitātes pilsētā</p>
      <div class="news-grid">
        <div class="news-card" v-for="item in newsItems" :key="item.id">
          <div class="news-img-wrap">
            <img :src="item.img" :alt="item.title" />
            <span class="news-tag" :class="'tag-' + item.tagClass">{{ item.tag }}</span>
          </div>
          <div class="news-body">
            <span class="news-date"><i class="fa fa-calendar-alt"></i> {{ item.date }}</span>
            <h3>{{ item.title }}</h3>
            <p>{{ item.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Traveller info section -->
    <section class="traveller-section">
      <h2>Praktiskā informācija ceļotājam</h2>
      <p class="section-sub">Svarīgākā informācija pirms brauciena uz Rīgu</p>
      <div class="traveller-grid">
        <div class="traveller-card">
          <div class="t-icon"><i class="fa fa-coins"></i></div>
          <h4>Valūta</h4>
          <p>Euro (€)</p>
          <span>Bankomāti pieejami visā pilsētā</span>
        </div>
        <div class="traveller-card">
          <div class="t-icon"><i class="fa fa-language"></i></div>
          <h4>Valoda</h4>
          <p>Latviešu</p>
          <span>Angļu plaši saprotama tūrisma vietās</span>
        </div>
        <div class="traveller-card">
          <div class="t-icon"><i class="fa fa-clock"></i></div>
          <h4>Laika zona</h4>
          <p>UTC+2 (EET)</p>
          <span>Vasarā UTC+3 (EEST)</span>
        </div>
        <div class="traveller-card">
          <div class="t-icon"><i class="fa fa-bus"></i></div>
          <h4>Transports</h4>
          <p>Tramvajs, trolejbuss, autobuss</p>
          <span>Rīgas satiksme — viena biļete visiem</span>
        </div>
        <div class="traveller-card">
          <div class="t-icon"><i class="fa fa-plane"></i></div>
          <h4>Lidosta</h4>
          <p>RIX — Rīgas Starptautiskā</p>
          <span>~10 km no pilsētas centra</span>
        </div>
      </div>
    </section>

    <!-- Members benefits section — visible only when not signed in -->
    <section class="join-section" v-if="!auth">
      <div class="join-inner">
        <div class="join-text">
          <h2>{{ t('home.joinTitle') }}</h2>
          <p class="join-sub">{{ t('home.joinSub') }}</p>
          <ul class="join-benefits">
            <li><i class="fa fa-star"></i> {{ t('home.joinB1') }}</li>
            <li><i class="fa fa-list-check"></i> {{ t('home.joinB2') }}</li>
          </ul>
          <div class="join-actions">
            <router-link to="/signin" class="join-btn-primary">{{ t('home.joinBtn') }}</router-link>
            <span class="join-already">{{ t('home.joinAlready') }} <router-link to="/signin">{{ t('home.joinSignIn') }}</router-link></span>
          </div>
          <div class="submit-cta">
            <router-link to="/submit" class="submit-link">
              <i class="fa fa-store"></i> {{ t('home.submitPlace') }}
            </router-link>
          </div>
        </div>
        <div class="join-visual" aria-hidden="true">
          <i class="fa fa-compass"></i>
        </div>
      </div>
    </section>

    <footer>
      <div class="footer-inner">
        <div class="footer-brand">
          <span class="footer-logo">ExploreRiga</span>
          <p>{{ t('footer.tagline') }}</p>
        </div>
        <div class="footer-links">
          <h4>{{ t('footer.explore') }}</h4>
          <a href="/museums">{{ t('nav.museums') }}</a>
          <a href="/restaurants">{{ t('nav.restaurants') }}</a>
          <a href="/culture">{{ t('nav.culture') }}</a>
        </div>
        <div class="footer-links">
          <h4>{{ t('footer.company') }}</h4>
          <a href="/contacts">{{ t('nav.contacts') }}</a>
          <a href="/team">{{ t('nav.team') }}</a>
          <a href="/information">{{ t('nav.information') }}</a>
        </div>
        <div class="footer-social">
          <h4>{{ t('footer.follow') }}</h4>
          <div class="social-icons">
            <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>{{ t('footer.copyright') }}</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { signOut, isAuthenticated } from '../auth'
import { useI18n } from '../i18n'
import { ref, onMounted, onUnmounted } from 'vue'
import { useDarkMode } from '@/composables/useDarkMode'

const { t, currentLang, setLang } = useI18n()
const langs = ['lv', 'en', 'et', 'lt']

const { isDark: isDarkMode, toggleDarkMode } = useDarkMode()
const dropdownOpen = ref(false)
const navVisible = ref(false)
const auth = isAuthenticated
const videoGrid = ref(null)
let videoObserver = null
const weather = ref(null)
const weatherForecast = ref(null)

const weatherDescriptions = {
  0: { desc: 'Skaidrs', icon: 'fa-sun' },
  1: { desc: 'Galvenokārt skaidrs', icon: 'fa-sun' },
  2: { desc: 'Daļēji mākoņains', icon: 'fa-cloud-sun' },
  3: { desc: 'Apmācies', icon: 'fa-cloud' },
  45: { desc: 'Migla', icon: 'fa-smog' },
  48: { desc: 'Migla', icon: 'fa-smog' },
  51: { desc: 'Viegla smidzināšana', icon: 'fa-cloud-drizzle' },
  53: { desc: 'Smidzināšana', icon: 'fa-cloud-drizzle' },
  55: { desc: 'Stipra smidzināšana', icon: 'fa-cloud-drizzle' },
  61: { desc: 'Viegls lietus', icon: 'fa-cloud-rain' },
  63: { desc: 'Lietus', icon: 'fa-cloud-rain' },
  65: { desc: 'Stiprs lietus', icon: 'fa-cloud-showers-heavy' },
  71: { desc: 'Viegls sniegs', icon: 'fa-snowflake' },
  73: { desc: 'Sniegs', icon: 'fa-snowflake' },
  75: { desc: 'Stiprs sniegs', icon: 'fa-snowflake' },
  80: { desc: 'Lietus gāzes', icon: 'fa-cloud-rain' },
  81: { desc: 'Lietus gāzes', icon: 'fa-cloud-rain' },
  82: { desc: 'Spēcīgas lietus gāzes', icon: 'fa-cloud-showers-heavy' },
  95: { desc: 'Pērkona negaiss', icon: 'fa-bolt' },
  96: { desc: 'Pērkona negaiss ar krusu', icon: 'fa-bolt' },
  99: { desc: 'Pērkona negaiss ar krusu', icon: 'fa-bolt' },
}

function formatHour(timeStr) {
  return timeStr.split('T')[1].slice(0, 5)
}

async function fetchWeather() {
  try {
    const res = await fetch(
      'https://api.open-meteo.com/v1/forecast?latitude=56.9496&longitude=24.1052' +
      '&current=temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code' +
      '&hourly=temperature_2m,weather_code,precipitation_probability' +
      '&daily=temperature_2m_max,temperature_2m_min,weather_code,precipitation_probability_max' +
      '&wind_speed_unit=kmh&timezone=Europe%2FRiga&forecast_days=7'
    )
    const data = await res.json()

    const code = data.current.weather_code
    const info = weatherDescriptions[code] || { desc: 'Nezināms', icon: 'fa-cloud' }
    weather.value = {
      temp: Math.round(data.current.temperature_2m),
      humidity: data.current.relative_humidity_2m,
      wind: Math.round(data.current.wind_speed_10m),
      desc: info.desc,
      icon: info.icon,
    }

    const nowStr = data.current.time
    const startIdx = data.hourly.time.findIndex(t => t >= nowStr)
    const hourly = data.hourly.time.slice(startIdx, startIdx + 24).map((t, i) => ({
      time: t,
      temp: data.hourly.temperature_2m[startIdx + i],
      code: data.hourly.weather_code[startIdx + i],
      precip: data.hourly.precipitation_probability[startIdx + i],
    }))

    const dayNames = ['Svētd.', 'Pirmd.', 'Otrd.', 'Tresd.', 'Ceturd.', 'Piektd.', 'Sestd.']
    const daily = data.daily.time.map((date, i) => {
      const d = new Date(date + 'T12:00')
      const dayInfo = weatherDescriptions[data.daily.weather_code[i]] || { desc: 'Nezināms', icon: 'fa-cloud' }
      return {
        date,
        dayName: i === 0 ? 'Šodien' : i === 1 ? 'Rītdien' : dayNames[d.getDay()],
        tempMax: Math.round(data.daily.temperature_2m_max[i]),
        tempMin: Math.round(data.daily.temperature_2m_min[i]),
        icon: dayInfo.icon,
        precip: data.daily.precipitation_probability_max[i],
      }
    })

    weatherForecast.value = { hourly, daily }
  } catch {
    // silent fail
  }
}

function toggleDropdown() { dropdownOpen.value = !dropdownOpen.value }
function showMenu() { navVisible.value = true }
function hideMenu() { navVisible.value = false }
function handleSignOut() { signOut() }

onMounted(() => {
  fetchWeather()

  window.addEventListener('click', (e) => {
    if (!e.target.classList.contains('dropbtn')) dropdownOpen.value = false
  })

  videoObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.play()
      } else {
        entry.target.pause()
      }
    })
  }, { threshold: 0.5 })

  if (videoGrid.value) {
    videoGrid.value.querySelectorAll('video').forEach(v => videoObserver.observe(v))
  }
})

onUnmounted(() => {
  if (videoObserver) videoObserver.disconnect()
})

const places = [
  { img: 'https://s.inyourpocket.com/gallery/257731.jpg' },
  { img: 'https://assets.architecturaldigest.in/photos/60081fc808ae763b9ae83f5f/16:9/w_2560%2Cc_limit/The-building-of-the-Brotherhood-of-Blackheads-is-one-of-the-most-iconic-buildings-of-Old-Riga--1366x768.jpg' },
  { img: 'https://www.latvia.travel/sites/default/files/styles/max_1300x1300/public/media_image/tourism_sight/44256507012_666bad3106_k.jpg?itok=D0Mi0rK_' },
]

const newsItems = [
  {
    id: 1,
    tag: 'Kultūra',
    tagClass: 'kultura',
    date: '14. maijs, 2025',
    title: 'Latvijas Nacionālais mākslas muzejs atver jaunas ekspozīcijas telpas',
    desc: 'Renovētais muzejs atklāj jaunu 2000 m² lielu zāli ar vairāk nekā 300 latviešu mākslinieku darbiem no 19. un 20. gadsimta.',
    img: '/museums/lnmm.jpg',
  },
  {
    id: 2,
    tag: 'Pasākumi',
    tagClass: 'pasakumi',
    date: '9. maijs, 2025',
    title: 'Rīgas svētki 2025: pilsēta gatavojas lielākajam vasaras pasākumam',
    desc: 'Augustā Rīgā notiks vērienīgākie pilsētas svētki pēdējo gadu laikā — koncerti, gastronomijas tirdziņi un tradicionālā kuģu parāde Daugavā.',
    img: 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=600&q=80',
  },
  {
    id: 3,
    tag: 'Infrastruktūra',
    tagClass: 'infra',
    date: '3. maijs, 2025',
    title: 'Rīgas Centrāltirgus uzsāk lielāko rekonstrukciju kopš 1990. gada',
    desc: 'UNESCO pasaules mantojuma objekts — Rīgas Centrāltirgus — uzsāk daudzgadu renovācijas projektu. Tirgus strādās visu rekonstrukcijas laiku.',
    img: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&q=80',
  },
  {
    id: 4,
    tag: 'Tūrisms',
    tagClass: 'turizms',
    date: '28. apr., 2025',
    title: 'Rīga iekļauta Eiropas 10 populārākajās pilsētu tūrisma vietās',
    desc: 'Eiropas Tūrisma komisijas gada atskaitē Rīga ieņem 7. vietu populārākajā pilsētu galamērķu sarakstā, apsteidzot Tallinu un Viļņu.',
    img: 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?w=600&q=80',
  },
  {
    id: 5,
    tag: 'Transports',
    tagClass: 'transports',
    date: '21. apr., 2025',
    title: 'Jauna 4 km gara velojosla savieno Vecrīgu ar Āgenskalnu',
    desc: 'Rīgas dome atklāja jaunu velojoslu, kas šķērso Daugavu pa Akmens tiltu un nodrošina drošu savienojumu starp abiem krastiem.',
    img: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80',
  },
  {
    id: 6,
    tag: 'Kultūra',
    tagClass: 'kultura',
    date: '15. apr., 2025',
    title: 'Latvijas Nacionālā opera atklāj 2025./2026. gada sezonu ar divām premjerām',
    desc: 'Jaunajā sezonā gaidāmas divas pasaules klases premjeras, kā arī starptautiski viesizrādi no Berlīnes un Vīnes operām.',
    img: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&q=80',
  },
]
</script>

<style scoped>
* { margin: 0; padding: 0; font-family: 'Inter', sans-serif; box-sizing: border-box; }
h1, h2, h3 { font-family: 'Playfair Display', serif; }

/* ── Dark mode ── */
.dark-mode { background-color: #121212; color: #fff; }
.dark-mode .karte p, .dark-mode .section-sub { color: #ccc; }
.dark-mode .places-col { background-color: #1e1e1e; }
.dark-mode .card-body p { color: #bbb; }
.dark-mode .tourism_sight { background-color: #1a1a1a; }
.dark-mode .tourism_sight h2, .dark-mode .tourism_sight .section-sub { color: #fff; }
.dark-mode .karte h2, .dark-mode .places1 h2 { color: #fff; }

/* ── Hero ── */
.home-header {
  min-height: 100vh;
  width: 100%;
  background-image: linear-gradient(rgba(4,9,30,0.65), rgba(4,9,30,0.65)),
    url("https://goingbaltic.com/wp-content/uploads/2021/10/Latvia_Riga_-Blackheads-house_Saint-Peters-church_shutterstock_136863935-1.jpg");
  background-position: center;
  background-size: cover;
  background-attachment: scroll;
  position: relative;
}

/* ── Navigation ── */
nav {
  display: flex;
  padding: 1.5% 5%;
  align-items: center;
  gap: 12px;
}

.brand {
  font-family: 'Playfair Display', serif;
  font-size: 1.6rem;
  font-weight: 700;
  color: #fff;
  text-decoration: none;
  flex-shrink: 0;
}

.nav-links { flex: 1; text-align: right; }
.nav-links ul { padding: 0; margin: 0; list-style: none; }

.nav-links ul li {
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

/* ── Language switcher ── */
.lang-switcher {
  display: flex;
  gap: 4px;
  flex-shrink: 0;
}

.lang-switcher button {
  background: rgba(255,255,255,0.1);
  color: rgba(255,255,255,0.7);
  border: 1px solid rgba(255,255,255,0.2);
  padding: 5px 9px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.5px;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s;
}

.lang-switcher button:hover { background: rgba(255,255,255,0.2); color: #fff; }
.lang-switcher button.active {
  background: #991499;
  color: #fff;
  border-color: #991499;
}

/* ── Dark mode toggle ── */
.dark-mode-o {
  background: rgba(255,255,255,0.12);
  color: white;
  padding: 8px 14px;
  font-size: 15px;
  cursor: pointer;
  border: 1px solid rgba(255,255,255,0.25);
  border-radius: 8px;
  flex-shrink: 0;
  transition: background 0.3s;
}

.dark-mode-o:hover { background: rgba(255,255,255,0.22); }

/* ── Hero text ── */
.text-box {
  width: 90%;
  color: #fff;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
}

.text-box h1 {
  font-size: 72px;
  font-weight: 700;
  letter-spacing: -1px;
  text-shadow: 0 2px 20px rgba(0,0,0,0.4);
  margin-bottom: 16px;
}

.text-box p {
  margin: 0 0 36px;
  font-size: 20px;
  color: rgba(255,255,255,0.88);
  font-weight: 300;
}

.hero-btn {
  display: inline-block;
  padding: 15px 36px;
  background: #991499;
  color: #fff;
  text-decoration: none;
  font-size: 15px;
  font-weight: 600;
  border-radius: 50px;
  transition: background 0.3s, transform 0.2s, box-shadow 0.3s;
  box-shadow: 0 4px 20px rgba(153,20,153,0.4);
}

.hero-btn:hover { background: #7b0d7b; transform: translateY(-2px); color: #fff; }
.hero-btn i { margin-left: 8px; }

/* ── Karte ── */
.karte { max-width: 1000px; margin: 80px auto; text-align: center; padding: 0 24px; }
.karte h2 { font-size: 40px; color: #1a1a2e; margin-bottom: 16px; }
.karte p { color: #555; font-size: 17px; line-height: 1.7; max-width: 700px; margin: 0 auto 32px; }
.map-wrapper { border-radius: 12px; overflow: hidden; box-shadow: 0 6px 30px rgba(0,0,0,0.12); }

/* ── Places ── */
.places1 { max-width: 1200px; margin: 0 auto 80px; text-align: center; padding: 0 24px; }
.places1 h2 { font-size: 40px; color: #1a1a2e; margin-bottom: 12px; }
.section-sub { font-size: 17px; color: #666; max-width: 600px; margin: 0 auto 40px; line-height: 1.6; }

.row { display: flex; justify-content: space-between; gap: 24px; }

.places-col {
  flex: 1;
  background: #fff;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  transition: transform 0.35s, box-shadow 0.35s;
  display: flex;
  flex-direction: column;
}

.places-col:hover { transform: translateY(-8px); box-shadow: 0 14px 40px rgba(153,20,153,0.15); }

.img-wrapper { overflow: hidden; height: 210px; }
.img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
.places-col:hover .img-wrapper img { transform: scale(1.05); }

.card-body { padding: 20px 18px 24px; text-align: left; }
.card-body h3 { font-size: 20px; font-weight: 600; margin-bottom: 8px; color: #1a1a2e; }
.card-body p { font-size: 14px; color: #666; line-height: 1.6; margin: 0; }

/* ── Viral tour ── */
.tourism_sight { padding: 80px 24px; text-align: center; background-color: #f4f2f7; }
.tourism_sight h2 { font-size: 40px; color: #1a1a2e; margin-bottom: 10px; }

.video-grid { display: flex; gap: 24px; max-width: 1100px; margin: 0 auto; justify-content: center; }
.video-grid video { flex: 1; max-width: 520px; border-radius: 12px; box-shadow: 0 6px 30px rgba(0,0,0,0.14); }

/* ── News section ── */
.news-section {
  padding: 80px 24px;
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
}

.news-section h2 { font-size: 40px; color: #1a1a2e; margin-bottom: 12px; }

.news-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 28px;
  margin-top: 40px;
  text-align: left;
}

.news-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  display: flex;
  flex-direction: column;
  transition: transform 0.3s, box-shadow 0.3s;
}

.news-card:hover { transform: translateY(-6px); box-shadow: 0 12px 36px rgba(153,20,153,0.13); }

.news-img-wrap {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.news-img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s;
}

.news-card:hover .news-img-wrap img { transform: scale(1.06); }

.news-tag {
  position: absolute;
  top: 14px;
  left: 14px;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.tag-kultura     { background: #f5d0fe; color: #7e22ce; }
.tag-pasakumi    { background: #fde68a; color: #92400e; }
.tag-infra       { background: #bfdbfe; color: #1d4ed8; }
.tag-turizms     { background: #bbf7d0; color: #15803d; }
.tag-transports  { background: #fed7aa; color: #c2410c; }

.news-body {
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex: 1;
  gap: 8px;
}

.news-date {
  font-size: 12px;
  color: #999;
  display: flex;
  align-items: center;
  gap: 6px;
}

.news-body h3 {
  font-size: 17px;
  font-weight: 700;
  color: #1a1a2e;
  line-height: 1.35;
  font-family: 'Playfair Display', serif;
}

.news-body p {
  font-size: 13.5px;
  color: #666;
  line-height: 1.6;
  flex: 1;
  margin: 0;
}

.news-read-more {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: #991499;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  margin-top: 8px;
  transition: gap 0.2s;
}

.news-read-more:hover { gap: 10px; }
.news-read-more .fa { font-size: 11px; }

.dark-mode .news-section h2 { color: #fff; }
.dark-mode .news-card { background: #1e1e1e; }
.dark-mode .news-body h3 { color: #fff; }
.dark-mode .news-body p { color: #bbb; }

/* ── Traveller info section ── */
.traveller-section {
  padding: 70px 5%;
  background: #f8f4ff;
  text-align: center;
}

.traveller-section h2 {
  font-size: 32px;
  color: #3b053d;
  margin-bottom: 8px;
}

.traveller-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 24px;
  max-width: 1100px;
  margin: 40px auto 0;
}

.traveller-card {
  background: #fff;
  border-radius: 16px;
  padding: 32px 24px 28px;
  width: 190px;
  box-shadow: 0 4px 18px rgba(100,0,120,0.08);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.traveller-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 28px rgba(100,0,120,0.14);
}

.t-icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, #7b0d9b, #991499);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 8px;
}

.t-icon .fa {
  color: #fff;
  font-size: 22px;
}

.traveller-card h4 {
  font-size: 13px;
  font-weight: 700;
  color: #7b0d9b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin: 0;
}

.traveller-card p {
  font-size: 15px;
  font-weight: 600;
  color: #222;
  margin: 0;
}

.traveller-card span {
  font-size: 12px;
  color: #888;
  line-height: 1.4;
}

.dark-mode .traveller-section { background: #1a0a1e; }
.dark-mode .traveller-section h2 { color: #e0b0ff; }
.dark-mode .traveller-card { background: #2a1030; box-shadow: 0 4px 18px rgba(0,0,0,0.3); }
.dark-mode .traveller-card p { color: #eee; }
.dark-mode .traveller-card span { color: #aaa; }

/* ── Join / Benefits section ── */
.join-section {
  background: linear-gradient(135deg, #3b053d 0%, #7b0d9b 100%);
  padding: 80px 5%;
}

.join-inner {
  max-width: 1100px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 60px;
}

.join-text { flex: 1; }

.join-text h2 {
  font-size: 38px;
  color: #fff;
  margin-bottom: 12px;
  line-height: 1.2;
}

.join-sub {
  font-size: 17px;
  color: rgba(255,255,255,0.75);
  margin-bottom: 28px;
  line-height: 1.6;
}

.join-benefits {
  list-style: none;
  padding: 0;
  margin-bottom: 36px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.join-benefits li {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  color: rgba(255,255,255,0.9);
  font-size: 16px;
  line-height: 1.5;
}

.join-benefits li i {
  color: #f0c0f0;
  font-size: 18px;
  flex-shrink: 0;
  margin-top: 2px;
}

.join-actions {
  display: flex;
  align-items: center;
  gap: 24px;
  flex-wrap: wrap;
}

.join-btn-primary {
  display: inline-block;
  padding: 15px 32px;
  background: #fff;
  color: #6b0487;
  font-size: 15px;
  font-weight: 700;
  border-radius: 50px;
  text-decoration: none;
  transition: background 0.3s, transform 0.2s;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

.join-btn-primary:hover { background: #f0e0f8; transform: translateY(-2px); }

.join-already {
  color: rgba(255,255,255,0.7);
  font-size: 14px;
}

.join-already a {
  color: #f0c0f0;
  font-weight: 600;
  text-decoration: none;
}

.join-already a:hover { text-decoration: underline; }

.submit-cta { margin-top: 16px; }

.submit-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: rgba(255,255,255,0.7);
  font-size: 14px;
  text-decoration: none;
  border: 1px solid rgba(255,255,255,0.25);
  padding: 8px 18px;
  border-radius: 50px;
  transition: all 0.2s;
}

.submit-link:hover {
  background: rgba(255,255,255,0.1);
  color: #fff;
  border-color: rgba(255,255,255,0.5);
}

.join-visual {
  flex-shrink: 0;
  font-size: 140px;
  color: rgba(255,255,255,0.08);
  line-height: 1;
  user-select: none;
}

/* ── Footer ── */
footer { background: linear-gradient(135deg, #1a001a 0%, #2d003a 100%); color: #fff; padding: 60px 5% 0; }

.footer-inner {
  display: flex;
  gap: 48px;
  flex-wrap: wrap;
  max-width: 1200px;
  margin: 0 auto;
  padding-bottom: 40px;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

.footer-brand { flex: 2; min-width: 160px; }
.footer-logo { font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; color: #e8a0e8; display: block; margin-bottom: 10px; }
.footer-brand p { font-size: 14px; color: rgba(255,255,255,0.6); margin: 0; }

.footer-links { flex: 1; min-width: 120px; display: flex; flex-direction: column; gap: 8px; }
.footer-links h4 { font-size: 13px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 4px; }
.footer-links a { color: rgba(255,255,255,0.8); text-decoration: none; font-size: 14px; transition: color 0.2s; }
.footer-links a:hover { color: #e8a0e8; }

.footer-social { flex: 1; min-width: 120px; }
.footer-social h4 { font-size: 13px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 12px; }

.social-icons { display: flex; gap: 14px; }
.social-icons a {
  width: 38px; height: 38px; border-radius: 50%;
  background: rgba(255,255,255,0.1);
  display: flex; align-items: center; justify-content: center;
  color: #fff; text-decoration: none; font-size: 15px;
  transition: background 0.2s, transform 0.2s;
}
.social-icons a:hover { background: #991499; transform: translateY(-2px); }

.footer-bottom { max-width: 1200px; margin: 0 auto; padding: 18px 0; text-align: center; }
.footer-bottom p { font-size: 13px; color: rgba(255,255,255,0.4); margin: 0; }

/* ── Dropdown ── */
.dropbtn { color: #fff; font-size: 13px; font-weight: 500; letter-spacing: 0.8px; padding: 8px 12px; background: none; border: none; cursor: pointer; text-decoration: none; }
.dropdown { position: relative; display: inline-block; }
.dropdown-content {
  display: none; position: absolute; background: #3b053d;
  min-width: 160px; box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  z-index: 10; border-radius: 8px; overflow: hidden;
  opacity: 0; transform: translateY(-8px);
  transition: opacity 0.25s, transform 0.25s;
}
.dropdown-content a { color: #fff; padding: 11px 16px; text-decoration: none; display: block; font-size: 13px; }
.dropdown-content a:hover { background: rgba(255,255,255,0.1); }
.dropdown-content.show { display: block; opacity: 1; transform: translateY(0); }

/* ── Weather section ── */
.weather-section {
  background: #f0f2f8;
  padding: 70px 5%;
}

.dark-mode .weather-section { background: #0e0e1e; }

.weather-section-inner {
  max-width: 1100px;
  margin: 0 auto;
}

.weather-section h2 {
  font-size: 36px;
  color: #1a1a2e;
  margin-bottom: 32px;
  text-align: center;
}

.dark-mode .weather-section h2 { color: #fff; }

.weather-main {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  align-items: stretch;
}

.weather-current {
  background: linear-gradient(135deg, #1a003d 0%, #3b053d 100%);
  border-radius: 20px;
  padding: 30px 26px;
  color: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  flex-shrink: 0;
  min-width: 170px;
  box-shadow: 0 4px 20px rgba(59,5,61,0.25);
}

.wc-icon .fa { font-size: 52px; }

.wc-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 4px;
}

.wc-temp {
  font-size: 46px;
  font-weight: 700;
  line-height: 1;
  font-family: 'Inter', sans-serif;
}

.wc-desc { font-size: 14px; color: rgba(255,255,255,0.8); }

.wc-stats {
  display: flex;
  gap: 14px;
  font-size: 12px;
  color: rgba(255,255,255,0.65);
  margin-top: 6px;
}

.wc-label {
  font-size: 10px;
  color: rgba(255,255,255,0.4);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-top: 4px;
}

.weather-daily {
  display: flex;
  gap: 10px;
  flex: 1;
  overflow-x: auto;
  padding-bottom: 2px;
}

.wd-card {
  background: #fff;
  border-radius: 16px;
  padding: 18px 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
  flex: 1;
  min-width: 90px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.07);
  transition: transform 0.2s, box-shadow 0.2s;
}

.wd-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(59,5,61,0.12); }

.dark-mode .wd-card { background: #1e1e2e; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }

.wd-dayname {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #888;
}

.wd-card .fa { font-size: 22px; color: #7b0d9b; }

.wd-high { font-size: 18px; font-weight: 700; color: #1a1a2e; }
.dark-mode .wd-high { color: #fff; }

.wd-low { font-size: 13px; color: #999; }

.wd-precip { font-size: 11px; color: #5b9bd5; }

/* Hourly strip */
.weather-hourly-wrap {
  background: #fff;
  border-radius: 16px;
  overflow-x: auto;
  box-shadow: 0 2px 10px rgba(0,0,0,0.07);
}

.dark-mode .weather-hourly-wrap { background: #1e1e2e; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }

.weather-hourly {
  display: flex;
  padding: 14px 8px;
  min-width: max-content;
}

.wh-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  padding: 8px 14px;
  border-right: 1px solid rgba(0,0,0,0.06);
  min-width: 68px;
}

.dark-mode .wh-item { border-right-color: rgba(255,255,255,0.06); }
.wh-item:last-child { border-right: none; }

.wh-time {
  font-size: 11px;
  font-weight: 700;
  color: #999;
}

.wh-item .fa { font-size: 17px; color: #7b0d9b; }

.wh-temp { font-size: 16px; font-weight: 600; color: #1a1a2e; }
.dark-mode .wh-temp { color: #fff; }

.wh-precip { font-size: 10px; color: #5b9bd5; }

/* ── Weather widget ── */
.weather-widget {
  position: absolute;
  bottom: 36px;
  left: 5%;
  display: flex;
  align-items: center;
  gap: 16px;
  background: rgba(255,255,255,0.12);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 18px;
  padding: 16px 24px;
  color: #fff;
  min-width: 230px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.18);
}

.weather-icon .fa {
  font-size: 44px;
  filter: drop-shadow(0 2px 6px rgba(0,0,0,0.25));
}

.weather-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.weather-temp {
  font-size: 30px;
  font-weight: 700;
  line-height: 1;
  font-family: 'Inter', sans-serif;
}

.weather-desc {
  font-size: 13px;
  color: rgba(255,255,255,0.85);
}

.weather-details {
  font-size: 12px;
  color: rgba(255,255,255,0.7);
  margin-top: 5px;
}

.weather-city {
  font-size: 10px;
  color: rgba(255,255,255,0.5);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  margin-top: 2px;
}

@media (max-width: 1024px) and (min-width: 701px) {
  .news-grid { grid-template-columns: repeat(2, 1fr); }
}

/* ── Mobile ── */
@media (max-width: 700px) {
  .text-box h1 { font-size: 38px; }
  .text-box p { font-size: 16px; }
  .row { flex-direction: column; }

  .nav-links {
    position: fixed; background: #3b053d;
    height: 100vh; width: 240px; top: 0; right: -240px;
    text-align: left; z-index: 1000; transition: 0.4s;
    overflow-y: auto;
  }
  .nav-links ul li { display: block; }
  .nav-links ul { padding: 30px 20px; }
  nav .fa-bars { display: block; color: #fff; font-size: 22px; cursor: pointer; }
  .nav-links .fa-times { display: block; color: #fff; font-size: 22px; cursor: pointer; padding: 20px 20px 0; }

  .lang-switcher { gap: 3px; }
  .lang-switcher button { padding: 4px 7px; font-size: 10px; }

  .join-inner { flex-direction: column; gap: 32px; }
  .join-visual { display: none; }
  .join-text h2 { font-size: 28px; }

  .video-grid { flex-direction: column; }
  .video-grid video { max-width: 100%; }
  .footer-inner { flex-direction: column; gap: 28px; }
  .news-grid { grid-template-columns: 1fr; }

  .traveller-grid { gap: 14px; }
  .traveller-card { width: calc(50% - 7px); padding: 22px 14px 18px; }

  .join-actions { flex-direction: column; align-items: flex-start; gap: 14px; }

  .weather-widget {
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    min-width: unset;
    width: calc(100% - 40px);
    max-width: 340px;
  }

  .weather-section { padding: 50px 5%; }
  .weather-section h2 { font-size: 28px; margin-bottom: 24px; }
  .weather-main { flex-direction: column; }
  .weather-current {
    flex-direction: row;
    padding: 20px;
    gap: 20px;
    align-items: center;
    min-width: unset;
  }
  .wc-icon .fa { font-size: 40px; }
  .wc-info { align-items: flex-start; text-align: left; }
  .wd-card { min-width: 78px; padding: 14px 8px; }
  .wh-item { padding: 8px 10px; min-width: 58px; }
}
</style>
