<template>
  <div :class="{ 'dark-mode': isDark }">
    <div class="fav-hero">
      <nav>
        <a href="/" class="brand">ExploreRiga</a>
        <div class="nav-links" id="navLinksFav">
          <i class="fa fa-times" @click="hideMenu"></i>
          <ul>
            <li class="dropdown">
              <a href="#" class="dropbtn" @click.prevent="dropdownOpen = !dropdownOpen">Par mums</a>
              <div class="dropdown-content" :class="{ show: dropdownOpen }">
                <a href="/contacts">Kontakti</a>
                <a href="/team">Komanda</a>
                <a href="/information">Informācija</a>
              </div>
            </li>
            <li><router-link to="/">Sākums</router-link></li>
            <li><router-link to="/museums">Muzeji</router-link></li>
            <li><router-link to="/restaurants">Restorāni</router-link></li>
            <li><router-link to="/culture">Kultūra</router-link></li>
            <li><router-link to="/favorites" class="active-link">Izlase</router-link></li>
            <li v-if="isAuth"><router-link to="/todo">Uzdevumi</router-link></li>
            <li v-else><router-link to="/signin">Pieslēgties</router-link></li>
            <li v-if="isAuth"><a href="#" @click.prevent="handleSignOut">Iziet</a></li>
            <li><button class="dark-mode-o" @click="toggleDarkMode"><i :class="isDark ? 'fa fa-sun' : 'fa fa-moon'"></i></button></li>
          </ul>
        </div>
        <i class="fa fa-bars" @click="showMenu"></i>
      </nav>
      <div class="hero-text">
        <h1>Mana izlase</h1>
        <p>Saglabātās vietas Rīgā</p>
      </div>
    </div>

    <div class="favorites-wrap">
      <div v-if="!favorites.length" class="empty-state">
        <i class="fa fa-heart-o empty-icon"></i>
        <h2>Nav saglabātu vietu</h2>
        <p>Pievienojiet vietas izlasei, nospiežot sirds ikonu muzeju, restorānu vai kultūras vietu lapās.</p>
        <div class="empty-links">
          <router-link to="/museums" class="empty-btn">Muzeji</router-link>
          <router-link to="/restaurants" class="empty-btn">Restorāni</router-link>
          <router-link to="/culture" class="empty-btn">Kultūra</router-link>
        </div>
      </div>

      <template v-else>
        <template v-for="group in groups" :key="group.type">
          <template v-if="group.items.length">
            <h2 class="group-title">{{ group.label }}</h2>
            <div class="fav-grid">
              <div v-for="fav in group.items" :key="fav.key" class="fav-card">
                <div class="fav-img-wrap">
                  <img :src="fav.image" :alt="fav.name" />
                  <span class="type-badge" :class="fav.type">{{ typeLabel(fav.type) }}</span>
                </div>
                <div class="fav-body">
                  <h3>{{ fav.name }}</h3>
                  <p class="fav-address"><i class="fa fa-map-marker"></i> {{ fav.address }}</p>
                  <p class="fav-desc">{{ fav.description }}</p>
                  <div class="fav-actions">
                    <a :href="fav.link" target="_blank" class="fav-visit">Apmeklēt →</a>
                    <button @click="removeFavorite(fav.key)" class="fav-remove">
                      <i class="fa fa-heart"></i> Noņemt
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </template>
      </template>
    </div>

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

<script setup>
import { computed, ref } from 'vue'
import { isAuthenticated, signOut } from '@/auth'
import { useRouter } from 'vue-router'
import { useFavorites } from '@/composables/useFavorites'
import { useDarkMode } from '@/composables/useDarkMode'

const { favorites, removeFavorite } = useFavorites()
const isAuth = isAuthenticated
const router = useRouter()
const dropdownOpen = ref(false)
const { isDark, toggleDarkMode } = useDarkMode()

function handleSignOut() {
  signOut()
  router.push('/signin')
}
function showMenu() {
  const el = document.getElementById('navLinksFav')
  if (el) el.style.right = '0'
}
function hideMenu() {
  const el = document.getElementById('navLinksFav')
  if (el) el.style.right = '-240px'
}

function typeLabel(type) {
  return { museum: 'Muzejs', restaurant: 'Restorāns', culture: 'Kultūra' }[type] || ''
}

