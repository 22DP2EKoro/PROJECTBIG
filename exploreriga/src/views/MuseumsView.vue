<template>
  <div>
    <div class="mus-container">
      <nav>
        <div class="nav-links" id="navLinks">
          <i class="fa fa-times" @click="hideMenu"></i>
          <ul>
            <li class="dropdown">
              <a href="#" class="dropbtn" @click="toggleDropdown($event)">ABOUT US</a>
              <div id="myDropdown" class="dropdown-content" :class="{ show: dropdownOpen }">
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
            <li><button class="dark-mode-o" @click="toggleDarkMode">Dark mode</button></li>
          </ul>
        </div>
        <i class="fa fa-bars" @click="showMenu"></i>
      </nav>

      <div class="row">
        <div class="col">
          <h1>Museums</h1>
          <p>
            Museums preserve culture, educate the public, and inspire future
            generations by showcasing history, art, and science.
          </p>
        </div>
        <div class="col"></div>
      </div>
    </div>

    <div class="search-bar">
      <input type="text" v-model="searchQuery" placeholder="Search museums..." class="search-input" />
    </div>

    <!-- Sorting Dropdown -->
    <div class="sort-dropdown">
      <label for="sortOptions">Sort by:</label>
      <select v-model="sortOption" @change="applySort">
        <option value="alphabetical">Alphabetical (A-Z)</option>
        <option value="reverse-alphabetical">Alphabetical (Z-A)</option>
      </select>
    </div>

    <div class="museums">
      <div class="flex-container" v-for="(museum, index) in filteredMuseums" :key="index">
        <div class="places-col" v-if="index % 2 === 0">
          <img :src="museum.image" :alt="museum.name" />
          <a :href="museum.link" class="visit-button" target="_blank">Visit Website</a>
        </div>
        <div class="text-box">
          <h3>{{ museum.name }}</h3>
          <p>{{ museum.description }}</p>
        </div>
        <div class="places-col" v-if="index % 2 !== 0">
          <img :src="museum.image" :alt="museum.name" />
          <a :href="museum.link" class="visit-button" target="_blank">Visit Website</a>
        </div>
      </div>
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

<script>
import { isAuthenticated, signOut } from '@/auth'
import { useRouter } from 'vue-router'

const router = useRouter()

function handleSignOut() {
  signOut()
  router.push('/signin')
}
export default {
  data() {
    return {
      searchQuery: '',
      dropdownOpen: false,
      sortOption: 'alphabetical', // Default sorting option
      museums: [
        {
          name: "Mākslas muzejs",
          description: "Mākslas muzejs piedāvā plašu izstāžu klāstu. Apmeklē un atklāj jaunas mākslas pasaules!",
          image: "https://www.redzet.eu/img/photo/1576523378.jpg",
          link: "https://www.lnmm.lv/",
        },
        {
          name: "Kara muzejs",
          description: "Kara muzejs piedāvā plašu izstāžu klāstu. Apmeklē un atklāj jaunas mākslas pasaules!",
          image: "https://image.arrivalguides.com/x/18/6c4ac024cb7641392d657ed3543a2f4a.jpg",
          link: "https://www.karamuzejs.lv/",
        },
        {
          name: "Dabas muzejs",
          description: "Dabas muzejs piedāvā plašu izstāžu klāstu. Apmeklē un atklāj jaunas mākslas pasaules!",
          image: "https://skolenuekskursijas.lv/wp-content/uploads/2018/10/DabasmuzejsEkspozicija_Pasaules_ziditaji-790x500.jpg",
          link: "https://www.dabasmuzejs.gov.lv/lv",
        },
        {
          name: "Paula Stradiņa Medicīnas vēstures muzejs",
          description: "Paula Stradiņa Medicīnas vēstures muzejs ir unikāls muzejs, kas piedāvā plašu izstāžu klāstu par medicīnas vēsturi un attīstību.",
          image: "https://th.bing.com/th/id/OIP.A2lY9GR_nroBSfYmaiTPYAHaE-?w=250&h=180&c=7&r=0&o=5&cb=iwc1&dpr=1.3&pid=1.7",
          link: "https://www.stradini.lv/",
        },
        {
          name: "Dekoratīvās mākslas un dizaina muzejs",
          description: "Muzejs iepazīstina sabiedrību ar aktuālākajām dizaina un dekoratīvās mākslas tendencēm Latvijā un pasaulē un rosina diskusiju par jomas attīstību.",
          image: "https://muzeji.lv/storage/images/cache/2fd5a91d13cd13c43fee18ce076ff5ac7f18cdc2/2fd5a91d13cd13c43fee18ce076ff5ac7f18cdc2_museum-card.jpg",
          link: "https://lnmm.gov.lv/dekorativas-makslas-un-dizaina-muzejs",
        },
        {
          name: "Rīgas Motormuzejs",
          description: "Rīgas Motormuzejs ir unikāls muzejs, kas piedāvā plašu izstāžu klāstu par automobiļu vēsturi un attīstību.",
          image: "https://th.bing.com/th/id/OIP.b1QccJjwkaYHlVA0e61etAHaE8?w=251&h=180&c=7&r=0&o=5&cb=iwc1&dpr=1.3&pid=1.7",
          link: "https://www.motormuseum.lv/",
        },
      ],
    };
  },

  computed: {
    filteredMuseums() {
      const query = this.searchQuery.toLowerCase();
      const filtered = this.museums.filter(
        museum =>
          museum.name.toLowerCase().includes(query) ||
          museum.description.toLowerCase().includes(query)
      );

      if (this.sortOption === 'alphabetical') {
        return filtered.sort((a, b) => a.name.localeCompare(b.name));
      } else if (this.sortOption === 'reverse-alphabetical') {
        return filtered.sort((a, b) => b.name.localeCompare(a.name));
      }

      return filtered;
    },
  },

  methods: {
    toggleDarkMode() {
      document.body.classList.toggle("dark-mode");
    },
    toggleDropdown(event) {
      event.preventDefault();
      this.dropdownOpen = !this.dropdownOpen;
    },
    showMenu() {
      const navLinks = document.getElementById("navLinks");
      navLinks.style.right = "0";
    },
    hideMenu() {
      const navLinks = document.getElementById("navLinks");
      navLinks.style.right = "-200px";
    },
    applySort() {
      // Sorting is already applied in the computed property based on the selected option.
    }
  },
};
</script>

