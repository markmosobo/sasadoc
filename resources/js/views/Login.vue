<template>
    <div class="app-container">
      <div class="login-container">
        <h2>Login to SasaDoc</h2>
        <form @submit.prevent="login">
          <input type="email" v-model="email" placeholder="Email" required />
          <input type="password" v-model="password" placeholder="Password" required />
          <button type="submit">Login</button>
        </form>
        <p class="switch-auth">Don't have an account? <router-link to="/register">Sign Up</router-link></p>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        email: '',
        password: ''
      };
    },
    methods: {
      async login() {
        try {
          const response = await axios.post('/api/login', {
            email: this.email,
            password: this.password
          });
          this.$router.push('/home');
        } catch (error) {
          alert('Login failed. Please check your credentials.');
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .app-container {
    display: flex;
    height: 100vh;
    justify-content: center;
    align-items: center;
    background-color: #121212;
    color: white;
  }
  
  .login-container {
    background: #1e1e1e;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    width: 300px;
  }
  
  h2 {
    margin-bottom: 20px;
  }
  
  input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: none;
    border-radius: 4px;
    background: #333;
    color: white;
  }
  
  button {
    width: 100%;
    padding: 10px;
    background: #0084ff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .switch-auth {
    margin-top: 10px;
  }
  
  .switch-auth a {
    color: #0084ff;
    text-decoration: none;
  }
  </style>
  