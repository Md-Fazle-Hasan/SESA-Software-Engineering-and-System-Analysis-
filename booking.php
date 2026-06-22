 <?php 
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';
$selectedFlight = null;

$conn = new mysqli("localhost", "root", "", "Airline");

// Get flight details if flight_id is provided
if(isset($_GET['flight_id'])) {
    $flight_id = $_GET['flight_id'];
    $result = $conn->query("SELECT * FROM flights WHERE id = $flight_id");
    $selectedFlight = $result->fetch_assoc();
}

// Get all flights for dropdown
$flightsResult = $conn->query("SELECT * FROM flights");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $flight_id = $_POST['flight_id'];
    $passenger_name = $_POST['passenger_name'];
    $phone = $_POST['phone'];
    $ticket_class = $_POST['ticket_class'];
    $emergency_type = $_POST['emergency_type'];
    $num_tickets = $_POST['num_tickets'];
    
    // Calculate price
    $flight = $conn->query("SELECT * FROM flights WHERE id = $flight_id")->fetch_assoc();
    $class_multiplier = 1.0;
    if($ticket_class == 'business') $class_multiplier = 1.5;
    if($ticket_class == 'first') $class_multiplier = 2.0;
    
    $emergency_multiplier = ($emergency_type != 'none') ? 1.2 : 1.0;
    $price_per_ticket = $flight['base_price'] * $class_multiplier * $emergency_multiplier;
    $total_price = $price_per_ticket * $num_tickets;
    
    $booking_ref = 'NX' . strtoupper(uniqid());
    
    $query = "INSERT INTO bookings (booking_ref, user_id, flight_id, passenger_name, passenger_email, passenger_phone, ticket_class, emergency_type, number_of_tickets, total_price) 
              VALUES ('$booking_ref', {$_SESSION['user_id']}, $flight_id, '$passenger_name', '{$_SESSION['user_email']}', '$phone', '$ticket_class', '$emergency_type', $num_tickets, $total_price)";
    
    if($conn->query($query)) {
        // Update booked seats
        $conn->query("UPDATE flights SET booked_seats = booked_seats + $num_tickets WHERE id = $flight_id");
        $success = "Booking successful! Your booking reference: $booking_ref";
    } else {
        $error = "Booking failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Flight - Nexus Airways</title>
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
        .container { max-width: 800px; margin: 0 auto; }
        .booking-form {
            background: rgba(18, 28, 48, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 2rem;
            padding: 2rem;
            margin-top: 2rem;
        }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #b9c7dd;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.8rem;
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(0,224,255,0.3);
            border-radius: 0.5rem;
            color: white;
            font-size: 1rem;
        }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(95deg, #00e0ff, #0077ff);
            border: none;
            border-radius: 0.5rem;
            color: white;
            font-weight: 700;
            cursor: pointer;
            font-size: 1rem;
        }
        .error { background: rgba(255,0,0,0.2); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
        .success { background: rgba(0,255,0,0.2); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
        h1 { text-align: center; }
        .back-link { display: inline-block; margin-top: 1rem; color: #00e0ff; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <h1>✈ Book Your Flight</h1>
    
    <?php if($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <div class="booking-form">
        <form method="POST">
            <div class="form-group">
                <label>Select Flight</label>
                <select name="flight_id" required>
                    <option value="">Choose a flight</option>
                    <?php while($flight = $flightsResult->fetch_assoc()): ?>
                        <option value="<?php echo $flight['id']; ?>" <?php echo ($selectedFlight && $selectedFlight['id'] == $flight['id']) ? 'selected' : ''; ?>>
                            <?php echo $flight['origin']; ?> → <?php echo $flight['destination']; ?> | <?php echo date('g:i A', strtotime($flight['departure_time'])); ?> | $<?php echo $flight['base_price']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Passenger Name</label>
                <input type="text" name="passenger_name" required placeholder="Full name as on ID">
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="Contact number">
            </div>
            
            <div class="form-group">
                <label>Ticket Class</label>
                <select name="ticket_class">
                    <option value="economy">Economy (x1.0)</option>
                    <option value="business">Business (x1.5)</option>
                    <option value="first">First Class (x2.0)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Emergency Assistance (+20%)</label>
                <select name="emergency_type">
                    <option value="none">None</option>
                    <option value="pregnant">🤰 Pregnant Passenger</option>
                    <option value="physically_challenged">♿ Physically Challenged</option>
                    <option value="medical">🩺 Medical Emergency</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Number of Tickets (Max 5)</label>
                <input type="number" name="num_tickets" min="1" max="5" value="1" required>
            </div>
            
            <button type="submit" class="btn-submit">Confirm Booking & Pay</button>
        </form>
    </div>
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>
</body>
</html> 
