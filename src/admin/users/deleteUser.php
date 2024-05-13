<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';

check(['0']);
$conn = getDatabaseConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id']) && is_numeric($data['id'])) {
        $query = "DELETE FROM questions WHERE creator = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $data['id']);
        $stmt->execute();
        $stmt->close();
        $query = "DELETE FROM abc_questions WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $data['id']);
        $stmt->execute();
        $stmt->close();
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $data['id']);
        $stmt->execute();
        if ($stmt->affected_rows === 1) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made to the user.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid question ID.']);
    }
}