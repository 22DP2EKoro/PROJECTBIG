<template>
  <div :class="{ 'dark-mode': isDarkMode }">
    <section class="home-header">
      <nav>
        <div class="nav-links" :style="{ right: navVisible ? '0' : '-200px' }">
          <i class="fa fa-times" @click="hideMenu"></i>
          <ul>
            <li class="dropdown">
              <a href="#" class="dropbtn" @click.prevent="toggleDropdown">ABOUT US</a>
              <div class="dropdown-content" :class="{ show: dropdownOpen }">
                <a href="/contacts">CONTACTS</a>
                <a href="/team">TEAM</a>
                <a href="/information">INFORMATION</a>
              </div>
            </li>
            <li><router-link to="/">HOME</router-link></li>
            <li><router-link to="/museums">MUSEUMS</router-link></li>
            <li><router-link to="/restaurants">RESTAURANTS</router-link></li>
            <li><router-link to="/culture">CULTURE</router-link></li>
            <li v-if="auth"><router-link to="/todo">TO-DO</router-link></li>
            <li v-else><router-link to="/signin">SIGN IN</router-link></li>
            <li v-if="auth">
              <a href="#" @click.prevent="handleSignOut">SIGN OUT</a>
            </li>

          </ul>
        </div>
        <button class="dark-mode-o" @click="toggleDarkMode">Dark mode</button>
        <i class="fa fa-bars" @click="showMenu"></i>
      </nav>
      <div class="text-box">
        <h1>Explore Riga</h1>
        <p>Comprehensive guide to the city of Riga's cultural events and local dining experiences.</p>
      </div>
    </section>

    <section class="karte">
      <h1>Riga</h1>
      <p>
        Riga is the capital and largest city of Latvia...
      </p>
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2244.1234567890123!2d24.1052!3d56.9496!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46eecb8a8a8a8a8a%3A0x8a8a8a8a8a8a8a8a!2sRiga%2C%20Latvia!5e0!3m2!1sen!2sus!4v1616161616161"
        width="600" height="450" style="border:0;" allowfullscreen loading="lazy"></iframe>
    </section>

    <section class="places1">
      <h1>Places to visit</h1>
      <p>Exploring new places offers a unique opportunity to experience diverse cultures...</p>
      <div class="row">
        <div class="places-col" v-for="(place, index) in places" :key="index">
          <img :src="place.img" :alt="place.title" />
          <h3>{{ place.title }}</h3>
          <p>{{ place.description }}</p>
        </div>
      </div>
    </section>

    <section class="tourism_sight">
      <h1>VIRAL TOUR</h1>
      <video controls autoplay muted>
        <source src="https://videos.pexels.com/video-files/8243661/8243661-uhd_2732_1440_24fps.mp4" type="video/mp4" />
        Your browser does not support the video tag.
      </video>
      <video controls autoplay muted>
        <source src="https://videos.pexels.com/video-files/8467767/8467767-hd_1920_1080_30fps.mp4" type="video/mp4" />
        Your browser does not support the video tag.
      </video>
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

<script setup>
import { signOut } from '../auth'
import { isAuthenticated } from '../auth' // Adjust path if needed

import { ref, onMounted } from 'vue'

const isDarkMode = ref(false)
const dropdownOpen = ref(false)
const navVisible = ref(false)
const auth = isAuthenticated

function toggleDarkMode() {
  isDarkMode.value = !isDarkMode.value
}

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value
}

function showMenu() {
  navVisible.value = true
}

function hideMenu() {
  navVisible.value = false
}


function handleSignOut() {
  signOut()
}



onMounted(() => {
  window.addEventListener('click', (e) => {
    if (!e.target.classList.contains('dropbtn')) {
      dropdownOpen.value = false
    }
  })
})

const places = [
  {
    img: 'https://s.inyourpocket.com/gallery/257731.jpg',
    title: 'Restaurants',
    description: 'Restaurants provide a space to enjoy delicious food, experience different cuisines.',
  },
  {
    img: 'https://assets.architecturaldigest.in/photos/60081fc808ae763b9ae83f5f/16:9/w_2560%2Cc_limit/The-building-of-the-Brotherhood-of-Blackheads-is-one-of-the-most-iconic-buildings-of-Old-Riga--1366x768.jpg',
    title: 'Museums',
    description: 'Museums offer a fascinating journey through history.',
  },
  {
    img: 'https://www.latvia.travel/sites/default/files/styles/max_1300x1300/public/media_image/tourism_sight/44256507012_666bad3106_k.jpg?itok=D0Mi0rK_',
    title: 'Parks',
    description: 'Parks provide a peaceful escape into nature...',
  },
]
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  font-family: 'Merriweather', serif;
  box-sizing: border-box;

}

/* Dark mode*/
.dark-mode {
  background-color: #121212;
  color: #ffffff;
}

.dark-mode .karte p {
  color: #ffffff;
}


.dark-mode .places-col {
  background-color: #333333;
}

.dark-mode footer {
  background-color: #1c1c1c;
}

.dark-mode .text-box h1 {
  color: #ffffff;
}

.dark-mode .text-box p {
  color: #ffffff;
}

.dark-mode .places-col {
  background-color: #333333;
}

.dark-mode .tourism_sight {
  background-color: #1c1c1c;
}

