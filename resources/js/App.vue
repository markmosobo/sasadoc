<template>
    <div class="chat-container">
      <div class="chat-box">
        <div v-for="(msg, index) in messages" :key="index" class="chat-message" :class="msg.sender">
          <span class="chat-avatar">
            <span v-if="msg.sender === 'bot'">🤖</span>
            <span v-else>🧑</span>
          </span>
          <p class="chat-text">{{ msg.text }}</p>
        </div>
      </div>
  
      <!-- Typing input -->
      <div class="typing-container">
        <input v-model="userMessage" @keyup.enter="sendMessage" placeholder="Ask SasaDoc anything..." />
        <button @click="sendMessage">➤</button>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        userMessage: "",
        messages: [
          { text: "Hello! How can I assist you today?", sender: "bot" }
        ],
      };
    },
    methods: {
      async sendMessage() {
        if (!this.userMessage.trim()) return;
  
        // Add user's message to chat
        this.messages.push({ text: this.userMessage, sender: "user" });
  
        // Show typing indicator
        this.messages.push({ text: "SasaDoc is thinking...", sender: "bot" });
  
        try {
          // Make API call to Laravel backend
          const response = await axios.post("/chat", {
            message: this.userMessage,
          });
  
          // Remove typing indicator and add actual response
          this.messages.pop();
          this.messages.push({ text: response.data.reply, sender: "bot" });
  
        } catch (error) {
          this.messages.pop();
          this.messages.push({ text: "Error: Could not get a response.", sender: "bot" });
        }
  
        this.userMessage = "";
      },
    },
  };
  </script>
  
  
  <style scoped>
  .chat-container {
    width: 100%;
    max-width: 600px;
    height: 80vh;
    margin: 20px auto;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: var(--incoming-chat-bg);
    border-radius: 8px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
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
    display: flex;
    align-items: center;
    padding: 10px;
    margin: 5px 0;
    border-radius: 8px;
    max-width: 80%;
    word-wrap: break-word;
  }
  
  .user {
    background: var(--outgoing-chat-bg);
    color: var(--text-color);
    align-self: flex-end;
  }
  
  .bot {
    background: var(--incoming-chat-bg);
    color: var(--text-color);
    align-self: flex-start;
  }
  
  /* Avatar */
  .chat-avatar {
    width: 30px;
    height: 30px;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
  }
  
  /* Input box */
  .typing-container {
    display: flex;
    align-items: center;
    padding: 10px;
    background: var(--incoming-chat-bg);
    border-top: 1px solid var(--incoming-chat-border);
  }
  
  input {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 4px;
    background: var(--outgoing-chat-bg);
    color: var(--text-color);
  }
  
  button {
    padding: 10px;
    margin-left: 10px;
    background: var(--icon-hover-bg);
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  </style>
  