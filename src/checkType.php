<?php
function check($allowedTypes = []): void
{
    if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'There is no user logged in!'];
        header('Location: /login.php');
        exit();
    }
    $type = $_SESSION['user']['type'];
    if (!in_array($type, $allowedTypes)) {
        session_unset();
        session_destroy();
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'You do not have permission to access this page!'];
        header('Location: /login.php');
        exit();
    }
}
