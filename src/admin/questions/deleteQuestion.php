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
                if($data['type'] === 'One Answer') {
                    $stmt = $conn->prepare("DELETE FROM answers WHERE question_id = ?");
                    $stmt->bind_param('i', $questionId);
                    $stmt->execute();
                    $stmt = $conn->prepare("DELETE FROM answers_archive WHERE question_id = ?");
                    $stmt->bind_param('i', $questionId);
                    $stmt->execute();
                } elseif ($data['type'] === 'Multiple Choice') {
                    $stmt = $conn->prepare("DELETE FROM abc_answers WHERE question_id = ?");
                    $stmt->bind_param('i', $questionId);
                    $stmt->execute();
                    $stmt = $conn->prepare("DELETE FROM abc_answers_archive WHERE question_id = ?");
                    $stmt->bind_param('i', $questionId);
                    $stmt->execute();
                }
                echo json_encode(['success' => true, 'message' => 'Question deleted successfully.']);
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