<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';

check(['0']);
$conn = getDatabaseConnection();

$sql = "SELECT * FROM users WHERE id != ?";
$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $_SESSION['user']['id']);
$stmt->execute();
$result = $stmt->get_result();
$users = [];

for ($i = 0; $i < $result->num_rows; $i++) {
    $row = $result->fetch_assoc();
    $users[] = [
        'id' => $row['id'],
        'username' => $row['username'],
    ];
}

header('Content-Type: application/json');
echo json_encode(['data' => $users]);
