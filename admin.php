<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../config/Database.php';

session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['type'])) {
        switch($_GET['type']) {
            case 'stats':
                // Get statistics
                $stats = [];
                
     // Total users
                $query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                // Total bookings
                $query = "SELECT COUNT(*) as total FROM bookings";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $stats['total_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                // Total flights
                $query = "SELECT COUNT(*) as total FROM flights";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $stats['total_flights'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                // Total revenue
                $query = "SELECT SUM(total_price) as total FROM bookings WHERE booking_status = 'confirmed'";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                
                // Emergency bookings
                $query = "SELECT COUNT(*) as total FROM bookings WHERE emergency_type != 'none'";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $stats['emergency_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                // Hotel bookings
                $query = "SELECT COUNT(*) as total FROM hotel_bookings";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $stats['hotel_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                // Car bookings
                $query = "SELECT COUNT(*) as total FROM car_bookings";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $stats['car_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                echo json_encode(["success" => true, "stats" => $stats]);
                break;
                
            case 'bookings':
                $query = "SELECT b.*, f.flight_no, f.origin, f.destination, u.name as user_name 
                          FROM bookings b
                          LEFT JOIN flights f ON b.flight_id = f.id
                          LEFT JOIN users u ON b.user_id = u.id
                          ORDER BY b.created_at DESC LIMIT 50";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(["success" => true, "bookings" => $bookings]);
                break;
                
            case 'users':
                $query = "SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(["success" => true, "users" => $users]);
                break;
                
            case 'flights':
                $query = "SELECT * FROM flights ORDER BY departure_date, departure_time";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(["success" => true, "flights" => $flights]);
                break;
                
            case 'seats':
                $flight_id = $_GET['flight_id'] ?? 0;
                $query = "SELECT seat_number, is_booked, booked_by FROM seats WHERE flight_id = :flight_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":flight_id", $flight_id);
                $stmt->execute();
                $seats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $booked = 0;
                $available = 0;
                foreach($seats as $seat) {
                    if($seat['is_booked']) $booked++;
                    else $available++;
                }
                
                echo json_encode(["success" => true, "seats" => $seats, "booked" => $booked, "available" => $available]);
                break;
                
            case 'emergency':
                $query = "SELECT b.*, f.flight_no, f.origin, f.destination, f.departure_date, f.departure_time
                          FROM bookings b
                          LEFT JOIN flights f ON b.flight_id = f.id
                          WHERE b.emergency_type != 'none'
                          ORDER BY b.created_at DESC";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $emergencies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(["success" => true, "emergencies" => $emergencies]);
                break;
        }
    }
} 
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['action'])) {
        if ($data['action'] === 'add_flight') {
            $query = "INSERT INTO flights (flight_no, airline, origin, destination, departure_date, departure_time, gate, price_economy, price_business, price_first, total_seats) 
                      VALUES (:flight_no, :airline, :origin, :destination, :departure_date, :departure_time, :gate, :price_economy, :price_business, :price_first, 240)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(":flight_no", $data['flight_no']);
            $stmt->bindParam(":airline", $data['airline']);
            $stmt->bindParam(":origin", $data['origin']);
            $stmt->bindParam(":destination", $data['destination']);
            $stmt->bindParam(":departure_date", $data['departure_date']);
            $stmt->bindParam(":departure_time", $data['departure_time']);
            $stmt->bindParam(":gate", $data['gate']);
            $stmt->bindParam(":price_economy", $data['price_economy']);
            $stmt->bindParam(":price_business", $data['price_business']);
            $stmt->bindParam(":price_first", $data['price_first']);
            
            if($stmt->execute()) {
                $flight_id = $db->lastInsertId();
                
                // Generate seats for this flight
                for($row = 1; $row <= 40; $row++) {
                    foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
                        $seat_num = $row . $col;
                        $query2 = "INSERT INTO seats (flight_id, seat_number, seat_class, is_booked) VALUES (:flight_id, :seat_num, 'economy', FALSE)";
                        $stmt2 = $db->prepare($query2);
                        $stmt2->bindParam(":flight_id", $flight_id);
                        $stmt2->bindParam(":seat_num", $seat_num);
                        $stmt2->execute();
                    }
                }
                
                echo json_encode(["success" => true, "message" => "Flight added successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to add flight"]);
            }
        }
        elseif ($data['action'] === 'cancel_booking') {
            $query = "UPDATE bookings SET booking_status = 'cancelled' WHERE booking_ref = :booking_ref";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":booking_ref", $data['booking_ref']);
            
            if($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Booking cancelled"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to cancel booking"]);
            }
        }
    }
}
?>