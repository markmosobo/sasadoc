<template>
    <div class="auth-container">
      <div class="auth-box">
        <h2>Register</h2>
        <form @submit.prevent="register">
          <input v-model="firstName" type="text" placeholder="First Name" required />
          <input v-model="lastName" type="text" placeholder="Last Name" required />
          <input v-model="email" type="email" placeholder="Email" required />
          <input v-model="password" type="password" placeholder="Password" required />
          <input v-model="confirmPassword" type="password" placeholder="Confirm Password" required />
          <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <router-link to="/">Login</router-link></p>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios"; // Ensure Axios is imported
  
  export default {
    data() {
      return {
        firstName: "",
        lastName: "",
        email: "",
        password: "",
        confirmPassword: "",
      };
    },
    methods: {
      async register() {
        if (this.password !== this.confirmPassword) {
          alert("Passwords do not match!");
          return;
        }
  
        const userData = {
          first_name: this.firstName,
          last_name: this.lastName,
          email: this.email,
          password: this.password,
          password_confirmation: this.confirmPassword,
        };
  
        try {
          const response = await axios.post("/api/register", userData);
          console.log("User registered successfully:", response.data);
          this.$router.push("/");
  
        } catch (error) {
          console.error("Registration error:", error.response?.data || error.message);
          alert(error.response?.data?.message || "Registration failed. Please try again.");
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #121212;
    color: white;
  }
  
  .auth-box {
    background: #1e1e1e;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
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
    border-radius: 5px;
    background: #333;
    color: white;
  }
  
  button {
    width: 100%;
    padding: 10px;
    background: #0084ff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  p {
    margin-top: 10px;
  }
  </style>
  