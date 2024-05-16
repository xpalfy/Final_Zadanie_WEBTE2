<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    require 'config.php';
    $conn = getDatabaseConnection();
    if (!isset($data['question_id']) || !is_numeric($data['question_id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid Question ID.']);
        exit;
    }
    $questionId = $data['question_id'];
    switch ($data['type']) {
        case 'one_answer':
            $stmt = $conn->prepare("SELECT * FROM answers WHERE question_id = ?");
            break;
        case 'abc_answer':
            $stmt = $conn->prepare("SELECT * FROM abc_answers WHERE question_id = ?");
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid question type.']);
            exit;
    }
    if ($stmt) {
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $answers = [];
            $vote_count = 0;
            while ($row = $result->fetch_assoc()) {
                $answers[] = $row;
                $vote_count += $row['count'];
            }
            echo json_encode(['success' => true, 'answers' => $answers, 'vote_count' => $vote_count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No answers found for the given question ID.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
