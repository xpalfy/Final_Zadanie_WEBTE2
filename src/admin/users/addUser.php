<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';
check(['0']);

$conn = getDatabaseConnection();

function isAlreadyUser($conn, $username): void
{
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'User already exists.']);
        exit();
    }
}

function createUser($username, $password, $type): void
{
    $conn = getDatabaseConnection();
    isAlreadyUser($conn, $username);
    $stmt = $conn->prepare('INSERT INTO users (username, password, type) VALUES (?, ?, ?)');
    $stmt->bind_param('ssi', $username, $password, $type);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => true]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'];
    $password = $data['password'];
    $type = $data['type'];

    if (!is_string($username) || strlen($username) > 255) {
        echo json_encode(['success' => false, 'message' => 'Invalid username.']);
        exit();
    }

    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long.']);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    createUser($username, $hashedPassword, $type);

    exit();
}

