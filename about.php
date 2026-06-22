<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Nexus Airways</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 2rem;
        }
        .about-content {
            background: rgba(18, 28, 48, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 2rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .feature {
            text-align: center;
            padding: 1.5rem;
        }
        .feature i {
            font-size: 3rem;
            color: #00e0ff;
            margin-bottom: 1rem;
        }
        .makers {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .maker {
            text-align: center;
        }
        .maker i {
            font-size: 4rem;
            background: linear-gradient(135deg, #00e0ff, #0077ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>About Nexus Airways</h1>
    
    <div class="about-content">
        <p style="font-size: 1.2rem; text-align: center; margin-bottom: 2rem;">
            Nexus Airways is revolutionizing air travel through intelligent systems, 
            exceptional service, and passenger-first innovations.
        </p>
        
        <div class="features">
            <div class="feature">
                <i class="fas fa-robot"></i>
                <h3>AI-Powered Travel</h3>
                <p>Smart flight recommendations and real-time assistance</p>
            </div>
            <div class="feature">
                <i class="fas fa-hand-holding-heart"></i>
                <h3>Emergency Care</h3>
                <p>Special medical priority system for passengers in need</p>
            </div>
            <div class="feature">
                <i class="fas fa-globe"></i>
                <h3>Global Network</h3>
                <p>Connecting major destinations worldwide</p>
            </div>
            <div class="feature">
                <i class="fas fa-shield-alt"></i>
                <h3>Secure Booking</h3>
                <p>Safe and encrypted payment processing</p>
            </div>
        </div>
    </div>
    
    <div class="about-content">
        <h2 style="text-align: center;">Our Mission</h2>
        <p style="text-align: center; margin-top: 1rem;">
            To provide seamless, intelligent, and caring air travel experiences 
            that prioritize passenger comfort, safety, and convenience.
        </p>
    </div>
    
    <div class="about-content">
        <h2 style="text-align: center;">Meet the Makers</h2>
        <div class="makers">
            <div class="maker">
                <i class="fas fa-user-astronaut"></i>
                <h3>Fazle Hasan</h3>
                <p>Lead Engineer & Design</p>
            </div>
            <div class="maker">
                <i class="fas fa-heart"></i>
                <h3>Tahmina Tuly</h3>
                <p>Lead Architect & Developer</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>