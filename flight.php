<?php
require_once __DIR__ . '/../config/Database.php';

class Flight {
    private $conn;
    private $table = "flights";

    public $id;
    public $flight_no;
    public $origin;
    public $destination;
    public $departure_time;
    public $departure_date;
    public $total_seats;
    public $booked_seats;
    public $base_price;
    public $status;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllFlights() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY departure_date, departure_time";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchFlights($destination, $date = null) {
        $query = "SELECT * FROM " . $this->table . " WHERE destination LIKE :destination";
        if($date) {
            $query .= " AND departure_date = :date";
        }
        $query .= " ORDER BY departure_time";
        
        $stmt = $this->conn->prepare($query);
        $dest = "%" . $destination . "%";
        $stmt->bindParam(":destination", $dest);
        if($date) {
            $stmt->bindParam(":date", $date);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFlightById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSeats($flightId, $ticketsCount) {
        $query = "UPDATE " . $this->table . " SET booked_seats = booked_seats + :tickets WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tickets", $ticketsCount);
        $stmt->bindParam(":id", $flightId);
        return $stmt->execute();
    }

    public function getAvailableSeats($flightId) {
        $flight = $this->getFlightById($flightId);
        if($flight) {
            return $flight['total_seats'] - $flight['booked_seats'];
        }
        return 0;
    }

    public function updateFlightStatus($flightId, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $flightId);
        return $stmt->execute();
    }
}
?>
