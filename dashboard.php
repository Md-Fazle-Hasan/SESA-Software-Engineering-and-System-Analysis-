<?php 
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "Airline");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$currentDateTime = date('Y-m-d H:i:s');
$currentDate = date('Y-m-d');

// Get flight bookings
$flightBookings = $conn->query("SELECT b.*, f.flight_no, f.origin, f.destination, f.departure_time, f.departure_date, f.status as flight_status
                          FROM bookings b 
                          JOIN flights f ON b.flight_id = f.id 
                          WHERE b.user_id = $user_id 
                          ORDER BY b.booking_date DESC");

// Get hotel bookings
$hotelBookings = $conn->query("SELECT * FROM hotel_bookings WHERE user_id = $user_id ORDER BY booking_date DESC");

// Get car bookings
$carBookings = $conn->query("SELECT * FROM car_bookings WHERE user_id = $user_id ORDER BY booking_date DESC");

// Calculate stats with proper upcoming flight count
$totalBookings = $flightBookings->num_rows;
$totalSpent = 0;
$upcomingFlightsCount = 0;
$pastFlightsCount = 0;
$flightBookingsData = [];
$upcomingFlightsList = [];

if($flightBookings && $flightBookings->num_rows > 0) {
    $flightBookings->data_seek(0);
    while($booking = $flightBookings->fetch_assoc()) {
        $totalSpent += $booking['total_price'];
        $flightDateTime = $booking['departure_date'] . ' ' . $booking['departure_time'];
        
        // Properly compare flight date/time with current date/time
        if(strtotime($flightDateTime) > time()) {
            $upcomingFlightsCount++;
            $upcomingFlightsList[] = $booking;
        } else {
            $pastFlightsCount++;
        }
        $flightBookingsData[] = $booking;
    }
}

// Get hotel stats
$totalHotelBookings = $hotelBookings->num_rows;
$totalCarBookings = $carBookings->num_rows;
$totalSpentHotels = 0;
$totalSpentCars = 0;

if($hotelBookings && $hotelBookings->num_rows > 0) {
    $hotelBookings->data_seek(0);
    while($hotel = $hotelBookings->fetch_assoc()) {
        $totalSpentHotels += $hotel['total_price'];
    }
}

if($carBookings && $carBookings->num_rows > 0) {
    $carBookings->data_seek(0);
    while($car = $carBookings->fetch_assoc()) {
        $totalSpentCars += $car['total_price'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Nexus Airways</title>
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
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.pexels.com/photos/2169620/pexels-photo-2169620.jpeg?auto=compress&cs=tinysrgb&w=1600');
            background-size: cover;
            background-position: center;
            opacity: 0.08;
            z-index: -2;
            animation: slowZoom 30s infinite alternate;
        }

        @keyframes slowZoom {
            0% { transform: scale(1); opacity: 0.06; }
            100% { transform: scale(1.1); opacity: 0.1; }
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(0, 224, 255, 0.3);
            border-radius: 50%;
            animation: floatParticle 15s infinite linear;
        }

        @keyframes floatParticle {
            0% { transform: translateY(100vh) translateX(0); opacity: 0; }
            10% { opacity: 0.5; }
            90% { opacity: 0.5; }
            100% { transform: translateY(-10vh) translateX(50px); opacity: 0; }
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
            background: rgba(18, 28, 48, 0.6);
            backdrop-filter: blur(20px);
            padding: 1.5rem 2rem;
            border-radius: 2rem;
            border: 1px solid rgba(0, 224, 255, 0.2);
        }

        .welcome-section h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #fff, #00e0ff, #7ad0ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .welcome-section p {
            color: #b9c7dd;
            margin-top: 0.3rem;
        }

        .member-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            padding: 0.2rem 0.8rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 600;
            color: #0a1428;
            margin-top: 0.5rem;
        }

        .nav-links {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            background: rgba(0, 224, 255, 0.1);
            border-radius: 2rem;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
            border: 1px solid rgba(0, 224, 255, 0.2);
        }

        .nav-links a:hover {
            background: rgba(0, 224, 255, 0.3);
            transform: translateY(-2px);
        }

        .logout-btn {
            background: rgba(255, 68, 68, 0.2) !important;
            border-color: rgba(255, 68, 68, 0.3) !important;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: rgba(18, 28, 48, 0.6);
            backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(0, 224, 255, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: #00e0ff;
        }

        .stat-icon {
            font-size: 2.5rem;
            color: #00e0ff;
            margin-bottom: 0.8rem;
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            color: #00e0ff;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #b9c7dd;
            margin-top: 0.3rem;
        }

        .section {
            background: rgba(18, 28, 48, 0.6);
            backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(0, 224, 255, 0.15);
        }

        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title h2 {
            font-size: 1.3rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title h2 i {
            color: #00e0ff;
        }

        .flights-grid, .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1rem;
        }

        .flight-card, .service-card {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 1rem;
            padding: 1rem;
            border-left: 3px solid #00e0ff;
        }

        .flight-route {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .flight-details, .service-details {
            font-size: 0.8rem;
            color: #b9c7dd;
            margin-top: 0.5rem;
        }

        .count-badge {
            background: #00e0ff;
            color: #0a1428;
            padding: 0.2rem 0.6rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #b9c7dd;
        }

        .time-left {
            background: rgba(0, 224, 255, 0.2);
            padding: 0.2rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.7rem;
            display: inline-block;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .container { padding: 1rem; }
            .flights-grid, .services-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

<div class="particles" id="particles"></div>

<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="welcome-section">
            <h1><i class="fas fa-crown" style="color: #ffd700;"></i> Welcome, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>!</h1>
            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
            <div class="member-badge"><i class="fas fa-gem"></i> Nexus Elite Member</div>
        </div>
        <div class="nav-links">
            <a href="booking.php"><i class="fas fa-ticket-alt"></i> Book Flight</a>
            <a href="search.php"><i class="fas fa-search"></i> Search</a>
            <a href="hotels.php"><i class="fas fa-hotel"></i> Hotels</a>
            <a href="cars.php"><i class="fas fa-car"></i> Cars</a>
            <a href="checkin.php"><i class="fas fa-check-in"></i> Check-in</a>
            <a href="chatbot.php"><i class="fas fa-robot"></i> AI Help</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-plane"></i></div>
            <div class="stat-number"><?php echo $totalBookings; ?></div>
            <div class="stat-label">Flight Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-number" id="upcomingCount"><?php echo $upcomingFlightsCount; ?></div>
            <div class="stat-label">Upcoming Flights</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-hotel"></i></div>
            <div class="stat-number"><?php echo $totalHotelBookings; ?></div>
            <div class="stat-label">Hotel Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-car"></i></div>
            <div class="stat-number"><?php echo $totalCarBookings; ?></div>
            <div class="stat-label">Car Rentals</div>
        </div>
    </div>

    <!-- Upcoming Flights Section -->
    <div class="section">
        <div class="section-title">
            <h2><i class="fas fa-clock"></i> Upcoming Flights 
                <span class="count-badge"><?php echo $upcomingFlightsCount; ?></span>
            </h2>
            <i class="fas fa-hourglass-half" style="color: #00e0ff;"></i>
        </div>
        
        <?php if($upcomingFlightsCount > 0): ?>
            <div class="flights-grid">
                <?php foreach($upcomingFlightsList as $flight): ?>
                    <?php 
                        $flightDateTime = strtotime($flight['departure_date'] . ' ' . $flight['departure_time']);
                        $timeDiff = $flightDateTime - time();
                        $daysLeft = floor($timeDiff / (60 * 60 * 24));
                        $hoursLeft = floor(($timeDiff % (60 * 60 * 24)) / (60 * 60));
                        
                        if($daysLeft > 0) {
                            $timeLeftText = "$daysLeft days left";
                        } elseif($hoursLeft > 0) {
                            $timeLeftText = "$hoursLeft hours left";
                        } else {
                            $timeLeftText = "Today!";
                        }
                    ?>
                    <div class="flight-card">
                        <div class="flight-route">
                            <i class="fas fa-plane-departure"></i> <?php echo $flight['origin']; ?> → <?php echo $flight['destination']; ?>
                        </div>
                        <div class="flight-details">
                            <div><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($flight['departure_date'])); ?></div>
                            <div><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($flight['departure_time'])); ?></div>
                            <div><i class="fas fa-ticket-alt"></i> Ref: <?php echo $flight['booking_ref']; ?></div>
                            <div class="time-left"><i class="fas fa-hourglass-start"></i> <?php echo $timeLeftText; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times" style="font-size: 2rem;"></i>
                <p>No upcoming flights. <a href="booking.php" style="color: #00e0ff;">Book a flight now!</a></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Hotel Bookings Section -->
    <div class="section">
        <div class="section-title">
            <h2><i class="fas fa-hotel"></i> My Hotel Bookings</h2>
            <a href="hotels.php" style="color: #00e0ff; font-size: 0.8rem;">Book More →</a>
        </div>
        
        <?php 
        $hotelBookings->data_seek(0);
        if($hotelBookings && $hotelBookings->num_rows > 0): ?>
            <div class="services-grid">
                <?php while($hotel = $hotelBookings->fetch_assoc()): ?>
                    <div class="service-card" style="border-left-color: #ffaa00;">
                        <div class="flight-route">
                            <i class="fas fa-building"></i> <?php echo htmlspecialchars($hotel['hotel_name']); ?>
                        </div>
                        <div class="flight-details">
                            <div><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel['location']); ?></div>
                            <div><i class="fas fa-calendar-check"></i> Check-in: <?php echo $hotel['check_in_date']; ?></div>
                            <div><i class="fas fa-calendar-times"></i> Check-out: <?php echo $hotel['check_out_date']; ?></div>
                            <div><i class="fas fa-dollar-sign"></i> Total: $<?php echo number_format($hotel['total_price'], 2); ?></div>
                            <div><i class="fas fa-tag"></i> Ref: #<?php echo $hotel['id']; ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-hotel" style="font-size: 2rem; opacity: 0.5;"></i>
                <p>No hotel bookings yet. <a href="hotels.php" style="color: #00e0ff;">Book a hotel!</a></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Car Bookings Section -->
    <div class="section">
        <div class="section-title">
            <h2><i class="fas fa-car"></i> My Car Rentals</h2>
            <a href="cars.php" style="color: #00e0ff; font-size: 0.8rem;">Rent More →</a>
        </div>
        
        <?php 
        $carBookings->data_seek(0);
        if($carBookings && $carBookings->num_rows > 0): ?>
            <div class="services-grid">
                <?php while($car = $carBookings->fetch_assoc()): ?>
                    <div class="service-card" style="border-left-color: #00ff88;">
                        <div class="flight-route">
                            <i class="fas fa-car-side"></i> <?php echo htmlspecialchars($car['car_name']); ?>
                        </div>
                        <div class="flight-details">
                            <div><i class="fas fa-tag"></i> <?php echo htmlspecialchars($car['car_type']); ?></div>
                            <div><i class="fas fa-calendar-plus"></i> Pickup: <?php echo $car['pickup_date']; ?></div>
                            <div><i class="fas fa-calendar-minus"></i> Return: <?php echo $car['return_date']; ?></div>
                            <div><i class="fas fa-dollar-sign"></i> Total: $<?php echo number_format($car['total_price'], 2); ?></div>
                            <div><i class="fas fa-tag"></i> Ref: #<?php echo $car['id']; ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-car" style="font-size: 2rem; opacity: 0.5;"></i>
                <p>No car rentals yet. <a href="cars.php" style="color: #00e0ff;">Rent a car!</a></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Past Flights Section -->
    <?php if($pastFlightsCount > 0): ?>
    <div class="section">
        <div class="section-title">
            <h2><i class="fas fa-history"></i> Past Flights</h2>
            <i class="fas fa-check-circle" style="color: #00ff88;"></i>
        </div>
        <div class="flights-grid">
            <?php 
            $flightBookings->data_seek(0);
            while($flight = $flightBookings->fetch_assoc()):
                $flightDateTime = $flight['departure_date'] . ' ' . $flight['departure_time'];
                if(strtotime($flightDateTime) <= time()):
            ?>
                <div class="flight-card" style="border-left-color: #ffaa00; opacity: 0.8;">
                    <div class="flight-route">
                        <i class="fas fa-check-circle" style="color: #00ff88;"></i> <?php echo $flight['origin']; ?> → <?php echo $flight['destination']; ?>
                    </div>
                    <div class="flight-details">
                        <div><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($flight['departure_date'])); ?></div>
                        <div><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($flight['departure_time'])); ?></div>
                        <div><i class="fas fa-ticket-alt"></i> Ref: <?php echo $flight['booking_ref']; ?></div>
                        <div><i class="fas fa-dollar-sign"></i> $<?php echo number_format($flight['total_price'], 2); ?></div>
                    </div>
                </div>
            <?php 
                endif;
            endwhile; 
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function createParticles() {
    const container = document.getElementById('particles');
    if(!container) return;
    
    for(let i = 0; i < 50; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        const size = Math.random() * 4 + 2;
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDuration = (Math.random() * 20 + 10) + 's';
        particle.style.animationDelay = (Math.random() * 10) + 's';
        container.appendChild(particle);
    }
}

createParticles();

// Auto-refresh every minute to update upcoming flight count
setInterval(function() {
    location.reload();
}, 60000);
</script>
</body>
</html>