const groups = computed(() => [
  { type: 'museum', label: 'Muzeji', items: favorites.value.filter(f => f.type === 'museum') },
  { type: 'restaurant', label: 'Restorāni', items: favorites.value.filter(f => f.type === 'restaurant') },
  { type: 'culture', label: 'Kultūras vietas', items: favorites.value.filter(f => f.type === 'culture') },
])
</script>

<style scoped>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
h1, h2, h3 { font-family: 'Playfair Display', serif; }

/* ── Hero ── */
.fav-hero {
  width: 100%;
  min-height: 340px;
  background-image: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
    url('https://images.unsplash.com/photo-1519677100203-a0e668c92439?w=1600&q=80');
  background-position: center;
  background-size: cover;
  padding: 0 6%;
  display: flex;
  flex-direction: column;
}

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
.nav-links ul li { list-style: none; display: inline-block; padding: 8px 12px; position: relative; }
.nav-links ul li a { color: #fff; text-decoration: none; font-size: 13px; font-weight: 500; letter-spacing: 0.8px; text-transform: uppercase; }
.nav-links ul li::after { content: ''; width: 0%; height: 2px; background: #c847d8; display: block; margin: auto; transition: 0.4s; }
.nav-links ul li:hover::after { width: 100%; }
nav .fa-bars { display: none; }
.nav-links .fa-times { display: none; }
.active-link { color: #e8a0e8 !important; }

.dark-mode-o {
  background: rgba(255,255,255,0.12); color: white; padding: 7px 14px;
  font-size: 14px; cursor: pointer; border: 1px solid rgba(255,255,255,0.25);
  border-radius: 8px; transition: background 0.3s;
}
.dark-mode-o:hover { background: rgba(255,255,255,0.22); }

.dropbtn {
  color: #fff; text-decoration: none; font-size: 13px; font-weight: 500;
  letter-spacing: 0.8px; text-transform: uppercase; background: none; border: none; cursor: pointer; padding: 8px 12px;
}
.dropdown { position: relative; display: inline-block; }
.dropdown-content {
  display: none; position: absolute; background: #3b053d;
  min-width: 160px; box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  z-index: 10; border-radius: 8px; overflow: hidden;
  opacity: 0; transform: translateY(-8px); transition: opacity 0.25s, transform 0.25s;
}
.dropdown-content a { color: #fff; padding: 11px 16px; text-decoration: none; display: block; font-size: 13px; }
.dropdown-content a:hover { background: rgba(255,255,255,0.1); }
.dropdown-content.show { display: block; opacity: 1; transform: translateY(0); }

.hero-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 20px 0 40px;
}
.hero-text h1 { color: #fff; font-size: 64px; margin-bottom: 16px; line-height: 1.1; }
.hero-text p { color: rgba(255,255,255,0.85); font-size: 18px; }

/* ── Content ── */
.favorites-wrap {
  padding: 56px 6% 80px;
  min-height: 300px;
  background: #faf7fc;
}

/* ── Empty state ── */
.empty-state {
  text-align: center;
  padding: 80px 20px;
}
.empty-icon {
  font-size: 64px;
  color: #ddd;
  display: block;
  margin-bottom: 20px;
}
.empty-state h2 {
  font-size: 28px;
  color: #3b053d;
  margin-bottom: 12px;
}
.empty-state p {
  color: #888;
  font-size: 16px;
  max-width: 460px;
  margin: 0 auto 32px;
  line-height: 1.65;
}
.empty-links { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
.empty-btn {
  background: #991499; color: #fff; padding: 12px 28px;
  border-radius: 50px; text-decoration: none; font-size: 14px;
  font-weight: 600; transition: background 0.3s, transform 0.2s;
}
.empty-btn:hover { background: #6b0487; transform: translateY(-2px); }

/* ── Group title ── */
.group-title {
  font-size: 28px;
  color: #3b053d;
  margin-bottom: 24px;
  padding-bottom: 10px;
  border-bottom: 2px solid #e8d0f5;
}

/* ── Favorites grid ── */
.fav-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  margin-bottom: 56px;
}

.fav-card {
  background: #fff;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  transition: box-shadow 0.3s, transform 0.3s;
  display: flex;
  flex-direction: column;
}
.fav-card:hover {
  box-shadow: 0 10px 36px rgba(153,20,153,0.14);
  transform: translateY(-4px);
}

.fav-img-wrap {
  position: relative;
  height: 200px;
  overflow: hidden;
}
.fav-img-wrap img {
  width: 100%; height: 100%; object-fit: cover;
  transition: transform 0.4s;
}
.fav-card:hover .fav-img-wrap img { transform: scale(1.05); }

.type-badge {
  position: absolute; top: 12px; left: 12px;
  padding: 4px 10px; border-radius: 20px;
  font-size: 11px; font-weight: 700; letter-spacing: 0.4px;
  color: #fff;
}
.type-badge.museum { background: #991499; }
.type-badge.restaurant { background: #e67e22; }
.type-badge.culture { background: #2980b9; }

.fav-body {
  padding: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
}
.fav-body h3 { font-size: 18px; color: #1a1a2e; margin-bottom: 8px; }
.fav-address { font-size: 13px; color: #888; margin-bottom: 8px; }
.fav-address .fa-map-marker { color: #991499; margin-right: 5px; }
.fav-desc { font-size: 14px; color: #666; line-height: 1.55; flex: 1; margin-bottom: 16px; }

.fav-actions { display: flex; gap: 10px; align-items: center; margin-top: auto; }
.fav-visit {
  flex: 1; text-align: center; background: #991499; color: #fff;
  padding: 9px 16px; border-radius: 50px; text-decoration: none;
  font-size: 13px; font-weight: 600; transition: background 0.3s;
}
.fav-visit:hover { background: #6b0487; }
.fav-remove {
  background: none; border: 2px solid #991499; color: #991499;
  padding: 9px 14px; border-radius: 50px; cursor: pointer;
  font-size: 13px; font-weight: 600; transition: all 0.25s;
  white-space: nowrap;
}
.fav-remove:hover { background: #991499; color: #fff; }
.fav-remove .fa-heart { color: #991499; margin-right: 4px; }
.fav-remove:hover .fa-heart { color: #fff; }

/* ── Footer ── */
footer {
  background: linear-gradient(135deg, #1a001a 0%, #2d003a 100%);
  color: #fff; padding: 30px 5%; text-align: center;
}
.footer-content p { margin: 0 0 10px; font-size: 13px; color: rgba(255,255,255,0.5); }
.social-links a { color: rgba(255,255,255,0.7); text-decoration: none; margin: 0 10px; font-size: 14px; }
.social-links a:hover { color: #e8a0e8; }

/* ── Dark mode ── */
.dark-mode { background-color: #121212; color: #fff; }
.dark-mode .favorites-wrap { background: #0d0d0d; }
.dark-mode .group-title { color: #f0e6ff; border-bottom-color: #3a1a4a; }
.dark-mode .fav-card { background: #1e1e1e; box-shadow: 0 4px 20px rgba(0,0,0,0.4); }
.dark-mode .fav-body h3 { color: #f0e6ff; }
.dark-mode .fav-address { color: #999; }
.dark-mode .fav-desc { color: #bbb; }
.dark-mode .fav-remove { border-color: #c084e0; color: #c084e0; }
.dark-mode .fav-remove:hover { background: #7b0d9b; color: #fff; border-color: #7b0d9b; }
.dark-mode .fav-remove .fa-heart { color: #c084e0; }
.dark-mode .empty-state h2 { color: #f0e6ff; }
.dark-mode .empty-state p { color: #aaa; }
.dark-mode footer { background: linear-gradient(135deg, #0d0d0d, #1a001a); }

/* ── Responsive ── */
@media (max-width: 1024px) {
  .fav-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 700px) {
  .fav-grid { grid-template-columns: 1fr; }
  .hero-text h1 { font-size: 40px; }
  .nav-links {
    position: fixed; background: #3b053d; height: 100vh;
    width: 240px; top: 0; right: -240px; text-align: left; z-index: 1000; transition: 0.4s;
  }
  .nav-links ul li { display: block; }
  .nav-links ul { padding: 30px 20px; }
  nav .fa-bars { display: block; color: #fff; font-size: 22px; cursor: pointer; }
  .nav-links .fa-times { display: block; }
}
</style>
