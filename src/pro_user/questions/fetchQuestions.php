<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';

check(['1']);
$conn = getDatabaseConnection();

if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit();
}

$sql_1 = "
    SELECT q.id, q.question, q.category, q.active, q.qr_code, DATE(q.created_at) AS time, u.username 
    FROM questions q 
    JOIN users u ON q.creator = u.id
    WHERE q.creator = {$_SESSION['user']['id']}
";

$sql_2 = "
    SELECT q.id, q.question, q.category, q.active, q.qr_code, DATE(q.created_at) AS time, u.username 
    FROM abc_questions q 
    JOIN users u ON q.creator = u.id
    WHERE q.creator = {$_SESSION['user']['id']}
";

$result_1 = $conn->query($sql_1);
$result_2 = $conn->query($sql_2);

if ($result_1 && $result_2) {
    $questions = [];
    while ($row = $result_1->fetch_assoc()) {
        $questions[] = [
            'id' => $row['id'],
            'question' => $row['question'],
            'category' => $row['category'],
            'creator' => $row['username'],
            'time' => $row['time'],
            'type' => 'One Answer',
            'active' => $row['active'] ? 'true' : 'false',
            'qr_code' => $row['qr_code']
        ];
    }
    while ($row = $result_2->fetch_assoc()) {
        $questions[] = [
            'id' => $row['id'],
            'question' => $row['question'],
            'category' => $row['category'],
            'creator' => $row['username'],
            'time' => $row['time'],
            'type' => 'Multiple Choice',
            'active' => $row['active']? 'true' : 'false',
            'qr_code' => $row['qr_code']
        ];
    }
    header('Content-Type: application/json');
    echo json_encode(['data' => $questions]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'SQL error: ' . mysqli_error($conn)]);
}
$conn->close();
