<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';

check(['1']);
$conn = getDatabaseConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id']) && is_numeric($data['id'])) {
        if (isset($data['type']) && $data['type'] === 'One Answer') {
            $query = "DELETE FROM questions WHERE id = ?";
        } elseif (isset($data['type']) && $data['type'] === 'Multiple Choice') {
            $query = "DELETE FROM abc_questions WHERE id = ?";
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid question type.']);
            exit();
        }
        $questionId = $data['id'];
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param('i', $questionId);
            $stmt->execute();
            if ($stmt->affected_rows === 1) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No changes made to the question status.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error preparing the delete statement.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid question ID.']);
    }
}