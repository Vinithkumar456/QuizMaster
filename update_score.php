<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit();
}

$username = $_SESSION['username'];
$data = json_decode(file_get_contents('php://input'), true);
$score = $data['score'];
$subject = $data['subject'];

$subject_field ="score_".strtolower($subject); 

// Check if the subject field exists in the users table
$check_column_sql = "SHOW COLUMNS FROM users LIKE '$subject_field'";
$column_result = $conn->query($check_column_sql);

if ($column_result->num_rows == 0) {
    // Add a new column for the subject score if it doesn't exist
    $add_column_sql = "ALTER TABLE users ADD $subject_field INT DEFAULT 0";
    $conn->query($add_column_sql);
}

// Update the user's score for the subject
$stmt = $conn->prepare("UPDATE users SET $subject_field = ? WHERE username = ?");
$stmt->bind_param("is", $score, $username);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
