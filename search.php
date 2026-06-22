<?php
session_start();
$searchResults = [];
$searchTerm = '';

$conn = new mysqli("localhost", "root", "", "Airline");

if(isset($_GET['destination']) && !empty($_GET['destination'])) {
    $searchTerm = $_GET['destination'];
    $searchResults = $conn->query("SELECT * FROM flights WHERE destination LIKE '%$searchTerm%'");
} else {
    $searchResults = $conn->query("SELECT * FROM flights");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Flights - Nexus Airways</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a1428 0%, #03060f 100%);
            color: white;
            padding: 2rem;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .search-box {
            background: rgba(18, 28, 48, 0.9);
            border-radius: 2rem;
            padding: 2rem;
            margin-bottom: 2rem;
            margin-top: 1rem;
        }
        .search-form { display: flex; gap: 1rem; flex-wrap: wrap; }
        .search-input {
            flex: 1;
            padding: 1rem;
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(0,224,255,0.3);
            border-radius: 0.5rem;
            color: white;
            font-size: 1rem;
        }
        .search-btn {
            padding: 1rem 2rem;
            background: linear-gradient(95deg, #00e0ff, #0077ff);
            border: none;
            border-radius: 0.5rem;
            color: white;
            cursor: pointer;
        }
        .flight-card {
            background: rgba(18, 28, 48, 0.7);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .book-link {
            background: linear-gradient(95deg, #00e0ff, #0077ff);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
        }
        h1 { text-align: center; margin-bottom: 1rem; }
        .nav-back { display: inline-block; margin-bottom: 1rem; color: #00e0ff; text-decoration: none; }
        .available-seats { color: #00e0ff; font-size: 0.9rem; margin-top: 0.5rem; }
        .quick-destinations { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1rem; }
        .quick-dest {
            background: rgba(0,224,255,0.2);
            padding: 0.3rem 1rem;
            border-radius: 2rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" class="nav-back">← Back to Home</a>
    <h1>✈ Search Flights</h1>
    
    <div class="search-box">
        <form method="GET" class="search-form">
            <input type="text" name="destination" class="search-input" placeholder="Enter destination (e.g., Tokyo, New York, Sweden, Netherlands, Okinawa)" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i> Search Flights</button>
        </form>
        <div class="quick-destinations">
            <span class="quick-dest" onclick="searchDestination('Tokyo')">Tokyo</span>
            <span class="quick-dest" onclick="searchDestination('New York')">New York</span>
            <span class="quick-dest" onclick="searchDestination('Sweden')">Sweden</span>
            <span class="quick-dest" onclick="searchDestination('Netherlands')">Netherlands</span>
            <span class="quick-dest" onclick="searchDestination('Okinawa')">Okinawa</span>
        </div>
    </div>
    
    <h2>Available Flights</h2>
    <?php  if($searchResults && $searchResults->num_rows > 0): ?>
        <?php while($flight = $searchResults->fetch_assoc()): ?>
        <div class="flight-card">
            <div>
                <strong style="font-size: 1.2rem;"><?php echo htmlspecialchars($flight['origin']); ?> → <?php echo htmlspecialchars($flight['destination']); ?></strong><br>
                <i class="far fa-clock"></i> <?php echo date('g:i A', strtotime($flight['departure_time'])); ?><br>
                <i class="fas fa-calendar"></i> <?php echo $flight['departure_date']; ?><br>
                <div class="available-seats">
                    <i class="fas fa-chair"></i> Available Seats: <?php echo $flight['total_seats'] - $flight['booked_seats']; ?> / <?php echo $flight['total_seats']; ?>
                </div>
                Base Price: $<?php echo number_format($flight['base_price'], 2); ?>
            </div>
            <div>
                <a href="booking.php?flight_id=<?php echo $flight['id']; ?>" class="book-link">Book Now →</a>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center; padding: 2rem;">No flights found. Try a different destination!</p>
    <?php endif; ?>
</div>

<script>
function searchDestination(dest) {
    window.location.href = 'search.php?destination=' + encodeURIComponent(dest);
}
</script>
</body>
</html> 
