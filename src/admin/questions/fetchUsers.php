<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';

check(['0']);
$conn = getDatabaseConnection();

$result_1 = $conn->query("
    SELECT DISTINCT u.username
    FROM questions q
    JOIN users u ON q.creator = u.id
    ORDER BY u.username ASC
");
$result_2 = $conn->query("
    SELECT DISTINCT u.username
    FROM abc_questions q
    JOIN users u ON q.creator = u.id
    ORDER BY u.username ASC
");
$users = [];
while ($row = $result_1->fetch_assoc()) {
    $users[] = $row['username'];
}
while ($row = $result_2->fetch_assoc()) {
    $users[] = $row['username'];
}
$users = array_unique($users);
sort($users);
echo json_encode(['success' => true, 'users' => $users]);
$conn->close();
