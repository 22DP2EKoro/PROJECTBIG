<template>
    <div class="team-page">
      <div class="team-header">
        <h1>Meet Our Team</h1>
      </div>
  
      <!-- Team Section -->
      <div class="team-section">
        <!-- Director -->
        <div class="team-member">
          <img
            src="/workspaces/PROJECTBIG/exploreriga/public/es1.jpeg"
            alt="Director"
            class="team-member-photo"
          />
          <div class="team-member-info">
            <h3>Ervins Korolkovs</h3>
            <p class="position">Director</p>
            <p class="bio">Ervins is responsible for overseeing the company's overall operations and strategic direction. With over 20 years of experience in the industry, he brings a wealth of expertise.</p>
          </div>
        </div>
  
        <!-- CEO -->
        <div class="team-member">
          <img
            src="/workspaces/PROJECTBIG/exploreriga/public/es2.webp "
            alt="CEO"
            class="team-member-photo"
          />
          <div class="team-member-info">
            <h3>Ervins Korolkovs</h3>
            <p class="position">CEO</p>
            <p class="bio">Ervins leads the company with a vision to innovate and grow. Heisommitted to creating a culture of excellence and empowering her team to achieve great things.</p>
          </div>
        </div>
  
        <!-- Founder -->
        <div class="team-member">
          <img
            src="/workspaces/PROJECTBIG/exploreriga/public/es3.webp"
            alt="Founder"
            class="team-member-photo"
          />
          <div class="team-member-info">
            <h3>Ervins Korolkovs</h3>
            <p class="position">Founder</p>
            <p class="bio">Ervins founded the company with a passion for creating impactful solutions. With an entrepreneurial spirit, Ervinsbeen the driving force behind the company's growth and success.</p>
          </div>
        </div>
      </div>
  
      <!-- Footer Section -->
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
    name: 'TeamPage',
  };
  </script>
  
  <style scoped>
  .team-page {
    font-family: Arial, sans-serif;
  }
  
  .team-header {
    background-color: #4e086e;
    color: white;
    text-align: center;
    padding: 50px;
  }
  
  .team-header h1 {
    margin: 0;
    font-size: 36px;
  }
  
  .team-section {
    display: flex;
    justify-content: space-around;
    padding: 50px;
    background-color: #f8f8f8;
    flex-wrap: wrap;
  }
  
  .team-member {
    width: 30%;
    margin: 20px 0;
    text-align: center;
  }
  
  .team-member-photo {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
  }
  
  .team-member-info {
    margin-top: 20px;
  }
  
  .team-member h3 {
    font-size: 24px;
    margin-bottom: 10px;
  }
  
  .position {
    font-style: italic;
    color: #7f8c8d;
    margin-bottom: 10px;
  }
  
  .bio {
    font-size: 16px;
    color: #2c3e50;
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
  