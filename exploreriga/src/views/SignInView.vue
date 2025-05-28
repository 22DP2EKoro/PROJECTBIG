
<template>
  <div class="signin-container">
    <div class="wrapper">
      <header class="nav-top"></header>

      <form @submit.prevent="handleSubmit">
        <h2>{{ isRegistering ? 'Register' : 'Login' }}</h2>

        <div class="input-field">
          <input
            id="email"
            type="text"
            v-model="email"
            required
            placeholder=" "
          />
          <label for="email">Enter your email</label>
        </div>

        <div class="input-field">
          <input
            id="password"
            type="password"
            v-model="password"
            required
            placeholder=" "
          />
          <label for="password">Enter your password</label>
        </div>

        <div class="forget">
          <label for="remember">
            <input type="checkbox" id="remember" v-model="rememberMe" />
            <span>Remember me</span>
          </label>
          <a href="#">Forgot password?</a>
        </div>

        <button type="submit">{{ isRegistering ? 'Register' : 'Log In' }}</button>

        <div class="register">
          <p>
            <span v-if="isRegistering">Already have an account? </span>
            <span v-else>Don't have an account? </span>
            <a href="#" @click="toggleForm">{{ isRegistering ? 'Login' : 'Register' }}</a>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { signIn } from '../auth' // Assuming this sets isAuthenticated


export default {
  data() {
    return {
      email: '',
      password: '',
      rememberMe: false,
      isRegistering: false,
    }
  },
 
 
  methods: {
    async handleSubmit() {
      console.log('Form submitted with:', this.email, this.password)

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
          alert(msg)

          // Mark user as authenticated
          signIn({ email: this.email, role })
        

          // Redirect based on role
          if (role === 'admin') {
            this.$router.push('/admin')
          } else {
            this.$router.push('/')
          }
        } else {
          alert('Error: ' + msg)
        }
      } catch (error) {
        console.error('There was an error with the request!', error)
        alert('Server error. Try again later.')
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
</style>
