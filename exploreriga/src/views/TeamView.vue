<template>
  <div class="team-page">
    <div class="team-header">
      <a href="/" class="brand">ExploreRiga</a>
      <h1>{{ t('team.title') }}</h1>
      <p>{{ t('team.subtitle') }}</p>
    </div>

    <div class="team-section">
      <div class="team-member">
        <div class="member-photo-wrap">
          <img src="/es1.jpeg" alt="Director" class="team-member-photo" />
        </div>
        <div class="team-member-info">
          <h3>Ervins Korolkovs</h3>
          <p class="position">{{ t('team.pos_director') }}</p>
          <p class="bio">{{ t('team.bio_director') }}</p>
        </div>
      </div>

      <div class="team-member">
        <div class="member-photo-wrap">
          <img src="/es2.webp" alt="CEO" class="team-member-photo" />
        </div>
        <div class="team-member-info">
          <h3>Ervins Korolkovs</h3>
          <p class="position">{{ t('team.pos_ceo') }}</p>
          <p class="bio">{{ t('team.bio_ceo') }}</p>
        </div>
      </div>

      <div class="team-member">
        <div class="member-photo-wrap">
          <img src="/es3.webp" alt="Founder" class="team-member-photo" />
        </div>
        <div class="team-member-info">
          <h3>Ervins Korolkovs</h3>
          <p class="position">{{ t('team.pos_founder') }}</p>
          <p class="bio">{{ t('team.bio_founder') }}</p>
        </div>
      </div>
    </div>

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

<script>
import { useI18n } from '../i18n'

export default {
  name: 'TeamPage',
  setup() {
    const { t } = useI18n()
    return { t }
  },
};
</script>

<style scoped>
* {
  font-family: 'Inter', sans-serif;
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

h1, h2, h3 { font-family: 'Playfair Display', serif; }

.team-page { background: #faf7fc; min-height: 100vh; }

/* ── Header ── */
.team-header {
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
  letter-spacing: -0.2px;
}

.brand:hover { color: #fff; }

.team-header h1 {
  font-size: 48px;
  margin-bottom: 12px;
}

.team-header p {
  font-size: 17px;
  color: rgba(255,255,255,0.75);
  font-weight: 300;
}

/* ── Team grid ── */
.team-section {
  display: flex;
  justify-content: center;
  gap: 32px;
  padding: 64px 5%;
  flex-wrap: wrap;
  max-width: 1200px;
  margin: 0 auto;
}

.team-member {
  flex-basis: 280px;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 6px 28px rgba(0,0,0,0.09);
  overflow: hidden;
  text-align: center;
  transition: transform 0.3s, box-shadow 0.3s;
}

.team-member:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 48px rgba(107,4,135,0.15);
}

.member-photo-wrap {
  width: 100%;
  height: 240px;
  overflow: hidden;
}

.team-member-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: top;
  transition: transform 0.4s;
}

.team-member:hover .team-member-photo { transform: scale(1.04); }

.team-member-info {
  padding: 24px 20px 28px;
}

.team-member-info h3 {
  font-size: 22px;
  color: #1a1a2e;
  margin-bottom: 6px;
}

.position {
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: #991499;
  margin-bottom: 14px;
}

.bio {
  font-size: 14px;
  color: #555;
  line-height: 1.65;
}

/* ── Footer ── */
footer {
  background: linear-gradient(135deg, #1a001a 0%, #2d003a 100%);
  color: #fff;
  padding: 28px 5%;
  text-align: center;
}

.footer-content { max-width: 1200px; margin: 0 auto; }

.footer-content p {
  font-size: 13px;
  color: rgba(255,255,255,0.5);
  margin-bottom: 8px;
}

.social-links a {
  color: rgba(255,255,255,0.7);
  text-decoration: none;
  margin: 0 10px;
  font-size: 14px;
  transition: color 0.2s;
}

.social-links a:hover { color: #e8a0e8; }

@media (max-width: 600px) {
  .team-header {
    padding: 80px 24px 40px;
  }

  .brand {
    top: 18px;
    left: 18px;
    font-size: 1.1rem;
  }

  .team-header h1 {
    font-size: 34px;
  }

  .team-section {
    padding: 40px 5%;
    gap: 20px;
  }

  .team-member {
    flex-basis: 100%;
  }
}
</style>
