<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Airways - Elevating Air Travel</title>
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
            background: #0a0f1a;
            color: #fff;
        }
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(5, 10, 22, 0.95);
            backdrop-filter: blur(20px);
            z-index: 1000;
            padding: 1rem 2rem;
            border-bottom: 1px solid rgba(0, 224, 255, 0.2);
        }
        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #FFFFFF, #00e0ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .nav-links a {
            color: #f0f3fa;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }
        .nav-links a:hover {
            color: #00e0ff;
        }
        .login-btn {
            background: linear-gradient(95deg, #00e0ff, #0077ff);
            padding: 0.5rem 1.5rem;
            border-radius: 40px;
            color: white !important;
        }
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 6rem 2rem 4rem;
            background: linear-gradient(135deg, #0a1428 0%, #03060f 100%);
        }
        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 3rem;
        }
        .hero-text {
            flex: 1;
        }
        .hero-text h1 {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(to right, #fff, #7ad0ff, #00e0ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .hero-text p {
            margin: 1.5rem 0;
            color: #b9c7dd;
            font-size: 1.1rem;
        }
        .btn-primary {
            background: linear-gradient(95deg, #00e0ff, #0077ff);
            border: none;
            padding: 1rem 2rem;
            border-radius: 40px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
        }
        .section {
            padding: 80px 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
        }
        .section-title span {
            background: linear-gradient(120deg, #00e0ff, #3b82f6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .flights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        .flight-card {
            background: rgba(18, 28, 48, 0.7);
            backdrop-filter: blur(8px);
            border-radius: 24px;
            padding: 1.5rem;
            border: 1px solid rgba(0,224,255,0.2);
            transition: 0.3s;
        }
        .flight-card:hover {
            transform: translateY(-5px);
            border-color: #00e0ff;
        }
        .flight-card h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }
        .flight-time {
            color: #00e0ff;
            font-size: 1.1rem;
            margin: 0.5rem 0;
        }
        .flight-price {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 1rem 0;
        }
        .btn-book {
            background: transparent;
            border: 1px solid #00e0ff;
            padding: 0.5rem 1rem;
            border-radius: 40px;
            color: white;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
            text-align: center;
            display: inline-block;
            text-decoration: none;
        }
        .btn-book:hover {
            background: #00e0ff;
            color: #0a0f1a;
        }
        footer {
            background: #050a14;
            padding: 2rem;
            text-align: center;
            border-top: 1px solid #1e2a47;
        }
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            .hero-text h1 {
                font-size: 2rem;
            }
        }
        .user-greeting {
            color: #00e0ff;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <div class="logo">✈ Nexus Airways</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="search.php">Search Flights</a>
            <a href="booking.php">Book Flight</a>
            <a href="hotels.php">Hotels</a>
            <a href="cars.php">Cars</a>
            <a href="checkin.php">Check-in</a>
            <a href="chatbot.php">AI Assistant</a>
            <a href="about.php">About</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php" class="login-btn">Logout</a>
                <span class="user-greeting">👤 <?php echo $_SESSION['user_name']; ?></span>
            <?php else: ?>
                <a href="login.php" class="login-btn">Login/SignUp</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <div class="hero-text">
            <h1>Elevating Air Travel Through Intelligent Systems</h1>
            <p>Experience seamless innovation from flight booking to premium services. Nexus Airways redefines your journey with cutting-edge technology.</p>
            <a href="search.php" class="btn-primary">Explore Destinations →</a>
        </div>
        <div class="hero-visual">
            <div class="aircraft-showcase">
                <i class="fas fa-plane" style="font-size: 5rem; color: #00e0ff;"></i>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="section-title">Featured <span>Flights</span></div>
    <div class="flights-grid" id="featuredFlights">
        <?php
        // Direct database connection
        $conn = new mysqli("localhost", "root", "", "Airline");
        if ($conn->connect_error) {
            echo "<p>Database connection failed. Please make sure XAMPP is running.</p>";
        } else {
            $result = $conn->query("SELECT * FROM flights LIMIT 3");
            if($result && $result->num_rows > 0) {
                while($flight = $result->fetch_assoc()) {
        ?>
        <div class="flight-card">
            <h3><?php echo htmlspecialchars($flight['origin']); ?> → <?php echo htmlspecialchars($flight['destination']); ?></h3>
            <div class="flight-time"><i class="far fa-clock"></i> <?php echo date('g:i A', strtotime($flight['departure_time'])); ?></div>
            <div class="flight-price">$<?php echo number_format($flight['base_price'], 2); ?></div>
            <a href="booking.php?flight_id=<?php echo $flight['id']; ?>" class="btn-book">Book Now →</a>
        </div>
        <?php
                }
            } else {
                echo "<p>No flights available. Please run the SQL script first.</p>";
            }
            $conn->close();
        }
        ?>
    </div>
</section>

<footer>
    <p>© 2025 Nexus Airways — Elevating Air Travel Through Intelligent Systems</p>
</footer>
</body>
</html>

