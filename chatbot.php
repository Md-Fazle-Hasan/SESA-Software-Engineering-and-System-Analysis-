<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Assistant - Nexus Airways</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a1428 0%, #03060f 100%);
            color: white;
            padding: 2rem;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .chat-container {
            background: rgba(18, 28, 48, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 2rem;
            overflow: hidden;
            margin-top: 2rem;
        }
        .chat-header {
            background: rgba(0,224,255,0.2);
            padding: 1rem;
            text-align: center;
        }
        .chat-messages {
            height: 500px;
            overflow-y: auto;
            padding: 1rem;
        }
        .message {
            margin-bottom: 1rem;
            display: flex;
        }
        .message.user {
            justify-content: flex-end;
        }
        .message.bot {
            justify-content: flex-start;
        }
        .message-content {
            max-width: 70%;
            padding: 0.8rem;
            border-radius: 1rem;
        }
        .message.user .message-content {
            background: linear-gradient(95deg, #00e0ff, #0077ff);
        }
        .message.bot .message-content {
            background: rgba(255,255,255,0.1);
        }
        .chat-input {
            display: flex;
            padding: 1rem;
            background: rgba(0,0,0,0.3);
        }
        .chat-input input {
            flex: 1;
            padding: 0.8rem;
            background: rgba(255,255,255,0.1);
            border: none;
            border-radius: 0.5rem;
            color: white;
        }
        .chat-input button {
            padding: 0.8rem 1.5rem;
            background: linear-gradient(95deg, #00e0ff, #0077ff);
            border: none;
            border-radius: 0.5rem;
            color: white;
            margin-left: 0.5rem;
            cursor: pointer;
        }
        .quick-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            padding: 1rem;
            background: rgba(0,0,0,0.2);
        }
        .quick-btn {
            background: rgba(0,224,255,0.2);
            padding: 0.3rem 0.8rem;
            border-radius: 1rem;
            cursor: pointer;
            font-size: 0.8rem;
        }
        h1 {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1><i class="fas fa-robot"></i> Nexus AI Assistant</h1>
    
    <div class="chat-container">
        <div class="chat-header">
            <strong>Nexia</strong> - Your 24/7 Travel Assistant
        </div>
        
        <div class="chat-messages" id="chatMessages">
            <div class="message bot">
                <div class="message-content">
                    ✈️ Welcome to Nexus Airways! I'm Nexia, your AI assistant.<br>
                    Ask me about flights, baggage, classes, emergency assistance, or anything travel-related!
                </div>
            </div>
        </div>
        
        <div class="quick-buttons">
            <span class="quick-btn" onclick="sendQuickQuestion('baggage policy')">🧳 Baggage Policy</span>
            <span class="quick-btn" onclick="sendQuickQuestion('flight classes')">💺 Flight Classes</span>
            <span class="quick-btn" onclick="sendQuickQuestion('emergency assistance')">🚑 Emergency Care</span>
            <span class="quick-btn" onclick="sendQuickQuestion('check-in time')">⏰ Check-in Time</span>
            <span class="quick-btn" onclick="sendQuickQuestion('cancellation policy')">🔄 Cancellation</span>
        </div>
        
        <div class="chat-input">
            <input type="text" id="chatInput" placeholder="Type your question..." onkeypress="if(event.key==='Enter') sendMessage()">
            <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i> Send</button>
        </div>
    </div>
</div>

<script>
function sendQuickQuestion(question) {
    document.getElementById('chatInput').value = question;
    sendMessage();
}

function sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if(!message) return;
    
    addMessage(message, 'user');
    input.value = '';
    
    setTimeout(() => {
        const response = getBotResponse(message);
        addMessage(response, 'bot');
    }, 500);
}

function addMessage(text, sender) {
    const messagesDiv = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${sender}`;
    messageDiv.innerHTML = `<div class="message-content">${text}</div>`;
    messagesDiv.appendChild(messageDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function getBotResponse(question) {
    const q = question.toLowerCase();
    
    if(q.includes('baggage') || q.includes('luggage')) {
        return "🧳 Nexus Baggage Policy:\n• Economy: 1 carry-on (7kg) + 1 checked (23kg)\n• Business: 2 checked bags (32kg each)\n• First Class: 3 checked bags (32kg each)";
    }
    if(q.includes('class') || q.includes('economy') || q.includes('business') || q.includes('first')) {
        return "💺 Ticket Classes:\n• Economy: Standard comfort, meal included\n• Business: Extra legroom, lounge access\n• First Class: Private suite, fine dining, priority service";
    }
    if(q.includes('emergency') || q.includes('medical') || q.includes('pregnant')) {
        return "🚑 Emergency Assistance: We provide special care for pregnant passengers, physically challenged, and medical emergencies. Additional 20% fee applies for priority medical services.";
    }
    if(q.includes('check-in') || q.includes('checkin')) {
        return "⏰ Check-in opens 48 hours before departure and closes 90 minutes before flight. Online check-in is available 24/7.";
    }
    if(q.includes('cancel') || q.includes('refund')) {
        return "🔄 Cancellation Policy: Free cancellation within 24 hours of booking. After that, fees may apply. Refunds processed within 5-7 business days.";
    }
    if(q.includes('hotel') || q.includes('stay')) {
        return "🏨 Yes! Nexus offers hotel bookings at your destination. Check our Hotels page for luxury accommodations.";
    }
    if(q.includes('car') || q.includes('rental')) {
        return "🚗 We offer premium car rentals including SUVs and luxury vehicles. Visit our Cars page to book.";
    }
    
    return "✈️ Thanks for your question! For specific inquiries, please contact our customer support at support@nexusairways.com or call +1-888-NEXUS-AIR.";
}

// Add some starter suggestions
setTimeout(() => {
    addMessage("Try asking me about baggage policy, flight classes, or emergency assistance!", 'bot');
}, 1000);
</script>
</body>
</html>

