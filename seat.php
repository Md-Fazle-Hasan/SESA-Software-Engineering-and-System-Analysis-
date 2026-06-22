<?php
class Seat {
    private $conn;
    private $table = "flights";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAvailableSeatsCount($flightId) {
        $query = "SELECT (total_seats - booked_seats) as available FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $flightId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['available'] : 0;
    }

    public function getAvailableSeats($flightId) {
        $query = "SELECT total_seats, booked_seats, (total_seats - booked_seats) as available FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $flightId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

