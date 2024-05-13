<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';

date_default_timezone_set('Europe/Bratislava');

check(['1']);

$conn = getDatabaseConnection();

$conn->query("SET time_zone = '+01:00'");

$result_1 = $conn->query("SELECT DISTINCT DATE(created_at) AS created_date FROM questions WHERE creator = {$_SESSION['user']['id']}  ORDER BY created_date ASC");

$result_2 = $conn->query("SELECT DISTINCT DATE(created_at) AS created_date FROM abc_questions WHERE creator = {$_SESSION['user']['id']} ORDER BY created_date ASC");

$created_dates = [];

while ($row = $result_1->fetch_assoc()) {
    $created_dates[] = $row['created_date'];
}

while ($row = $result_2->fetch_assoc()) {
    $created_dates[] = $row['created_date'];
}

$created_dates = array_unique($created_dates);
sort($created_dates);

echo json_encode(['success' => true, 'created_dates' => $created_dates]);

$conn->close();