.dark-mode .tourism_sight h1 {
  color: #fff
}

.dark-mode footer {
  background-color: #151515;
}

.dark-mode-o {
  background-color: #3b053d;
  color: white;
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.3s;
}

.dark-mode-o:hover {
  background-color: #7b8186;
  transform: translateY(-1px);
}

.dark-mode .places1 p {
  color: #ffffff;
}

.home-header {
  min-height: 100vh;
  width: 100%;
  background-image: linear-gradient(rgba(4, 9, 30, 0.7), rgba(4, 9, 30, 0.7)), url("https://goingbaltic.com/wp-content/uploads/2021/10/Latvia_Riga_-Blackheads-house_Saint-Peters-church_shutterstock_136863935-1.jpg");
  background-position: top;
  background-size: cover;
  position: relative;
  background-attachment: fixed;

}

nav {
  display: flex;
  padding: 2% 6%;
  justify-content: space-between;
  align-items: center;


}

.nav-links {
  flex: 1;
  text-align: right;

}

.nav-links ul {
  padding: 0;
  margin: 0;
  list-style: none;

}

.nav-links ul li {
  display: inline-block;
  padding: 10px 15px;
  position: relative;
}

.nav-links ul li a {
  color: #fff;
  text-decoration: none;
  font-size: 20px;
}

.nav-links ul li::after {
  content: '';
  width: 0%;
  height: 2px;
  background: #991499;
  display: block;
  margin: auto;
  transition: 0.5s;
}

.nav-links ul li:hover::after {
  width: 100%;
}

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
  font-size: 62px;
}

.text-box p {
  margin: 10px 0 40px;
  font-size: 25px;
  color: #fff;
}

nav .fa {
  display: none;
}


/*--------karte----------*/

.karte {
  width: 100%;
  max-width: 1200px;
  margin: auto;
  text-align: center;
  padding-top: 100px;



}

.karte p {
  color: #000000;
  font-size: 24px;

}


/*--------- places ------*/


.places1 {
  width: 80%;
  max-width: 1200px;
  margin: auto;
  text-align: center;
  padding-top: 100px;
}

h1 {
  font-size: 36px;
  font-weight: 600;
}

p {
  color: #000000;
  font-size: 24px;
  font-weight: 300;
  line-height: 22px;
  padding: 10px;

}

.row {
  margin-top: 5%;
  display: flex;
  justify-content: space-between;
}

.places-col {
  flex-basis: 31%;
  background: #fff3f3;
  border-radius: 10px;
  margin-bottom: 5%;
  padding: 20px 12px;
  box-sizing: border-box;
  transition: 0.5s;

}

.places-col img {
  width: 100%;
  height: auto;
}

h3 {
  text-align: center;
  font-weight: 600;
  margin: 10px 0;
}

.places-col:hover {
  transform: translateY(-10px);
  box-shadow: 0 0 20px 0px rgba(0, 0, 0, 0.2)
}

.tourism_sight {
  padding: 50px 0;
  text-align: center;
  background-color: #fff;
}

.tourism_sight h1 {
  font-size: 36px;
  color: #000000;
  margin-bottom: 20px;
}

.tourism_sight video {
  width: 80%;
  max-width: 1000px;
  background-color: #fff;
  border-radius: 10px;

}

/*------footer----*/
footer {
  background-color: #333;
  color: #fff;
  padding: 20px 0;
  text-align: center;
  position: relative;
  bottom: 0;
  width: 100%;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
}

.footer-content p {
  margin: 0;
  font-size: 14px;
  color: #ffffff;

}

.social-links a {
  color: #fff;
  text-decoration: none;
  margin: 0 10px;
  transition: color 0.3s ease;
}

.social-links a:hover {
  color: #00aced;
}



/* DROPDOWN */

/* Style for the Contacts button */
.dropbtn {
  color: #fff;
  text-decoration: none;
  font-size: 20px;
  padding: 10px 15px;
  display: inline-block;
  position: relative;
  background: none;
  border: none;
  cursor: pointer;
}


.dropbtn:hover::after {
  width: 100%;
}

/* Dropdown container */
.dropdown {
  position: relative;
  display: inline-block;
}

/* Dropdown content (hidden by default) */
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #991499;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
  z-index: 1;
  transition: opacity 0.3s ease, transform 0.3s ease;
  opacity: 0;
  transform: translateY(-10px);
}

/* Links inside the dropdown */
.dropdown-content a {
  color: #fff;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

/* Hover effect for dropdown links */
.dropdown-content a:hover {
  background-color: #7b125e;
}

/* Show the dropdown */
.dropdown-content.show {
  display: block;
  opacity: 1;
  transform: translateY(0);
}

@media(max-width: 700px) {
  .text-box h1 {
    font-size: 20px;
  }

  .row {
    flex-direction: column;
  }

  .header {
    min-height: 50vh;
  }

  .nav-links ul li {
    display: block;
  }

  .nav-links {
    position: absolute;
    background: #991499;
    height: 100vh;
    width: 200px;
    top: 0;
    right: -200px;
    text-align: left;
    z-index: 2;
    transition: 0.5s;
  }

  nav .fa {
    display: block;
    color: #fff;
    margin: 10px;
    font-size: 22px;
    cursor: pointer;
  }

  .nav-links ul {
    padding: 30px;
  }
}
</style>
