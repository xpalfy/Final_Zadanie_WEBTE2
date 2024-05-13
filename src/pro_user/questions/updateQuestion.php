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
    $id = $data['questionId'];
    if ($data['questionType'] == '2' && isset($data['optionA'], $data['optionB'], $data['optionC'])) {
        $sql = "UPDATE abc_questions SET question = ?, a = ?, b = ?, c = ?, category = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
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
        $stmt->bind_param("ssi",
            $data['questionText'],
            $data['questionCategory'],
            $data['questionId']
        );
    }

    $stmt->execute();
    if ($stmt->affected_rows > 0 || $stmt->errno === 0) {
        echo json_encode(['success' => true, 'message' => 'Question updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update question']);
    }
}