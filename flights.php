<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        // Get single flight by ID
        $query = "SELECT *, (total_seats - booked_seats) as available_seats FROM flights WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $_GET['id']);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $flight = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(["success" => true, "flight" => $flight]);
        } else {
            echo json_encode(["success" => false, "message" => "Flight not found"]);
        }
    } 
    elseif (isset($_GET['origin']) || isset($_GET['destination']) || isset($_GET['date'])) {
        // Search flights
        $origin = $_GET['origin'] ?? '';
        $destination = $_GET['destination'] ?? '';
        $date = $_GET['date'] ?? '';
        
        $query = "SELECT *, (total_seats - booked_seats) as available_seats FROM flights WHERE 1=1";
        
        if (!empty($origin)) {
            $query .= " AND origin LIKE :origin";
        }
        if (!empty($destination)) {
            $query .= " AND destination LIKE :destination";
        }
        if (!empty($date)) {
            $query .= " AND departure_date = :date";
        }
        
        $stmt = $db->prepare($query);
        
        if (!empty($origin)) {
            $originParam = "%" . $origin . "%";
            $stmt->bindParam(":origin", $originParam);
        }
        if (!empty($destination)) {
            $destinationParam = "%" . $destination . "%";
            $stmt->bindParam(":destination", $destinationParam);
        }
        if (!empty($date)) {
            $stmt->bindParam(":date", $date);
        }
        
        $stmt->execute();
        $flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "flights" => $flights]);
    }
    else {
        // Get all flights
        $query = "SELECT *, (total_seats - booked_seats) as available_seats FROM flights ORDER BY departure_date, departure_time";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "flights" => $flights]);
    }
}

// Handle POST request for creating booking
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['action'])) {
        if ($data['action'] === 'book') {
            // Create booking
            $booking_ref = 'NX' . strtoupper(uniqid());
            $user_id = $data['user_id'] ?? 1;
            $flight_id = $data['flight_id'];
            $passenger_name = $data['passenger_name'];
            $passenger_email = $data['passenger_email'];
            $passenger_phone = $data['passenger_phone'] ?? '';
            $ticket_class = $data['ticket_class'] ?? 'economy';
            $emergency_type = $data['emergency_type'] ?? 'none';
            $num_tickets = $data['num_tickets'] ?? 1;
            $total_price = $data['total_price'];
            
            $query = "INSERT INTO bookings (booking_ref, user_id, flight_id, passenger_name, passenger_email, passenger_phone, ticket_class, emergency_type, number_of_tickets, total_price) 
                      VALUES (:ref, :user_id, :flight_id, :name, :email, :phone, :class, :emergency, :tickets, :price)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(":ref", $booking_ref);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":flight_id", $flight_id);
            $stmt->bindParam(":name", $passenger_name);
            $stmt->bindParam(":email", $passenger_email);
            $stmt->bindParam(":phone", $passenger_phone);
            $stmt->bindParam(":class", $ticket_class);
            $stmt->bindParam(":emergency", $emergency_type);
            $stmt->bindParam(":tickets", $num_tickets);
            $stmt->bindParam(":price", $total_price);
            
            if ($stmt->execute()) {
                // Update booked seats
                $updateQuery = "UPDATE flights SET booked_seats = booked_seats + :tickets WHERE id = :flight_id";
                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->bindParam(":tickets", $num_tickets);
                $updateStmt->bindParam(":flight_id", $flight_id);
                $updateStmt->execute();
                
                echo json_encode(["success" => true, "booking_ref" => $booking_ref, "message" => "Booking successful"]);
            } else {
                echo json_encode(["success" => false, "message" => "Booking failed"]);
            }
        }
    }
}

// Handle PUT request for updating flight status (admin)
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['flight_id']) && isset($data['status'])) {
        $query = "UPDATE flights SET status = :status WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":status", $data['status']);
        $stmt->bindParam(":id", $data['flight_id']);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Flight status updated"]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed"]);
        }
    }
}

// Handle DELETE request for cancelling booking
elseif ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    
    if (isset($data['booking_id'])) {
        $query = "UPDATE bookings SET booking_status = 'cancelled' WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $data['booking_id']);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Booking cancelled"]);
        } else {
            echo json_encode(["success" => false, "message" => "Cancellation failed"]);
        }
    }
}
?>

