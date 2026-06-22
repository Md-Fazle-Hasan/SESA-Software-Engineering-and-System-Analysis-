<?php
session_start();

if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "Airline");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$bookingInfo = null;
$error = '';
$success = false;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_ref = $conn->real_escape_string($_POST['booking_ref']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    
    $query = "SELECT b.*, f.flight_no, f.origin, f.destination, f.departure_time, f.departure_date, f.status as flight_status, u.name as user_name 
              FROM bookings b 
              JOIN flights f ON b.flight_id = f.id 
              JOIN users u ON b.user_id = u.id 
              WHERE b.booking_ref = '$booking_ref' AND (b.passenger_name LIKE '%$last_name%' OR u.name LIKE '%$last_name%')
              AND b.booking_status = 'confirmed'";
    
    $result = $conn->query($query);

    if($result && $result->num_rows > 0) {
        $bookingInfo = $result->fetch_assoc();
        $success = true;
    } else {
        $error = "Booking not found. Please check your reference number and last name.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Admin Check-in - Nexus Airways</title>
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
            padding: 2rem;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .admin-badge {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            display: inline-block;
            padding: 0.3rem 1rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }
        h1 {
            text-align: center;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        .checkin-form {
            background: rgba(18, 28, 48, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 2rem;
            padding: 2rem;
            margin-top: 1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #b9c7dd;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(0,224,255,0.3);
            border-radius: 0.5rem;
            color: white;
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: #00e0ff;
        }
        .btn-checkin {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(95deg, #00e0ff, #0077ff);
            border: none;
            border-radius: 0.5rem;
            color: white;
            font-weight: 700;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.3s;
        }
        .btn-checkin:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,224,255,0.3);
        }
        .boarding-pass {
            background: linear-gradient(135deg, rgba(0,224,255,0.15), rgba(0,119,255,0.1));
            border-radius: 1.5rem;
            padding: 2rem;
            margin-top: 2rem;
            border: 1px solid rgba(0,224,255,0.3);
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .boarding-header {
            text-align: center;
            border-bottom: 2px dashed rgba(0,224,255,0.3);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        .boarding-header h2 {
            color: #00e0ff;
        }
        .boarding-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .detail-item {
            text-align: center;
            padding: 0.8rem;
            background: rgba(0,0,0,0.2);
            border-radius: 1rem;
        }
        .detail-label {
            font-size: 0.7rem;
            color: #b9c7dd;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .detail-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #00e0ff;
        }
        .qr-code {
            text-align: center;
            margin: 1rem 0;
        }
        .qr-code i {
            font-size: 5rem;
            color: #00e0ff;
        }
        .error {
            background: rgba(255,0,0,0.2);
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #ff4444;
        }
        .success-msg {
            background: rgba(0,255,0,0.2);
            padding: 0.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .nav-back {
            display: inline-block;
            margin-bottom: 1rem;
            color: #00e0ff;
            text-decoration: none;
        }
        .test-info {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(0,0,0,0.3);
            border-radius: 1rem;
        }
        .test-info h4 {
            color: #ffd700;
            margin-bottom: 0.5rem;
        }
        .test-info ul {
            margin-left: 1.5rem;
            color: #b9c7dd;
        }
        .test-info li {
            margin: 0.3rem 0;
        }
        .admin-actions {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        .admin-btn {
            background: rgba(0,224,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            color: white;
            text-decoration: none;
            font-size: 0.8rem;
        }
        @media (max-width: 768px) {
            body { padding: 1rem; }
            .boarding-details { grid-template-columns: 1fr; }
            h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="admin-badge">
        <i class="fas fa-user-shield"></i> ADMIN ACCESS ONLY
    </div>
    <a href="admin_dashboard.php" class="nav-back">← Back to Admin Dashboard</a>
    <h1><i class="fas fa-check-circle"></i> Admin - Passenger Check-in</h1>
    
    <div class="checkin-form">
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success-msg">✓ Booking found! Generating boarding pass...</div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-ticket-alt"></i> Booking Reference</label>
                <input type="text" name="booking_ref" placeholder="e.g., NXDEMO001" required value="<?php echo isset($_POST['booking_ref']) ? htmlspecialchars($_POST['booking_ref']) : ''; ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-user"></i> Last Name / Passenger Name</label>
                <input type="text" name="last_name" placeholder="Enter passenger last name" required value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
            </div>
            <button type="submit" class="btn-checkin"><i class="fas fa-check-circle"></i> Process Check-in</button>
        </form>
        
        <?php if($success && $bookingInfo): ?>
        <div class="boarding-pass">
            <div class="boarding-header">
                <i class="fas fa-plane" style="font-size: 2rem; color: #00e0ff;"></i>
                <h2>NEXUS AIRWAYS</h2>
                <p>BOARDING PASS</p>
            </div>
            
            <div class="boarding-details">
                <div class="detail-item">
                    <div class="detail-label">Passenger Name</div>
                    <div class="detail-value"><?php echo htmlspecialchars($bookingInfo['passenger_name']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Flight Number</div>
                    <div class="detail-value"><?php echo htmlspecialchars($bookingInfo['flight_no']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Route</div>
                    <div class="detail-value"><?php echo htmlspecialchars($bookingInfo['origin']); ?> → <?php echo htmlspecialchars($bookingInfo['destination']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Date</div>
                    <div class="detail-value"><?php echo date('F j, Y', strtotime($bookingInfo['departure_date'])); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Departure Time</div>
                    <div class="detail-value"><?php echo date('g:i A', strtotime($bookingInfo['departure_time'])); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Gate</div>
                    <div class="detail-value"><?php echo $bookingInfo['flight_no'] == 'NX101' ? 'A12' : ($bookingInfo['flight_no'] == 'NX102' ? 'B8' : 'C5'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Class</div>
                    <div class="detail-value"><?php echo ucfirst($bookingInfo['ticket_class']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tickets</div>
                    <div class="detail-value"><?php echo $bookingInfo['number_of_tickets']; ?> x</div>
                </div>
                <?php if($bookingInfo['emergency_type'] != 'none'): ?>
                <div class="detail-item">
                    <div class="detail-label">Special Assistance</div>
                    <div class="detail-value">✓ Priority Care</div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="qr-code">
                <i class="fas fa-qrcode"></i>
                <p style="font-size: 0.7rem; margin-top: 0.5rem;"><?php echo $bookingInfo['booking_ref']; ?></p>
            </div>
            
            <div style="text-align: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed rgba(0,224,255,0.3);">
                <p style="color: #00ff88;"><i class="fas fa-check-circle"></i> Check-in successful!</p>
                <p style="font-size: 0.7rem; margin-top: 0.5rem;">Please advise passenger to arrive 2 hours before departure.</p>
                <p style="font-size: 0.7rem;">Boarding closes 30 minutes before departure.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="test-info">
        <h4><i class="fas fa-info-circle"></i> Admin Information:</h4>
        <ul>
            <li>📌 As an admin, you can check-in any passenger by their booking reference</li>
            <li>📌 Booking references can be found in the <a href="admin_dashboard.php" style="color: #00e0ff;">Admin Dashboard</a></li>
        </ul>
        
        <?php
        // Query to show existing bookings for admin
        $testQuery = "SELECT booking_ref, passenger_name FROM bookings LIMIT 5";
        $testResult = $conn->query($testQuery);
        if($testResult && $testResult->num_rows > 0):
        ?>
        <ul style="margin-top: 0.5rem;">
            <strong>Recent Bookings:</strong>
            <?php while($row = $testResult->fetch_assoc()): ?>
                <li>📌 <strong><?php echo $row['booking_ref']; ?></strong> - Passenger: <?php echo $row['passenger_name']; ?></li>
            <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <ul style="margin-top: 0.5rem;">
            <li>📌 <strong>NXDEMO001</strong> - Demo User (Economy to Tokyo)</li>
            <li>📌 <strong>NXDEMO002</strong> - Demo User (Business to New York)</li>
        </ul>
        <?php endif; ?>
    </div>
    
    <div class="admin-actions">
        <a href="admin_dashboard.php" class="admin-btn"><i class="fas fa-chart-line"></i> Admin Dashboard</a>
        <a href="logout.php" class="admin-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
</body>
</html>
