<template>
  <div :class="{ 'dark-mode': darkMode }">
    <!-- Navigation Bar -->
    <nav class="navbar">
      <div class="navbar-container">
       
        <div class="nav-links" id="navLinks">
          <i class="fa fa-times close-icon" @click="hideMenu"></i>
          <ul>
            <li><router-link to="/">HOME</router-link></li>
            <li><router-link to="/restaurants">RESTAURANTS</router-link></li>
            <li><router-link to="/culture">CULTURE</router-link></li>
            <li class="dropdown">
              <a href="#" class="dropbtn" @click="toggleDropdown($event)">CONTACTS</a>
              <div id="myDropdown" class="dropdown-content" :class="{ show: dropdownOpen }">
                <a href="#">Phone number</a>
                <a href="#">E-pasts</a>
                <a href="#">Inbox</a>
              </div>
            </li>
            <li>
              <button class="dark-mode-toggle" @click="toggleDarkMode">
                {{ darkMode ? '☀️ Light' : '🌙 Dark' }} mode
              </button>
            </li>
          </ul>
        </div>
        <i class="fa fa-bars menu-icon" @click="showMenu"></i>
      </div>
    </nav>

    <!-- Articles Section -->
    <section class="articles">
      <h1 class="section-title">Explore Riga's Landmarks</h1>
      <div class="articles-grid">
        <article v-for="(article, index) in articles" :key="index" class="article-card">
          <div class="article-wrapper">
            <figure class="article-image">
              <img :src="article.image" :alt="article.title" />
            </figure>
            <div class="article-body">
              <h2>{{ article.title }}</h2>
              <p class="article-excerpt">{{ article.description }}</p>
              <router-link to="#" class="read-more">
                Read more
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </router-link>
            </div>
          </div>
        </article>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      darkMode: false,
      dropdownOpen: false,
      articles: [
        {
          title: "Vanšu tilts",
          description: "The Cable Bridge is a modern engineering marvel connecting the two banks of the Daugava River with its distinctive cable-stayed design.",
          image: "https://picsum.photos/id/1011/800/450"
        },
        {
          title: "Brīvības piemineklis",
          description: "The Freedom Monument honors soldiers killed during the Latvian War of Independence (1918-1920). Standing at 42 meters, it's a symbol of Latvian sovereignty.",
          image: "https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/Brivibas_piemineklis_2010_06_17.jpg/800px-Brivibas_piemineklis_2010_06_17.jpg"
        },
        {
          title: "Rīgas Doms",
          description: "Riga Cathedral, founded in 1211, is one of the most recognizable landmarks in Latvia with its mix of Romanesque, Gothic, and Baroque architectural elements.",
          image: "https://upload.wikimedia.org/wikipedia/commons/thumb/6/63/Rigas_Doms_2012.JPG/800px-Rigas_Doms_2012.JPG"
        },
        {
          title: "Latvijas Nacionālā bibliotēka",
          description: "Also called the Castle of Light, this modern architectural icon houses the nation's cultural heritage with over 4 million items in its collection.",
          image: "https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Latvian_National_Library_June_2014.jpg/800px-Latvian_National_Library_June_2014.jpg"
        }
      ]
    };
  },
  methods: {
    toggleDarkMode() {
      this.darkMode = !this.darkMode;
    },
    toggleDropdown(event) {
      event.preventDefault();
      this.dropdownOpen = !this.dropdownOpen;
    },
    showMenu() {
      const navLinks = document.getElementById('navLinks');
      navLinks.style.right = '0';
    },
    hideMenu() {
      const navLinks = document.getElementById('navLinks');
      navLinks.style.right = '-200px';
    }
  }
};
</script>

<style scoped>
/* Base Styles */
:root {
  --primary-color: #0056b3;
  --secondary-color: #6c757d;
  --dark-color: #343a40;
  --light-color: #4e5f71;
  --accent-color: #991499;
  --text-color: #212529;
  --bg-color: #2b2929;
  --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  --transition: all 0.3s ease;
}

.dark-mode {
  --primary-color: #4da6ff;
  --secondary-color: #868e96;
  --dark-color: #f8f9fa;
  --light-color: #343a40;
  --accent-color: #c77dc7;
  --text-color: #f8f9fa;
  --bg-color: #212529;
  --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
  color: var(--text-color);
  transition: var(--transition);
}

