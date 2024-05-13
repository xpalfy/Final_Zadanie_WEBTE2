<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';

check(['0']);
$conn = getDatabaseConnection();

$result = $conn->query("SELECT username FROM users");
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row['username'];
}
$users = array_unique($users);
sort($users);
echo json_encode(['success' => true, 'users' => $users]);
$conn->close();
