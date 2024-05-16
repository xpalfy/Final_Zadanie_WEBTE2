<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';

check(['1']);
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

                if($newStatus == 0){
                    switch ($data['type']) {
                        case 'One Answer':
                            $query = "SELECT * FROM answers WHERE question_id = ?";
                            $archivestmt = $conn->prepare($query);
                            $archivestmt->bind_param('i', $questionId);
                            $archivestmt->execute();
                            $result = $archivestmt->get_result();
                            if ($result->num_rows > 0) {
                                $time = date('Y-m-d H:i:s');
                                $archiveQuery = "INSERT INTO answers_archive (question_id, answer, count, time) VALUES (?, ?, ?, ?)";
                                $archiveStmt = $conn->prepare($archiveQuery);
                                while ($row = $result->fetch_assoc()) {
                                    $archiveStmt->bind_param('isis', $row['question_id'], $row['answer'], $row['count'], $time);
                                    $archiveStmt->execute();
                                }
                                $archiveStmt->close();
                                // delete the answers
                                $deleteQuery = "DELETE FROM answers WHERE question_id = ?";
                                $deleteStmt = $conn->prepare($deleteQuery);
                                $deleteStmt->bind_param('i', $questionId);
                                $deleteStmt->execute();
                                $deleteStmt->close();
                            }
                            break;
                        case 'Multiple Choice':
                            $query = "SELECT * FROM abc_answers WHERE question_id = ?";
                            $archivestmt = $conn->prepare($query);
                            $archivestmt->bind_param('i', $questionId);
                            $archivestmt->execute();
                            $result = $archivestmt->get_result();
                            if ($result->num_rows > 0) {
                                $time = date('Y-m-d H:i:s');
                                $archiveQuery = "INSERT INTO abc_answers_archive (question_id, answer, correct, time) VALUES (?, ?, ?, ?)";
                                $archiveStmt = $conn->prepare($archiveQuery);
                                while ($row = $result->fetch_assoc()) {
                                    $archiveStmt->bind_param('isis', $row['question_id'], $row['answer'], $row['correct'], $time);
                                    $archiveStmt->execute();
                                }
                                $archiveStmt->close();
                                // delete the answers
                                $deleteQuery = "DELETE FROM abc_answers WHERE question_id = ?";
                                $deleteStmt = $conn->prepare($deleteQuery);
                                $deleteStmt->bind_param('i', $questionId);
                                $deleteStmt->execute();
                                $deleteStmt->close();
                            }
                            break;
                    }
                }

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
