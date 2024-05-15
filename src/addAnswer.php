<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

$conn = getDatabaseConnection();
$active = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['question_id'], $data['answer'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
        exit;
    }

    try {
        $conn->begin_transaction();
        if ($data['type'] == 'abc_answer') {
            $sql = "INSERT INTO abc_answers (question_id, answer) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception($conn->error);
            }
            $stmt->bind_param("is",
                $data['question_id'],
                $data['answer']
            );
        } else {
            $sql = "INSERT INTO answers (question_id, answer) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception($conn->error);
            }
            $stmt->bind_param("is",
                $data['question_id'],
                $data['answer']
            );
        }

        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Question added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add question']);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    } finally {
        $stmt->close();
        $conn->close();
    }
}