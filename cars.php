<?php
session_start();
$conn = new mysqli("localhost", "root", "", "Airline");

// Handle car booking
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_car'])) {
    $user_id = $_SESSION['user_id'];
    $car_id = $_POST['car_id'];
    $car_name = $_POST['car_name'];
    $car_type = $_POST['car_type'];
    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];
    $price_per_day = $_POST['price_per_day'];
    
    $days = (strtotime($return_date) - strtotime($pickup_date)) / (60 * 60 * 24);
    $total_price = $price_per_day * $days;
    
    $query = "INSERT INTO car_bookings (user_id, car_id, car_name, car_type, pickup_date, return_date, price_per_day, total_price) 
              VALUES ($user_id, $car_id, '$car_name', '$car_type', '$pickup_date', '$return_date', $price_per_day, $total_price)";
    
    if($conn->query($query)) {
        echo "<script>alert('Car booked successfully! Check your dashboard.'); window.location.href='dashboard.php';</script>";
    }
}

$cars = $conn->query("SELECT * FROM car_rentals");
?>
<!DOCTYPE html>
<html>
<head><title>Cars - Nexus Airways</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0a1428 0%,#03060f 100%);color:white;padding:2rem;}
.container{max-width:1200px;margin:0 auto;}
h1{text-align:center;margin-bottom:2rem;}
.car-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:2rem;}
.car-card{background:rgba(18,28,48,0.9);border-radius:1rem;padding:1.5rem;text-align:center;}
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
<h1>🚗 Car Rentals</h1>
<div class="car-grid">
<?php while($car = $cars->fetch_assoc()): ?>
<div class="car-card">
    <i class="fas fa-car" style="font-size:3rem;color:#00e0ff;"></i>
    <h3><?php echo $car['car_name']; ?></h3>
    <p><?php echo $car['car_type']; ?></p>
    <div class="price">$<?php echo $car['price_per_day']; ?>/day</div>
    <button class="book-btn" onclick="showBookingForm(<?php echo $car['id']; ?>, '<?php echo addslashes($car['car_name']); ?>', '<?php echo addslashes($car['car_type']); ?>', <?php echo $car['price_per_day']; ?>)">Rent Now →</button>
    
    <div id="bookingForm_<?php echo $car['id']; ?>" class="booking-form">
        <form method="POST">
            <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
            <input type="hidden" name="car_name" value="<?php echo $car['car_name']; ?>">
            <input type="hidden" name="car_type" value="<?php echo $car['car_type']; ?>">
            <input type="hidden" name="price_per_day" value="<?php echo $car['price_per_day']; ?>">
            <input type="date" name="pickup_date" required placeholder="Pickup Date">
            <input type="date" name="return_date" required placeholder="Return Date">
            <button type="submit" name="book_car">Confirm Rental</button>
            <button type="button" onclick="hideBookingForm(<?php echo $car['id']; ?>)" style="background:#ff4444;">Cancel</button>
        </form>
    </div>
</div>
<?php endwhile; ?>
</div>
</div>

<script>
function showBookingForm(id, name, type, price) {
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