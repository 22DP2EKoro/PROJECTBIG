<template>
  <div :class="{ 'dark-mode': isDarkMode }">
    <div class="res-container">
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

      <div class="roww">
        <div class="col">
          <h1>Restaurants</h1>
          <p>Restaurants provide a space to enjoy delicious food and experience different cuisines.</p>
        </div>
      </div>
    </div>

    <div class="locations">
      <h1>Locations</h1>
      <p>
        A great location is easily accessible, either centrally situated in a city or near well-known landmarks, with
        convenient public transport links or parking.
      </p>
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d139178.6403659644!2d23.964268579406752!3d56.97164918740555!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46eecfb0e5073ded%3A0x400cfcd68f2fe30!2zUsSrZ2E!5e0!3m2!1slv!2slv!4v1727600341753!5m2!1slv!2slv"
        loading="lazy"></iframe>
    </div>
    <section class="search-section">
      <input type="text" v-model="searchQuery" placeholder="Search restaurants..." class="search-bar" />
    </section>

    <section class="places">
  <h1>Places to visit</h1>
  <div class="sort-options">
    <label for="sortOrder">Sort by:</label>
    <select id="sortOrder" v-model="sortOrder">
      <option value="asc">A–Z</option>
      <option value="desc">Z–A</option>
    </select>
  </div>

  <div class="rowww" v-for="(row, index) in filteredRestaurantRows" :key="index">
    <div class="places-col1" v-for="place in row" :key="place.title">
      <img :src="place.image" :alt="place.title" />
      <h3>{{ place.title }}</h3>
      <p>{{ place.description }}</p>
      
      <div class="comment-section">
        <h4>Leave a review</h4>
        <button @click="toggleCommentForm(place.title)">
          {{ showCommentForm[place.title] ? 'Hide Review Form' : 'Leave a Review' }}
        </button>

        <!-- Only show the form when toggled -->
        <div v-if="showCommentForm[place.title]" class="review-form">
          <textarea v-model="newComments[place.title]" placeholder="Write your comment..."></textarea>
          <select v-model="newRatings[place.title]">
            <option disabled value="">Rate</option>
            <option v-for="n in 5" :key="n" :value="n">{{ n }} stars</option>
          </select>
          <button @click="submitReview(place.title)">Submit</button>
        </div>

        <div class="reviews">
          <h4>Reviews:</h4>
          <div v-for="(review, idx) in reviews[place.title] || []" :key="idx" class="review">
            <p><strong>Rating:</strong> {{ review.rating }}★</p>
            <p>{{ review.comment }}</p>
          </div>
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

const searchQuery = ref('')
const sortOrder = ref('asc')
const newComments = ref({})
const newRatings = ref({})
const reviews = ref({})
const showCommentForm = ref({})
import { isAuthenticated, signOut } from '@/auth'
import { useRouter } from 'vue-router'

const router = useRouter()

function handleSignOut() {
  signOut()
  router.push('/signin')
}

const toggleCommentForm = (placeTitle) => {
  showCommentForm.value[placeTitle] = !showCommentForm.value[placeTitle]
}


const submitReview = (placeTitle) => {
  if (!newComments.value[placeTitle] || !newRatings.value[placeTitle]) return
  const newReview = {
    comment: newComments.value[placeTitle],
    rating: newRatings.value[placeTitle],
  }

  if (!reviews.value[placeTitle]) {
    reviews.value[placeTitle] = []
  }

  reviews.value[placeTitle].push(newReview)
  newComments.value[placeTitle] = ''
  newRatings.value[placeTitle] = ''
}

// Example restaurants (you can move this to a separate file or fetch from API)
const restaurantRows = [
  [
    {
      title: 'Riviera',
      image: 'https://th.bing.com/th/id/OIP.pzytZZsfO5kqiWnbvab0kQHaE8?w=232&h=180&c=7&r=0&o=5&dpr=1.3&pid=1.7',
      description:
        'Resautrant Riviera is created according to the best traditions of Mediterranean cuisine. This is very special place...',
    },
    {
      title: 'Max Cekot Kitchen',
      image: 'https://tendences.lv/wp-content/uploads/2023/10/fabrikas-restorans-ediens-1024x683.jpg',
      description: 'Restaurant offers a fascinating journey through kitchen.',
    },
    {
      title: 'Tails',
      image: 'https://tendences.lv/wp-content/uploads/2023/10/735a57e42ca9e59861b13fa7aa5f5cf8.jpg',
      description: 'This is very special place, designed in peaceful colors with stone mosaics.',
    },
  ],
  [
    {
      title: 'The Catch',
      image: 'https://tendences.lv/wp-content/uploads/2023/10/64e352a625f1f.jpg',
      description:
        'The Catch apvieno tradīcijas un inovācijas, piedāvājot autentiskas Japāņu garšas, piemēram, robatayaki grilētus ēdienus, sushi un tempuru.',
    },
    {
      title: 'Le Dome',
      image: 'https://tendences.lv/wp-content/uploads/2023/10/entresol-restorans-1068x712.jpg',
      description: 'Le Dome piedāvā franču kulinārijas eleganci Rīgas vecpilsētas sirdī.',
    },
    {
      title: 'Entresol',
      image: 'https://tendences.lv/wp-content/uploads/2023/10/59215ecd4d4602502e124e7369358d96.jpg',
      description: 'Entresol ir pazīstams ar savu unikālo pieeju nelielu, bet bagātīgu ēdienu pasniegšanā.',
    },
  ],
  [
    {
      title: 'Trīs pavāru restorāns',
      image: 'https://tendences.lv/wp-content/uploads/2023/10/Krassky_Barents_Restaurant_3.jpg',
      description:
        'Trīs pavāru restorāns ir gastronomiska pieredze, kur ēdiens tiek gatavots viesu acu priekšā...',
    },
    {
      title: 'Barents',
      image: 'https://tendences.lv/wp-content/uploads/2023/10/tails-restorans-1024x683.jpg',
      description: 'Barents piedāvā Ziemeļu kulinārijas piedzīvojumu ar minimālistisku, taču elegantu interjeru.',
    },
    {
      title: 'Lido',
      image: 'https://th.bing.com/th/id/OIP.tIFPS4QsnbmXsR7XYM_LvQAAAA?w=284&h=190&c=7&r=0&o=5&dpr=1.3&pid=1.7',
      description: 'Vislabākais.',
    },
  ],
]
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

