<template>
    <div class="contact-page">
      <div class="contact-header">
        <h1>Contact Us</h1>
      </div>
  
      <!-- Contact Information Section -->
      <div class="contact-info">
        <div class="contact-item">
          <h3>Address</h3>
          <p>123 Main Street, Example City, Country</p>
        </div>
        <div class="contact-item">
          <h3>Email</h3>
          <p>contact@example.com</p>
        </div>
        <div class="contact-item">
          <h3>Phone</h3>
          <p>+123 456 7890</p>
        </div>
      </div>
  
      <!-- Optional: Google Map (if you want to add a map of your location) -->
      <div class="google-map">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3162.444030694869!2d-122.08424948469347!3d37.421999779825245!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fbbd6b9b8b241%3A0xf5f6048bc8e42fe1!2sGoogleplex!5e0!3m2!1sen!2sus!4v1620043989985!5m2!1sen!2sus"
          width="100%"
          height="400"
          style="border:0;"
          allowfullscreen=""
          loading="lazy"
        ></iframe>
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
    name: 'ContactPage',
  };
  </script>
  
  <style scoped>
  .contact-page {
    font-family: Arial, sans-serif;
  }
  
  .contact-header {
    background-color: #4e086e;
    color: white;
    text-align: center;
    padding: 50px;
  }
  
  .contact-header h1 {
    margin: 0;
    font-size: 36px;
  }
  
  .contact-info {
    display: flex;
    justify-content: center;
    padding: 50px 10%;
    background-color: #fff;
  }
  
  .contact-info .contact-item {
    width: 30%;
    margin: 0 15px;
    text-align: center;
  }
  
  .contact-info h3 {
    margin-bottom: 10px;
  }
  
  .contact-info p {
    font-size: 18px;
  }
  
  .google-map {
    width: 100%;
    height: 400px;
    margin: 50px 0;
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
  </style>
  