<style scoped>
.sort-dropdown {
  text-align: center;
  margin: 20px 0;
}

.sort-dropdown select {
  padding: 10px;
  font-size: 16px;
  border: 1px solid #991499;
  border-radius: 8px;
}
/* Dark mode */
body.dark-mode {
  background-color: #121212;
  color: #ffffff;
}

body.dark-mode .museums {
  background-color: #000000;
}

body.dark-mode footer {
  background-color: #1c1c1c;
}

body.dark-mode .text-box h3 {
  color: #ffffff;
}

body.dark-mode .text-box p {
  color: #ffffff;
}

body.dark-mode .places-col {
  background-color: #000000;
}

body.dark-mode .flex-container {
  background-color: #333;
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

.mus-container {
  width: 100%;
  height: 100vh;
  background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
    url("https://assets.vogue.com/photos/5cf02219747ce17f10fb866a/master/w_2560%2Cc_limit/00-story-image-the-10-most-popular-museums-in-the-world.jpg");
  background-position: center;
  background-size: cover;
  padding-left: 8%;
  padding-right: 8%;
}

.search-bar {
  text-align: center;
  margin: 30px 0 20px;
}

.search-input {
  padding: 12px 20px;
  width: 80%;
  max-width: 500px;
  border: 2px solid #991499;
  border-radius: 8px;
  font-size: 16px;
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
  color: #fff;
  font-size: 80px;
  margin-bottom: 20px;
}

p {
  color: #fff;
  font-size: 22px;
  line-height: 1.5;
  max-width: 600px;
}

img {
  max-width: 320px;
}

.museums {
  background-color: #f9f9f929;
  padding: 50px 0;
}

.flex-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 30px;
  margin-bottom: 40px;
  flex-wrap: wrap;
}

.places-col {
  flex-basis: 30%;
  background: #fff;
  border-radius: 0px;
  padding: 20px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  text-align: center;
}

.places-col img {
  width: 100%;
  height: auto;
  border-radius: 0px;
  transition: transform 0.3s;
}

.places-col:hover img {
  transform: scale(1.05);
}

.places-col:hover {
  transform: translateY(-10px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.text-box {
  flex-basis: 60%;
  padding: 0 20px;
  text-align: left;
}

.text-box h3 {
  font-size: 28px;
  color: #333;
  margin-bottom: 15px;
  text-align: left;
}

.text-box p {
  font-size: 18px;
  color: #555;
  line-height: 1.6;
}

.visit-button {
  display: inline-block;
  margin-top: 20px;
  background-color: #2413bd;
  color: rgb(255, 255, 255);
  padding: 12px 25px;
  border-radius: 5px;
  text-decoration: none;
  transition: background-color 0.3s;
}

.visit-button:hover {
  background-color: #282b74;
}

/* Footer */
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
  .flex-container {
    flex-direction: column;
    align-items: center;
  }

  .places-col,
  .text-box {
    flex-basis: 90%;
    margin: 10px 0;
  }

  .text-box h1 {
    font-size: 20px;
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
