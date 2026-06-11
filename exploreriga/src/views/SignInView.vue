
<template>
  <div class="signin-container">
    <div class="wrapper">
      <header class="nav-top"></header>

      <form @submit.prevent="handleSubmit">
        <h2>{{ isRegistering ? t('signin.register') : t('signin.login') }}</h2>

        <div class="input-field">
          <input id="email" type="text" v-model="email" required placeholder=" " />
          <label for="email">{{ t('signin.emailLabel') }}</label>
        </div>

        <div class="input-field">
          <input id="password" type="password" v-model="password" required placeholder=" " />
          <label for="password">{{ t('signin.passwordLabel') }}</label>
        </div>
        <div v-if="isRegistering && passwordError" class="password-error">
          {{ passwordError }}
        </div>
        <ul v-if="isRegistering" class="password-rules">
          <li :class="{ met: password.length >= 8 }">Vismaz 8 simboli</li>
          <li :class="{ met: /[A-Z]/.test(password) }">Vismaz 1 lielais burts</li>
          <li :class="{ met: /[0-9]/.test(password) }">Vismaz 1 cipars</li>
        </ul>

        <div class="forget">
          <label for="remember">
            <input type="checkbox" id="remember" v-model="rememberMe" />
            <span>{{ t('signin.rememberMe') }}</span>
          </label>
          <a href="#">{{ t('signin.forgotPassword') }}</a>
        </div>

        <div v-if="errorMsg" class="form-error">
          <i class="fa fa-circle-exclamation"></i> {{ errorMsg }}
        </div>

        <button type="submit">{{ isRegistering ? t('signin.registerBtn') : t('signin.loginBtn') }}</button>

        <div class="register">
          <p>
            <span>{{ isRegistering ? t('signin.haveAccount') : t('signin.noAccount') }} </span>
            <a href="#" @click="toggleForm">{{ isRegistering ? t('signin.loginLink') : t('signin.registerLink') }}</a>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { signIn } from '../auth'
import { useI18n } from '../i18n'


export default {
  setup() {
    const { t } = useI18n()
    return { t }
  },
  data() {
    return {
      email: '',
      password: '',
      rememberMe: false,
      isRegistering: false,
      passwordError: '',
      errorMsg: '',
    }
  },
 
 
  methods: {
    async handleSubmit() {
      this.passwordError = ''
      this.errorMsg = ''

      if (this.isRegistering) {
        if (this.password.length < 8) {
          this.passwordError = 'Parolei jābūt vismaz 8 simboliem.'
          return
        }
        if (!/[A-Z]/.test(this.password)) {
          this.passwordError = 'Parolei jāsatur vismaz viens lielais burts.'
          return
        }
        if (!/[0-9]/.test(this.password)) {
          this.passwordError = 'Parolei jāsatur vismaz viens cipars.'
          return
        }
      }

      try {
        const url = this.isRegistering
          ? 'http://127.0.0.1/explore-riga/register.php'
          : 'http://127.0.0.1/explore-riga/login.php'

        const response = await axios.post(url, {
          email: this.email,
          password: this.password,
        })

        const msg = response.data.message
        const role = response.data.role

        console.log('Server response:', response.data)

        // Handle all valid success messages
        if (
          msg === 'Login successful!' ||
          msg === 'Registration successful!' ||
          msg === 'Admin login successful!'
        ) {
          // Mark user as authenticated
          signIn({ email: this.email, role, id: response.data.id })
        

          // Redirect based on role
          if (role === 'admin') {
            this.$router.push('/admin')
          } else {
            this.$router.push('/')
          }
        } else {
          this.errorMsg = msg
        }
      } catch (error) {
        console.error('There was an error with the request!', error)
        this.errorMsg = 'Servera kļūda. Mēģini vēlāk.'
      }
    },
    toggleForm() {
      this.isRegistering = !this.isRegistering
    }
  }
}
</script>


<style scoped>
/* [No changes to your styles — same as before] */
.signin-container {
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: url("https://grandpalaceriga.com/wp-content/uploads/2014/04/peter.jpg") center/cover no-repeat;
  padding: 20px;
  position: relative;
}
.signin-container::before {
  content: "";
  position: absolute;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.6);
  z-index: 0;
}
.wrapper {
  position: relative;
  z-index: 1;
  background-color: rgba(107, 4, 135, 0.85);
  padding: 30px;
  border-radius: 12px;
  max-width: 400px;
  width: 100%;
  backdrop-filter: blur(6px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
}
form {
  display: flex;
  flex-direction: column;
}
h2 {
  font-size: 2rem;
  margin-bottom: 20px;
  color: #fff;
}
.input-field {
  position: relative;
  border-bottom: 2px solid #ccc;
  margin: 15px 0;
}
.input-field label {
  position: absolute;
  top: 50%;
  left: 0;
  transform: translateY(-50%);
  color: #fff;
  font-size: 16px;
  pointer-events: none;
  transition: 0.15s ease;
}
.input-field input {
  width: 100%;
  height: 40px;
  background: transparent;
  border: none;
  outline: none;
  font-size: 16px;
  color: #fff;
}
.input-field input:focus~label,
.input-field input:valid~label {
  font-size: 0.8rem;
  top: 10px;
  transform: translateY(-120%);
}
.forget {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 25px 0 35px 0;
  color: #fff;
}
#remember {
  accent-color: #fff;
}
.forget label {
  display: flex;
  align-items: center;
}
.forget label p {
  margin-left: 8px;
}
.wrapper a {
  color: #efefef;
  text-decoration: none;
}
.wrapper a:hover {
  text-decoration: underline;
}
button {
  background: #fff;
  color: #000;
  font-weight: 600;
  border: none;
  padding: 12px 20px;
  cursor: pointer;
  border-radius: 3px;
  font-size: 16px;
  border: 2px solid transparent;
  transition: 0.3s ease;
}
button:hover {
  color: #fff;
  border-color: #fff;
  background: rgba(255, 255, 255, 0.15);
}
.register {
  text-align: center;
  margin-top: 30px;
  color: #fff;
}
.form-error {
  background: rgba(255, 80, 80, 0.15);
  border: 1px solid rgba(255, 120, 120, 0.4);
  border-radius: 8px;
  color: #ffaaaa;
  font-size: 13px;
  padding: 10px 14px;
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.password-error {
  color: #ffaaaa;
  font-size: 13px;
  margin: -8px 0 8px;
}
.password-rules {
  list-style: none;
  padding: 0;
  margin: 0 0 16px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.password-rules li {
  font-size: 12px;
  color: rgba(255,255,255,0.5);
  padding-left: 18px;
  position: relative;
}
.password-rules li::before {
  content: '✗';
  position: absolute;
  left: 0;
  color: #ff8080;
}
.password-rules li.met {
  color: rgba(255,255,255,0.85);
}
.password-rules li.met::before {
  content: '✓';
  color: #80ffb0;
}
</style>
