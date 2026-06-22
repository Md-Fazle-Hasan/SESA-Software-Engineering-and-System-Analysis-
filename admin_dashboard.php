<?php
session_start();
if(!isset($_SESSION['is_admin'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "Airline");

// Handle refund



if(isset($_POST['refund_booking'])) {
    $booking_id = $_POST['booking_id'];
    $conn->query("UPDATE bookings SET booking_status = 'cancelled' WHERE id = $booking_id");
    header("Location: admin_dashboard.php");
    exit();
}

$bookings = $conn->query("SELECT b.*, f.flight_no, f.origin, f.destination, f.departure_date, u.name as user_name 
                          FROM bookings b 
                          JOIN flights f ON b.flight_id = f.id 
                          LEFT JOIN users u ON b.user_id = u.id 
                          ORDER BY b.booking_date DESC");

$flights = $conn->query("SELECT * FROM flights");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Nexus Airways</title>
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
        .container { max-width: 1400px; margin: 0 auto; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .admin-badge {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            padding: 0.3rem 1rem;
            border-radius: 2rem;
            font-size: 0.8rem;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(18, 28, 48, 0.9);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #00e0ff;
        }
        .admin-nav {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }
        .admin-nav a {
            background: rgba(0,224,255,0.1);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            color: white;
            text-decoration: none;
            border: 1px solid rgba(0,224,255,0.3);
            transition: 0.3s;
        }
        .admin-nav a:hover {
            background: rgba(0,224,255,0.3);
        }
        table {
            width: 100%;
            background: rgba(18, 28, 48, 0.9);
            border-radius: 1rem;
            overflow: hidden;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        th { background: rgba(0,224,255,0.2); }
        .btn-refund {
            background: rgba(255,0,0,0.2);
            border: 1px solid #ff4444;
            padding: 0.3rem 0.8rem;
            border-radius: 0.5rem;
            color: #ff4444;
            cursor: pointer;
        }
        .btn-checkin {
            background: rgba(0,255,0,0.2);
            border: 1px solid #00ff88;
            padding: 0.3rem 0.8rem;
            border-radius: 0.5rem;
            color: #00ff88;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .logout { color: #00e0ff; text-decoration: none; }
        h2 { margin: 1rem 0; }
        @media (max-width: 768px) {
            body { padding: 1rem; }
            table { font-size: 0.8rem; }
            th, td { padding: 0.5rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1>🛡 Admin Dashboard</h1>
            <div class="admin-badge"><i class="fas fa-user-shield"></i> Administrator Access</div>
        </div>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    
    <!-- Admin Navigation Menu -->
    <div class="admin-nav">
        <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="checkin.php"><i class="fas fa-check-circle"></i> Passenger Check-in</a>
    </div>
    
    <div class="stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo $bookings->num_rows; ?></div>
            <div>Total Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $flights->num_rows; ?></div>
            <div>Active Flights</div>
        </div>
    </div>
    
    <h2><i class="fas fa-plane"></i> All Flights</h2>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr><th>Flight No</th><th>Route</th><th>Time</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php $flights = $conn->query("SELECT * FROM flights"); ?>
                <?php while($flight = $flights->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $flight['flight_no']; ?></td>
                    <td><?php echo $flight['origin']; ?> → <?php echo $flight['destination']; ?></td>
                    <td><?php echo date('g:i A', strtotime($flight['departure_time'])); ?></td>
                    <td><?php echo $flight['status']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <h2><i class="fas fa-ticket-alt"></i> All Bookings</h2>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr><th>Ref</th><th>Passenger</th><th>Flight</th><th>Class</th><th>Tickets</th><th>Total</th><th>Emergency</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php $bookings = $conn->query("SELECT b.*, f.flight_no, f.origin, f.destination FROM bookings b JOIN flights f ON b.flight_id = f.id ORDER BY b.booking_date DESC"); ?>
                <?php while($booking = $bookings->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $booking['booking_ref']; ?></td>
                    <td><?php echo htmlspecialchars($booking['passenger_name']); ?></td>
                    <td><?php echo $booking['origin']; ?> → <?php echo $booking['destination']; ?></td>
                    <td><?php echo ucfirst($booking['ticket_class']); ?></td>
                    <td><?php echo $booking['number_of_tickets']; ?></td>
                    <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                    <td><?php echo $booking['emergency_type'] != 'none' ? '✓ Emergency' : '-'; ?></td>
                    <td><?php echo $booking['booking_status']; ?></td>
                    <td>
                        <?php if($booking['booking_status'] == 'confirmed'): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            <button type="submit" name="refund_booking" class="btn-refund">Refund</button>
                        </form>
                        <a href="checkin.php?booking_ref=<?php echo $booking['booking_ref']; ?>" class="btn-checkin">Check-in</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>