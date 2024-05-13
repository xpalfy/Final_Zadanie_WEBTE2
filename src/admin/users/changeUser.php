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
    $username = $data['username'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $type = $data['type'];
    $id = $data['id'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, type = ? WHERE id = ?");
    $stmt->bind_param('sssi', $username, $password, $type, $id);
    $stmt->execute();
    if ($stmt->affected_rows > 0 || $stmt->errno === 0) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user']);
    }
}

