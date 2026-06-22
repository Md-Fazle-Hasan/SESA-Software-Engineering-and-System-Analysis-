<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../config/Database.php';

session_start();

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $name = $data['name'] ?? '';
    $comment = $data['comment'] ?? '';
    $rating = $data['rating'] ?? 5;
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    
    if (empty($name) || empty($comment)) {
        echo json_encode(["success" => false, "message" => "Name and comment required"]);
        exit();
    }
    
    $query = "INSERT INTO feedbacks (user_id, passenger_name, comment, rating) VALUES (:user_id, :name, :comment, :rating)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":comment", $comment);
    $stmt->bindParam(":rating", $rating);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Feedback submitted"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to submit feedback"]);
    }
} 
elseif ($method === 'GET') {
    $query = "SELECT * FROM feedbacks ORDER BY created_at DESC LIMIT 20";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(["success" => true, "feedbacks" => $feedbacks]);
}
?>

