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
            $query = "SELECT active FROM questions WHERE id = ?";
        } elseif (isset($data['type']) && $data['type'] === 'Multiple Choice') {
            $query = "SELECT active FROM abc_questions WHERE id = ?";
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid question type.']);
            exit();
        }
        $questionId = $data['id'];
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param('i', $questionId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $currentStatus = $row['active'];
                $newStatus = $currentStatus ? 0 : 1;

                if ($data['type'] === 'One Answer') {
                    $updateQuery = "UPDATE questions SET active = ? WHERE id = ?";
                } elseif ($data['type'] === 'Multiple Choice') {
                    $updateQuery = "UPDATE abc_questions SET active = ? WHERE id = ?";
                }
                $updateStmt = $conn->prepare($updateQuery);
                if ($updateStmt) {
                    $updateStmt->bind_param('ii', $newStatus, $questionId);
                    $updateStmt->execute();
                    if ($updateStmt->affected_rows === 1) {
                        echo json_encode(['success' => true, 'active' => $newStatus]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'No changes made to the question status.']);
                    }
                    $updateStmt->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error preparing the update statement.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Question not found.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error preparing the select statement.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid question ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
