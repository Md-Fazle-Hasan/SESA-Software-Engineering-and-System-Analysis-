<?php
session_start();
$conn = new mysqli("localhost", "root", "", "Airline");

// Handle hotel booking
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_hotel'])) {
    $user_id = $_SESSION['user_id'];
    $hotel_id = $_POST['hotel_id'];
    $hotel_name = $_POST['hotel_name'];
    $location = $_POST['location'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $price_per_night = $_POST['price_per_night'];
    
    $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
    $total_price = $price_per_night * $nights;
    
    $query = "INSERT INTO hotel_bookings (user_id, hotel_id, hotel_name, location, check_in_date, check_out_date, price_per_night, total_price) 
              VALUES ($user_id, $hotel_id, '$hotel_name', '$location', '$check_in', '$check_out', $price_per_night, $total_price)";
    
    if($conn->query($query)) {
        echo "<script>alert('Hotel booked successfully! Check your dashboard.'); window.location.href='dashboard.php';</script>";
    }
}

$hotels = $conn->query("SELECT * FROM hotels");
?>
<!DOCTYPE html>
<html>
<head><title>Hotels - Nexus Airways</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0a1428 0%,#03060f 100%);color:white;padding:2rem;}
.container{max-width:1200px;margin:0 auto;}
h1{text-align:center;margin-bottom:2rem;}
.hotel-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:2rem;}
.hotel-card{background:rgba(18,28,48,0.9);border-radius:1rem;padding:1.5rem;text-align:center;}
.price{font-size:1.5rem;color:#00e0ff;margin:0.5rem 0;}
.book-btn{background:linear-gradient(95deg,#00e0ff,#0077ff);padding:0.5rem 1rem;border:none;border-radius:0.5rem;color:white;margin-top:1rem;cursor:pointer;}
.nav-back{display:inline-block;margin-bottom:1rem;color:#00e0ff;text-decoration:none;}
.booking-form{display:none;margin-top:1rem;padding:1rem;background:rgba(0,0,0,0.5);border-radius:0.5rem;}
.booking-form input{width:100%;padding:0.5rem;margin:0.3rem 0;border-radius:0.3rem;border:none;}
.booking-form button{background:#00e0ff;color:#0a1428;padding:0.5rem;border:none;border-radius:0.3rem;cursor:pointer;}
</style>
</head>
<body>
<div class="container">
<a href="index.php" class="nav-back">← Back</a>
<h1>🏨 Luxury Hotels</h1>
<div class="hotel-grid">
<?php while($hotel = $hotels->fetch_assoc()): ?>
<div class="hotel-card">
    <i class="fas fa-hotel" style="font-size:3rem;color:#00e0ff;"></i>
    <h3><?php echo $hotel['name']; ?></h3>
    <p><?php echo $hotel['location']; ?></p>
    <div class="price">$<?php echo $hotel['price_per_night']; ?>/night</div>
    <button class="book-btn" onclick="showBookingForm(<?php echo $hotel['id']; ?>, '<?php echo addslashes($hotel['name']); ?>', '<?php echo addslashes($hotel['location']); ?>', <?php echo $hotel['price_per_night']; ?>)">Book Hotel →</button>
    
    <div id="bookingForm_<?php echo $hotel['id']; ?>" class="booking-form">
        <form method="POST">
            <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
            <input type="hidden" name="hotel_name" value="<?php echo $hotel['name']; ?>">
            <input type="hidden" name="location" value="<?php echo $hotel['location']; ?>">
            <input type="hidden" name="price_per_night" value="<?php echo $hotel['price_per_night']; ?>">
            <input type="date" name="check_in" required placeholder="Check-in Date">
            <input type="date" name="check_out" required placeholder="Check-out Date">
            <button type="submit" name="book_hotel">Confirm Booking</button>
            <button type="button" onclick="hideBookingForm(<?php echo $hotel['id']; ?>)" style="background:#ff4444;">Cancel</button>
        </form>
    </div>
</div>
<?php endwhile; ?>
</div>
</div>

<script>
function showBookingForm(id, name, location, price) {
    const form = document.getElementById('bookingForm_' + id);
    if(form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

function hideBookingForm(id) {
    document.getElementById('bookingForm_' + id).style.display = 'none';
}
</script>
</body>
</html>

