<template>
    <div class="app-container">
      <!-- Sidebar -->
      <div class="sidebar">
        <h2>SasaDoc</h2>
        <button class="new-chat-btn" @click="startNewChat">+ New Chat</button>
        <ul class="chat-list">
          <li v-for="(chat, index) in chatHistory" :key="index" @click="loadChat(index)">
            Chat {{ index + 1 }}
          </li>
        </ul>
      </div>
  
      <!-- Main Section -->
      <div class="main-container">
        <!-- Navbar -->
        <div class="navbar">
          <button class="auth-btn" @click="logout">Logout</button>
        </div>
        
        <!-- Chat Section -->
        <div class="chat-container">
          <div class="chat-box">
            <div v-for="(msg, index) in messages" :key="index" class="chat-message" :class="msg.sender">
              <p class="chat-text">{{ msg.text }}</p>
            </div>
          </div>
  
          <!-- Typing input -->
          <div class="typing-container">
            <input v-model="userMessage" @keyup.enter="sendMessage" placeholder="Ask SasaDoc anything..." />
            <button @click="sendMessage">➤</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        userMessage: "",
        messages: [{ text: "Hello! How can I assist you today?", sender: "bot" }],
        chatHistory: []
      };
    },
    methods: {
      async sendMessage() {
        if (!this.userMessage.trim()) return;
  
        this.messages.push({ text: this.userMessage, sender: "user" });
        this.messages.push({ text: "SasaDoc is thinking...", sender: "bot" });
  
        try {
          const response = await axios.post("/api/chat", { message: this.userMessage });
  
          this.messages.pop();
          this.messages.push({ text: response.data.reply.join("").trim(), sender: "bot" });
  
          // Save chat history
          this.chatHistory.push([...this.messages]);
        } catch (error) {
          this.messages.pop();
          this.messages.push({ text: "Error: Could not get a response.", sender: "bot" });
        }
  
        this.userMessage = "";
      },
      loadChat(index) {
        this.messages = this.chatHistory[index];
      },
      startNewChat() {
        this.messages = [{ text: "Hello! How can I assist you today?", sender: "bot" }];
      },
      logout() {
            axios.post('/api/logout', {}, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                }
            })
            .then(response => {
                console.log(response.data);
                alert(response.data.message);
                localStorage.removeItem("token"); // Clear token
                this.$router.push("/login"); // Redirect to login
            })
            .catch(error => {
                console.error("Logout failed:", error.response?.data || error.message);
            });
        }

    }
  };
  </script>
  
  <style scoped>
  /* App Container */
  .app-container {
    display: flex;
    height: 100vh;
    background-color: #121212;
    color: white;
  }
  
  /* Sidebar */
  .sidebar {
    width: 250px;
    background: #1e1e1e;
    padding: 20px;
    display: flex;
    flex-direction: column;
  }
  
  /* Sidebar Title */
  .sidebar h2 {
    font-size: 1.5rem;
    margin-bottom: 20px;
  }
  
  /* New Chat Button */
  .new-chat-btn {
    padding: 10px;
    background: #2a2a2a;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 20px;
    text-align: center;
    font-size: 1rem;
    width: 100%;
    max-width: 200px;
    transition: background 0.3s ease-in-out;
  }
  
  .new-chat-btn:hover {
    background: #0084ff;
  }
  
  /* Chat list */
  .chat-list {
    list-style: none;
    padding: 0;
  }
  
  .chat-list li {
    padding: 10px;
    cursor: pointer;
    border-radius: 5px;
    transition: background 0.3s;
  }
  
  .chat-list li:hover {
    background: #333;
  }
  
  /* Main Container */
  .main-container {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    background: #1a1a1a;
  }
  
  /* Navbar */
  .navbar {
    display: flex;
    justify-content: flex-end;
    background: #2a2a2a;
    padding: 10px 20px;
  }
  
  .auth-btn {
    padding: 8px 15px;
    background: #0084ff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 10px;
    color: white;
    font-size: 1rem;
    transition: background 0.3s;
  }
  
  .auth-btn:hover {
    background: #005bb5;
  }
  
  /* Chat container */
  .chat-container {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 15px;
  }
  
  /* Chat box */
  .chat-box {
    flex-grow: 1;
    overflow-y: auto;
    padding-bottom: 10px;
  }
  
  /* Individual message */
  .chat-message {
    padding: 10px;
    margin: 5px 0;
    border-radius: 8px;
    max-width: 80%;
    word-wrap: break-word;
  }
  
  .user {
    background: #0084ff;
    color: white;
    align-self: flex-end;
  }
  
  .bot {
    background: #2a2a2a;
    color: white;
    align-self: flex-start;
  }
  
  /* Input box */
  .typing-container {
    display: flex;
    align-items: center;
    padding: 10px;
    background: #2a2a2a;
    border-top: 1px solid #333;
  }
  
  input {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 4px;
    background: #333;
    color: white;
  }
  
  button {
    padding: 10px;
    margin-left: 10px;
    background: #0084ff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  </style>
  