const isDarkMode = ref(false)
const navVisible = ref(false)
const dropdownVisible = ref(false)

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
}

const toggleNav = (show) => {
  navVisible.value = show
}

const toggleDropdown = (e) => {
  dropdownVisible.value = !dropdownVisible.value
  e.stopPropagation()
}

onMounted(() => {
  window.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown')) {
      dropdownVisible.value = false
    }
  })
})


</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  font-family: 'Merriweather', serif;
  box-sizing: border-box;
}

/* Dark Mode */
.dark-mode {
  background-color: #121212;
  color: #ffffff;
}

.dark-mode footer p {
  color: #ffffff;
}

.dark-mode h3,
.dark-mode p {
  color: #000000;
}

.dark-mode .row h3,
.dark-mode .row p {
  color: #000000
}

.dark-mode .roww col {
  color: #ffffff
}

.dark-mode .locations h1,
p {
  color: #ffffff;
  text-align: center;
}

.dark-mode .locations p {
  color: #ffffff;
  text-align: center;
}

.dark-mode .places h1 {
  color: #ffffff;
}

.sort-options {
  text-align: center;
  margin-bottom: 20px;
}

.sort-options label {
  font-size: 16px;
  margin-right: 10px;
}

.sort-options select {
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.res-container {
  width: 100%;
  height: 100vh;
  background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
    url("https://th.bing.com/th/id/R.d25f5421697c2fd1c4fcdd5ff0e650c5?rik=6VxmyHlyxl8btw&pid=ImgRaw&r=0");
  background-position: center;
  background-size: cover;
  padding-left: 8%;
  padding-right: 8%;
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

.nav-links ul li {
  list-style: none;
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

nav .fa {
  display: none;
}

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
  color: #ffffff;
  font-size: 80px;
  margin-bottom: 20px;
}

.roww p {
  margin: 10px 0 40px;
  font-size: 24px;
  color: #fff;
}


.locations h1,
p {
  color: #000000;
  text-align: center;


}




.locations iframe {
  width: 100%;
  max-width: 1200px;
  height: 400px;
  /* Set a fixed height */
  display: block;
  margin: auto;
}


.places {
  width: 80%;
  max-width: 1200px;
  margin: auto;
  text-align: center;
  padding-top: 100px;
}

.places h1 {
  font-size: 36px;
  font-weight: 600;
  color: #000000
}

.places p {
  color: #000000;
  font-size: 24px;
  font-weight: 300;
  line-height: 22px;
  padding: 10px;

}

.row {
  display: flex;
  justify-content: space-between;
  align-items: stretch;
  /* Make all items the same height */
  flex-wrap: wrap;
}

.places-col1 {
  flex-basis: 31%;
  background: #fff3f3;
  border-radius: 10px;
  margin-bottom: 5%;
  padding: 20px 12px;
  box-sizing: border-box;
  transition: 0.5s;
  display: flex;
  flex-direction: column;
  /* Ensures content is stacked properly */
  min-height: 400px;
  /* Set a minimum height */
}


.places-col img {
  width: 100%;
  max-width: 100%;
  height: auto;
  border-radius: 10px;
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
  margin: auto;
  font-size: 14px;
  padding: 10px;
  color: #fff;
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

.search-section {
  text-align: center;
  margin-top: 40px;
}

.search-bar {
  padding: 12px 20px;
  width: 80%;
  max-width: 500px;
  font-size: 16px;
  border: 2px solid #ccc;
  border-radius: 8px;
  outline: none;
  transition: 0.3s;
}

.search-bar:focus {
  border-color: #991499;
  box-shadow: 0 0 8px rgba(153, 20, 153, 0.2);
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

.comment-section {
  margin-top: 20px;
  border-top: 1px solid #ccc;
  padding-top: 10px;
}

.comment-section textarea {
  width: 100%;
  height: 60px;
  padding: 8px;
  margin-bottom: 8px;
}

.comment-section select {
  margin-bottom: 10px;
}

.comment-section button {
  background-color: #991499;
  color: #fff;
  padding: 6px 12px;
  border: none;
  cursor: pointer;
}

.review {
  background: #f5f5f5;
  padding: 8px;
  margin-top: 5px;
  border-radius: 4px;
}



@media(max-width: 700px) {
  .nav-links ul li {
    display: block;
  }

  .row {
    flex-direction: column;
    align-items: center;
  }

  .places-col {
    flex-basis: 100%;
    min-height: auto;
    /* Adjust for smaller screens */
  }

  .locations iframe {
    height: 300px;
    /* Reduce height for smaller screens */
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

/* Dark Mode Button */
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
</style>
