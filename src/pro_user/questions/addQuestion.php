<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';
check(['1']);

$conn = getDatabaseConnection();
$active = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['questionText'], $data['questionCategory'], $data['questionType'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
        exit;
    }

    try {
        $conn->begin_transaction();
        $qr_code = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 5)), 0, 5);
        if ($data['questionType'] == '2' && isset($data['optionA'], $data['optionB'], $data['optionC'])) {
            $sql = "INSERT INTO abc_questions (question, a, b, c, category, active, creator, qr_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception($conn->error);
            }
            $stmt->bind_param("sssssiss",
                $data['questionText'],
                $data['optionA'],
                $data['optionB'],
                $data['optionC'],
                $data['questionCategory'],
                $active,
                $_SESSION["user"]["id"],
                $qr_code
            );
        } else {
            $sql = "INSERT INTO questions (question, category, active, creator, qr_code) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception($conn->error);
            }
            $stmt->bind_param("ssiss",
                $data['questionText'],
                $data['questionCategory'],
                $active,
                $_SESSION["user"]["id"],
                $qr_code
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