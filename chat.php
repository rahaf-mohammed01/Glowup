<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fashion Assistant - GlowUP</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #c5ab96 0%, #d4c4b0 100%);
      min-height: 100vh;
    }

    .phone-frame {
      width: 400px;
      height: 700px;
      background: #fff;
      border-radius: 25px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.15);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      position: relative;
    }

    .header {
      background: linear-gradient(135deg, #c5ab96, #b8a089);
      padding: 20px 16px;
      text-align: center;
      color: white;
      position: relative;
    }

    .header h1 {
      margin: 0;
      font-size: 20px;
      font-weight: 600;
    }

    .header .subtitle {
      font-size: 12px;
      opacity: 0.9;
      margin-top: 4px;
    }

    .chat-body {
      flex: 1;
      overflow-y: auto;
      padding: 16px;
      background: #fafafa;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .chat-body::-webkit-scrollbar {
      width: 4px;
    }

    .chat-body::-webkit-scrollbar-track {
      background: transparent;
    }

    .chat-body::-webkit-scrollbar-thumb {
      background: #c5ab96;
      border-radius: 2px;
    }

    .message {
      max-width: 85%;
      padding: 12px 16px;
      border-radius: 18px;
      line-height: 1.5;
      font-size: 14px;
      word-wrap: break-word;
      position: relative;
      animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .message.bot {
      background: #f8f9fa;
      color: #333;
      align-self: flex-start;
      border: 1px solid #e9ecef;
      margin-left: 0;
    }

    .message.user {
      background: linear-gradient(135deg, #c5ab96, #b8a089);
      color: white;
      align-self: flex-end;
      margin-right: 0;
    }

    .typing-indicator {
      display: none;
      align-self: flex-start;
      background: #f8f9fa;
      padding: 12px 16px;
      border-radius: 18px;
      border: 1px solid #e9ecef;
    }

    .typing-dots {
      display: flex;
      gap: 4px;
    }

    .typing-dots span {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #c5ab96;
      animation: bounce 1.4s ease-in-out infinite both;
    }

    .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
    .typing-dots span:nth-child(2) { animation-delay: -0.16s; }

    @keyframes bounce {
      0%, 80%, 100% { transform: scale(0); }
      40% { transform: scale(1); }
    }

    .product-card {
      background: white;
      border-radius: 12px;
      padding: 12px;
      margin: 8px 0;
      border: 1px solid #e9ecef;
      display: flex;
      gap: 12px;
      align-items: center;
      transition: all 0.2s ease;
    }

    .product-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(197, 171, 150, 0.2);
    }

    .product-image {
      width: 50px;
      height: 50px;
      border-radius: 8px;
      background: linear-gradient(135deg, #c5ab96, #d4c4b0);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 20px;
    }

    .product-info {
      flex: 1;
    }

    .product-name {
      font-weight: 600;
      font-size: 13px;
      color: #333;
      margin-bottom: 2px;
    }

    .product-price {
      color: #c5ab96;
      font-weight: 600;
      font-size: 14px;
    }

    .quick-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin: 12px 0;
    }

    .quick-action-btn {
      background: #f8f9fa;
      border: 1px solid #c5ab96;
      color: #c5ab96;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 12px;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .quick-action-btn:hover {
      background: #c5ab96;
      color: white;
    }

    .chat-input-area {
      display: flex;
      align-items: center;
      padding: 16px;
      background: white;
      border-top: 1px solid #e9ecef;
      gap: 8px;
    }

    .chat-input-container {
      flex: 1;
      position: relative;
    }

    .chat-input {
      width: 100%;
      padding: 12px 45px 12px 16px;
      border: 2px solid #e9ecef;
      border-radius: 25px;
      outline: none;
      font-size: 14px;
      transition: border-color 0.2s ease;
    }

    .chat-input:focus {
      border-color: #c5ab96;
    }

    .attachment-btn {
      position: absolute;
      right: 8px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #c5ab96;
      font-size: 16px;
      cursor: pointer;
      padding: 8px;
      border-radius: 50%;
      transition: background 0.2s ease;
    }

    .attachment-btn:hover {
      background: #f8f9fa;
    }

    .send-btn {
      background: linear-gradient(135deg, #c5ab96, #b8a089);
      color: white;
      border: none;
      padding: 12px 16px;
      border-radius: 50%;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .send-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(197, 171, 150, 0.3);
    }

    .send-btn:disabled {
      opacity: 0.5;
      transform: none;
      cursor: not-allowed;
    }

    .image-preview {
      max-width: 150px;
      border-radius: 12px;
      margin: 8px 0;
    }

    /* Update navbar brand link positioning */
    .navbar-brand {
      text-decoration: none;
      color: inherit !important;
    }

    /* Responsive design */
    @media (max-width: 768px) {
      .phone-frame {
        width: 95vw;
        height: 85vh;
        margin: 10px;
      }
    }
  </style>
</head>
<body>



<!-- Main Chat Interface -->
<main class="d-flex justify-content-center align-items-center" style="min-height: 90vh; padding: 20px 0;">
  <div class="phone-frame">
    <div class="header">
      <h1> Fashion Assistant</h1>
    <!--  <div class="subtitle">AI-Powered Style Recommendations</div>-->
    </div>

    <div class="chat-body" id="chat-body">
      <div class="message bot">
        <div>👋 Welcome to GlowUP! I'm your personal fashion assistant.</div>
        <div style="margin-top: 8px;">I can help you with:</div>
        <div class="quick-actions">
          <button class="quick-action-btn" onclick="sendQuickMessage('Show me trending items')">
            🔥 Trending Items
          </button>
          <button class="quick-action-btn" onclick="sendQuickMessage('Help me style an outfit')">
            ✨ Style Advice
          </button>
          <button class="quick-action-btn" onclick="sendQuickMessage('Find products in my budget')">
            💰 Budget Options
          </button>
          <button class="quick-action-btn" onclick="sendQuickMessage('What\'s new today?')">
            🆕 New Arrivals
          </button>
        </div>
      </div>
    </div>

    <div class="typing-indicator" id="typing-indicator">
      <div class="typing-dots">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>

    <div class="chat-input-area">
      <div class="chat-input-container">
        <input type="text" class="chat-input" id="chat-input" placeholder="Ask me about fashion, products, or styling...">
        <input type="file" id="chat-image" accept="image/*" style="display: none;">
        <button class="attachment-btn" id="camera-btn" title="Upload image">
          <i class="fas fa-camera"></i>
        </button>
      </div>
      <button class="send-btn" id="send-btn" title="Send message">
        <i class="fas fa-paper-plane"></i>
      </button>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Enhanced chatbot with better e-commerce features
const chatBody = document.getElementById('chat-body');
const chatInput = document.getElementById('chat-input');
const chatImage = document.getElementById('chat-image');
const cameraBtn = document.getElementById('camera-btn');
const sendBtn = document.getElementById('send-btn');
const typingIndicator = document.getElementById('typing-indicator');

// Product database
const products = [
  { name: "Tie-front Blouse", price: 200.99, category: "tops", icon: "👚", description: "Elegant and versatile" },
  { name: "Lace-detail Blouse", price: 200.99, category: "tops", icon: "👕", description: "Romantic and feminine" },
  { name: " Sneakers", price: 149.99, category: "shoes", icon: "👟", description: "Smart and comfortable" },
  { name: "Smart Jacket", price: 199.99, category: "outerwear", icon: "🧥", description: "Tech-enhanced style" },
  { name: "Designer Dress", price: 299.99, category: "dresses", icon: "👗", description: "Perfect for special occasions" },
  { name: "Casual Jeans", price: 129.99, category: "bottoms", icon: "👖", description: "Everyday comfort" },
  { name: "Elegant Heels", price: 179.99, category: "shoes", icon: "👠", description: "Step up your style" },
  { name: "Cozy Sweater", price: 159.99, category: "tops", icon: "🧶", description: "Warm and stylish" }
];

// Event listeners
sendBtn.addEventListener('click', sendTextMessage);
chatInput.addEventListener('keypress', (e) => {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendTextMessage();
  }
});
cameraBtn.addEventListener('click', () => chatImage.click());
chatImage.addEventListener('change', sendImageMessage);

// Auto-resize input
chatInput.addEventListener('input', () => {
  sendBtn.disabled = !chatInput.value.trim();
});

function sendQuickMessage(message) {
  chatInput.value = message;
  sendTextMessage();
}

function appendMessage(sender, content, isHtml = false) {
  const msgDiv = document.createElement('div');
  msgDiv.classList.add('message', sender);
  
  if (isHtml) {
    msgDiv.innerHTML = content;
  } else {
    msgDiv.textContent = content;
  }
  
  chatBody.appendChild(msgDiv);
  scrollToBottom();
}

function scrollToBottom() {
  chatBody.scrollTop = chatBody.scrollHeight;
}

function showTyping() {
  typingIndicator.style.display = 'block';
  scrollToBottom();
}

function hideTyping() {
  typingIndicator.style.display = 'none';
}

function createProductCard(product) {
  return `
    <div class="product-card">
      <div class="product-image">${product.icon}</div>
      <div class="product-info">
        <div class="product-name">${product.name}</div>
        <div class="product-price">SAR ${product.price}</div>
        <div style="font-size: 11px; color: #666; margin-top: 2px;">${product.description}</div>
      </div>
    </div>
  `;
}

function getSmartResponse(userMessage) {
  const msg = userMessage.toLowerCase();
  
  // Greeting responses
  if (msg.includes('hello') || msg.includes('hi') || msg.includes('hey')) {
    return "Hello! 👋 Welcome to GlowUP! I'm here to help you find the perfect fashion pieces. What are you looking for today?";
  }
  
  // Trending items
  if (msg.includes('trending') || msg.includes('popular') || msg.includes('hot')) {
    const trendingProducts = products.slice(0, 3);
    let response = "🔥 Here are our trending items right now:\n\n";
    response += trendingProducts.map(p => createProductCard(p)).join('');
    response += "\n\nWould you like to see more details about any of these items?";
    return response;
  }
  
  // Budget-related queries
  if (msg.includes('budget') || msg.includes('cheap') || msg.includes('affordable') || msg.includes('under')) {
    const budgetItems = products.filter(p => p.price < 180).slice(0, 3);
    let response = "💰 Here are some great budget-friendly options:\n\n";
    response += budgetItems.map(p => createProductCard(p)).join('');
    return response;
  }
  
  // New arrivals
  if (msg.includes('new') || msg.includes('latest') || msg.includes('arrivals')) {
    const newItems = products.slice(-3);
    let response = "🆕 Check out our latest arrivals:\n\n";
    response += newItems.map(p => createProductCard(p)).join('');
    return response;
  }
  
  // Category-specific searches
  if (msg.includes('dress')) {
    const dresses = products.filter(p => p.category === 'dresses');
    let response = "👗 Perfect! Here are our beautiful dresses:\n\n";
    response += dresses.map(p => createProductCard(p)).join('');
    return response;
  }
  
  if (msg.includes('shoe') || msg.includes('sneaker') || msg.includes('heel')) {
    const shoes = products.filter(p => p.category === 'shoes');
    let response = "👟 Great choice! Here are our shoe collections:\n\n";
    response += shoes.map(p => createProductCard(p)).join('');
    return response;
  }
  
  if (msg.includes('jacket') || msg.includes('coat')) {
    const outerwear = products.filter(p => p.category === 'outerwear');
    let response = "🧥 Stay stylish and warm with these pieces:\n\n";
    response += outerwear.map(p => createProductCard(p)).join('');
    return response;
  }
  
  if (msg.includes('top') || msg.includes('blouse') || msg.includes('shirt')) {
    const tops = products.filter(p => p.category === 'tops');
    let response = "👚 Here are some gorgeous tops for you:\n\n";
    response += tops.map(p => createProductCard(p)).join('');
    return response;
  }
  
  // Styling advice
  if (msg.includes('style') || msg.includes('outfit') || msg.includes('match') || msg.includes('pair')) {
    return `✨ I'd love to help you style the perfect outfit! Here are some great combinations:

    <div class="product-card">
      <div class="product-image">💼</div>
      <div class="product-info">
        <div class="product-name">Professional Look</div>
        <div style="font-size: 12px; color: #666;">Smart Jacket + Tie-front Blouse + Elegant Heels</div>
      </div>
    </div>
    
    <div class="product-card">
      <div class="product-image">🌟</div>
      <div class="product-info">
        <div class="product-name">Casual Chic</div>
        <div style="font-size: 12px; color: #666;">Casual Jeans + Cozy Sweater + AI-Powered Sneakers</div>
      </div>
    </div>

    Tell me about the occasion or your style preference for more personalized suggestions!`;
  }
  
  // Size questions
  if (msg.includes('size') || msg.includes('fit')) {
    return "📏 For sizing information:\n• Check our size guide on each product page\n• We offer sizes XS to XXL\n• Free exchanges within 30 days\n• Need help with a specific item? Just ask!";
  }
  
  // Shipping questions
  if (msg.includes('shipping') || msg.includes('delivery')) {
    return "🚚 Shipping Information:\n• Free shipping on orders over SAR 300\n• Standard delivery: 3-5 business days\n• Express delivery available\n• Track your order anytime in your account";
  }
  
  // Colors
  if (msg.includes('color') || msg.includes('black') || msg.includes('white') || msg.includes('red')) {
    return "🎨 We have items in various colors! Our most popular colors this season are:\n• Neutral tones (beige, cream, brown)\n• Classic black and white\n• Earthy colors (olive, rust, navy)\n\nWhat color are you looking for specifically?";
  }
  
  // Generic positive responses
  if (msg.includes('good') || msg.includes('nice') || msg.includes('love') || msg.includes('like')) {
    const randomProducts = products.sort(() => 0.5 - Math.random()).slice(0, 2);
    let response = "I'm so glad you like it! ✨ You might also love these items:\n\n";
    response += randomProducts.map(p => createProductCard(p)).join('');
    return response;
  }
  
  // Thank you responses
  if (msg.includes('thank') || msg.includes('thanks')) {
    return "You're very welcome! 😊 I'm here whenever you need fashion advice or help finding the perfect pieces. Happy shopping at GlowUP! ✨";
  }
  
  // Default response with suggestions
  return `I'd love to help you find exactly what you're looking for! 💫 

Try asking me about:
• "Show me trending items" 🔥
• "Help me style an outfit" ✨  
• "What's in my budget?" 💰
• "Do you have dresses?" 👗
• "Size and shipping info" 📦

Or browse our categories: Women, Men, Kids. What interests you most?`;
}

async function sendTextMessage() {
  const userMessage = chatInput.value.trim();
  if (!userMessage) return;
  
  // Show user message
  appendMessage('user', userMessage);
  chatInput.value = '';
  sendBtn.disabled = true;
  
  // Show typing indicator
  showTyping();
  
  // Simulate thinking time
  await new Promise(resolve => setTimeout(resolve, 1000 + Math.random() * 1000));
  
  hideTyping();
  
  // Get and show response
  const response = getSmartResponse(userMessage);
  appendMessage('bot', response, true);
}

async function sendImageMessage() {
  if (!chatImage.files[0]) return;
  
  const file = chatImage.files[0];
  const imageUrl = URL.createObjectURL(file);
  
  // Show user's image
  appendMessage('user', `<img src="${imageUrl}" class="image-preview" alt="Uploaded image">`, true);
  
  showTyping();
  await new Promise(resolve => setTimeout(resolve, 1500));
  hideTyping();
  
  // AI response for image
  const imageResponse = `📸 Great photo! Based on what I can see, here are some styling suggestions:

  ${createProductCard(products[Math.floor(Math.random() * products.length)])}
  ${createProductCard(products[Math.floor(Math.random() * products.length)])}
  
  Would you like more specific recommendations based on the occasion or style you're going for?`;
  
  appendMessage('bot', imageResponse, true);
  
  // Clear the file input
  chatImage.value = '';
}

// Initialize
sendBtn.disabled = true;
</script>

</body>
</html>