<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../checkType.php';
require '../../config.php';

check(['1']);
$conn = getDatabaseConnection();

$result_1 = $conn->query("SELECT DISTINCT category FROM questions WHERE creator = {$_SESSION['user']['id']} ORDER BY category ASC");
$result_2 = $conn->query("SELECT DISTINCT category FROM abc_questions WHERE creator = {$_SESSION['user']['id']} ORDER BY category ASC");
$categories = [];
while ($row = $result_1->fetch_assoc()) {
    $categories[] = $row['category'];
}
while ($row = $result_2->fetch_assoc()) {
    $categories[] = $row['category'];
}
$categories = array_unique($categories);
sort($categories);
echo json_encode(['success' => true, 'categories' => $categories]);
$conn->close();