/* Navigation bar */
.navbar {
  color: rgb(74, 66, 66);
  padding: 1rem 2rem;
  position: sticky;
  top: 0;
  z-index: 1000;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  background-color: #898181; /* Solid dark color */
}

.navbar-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
}



.nav-links ul {
  display: flex;
  gap: 1.5rem;
}

.nav-links ul li {
  list-style: none;
  position: relative;
  color:#991499
}

.nav-links ul li a {
  color: rgb(34, 34, 34);
  text-decoration: none;
  font-size: 1.1rem;
  font-weight: 500;
  padding: 0.5rem 0;
  transition: var(--transition);
}

.nav-links ul li a:hover {
  color: rgba(55, 2, 46, 0.8);
}

.nav-links ul li::after {
  content: '';
  width: 0%;
  height: 2px;
  background: var(--accent-color);
  display: block;
  margin: auto;
  transition: var(--transition);
}

.nav-links ul li:hover::after {
  width: 100%;
}

/* Dropdown styles */
.dropdown {
  position: relative;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: white;
  min-width: 160px;
  box-shadow: var(--card-shadow);
  z-index: 1;
  border-radius: 0.5rem;
  overflow: hidden;
  top: 100%;
  left: 0;
}

.dropdown-content a {
  color: var(--dark-color);
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  transition: var(--transition);
}

.dropdown-content a:hover {
  background-color: var(--light-color);
  color: var(--primary-color);
}

.dropdown-content.show {
  display: block;
}

/* Dark mode toggle */
.dark-mode-toggle {
  background: transparent;
  border: none;
  color: white;
  font-size: 1rem;
  cursor: pointer;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  transition: var(--transition);
}

.dark-mode-toggle:hover {
  background: rgba(255, 255, 255, 0.1);
}

/* Articles section */
.section-title {
  text-align: center;
  margin: 3rem 0;
  font-size: 2.5rem;
  color: var(--primary-color);
}

.articles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.article-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.article-card:hover {
  transform: translateY(-5px);
}

.article-wrapper {
  border-radius: 0.8rem;
  overflow: hidden;
  box-shadow: var(--card-shadow);
  height: 100%;
  display: flex;
  flex-direction: column;
  background: var(--light-color);
}

.article-image {
  height: 200px;
  overflow: hidden;
}

.article-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.article-card:hover .article-image img {
  transform: scale(1.05);
}

.article-body {
  padding: 1.5rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.article-body h2 {
  margin: 0 0 1rem 0;
  font-size: 1.5rem;
  color: var(--primary-color);
}

.article-excerpt {
  margin-bottom: 1.5rem;
  color: var(--secondary-color);
  flex-grow: 1;
}

.read-more {
  display: inline-flex;
  align-items: center;
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  transition: var(--transition);
}

.read-more:hover {
  color: var(--accent-color);
}

.read-more svg {
  margin-left: 0.5rem;
  width: 1rem;
  height: 1rem;
  transition: var(--transition);
}

.read-more:hover svg {
  transform: translateX(3px);
}

/* Mobile menu */
.menu-icon, .close-icon {
  display: none;
  cursor: pointer;
  font-size: 1.5rem;
  color: white;
}

/* Responsive styles */
@media (max-width: 768px) {
  .navbar {
    padding: 1rem;
  }
  
  .nav-links {
    position: fixed;
    height: 100vh;
    width: 200px;
    top: 0;
    right: -200px;
    text-align: left;
    z-index: 2000;
    transition: right 0.5s;
    padding-top: 3rem;
  }
  
  .nav-links ul {
    flex-direction: column;
    padding: 1rem;
  }
  
  .menu-icon, .close-icon {
    display: block;
  }
  
  .close-icon {
    position: absolute;
    top: 1rem;
    right: 1rem;
  }
  
  .dropdown-content {
    position: static;
    box-shadow: none;
    background-color: rgba(0, 0, 0, 0.1);
  }
  
  .dropdown-content a {
    color: white;
  }
  
  .dropdown-content a:hover {
    background-color: rgba(0, 0, 0, 0.2);
  }
}
</style>