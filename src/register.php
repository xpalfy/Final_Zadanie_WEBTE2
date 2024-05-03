<?php
require_once 'config.php';

function createUser($username, $password): void
{
    $conn = getDatabaseConnection();
    $stmt = $conn->prepare('INSERT INTO users (username, password, type) VALUES (?, ?, ?)');
    $type = 1;
    $stmt->bind_param('ssi', $username, $password, $type);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    createUser($username, password_hash($password, PASSWORD_DEFAULT));
    header('Location: login.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="styles/register.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="js/regex.js"></script>
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh;">
        <div class="col-sm-10 col-md-10 col-lg-10">
            <div class="register">
                <h1 class="text-center mb-5">Registration Form</h1>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Username:</label>
                        <input type="text" class="form-control" id="username" name="username" required
                               oninput="isValidInput(this)">
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password:</label>
                        <input type="password" class="form-control" id="password" name="password" autocomplete="off"
                               required oninput="isValidPassword(this)">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    let form = document.querySelector('form');
    form.addEventListener('submit', checkForm);
</script>
</body>
</html>
