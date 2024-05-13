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

    if (!isset($data['id']) || !is_numeric($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid Question ID.']);
        exit;
    }

    $questionId = $data['id'];

    if (isset($data['type']) && $data['type'] === 'Multiple Choice') {
        $stmt = $conn->prepare("SELECT id, question, category, a, b, c FROM abc_questions WHERE id = ?");
    } else {
        $stmt = $conn->prepare("SELECT id, question, category FROM questions WHERE id = ?");
    }

    if ($stmt) {
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $question = $result->fetch_assoc();

            if (isset($data['type']) && $data['type'] === 'Multiple Choice') {
                $question['options'] = [
                    'a' => $question['a'],
                    'b' => $question['b'],
                    'c' => $question['c']
                ];
                unset($question['a'], $question['b'], $question['c']);
            }

            echo json_encode(['success' => true, 'question' => $question]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No question found with the given ID.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
