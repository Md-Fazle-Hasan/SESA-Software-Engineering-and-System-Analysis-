<?php
require_once __DIR__ . '/../config/database.php';

class EmergencyTicket {
    private $conn;
    private $table_name = "emergency_tickets";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createEmergencyTicket($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET booking_id=:booking_id, passenger_name=:passenger_name, 
                      flight_id=:flight_id, emergency_category=:emergency_category,
                      medical_condition=:medical_condition, 
                      medical_charge_percentage=:medical_charge_percentage,
                      admin_notified=TRUE";
        
        $stmt = $this->conn->prepare($query);
        
        foreach($data as $key => $value) {
            $data[$key] = htmlspecialchars(strip_tags($value));
        }
        
        $stmt->bindParam(":booking_id", $data['booking_id']);
        $stmt->bindParam(":passenger_name", $data['passenger_name']);
        $stmt->bindParam(":flight_id", $data['flight_id']);
        $stmt->bindParam(":emergency_category", $data['emergency_category']);
        $stmt->bindParam(":medical_condition", $data['medical_condition']);
        $stmt->bindParam(":medical_charge_percentage", $data['medical_charge_percentage']);
        
        return $stmt->execute();
    }

    public function getEmergencyTickets() {
        $query = "SELECT e.*, f.flight_no, f.destination, f.departure_time 
                  FROM " . $this->table_name . " e
                  JOIN flights f ON e.flight_id = f.id
                  WHERE e.admin_notified = FALSE OR e.medical_staff_arranged = FALSE
                  ORDER BY e.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateMedicalStaff($ticket_id) {
        $query = "UPDATE " . $this->table_name . " SET medical_staff_arranged = TRUE WHERE id = :ticket_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ticket_id", $ticket_id);
        return $stmt->execute();
    }
}
?>