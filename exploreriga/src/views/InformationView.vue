<template>
  <div class="info-page">
    <div class="info-header">
      <a href="/" class="brand">ExploreRiga</a>
      <h1>{{ t('info.title') }}</h1>
    </div>

    <section class="info-content">
      <p v-html="t('info.p1')"></p>
      <p>{{ t('info.p2') }}</p>
      <p>{{ t('info.p3') }}</p>
    </section>

    <section class="info-values">
      <div class="values-inner">
        <div class="value-block">
          <h2>{{ t('info.visionTitle') }}</h2>
          <p>{{ t('info.visionText') }}</p>
        </div>
        <div class="value-block">
          <h2>{{ t('info.whyTitle') }}</h2>
          <ul>
            <li><i class="fa fa-check-circle"></i> {{ t('info.why1') }}</li>
            <li><i class="fa fa-check-circle"></i> {{ t('info.why2') }}</li>
            <li><i class="fa fa-check-circle"></i> {{ t('info.why3') }}</li>
            <li><i class="fa fa-check-circle"></i> {{ t('info.why4') }}</li>
          </ul>
        </div>
      </div>
    </section>

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
  name: 'AboutPage',
  setup() {
    const { t } = useI18n()
    return { t }
  },
}
</script>

<style scoped>
* { font-family: 'Inter', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
h1, h2 { font-family: 'Playfair Display', serif; }

.info-page { background: #faf7fc; min-height: 100vh; }

.info-header {
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

.info-header h1 { font-size: 48px; }

.info-content {
  max-width: 800px;
  margin: 56px auto;
  padding: 0 5%;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.info-content p {
  font-size: 17px;
  line-height: 1.75;
  color: #333;
}

.info-values {
  background: #fff;
  padding: 56px 5%;
}

.values-inner {
  max-width: 900px;
  margin: 0 auto;
  display: flex;
  gap: 48px;
  flex-wrap: wrap;
}

.value-block { flex: 1; min-width: 260px; }

.value-block h2 {
  font-size: 26px;
  color: #3b053d;
  margin-bottom: 16px;
}

.value-block p { font-size: 16px; color: #444; line-height: 1.7; }

.value-block ul { list-style: none; display: flex; flex-direction: column; gap: 12px; }

.value-block li {
  font-size: 15px;
  color: #333;
  display: flex;
  align-items: center;
  gap: 10px;
}

.value-block li .fa {
  color: #991499;
  font-size: 1rem;
  flex-shrink: 0;
}

footer {
  background: linear-gradient(135deg, #1a001a 0%, #2d003a 100%);
  color: #fff;
  padding: 28px 5%;
  text-align: center;
  margin-top: 64px;
}
.footer-content { max-width: 1200px; margin: 0 auto; }
.footer-content p { font-size: 13px; color: rgba(255,255,255,0.5); margin-bottom: 8px; }
.social-links a { color: rgba(255,255,255,0.7); text-decoration: none; margin: 0 10px; font-size: 14px; transition: color 0.2s; }
.social-links a:hover { color: #e8a0e8; }

@media (max-width: 600px) {
  .info-header {
    padding: 80px 24px 40px;
  }

  .brand {
    top: 18px;
    left: 18px;
    font-size: 1.1rem;
  }

  .info-header h1 {
    font-size: 34px;
  }

  .values-inner {
    gap: 28px;
  }
}
</style>
