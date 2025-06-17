<!-- Simple Chat Button - Add this to any page -->
<style>
  /* Simple Chat Button Styles */
  .simple-chat-btn {
    position: fixed;
    left: 20px;
    bottom: 20px;
    width: 65px;
    height: 65px;
    background: linear-gradient(135deg, #c5ab96, #b8a089);
    color: white;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    box-shadow: 0 4px 20px rgba(197, 171, 150, 0.4);
    transition: all 0.3s ease;
    cursor: pointer;
    z-index: 9999;
    border: 3px solid white;
    text-decoration: none;
  }

  .simple-chat-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(197, 171, 150, 0.6);
    color: white;
    text-decoration: none;
  }

  .simple-chat-btn:active {
    transform: scale(0.95);
  }

  /* Pulse animation */
  .simple-chat-btn::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: rgba(197, 171, 150, 0.3);
    animation: chatPulse 2s infinite;
    z-index: -1;
  }

  @keyframes chatPulse {
    0% {
      transform: scale(1);
      opacity: 1;
    }
    70% {
      transform: scale(1.4);
      opacity: 0;
    }
    100% {
      transform: scale(1.4);
      opacity: 0;
    }
  }

  /* Notification badge */
  .chat-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: chatBounce 1s infinite;
  }

  @keyframes chatBounce {
    0%, 20%, 50%, 80%, 100% {
      transform: translateY(0);
    }
    40% {
      transform: translateY(-10px);
    }
    60% {
      transform: translateY(-5px);
    }
  }

  /* Mobile responsive */
  @media (max-width: 768px) {
    .simple-chat-btn {
      left: 15px;
      bottom: 15px;
      width: 55px;
      height: 55px;
      font-size: 20px;
    }
  }
</style>

<!-- Chat Button HTML -->
<a href="chat.php" class="simple-chat-btn" target="_blank" title="Chat with Fashion Assistant">
  <i class="fas fa-comments"></i>
  <span class="chat-badge">!</span>
</a>

<!-- Demo content -->
<div style="padding: 50px; font-family: Arial, sans-serif;">
  <h1>Simple Chat Button Demo</h1>
  <p>This is a simple chat button that opens your Fashion Assistant page. Click the button in the bottom left!</p>
  
  <h2>How to Use:</h2>
  <ol>
    <li>Save your fashion assistant code as <code>fashion-assistant.html</code></li>
    <li>Copy the CSS and HTML code above</li>
    <li>Add it to any page where you want the chat button</li>
    <li>Make sure Font Awesome is included for the icons</li>
  </ol>

  <h2>Integration Options:</h2>
  
  <h3>Option 1: Direct Link (Recommended)</h3>
  <pre><code>&lt;a href="fashion-assistant.html" class="simple-chat-btn" target="_blank"&gt;
  &lt;i class="fas fa-comments"&gt;&lt;/i&gt;
&lt;/a&gt;</code></pre>

  <h3>Option 2: JavaScript Function</h3>
  <pre><code>&lt;button class="simple-chat-btn" onclick="openFashionChat()"&gt;
  &lt;i class="fas fa-comments"&gt;&lt;/i&gt;
&lt;/button&gt;

&lt;script&gt;
function openFashionChat() {
  // Open in new tab
  window.open('fashion-assistant.html', '_blank');
  
  // OR open in popup
  // window.open('chat.php', 'FashionChat', 'width=420,height=720');
}
&lt;/script&gt;</code></pre>

  <h3>Option 3: Same Window Navigation</h3>
  <pre><code>&lt;a href="fashion-assistant.html" class="simple-chat-btn"&gt;
  &lt;i class="fas fa-comments"&gt;&lt;/i&gt;
&lt;/a&gt;</code></pre>
</div>