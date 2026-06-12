<template>
  <div class="contact-page">
    <div class="contact-header">
      <a href="/" class="brand">ExploreRiga</a>
      <h1>{{ t('contacts.title') }}</h1>
    </div>

    <div class="contact-info">
      <div class="contact-item">
        <i class="fa fa-map-marker-alt"></i>
        <h3>{{ t('contacts.addressLabel') }}</h3>
        <p>Brīvības iela 55, Rīga, LV-1010</p>
      </div>
      <div class="contact-item">
        <i class="fa fa-envelope"></i>
        <h3>{{ t('contacts.emailLabel') }}</h3>
        <p>info@exploreriga.lv</p>
      </div>
      <div class="contact-item">
        <i class="fa fa-phone"></i>
        <h3>{{ t('contacts.phoneLabel') }}</h3>
        <p>+371 67 123 456</p>
      </div>
    </div>

    <div class="google-map">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d68899.25748476566!2d23.97339765!3d56.9460054!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46eecfb0e5073ded%3A0x400cfcd68f2fe30!2sRiga%2C%20Latvia!5e0!3m2!1sen!2sus!4v1715000000000!5m2!1sen!2sus"
        width="100%"
        height="420"
        style="border:0;"
        allowfullscreen=""
        loading="lazy"
      ></iframe>
    </div>

    <footer>
      <div class="footer-content">
        <p>{{ t('footer.copyright') }}</p>
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
import { useI18n } from '../i18n'

export default {
  name: 'ContactPage',
  setup() {
    const { t } = useI18n()
    return { t }
  },
}
</script>

<style scoped>
* { font-family: 'Inter', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
h1, h2, h3 { font-family: 'Playfair Display', serif; }

.contact-page { background: #faf7fc; min-height: 100vh; }

.contact-header {
  background: linear-gradient(135deg, #3b053d 0%, #6b0487 100%);
  color: white;
  text-align: center;
  padding: 60px 24px 50px;
  position: relative;
}

.brand {
  position: absolute;
  top: 24px;
  left: 32px;
  font-family: 'Playfair Display', serif;
  font-size: 1.4rem;
  font-weight: 700;
  color: rgba(255,255,255,0.85);
  text-decoration: none;
}
.brand:hover { color: #fff; }

.contact-header h1 { font-size: 48px; }

.contact-info {
  display: flex;
  justify-content: center;
  gap: 32px;
  padding: 64px 5%;
  flex-wrap: wrap;
  max-width: 1000px;
  margin: 0 auto;
}

.contact-item {
  flex-basis: 240px;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 6px 28px rgba(0,0,0,0.09);
  padding: 36px 28px;
  text-align: center;
  transition: transform 0.3s, box-shadow 0.3s;
}

.contact-item:hover {
  transform: translateY(-6px);
  box-shadow: 0 14px 40px rgba(107,4,135,0.14);
}

.contact-item .fa {
  font-size: 2rem;
  color: #991499;
  margin-bottom: 16px;
}

.contact-item h3 {
  font-size: 18px;
  color: #1a1a2e;
  margin-bottom: 8px;
}

.contact-item p {
  font-size: 15px;
  color: #555;
}

.google-map {
  width: 100%;
  max-width: 1100px;
  margin: 0 auto 64px;
  padding: 0 5%;
  border-radius: 16px;
  overflow: hidden;
}

.google-map iframe { display: block; border-radius: 16px; }

footer {
  background: linear-gradient(135deg, #1a001a 0%, #2d003a 100%);
  color: #fff;
  padding: 28px 5%;
  text-align: center;
}
.footer-content { max-width: 1200px; margin: 0 auto; }
.footer-content p { font-size: 13px; color: rgba(255,255,255,0.5); margin-bottom: 8px; }
.social-links a { color: rgba(255,255,255,0.7); text-decoration: none; margin: 0 10px; font-size: 14px; transition: color 0.2s; }
.social-links a:hover { color: #e8a0e8; }

@media (max-width: 600px) {
  .contact-header {
    padding: 80px 24px 40px;
  }

  .brand {
    top: 18px;
    left: 18px;
    font-size: 1.1rem;
  }

  .contact-header h1 {
    font-size: 34px;
  }

  .contact-info {
    padding: 40px 5%;
    gap: 16px;
  }

  .contact-item {
    flex-basis: 100%;
  }

  .google-map iframe {
    height: 280px;
  }
}
</style>
