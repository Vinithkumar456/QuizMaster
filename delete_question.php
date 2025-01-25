<?php
include 'db.php';

session_start();

$adminSubjects = ['admin_math' => 'math', 'admin_science' => 'science', 'admin_history' => 'history'];
$currentAdmin = $_SESSION['username'];

if (!isset($adminSubjects[$currentAdmin])) {
    http_response_code(403);
    exit();
}

$subject = $adminSubjects[$currentAdmin];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_id = $_POST['question_id'];

    // Ensure the question belongs to the subject of the current admin
    $checkSql = "SELECT * FROM questions WHERE id = ? AND subject = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("is", $question_id, $subject);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sql = "DELETE FROM questions WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $question_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized deletion attempt']);
    }
    $conn->close();
}
?>
