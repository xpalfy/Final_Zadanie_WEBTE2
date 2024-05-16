<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';

check(['1']);
$conn = getDatabaseConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
$data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id']) && is_numeric($data['id'])) {
        if (isset($data['type']) && $data['type'] === 'One Answer') {
            $query = "SELECT * FROM answers_archive WHERE question_id = ?";
        } elseif (isset($data['type']) && $data['type'] === 'Multiple Choice') {
            $query = "SELECT * FROM abc_answers_archive WHERE question_id = ?";
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
            if ($result->num_rows > 0) {
                $answers = [];
                while ($row = $result->fetch_assoc()) {
                    $row['type'] = $data['type'];
                    $answers[] = $row;
                }
                echo json_encode(['success' => true, 'questions' => $answers]);
            } else{
                echo json_encode(['success' => false, 'message' => 'No answers found for the question.']);
                exit();
            }
        } else{
            echo json_encode(['success' => false, 'message' => 'Error preparing the select statement.']);
            exit();
        }
    } else{
        echo json_encode(['success' => false, 'message' => 'Invalid question ID.']);
        exit();
    }
} else{
    echo json_encode(['success' => false, 'message' => 'Invalid method.']);
    exit();
}