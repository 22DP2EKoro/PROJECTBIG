<template>
    <div class="about-page">
      <header class="about-header">
        <h1>About ExploreRiga</h1>
      </header>
  
      <section class="about-content">
        <p>
          Welcome to <strong>ExploreRiga</strong> – your ultimate companion to discovering the rich culture,
          vibrant lifestyle, and hidden gems of Riga, Latvia. Our mission is to make your visit
          or stay in Riga unforgettable.
        </p>
        <p>
          Whether you're a tourist, local, or expat, ExploreRiga offers curated recommendations
          on sights, events, food, and experiences that showcase the true spirit of the city.
        </p>
        <p>
          We are a passionate team of locals who believe in promoting the beauty and uniqueness
          of Riga to the world. From historical landmarks to modern-day adventures, ExploreRiga
          connects you with the heart of the city.
        </p>
      </section>
  
      <section class="about-values">
        <h2>Our Vision</h2>
        <p>
          To become the most trusted and inspiring local guide for anyone looking to experience Riga in an authentic way.
        </p>
  
        <h2>Why Choose Us?</h2>
        <ul>
          <li>Curated local experiences</li>
          <li>Up-to-date information</li>
          <li>Multilingual support</li>
          <li>Passionate and experienced team</li>
        </ul>
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
  export default {
    name: 'AboutPage',
  };
  </script>
  
  <style scoped>
  .about-page {
    font-family: Arial, sans-serif;
    padding: 0px;
    color: #2c3e50;
  }
  
  .about-header {
    background-color: #4e086e;
    color: white;
    padding: 40px;
    text-align: center;
  }
  
  .about-content {
    margin: 40px auto;
    max-width: 800px;
    font-size: 18px;
    line-height: 1.6;
  }
  
  .about-values {
    background-color: #f4f4f4;
    padding: 30px;
    margin: 40px auto;
    max-width: 800px;
    border-radius: 10px;
  }
  
  .about-values h2 {
    color: #4e086e;
    margin-top: 20px;
  }
  
  .about-values ul {
    list-style-type: disc;
    padding-left: 20px;
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

  </style>
  