<template>
  <div class="submit-page">

    <div class="submit-hero">
      <nav>
        <a href="/" class="brand">ExploreRiga</a>
        <div class="nav-links" id="submitNavLinks">
          <i class="fa fa-times" @click="hideMenu"></i>
          <ul>
            <li><router-link to="/">{{ t('nav.home') }}</router-link></li>
            <li><router-link to="/museums">{{ t('nav.museums') }}</router-link></li>
            <li><router-link to="/restaurants">{{ t('nav.restaurants') }}</router-link></li>
            <li><router-link to="/culture">{{ t('nav.culture') }}</router-link></li>
          </ul>
        </div>
        <div class="lang-switcher">
          <button v-for="l in ['lv','en','et','lt']" :key="l" @click="setLang(l)" :class="{ active: currentLang === l }">{{ l.toUpperCase() }}</button>
        </div>
        <i class="fa fa-bars" @click="showMenu"></i>
      </nav>
      <div class="hero-content">
        <div class="hero-badge">{{ t('submit.badge') }}</div>
        <h1>{{ t('submit.title') }}</h1>
        <p>{{ t('submit.sub') }}</p>
      </div>
    </div>

    <!-- Success screen -->
    <div v-if="submitted" class="success-screen">
      <div class="success-card">
        <div class="success-icon"><i class="fa fa-check"></i></div>
        <h2>{{ t('submit.successTitle') }}</h2>
        <p>{{ t('submit.successMsg') }}</p>
        <router-link to="/" class="home-btn">{{ t('submit.backHome') }}</router-link>
      </div>
    </div>

    <!-- Form -->
    <div v-else class="form-wrapper">

      <div class="steps-indicator">
        <div v-for="(s, i) in steps" :key="i" class="step" :class="{ active: step === i, done: step > i }">
          <div class="step-dot">
            <i v-if="step > i" class="fa fa-check"></i>
            <span v-else>{{ i + 1 }}</span>
          </div>
          <span class="step-label">{{ s }}</span>
        </div>
      </div>

      <div class="form-card">

        <!-- Step 1: Place details -->
        <div v-if="step === 0">
          <h2 class="section-title"><i class="fa fa-store"></i> {{ t('submit.step1Title') }}</h2>
          <div class="form-grid">
            <div class="form-group full">
              <label>{{ t('submit.fieldName') }} *</label>
              <input v-model="form.place_name" type="text" :placeholder="t('submit.fieldNamePh')" />
            </div>
            <div class="form-group full">
              <label>{{ t('submit.fieldCategory') }} *</label>
              <div class="category-cards">
                <label v-for="cat in categories" :key="cat.value" class="cat-card" :class="{ selected: form.category === cat.value }">
                  <input type="radio" :value="cat.value" v-model="form.category" hidden />
                  <i :class="cat.icon"></i>
                  <span>{{ cat.label }}</span>
                </label>
              </div>
            </div>
            <div class="form-group full">
              <label>{{ t('submit.fieldDesc') }} *</label>
              <textarea v-model="form.description" :placeholder="t('submit.fieldDescPh')" rows="4"></textarea>
            </div>
            <div class="form-group full">
              <label>{{ t('submit.fieldAddress') }} *</label>
              <input v-model="form.address" type="text" :placeholder="t('submit.fieldAddressPh')" />
            </div>
          </div>
        </div>

        <!-- Step 2: Online presence -->
        <div v-if="step === 1">
          <h2 class="section-title"><i class="fa fa-globe"></i> {{ t('submit.step2Title') }}</h2>
          <div class="form-grid">
            <div class="form-group full">
              <label>{{ t('submit.fieldWebsite') }}</label>
              <input v-model="form.website" type="url" :placeholder="t('submit.fieldWebsitePh')" />
            </div>
            <div class="form-group full">
              <label>{{ t('submit.fieldImage') }}</label>
              <input v-model="form.image_url" type="url" :placeholder="t('submit.fieldImagePh')" />
              <div v-if="form.image_url" class="img-preview">
                <img :src="form.image_url" alt="preview" @error="imgError = true" @load="imgError = false" />
                <p v-if="imgError" class="img-error">{{ t('submit.imgError') }}</p>
              </div>
            </div>
            <div class="form-group full">
              <label>{{ t('submit.fieldHours') }}</label>
              <input v-model="form.opening_hours" type="text" :placeholder="t('submit.fieldHoursPh')" />
            </div>
          </div>
        </div>

        <!-- Step 3: Contact -->
        <div v-if="step === 2">
          <h2 class="section-title"><i class="fa fa-user"></i> {{ t('submit.step3Title') }}</h2>
          <div class="form-grid">
            <div class="form-group full">
              <label>{{ t('submit.fieldContactName') }}</label>
              <input v-model="form.contact_name" type="text" :placeholder="t('submit.fieldContactNamePh')" />
            </div>
            <div class="form-group full">
              <label>{{ t('submit.fieldEmail') }} *</label>
              <input v-model="form.contact_email" type="email" :placeholder="t('submit.fieldEmailPh')" />
            </div>
            <div class="form-group full">
              <label>{{ t('submit.fieldNotes') }}</label>
              <textarea v-model="form.notes" :placeholder="t('submit.fieldNotesPh')" rows="4"></textarea>
            </div>
          </div>
        </div>

        <!-- Navigation -->
        <div class="form-nav">
          <button v-if="step > 0" class="back-btn" @click="step--">
            <i class="fa fa-arrow-left"></i> {{ t('submit.back') }}
          </button>
          <span v-else></span>

          <button v-if="step < 2" class="next-btn" @click="nextStep" :disabled="!canAdvance">
            {{ t('submit.next') }} <i class="fa fa-arrow-right"></i>
          </button>
          <button v-else class="submit-btn" @click="submitForm" :disabled="submitting || !canSubmit">
            <span v-if="submitting"><i class="fa fa-spinner fa-spin"></i> {{ t('submit.sending') }}</span>
            <span v-else><i class="fa fa-paper-plane"></i> {{ t('submit.send') }}</span>
          </button>
        </div>

        <p v-if="errorMsg" class="error-msg"><i class="fa fa-exclamation-circle"></i> {{ errorMsg }}</p>
      </div>

      <div class="info-box">
        <h3>{{ t('submit.infoTitle') }}</h3>
        <ul>
          <li><i class="fa fa-clock"></i> {{ t('submit.info1') }}</li>
          <li><i class="fa fa-envelope"></i> {{ t('submit.info2') }}</li>
          <li><i class="fa fa-check-circle"></i> {{ t('submit.info3') }}</li>
        </ul>
      </div>

    </div>

    <footer>
      <p>&copy; 2024 ExploreRiga. Visas tiesības aizsargātas.</p>
    </footer>
  </div>
