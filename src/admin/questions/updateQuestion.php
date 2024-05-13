<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../checkType.php';
require '../../config.php';

check(['0']);
$conn = getDatabaseConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = (int)$data['questionId'];
    try {
        $conn->begin_transaction();
        if ($data['questionType'] == '2' && isset($data['optionA'], $data['optionB'], $data['optionC'])) {
            $sql = "UPDATE abc_questions SET question = ?, a = ?, b = ?, c = ?, category = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception($conn->error);
            }
            $stmt->bind_param("sssssi",
                $data['questionText'],
                $data['optionA'],
                $data['optionB'],
                $data['optionC'],
                $data['questionCategory'],
                $data['questionId']
            );
        } else {
            $sql = "UPDATE questions SET question = ?, category = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception($conn->error);
            }
            $stmt->bind_param("ssi",
                $data['questionText'],
                $data['questionCategory'],
                $data['questionId']
            );
        }

        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Question updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update question']);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    } finally {
        $stmt->close();
        $conn->close();
    }
}