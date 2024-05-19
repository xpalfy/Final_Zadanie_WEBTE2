<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';

check(['0']);
$conn = getDatabaseConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id']) && is_numeric($data['id'])) {
        if (isset($data['time']) && isset($data['type']) && $data['type'] === 'One Answer') {
            if($data['time'] === 'all') {
                $query = "SELECT * FROM answers_archive WHERE question_id = ?";
                $ALL = true;
            } else {
                $query = "SELECT * FROM answers_archive WHERE question_id = ? AND time = ?";
                $ALL = false;
            }
            $question_query = "SELECT * FROM questions WHERE id = ?";
        } elseif (isset($data['time']) && isset($data['type']) && $data['type'] === 'Multiple Choice') {
            if($data['time'] === 'all') {
                $query = "SELECT * FROM abc_answers_archive WHERE question_id = ?";
                $ALL = true;
            } else {
                $query = "SELECT * FROM abc_answers_archive WHERE question_id = ? AND time = ?";
                $ALL = false;
            }
            $question_query = "SELECT * FROM abc_questions WHERE id = ?";
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid question type or time.']);
            exit();
        }
        $questionId = $data['id'];
        $time = $data['time'];
        $stmt = $conn->prepare($query);
        if ($stmt) {
            if($ALL) {
                $stmt->bind_param('i', $questionId);
            } else {
                $stmt->bind_param('is', $questionId, $time);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $answers = [];
                while ($row = $result->fetch_assoc()) {
                    $answer = [];
                    $answer['answer'] = $row['answer'];
                    $answer['count'] = $row['count'];
                    $answer['time'] = $row['time'];
                    $answers[] = $answer;
                }
                $stmt = $conn->prepare($question_query);
                if ($stmt){
                    $stmt->bind_param('i', $questionId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $question = $result->fetch_assoc()['question'];
                        echo json_encode(['success' => true, 'question' => $question, 'answers' => $answers]);
                    } else{
                        echo json_encode(['success' => false, 'message' => 'No question found for the answers.']);
                        exit();
                    }
                } else{
                    echo json_encode(['success' => false, 'message' => 'Error preparing the select statement.']);
                    exit();
                }
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