</template>

<script>
import { useI18n } from '@/i18n'

export default {
  setup() {
    const { t, currentLang, setLang } = useI18n()
    return { t, currentLang, setLang }
  },
  data() {
    return {
      step: 0,
      submitted: false,
      submitting: false,
      imgError: false,
      errorMsg: '',
      form: {
        place_name: '',
        category: '',
        description: '',
        address: '',
        website: '',
        image_url: '',
        opening_hours: '',
        contact_name: '',
        contact_email: '',
        notes: '',
      },
    }
  },
  computed: {
    steps() {
      return [this.t('submit.step1'), this.t('submit.step2'), this.t('submit.step3')]
    },
    categories() {
      return [
        { value: 'museum',     label: this.t('nav.museums'),     icon: 'fa fa-landmark' },
        { value: 'restaurant', label: this.t('nav.restaurants'), icon: 'fa fa-utensils' },
        { value: 'culture',    label: this.t('nav.culture'),     icon: 'fa fa-theater-masks' },
      ]
    },
    canAdvance() {
      if (this.step === 0) return this.form.place_name.trim() && this.form.category && this.form.description.trim() && this.form.address.trim()
      return true
    },
    canSubmit() {
      return this.form.contact_email.trim() && this.form.contact_email.includes('@')
    },
  },
  methods: {
    showMenu() {
      const nav = document.getElementById('submitNavLinks')
      if (nav) nav.style.right = '0'
    },
    hideMenu() {
      const nav = document.getElementById('submitNavLinks')
      if (nav) nav.style.right = '-240px'
    },
    nextStep() {
      if (this.canAdvance) this.step++
    },
    async submitForm() {
      if (!this.canSubmit) return
      this.submitting = true
      this.errorMsg = ''
      try {
        const res = await fetch('/api/submit_place_request.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        })
        if (!res.ok) {
          this.errorMsg = `Servera kļūda: ${res.status} ${res.statusText}`
          return
        }
        const data = await res.json()
        if (data.error) {
          this.errorMsg = data.error
        } else {
          this.submitted = true
        }
      } catch (err) {
        this.errorMsg = err.message || this.t('submit.networkError')
      } finally {
        this.submitting = false
      }
    },
  },
}
</script>

<style scoped>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
h1, h2, h3 { font-family: 'Playfair Display', serif; }

/* Hero */
.submit-hero {
  width: 100%;
  min-height: 380px;
  background: linear-gradient(rgba(0,0,0,0.60), rgba(0,0,0,0.60)),
    url("https://images.unsplash.com/photo-1486325212027-8081e485255e?w=1600") center/cover;
  padding: 0 6%;
  display: flex;
  flex-direction: column;
}

nav {
  display: flex;
  padding: 1.8% 0;
  align-items: center;
  gap: 12px;
}

.brand {
  font-family: 'Playfair Display', serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: #fff;
  text-decoration: none;
  flex-shrink: 0;
}

.nav-links { flex: 1; text-align: right; }
.nav-links ul li { list-style: none; display: inline-block; padding: 8px 12px; }
.nav-links ul li a { color: #fff; text-decoration: none; font-size: 13px; font-weight: 500; letter-spacing: 0.8px; text-transform: uppercase; }
nav .fa-bars { display: none; }
.nav-links .fa-times { display: none; }

.lang-switcher { display: flex; gap: 4px; }
.lang-switcher button {
  background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);
  border: 1px solid rgba(255,255,255,0.2); padding: 5px 9px;
  font-size: 11px; font-weight: 600; cursor: pointer; border-radius: 6px; transition: all 0.2s;
}
.lang-switcher button:hover { background: rgba(255,255,255,0.2); color: #fff; }
.lang-switcher button.active { background: #991499; color: #fff; border-color: #991499; }

.hero-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 32px 0 52px;
  max-width: 660px;
}

.hero-badge {
  display: inline-block;
  background: rgba(153,20,153,0.85);
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  padding: 6px 16px;
  border-radius: 50px;
  margin-bottom: 18px;
  width: fit-content;
}

.hero-content h1 { color: #fff; font-size: 56px; line-height: 1.1; margin-bottom: 16px; }
.hero-content p  { color: rgba(255,255,255,0.82); font-size: 17px; line-height: 1.6; }

/* Form wrapper */
.form-wrapper {
  max-width: 780px;
  margin: 0 auto;
  padding: 52px 24px 80px;
}

/* Steps */
.steps-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  margin-bottom: 36px;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  flex: 1;
  position: relative;
}

.step:not(:last-child)::after {
  content: '';
  position: absolute;
  top: 18px;
  left: 50%;
  width: 100%;
  height: 2px;
  background: #e5e7eb;
  z-index: 0;
}

.step.done:not(:last-child)::after { background: #991499; }

.step-dot {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #e5e7eb;
  color: #aaa;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  position: relative;
  z-index: 1;
  transition: all 0.3s;
}

.step.active .step-dot { background: #991499; color: #fff; box-shadow: 0 0 0 4px rgba(153,20,153,0.18); }
.step.done .step-dot   { background: #991499; color: #fff; }

.step-label { font-size: 12px; color: #888; font-weight: 500; text-align: center; }
.step.active .step-label { color: #991499; font-weight: 700; }
.step.done .step-label   { color: #991499; }

/* Form card */
.form-card {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 6px 32px rgba(0,0,0,0.09);
  padding: 40px;
  margin-bottom: 28px;
}

.section-title {
  font-size: 22px;
  color: #1a1a2e;
  margin-bottom: 28px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.section-title .fa { color: #991499; font-size: 18px; }

.form-grid { display: flex; flex-direction: column; gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 13px; font-weight: 600; color: #444; text-transform: uppercase; letter-spacing: 0.4px; }

.form-group input,
.form-group textarea {
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 15px;
  font-family: 'Inter', sans-serif;
  color: #333;
  background: #fafafa;
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
}

.form-group input:focus,
.form-group textarea:focus {
  border-color: #991499;
  box-shadow: 0 0 0 3px rgba(153,20,153,0.1);
  background: #fff;
}

/* Category cards */
.category-cards {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.cat-card {
  flex: 1;
  min-width: 120px;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 16px 12px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  color: #888;
  background: #fafafa;
}

.cat-card .fa { font-size: 22px; }
.cat-card span { font-size: 13px; font-weight: 600; }

.cat-card:hover { border-color: #991499; color: #991499; }
.cat-card.selected { border-color: #991499; background: #fdf4fe; color: #991499; box-shadow: 0 0 0 3px rgba(153,20,153,0.1); }

/* Image preview */
.img-preview {
  margin-top: 10px;
  border-radius: 10px;
  overflow: hidden;
  max-height: 200px;
  border: 1.5px solid #e5e7eb;
}

.img-preview img { width: 100%; max-height: 200px; object-fit: cover; display: block; }
.img-error { color: #e11d48; font-size: 13px; padding: 8px 12px; }

/* Nav buttons */
.form-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1.5px solid #f0e8f4;
}

.back-btn {
  background: none;
  border: 2px solid #e5e7eb;
  color: #666;
  padding: 11px 24px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}

.back-btn:hover { border-color: #991499; color: #991499; }

.next-btn, .submit-btn {
  background: #991499;
  color: #fff;
  border: none;
  padding: 13px 32px;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background 0.2s, transform 0.15s, opacity 0.2s;
}

.next-btn:hover:not(:disabled),
.submit-btn:hover:not(:disabled) { background: #6b0487; transform: translateY(-1px); }

.next-btn:disabled,
.submit-btn:disabled { opacity: 0.45; cursor: not-allowed; }

.error-msg {
  color: #e11d48;
  font-size: 13px;
  margin-top: 12px;
  display: flex;
  align-items: center;
  gap: 6px;
}

/* Info box */
.info-box {
  background: #fdf4fe;
  border: 1.5px solid #e9d5ff;
  border-radius: 16px;
  padding: 28px 32px;
}

.info-box h3 { font-size: 16px; color: #6b0487; margin-bottom: 16px; }
.info-box ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }
.info-box li { display: flex; align-items: flex-start; gap: 12px; font-size: 14px; color: #555; line-height: 1.5; }
.info-box .fa { color: #991499; font-size: 14px; margin-top: 2px; flex-shrink: 0; }

/* Success */
.success-screen {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 80px 24px;
}

.success-card {
  background: #fff;
  border-radius: 24px;
  box-shadow: 0 8px 40px rgba(0,0,0,0.1);
  padding: 56px 48px;
  text-align: center;
  max-width: 520px;
}

.success-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #991499, #6b0487);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 24px;
  font-size: 32px;
  color: #fff;
}

.success-card h2 { font-size: 28px; color: #1a1a2e; margin-bottom: 12px; }
.success-card p  { color: #666; font-size: 16px; line-height: 1.6; margin-bottom: 32px; }

.home-btn {
  display: inline-block;
  background: #991499;
  color: #fff;
  padding: 13px 32px;
  border-radius: 50px;
  font-size: 15px;
  font-weight: 700;
  text-decoration: none;
  transition: background 0.2s;
}

.home-btn:hover { background: #6b0487; }

footer {
  background: linear-gradient(135deg, #1a001a 0%, #2d003a 100%);
  color: rgba(255,255,255,0.5);
  padding: 28px 5%;
  text-align: center;
  font-size: 13px;
}

@media (max-width: 700px) {
  .hero-content h1 { font-size: 38px; }
  .form-card { padding: 24px; }
  .category-cards { flex-direction: column; }
  .steps-indicator { gap: 0; }
  .step-label { font-size: 10px; }

  nav .fa-bars { display: block; color: #fff; font-size: 22px; cursor: pointer; }
  .nav-links {
    position: fixed; background: #3b053d;
    height: 100vh; width: 240px; top: 0; right: -240px;
    text-align: left; z-index: 1000; transition: 0.4s;
  }
  .nav-links ul li { display: block; }
  .nav-links ul { padding: 30px 20px; }
}
</style>
