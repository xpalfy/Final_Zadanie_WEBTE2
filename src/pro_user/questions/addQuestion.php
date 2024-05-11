<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';
check(['1']);

$conn = getDatabaseConnection();
$active= 1;

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['questionText'], $data['questionCategory'], $data['questionType'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
    exit;
}

try {
    $conn->begin_transaction();

    if ($data['questionType'] == '2' && isset($data['optionA'], $data['optionB'], $data['optionC'])) {
        $sql = "INSERT INTO abc_questions (question, a, b, c, category, active, creator) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Unable to do prepared statement: " . $conn->error);
        }
        $stmt->bind_param("sssssis",
            $data['questionText'],
            $data['optionA'],
            $data['optionB'],
            $data['optionC'],
            $data['questionCategory'],
            $active,
            $_SESSION["user"]["id"]
        );
    } else {
        $sql = "INSERT INTO questions (question, category, active, creator) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Unable to do prepared statement: " . $conn->error);
        }
        $stmt->bind_param("ssis",
            $data['questionText'],
            $data['questionCategory'],
            $active,
            $_SESSION["user"]["id"]
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
