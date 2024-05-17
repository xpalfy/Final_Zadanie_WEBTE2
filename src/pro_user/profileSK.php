<?php
require '../checkType.php';
require '../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
check(['1']);
$conn = getDatabaseConnection();

function samePassword($password, $conn): bool
{
    $id = $_SESSION['user']['id'];
    $hash = null;
    $stmt = $conn->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hash);
    $stmt->fetch();
    $stmt->close();
    return password_verify($password, $hash);
}

function changePassword($password, $conn): void
{
    $id = $_SESSION['user']['id'];
    $password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
    $stmt->bind_param('si', $password, $id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['toast'] = [
        'type' => 'success',
        'message' => 'Heslo bolo úspešne zmenené.'
    ];
    header('Location: profileSK.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    if (samePassword($password, $conn)) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Nové heslo sa musí líšiť od starého.'
        ];
    } else {
        changePassword($password, $conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Môj profil</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="../styles/base.css">
    <link rel="stylesheet" href="../styles/pro_user.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="../js/regexSK.js"></script>
</head>
<body>
<script>
    function checkToasts() {
        let toast = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
        if (toast) {
            toastr[toast.type](toast.message);
            <?php unset($_SESSION['toast']); ?>
        }
    }

    checkToasts();
</script>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <button aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"
                class="navbar-toggler"
                data-target="#navbarNav" data-toggle="collapse" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Anglická Verzia</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="menuSK.php">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="questionsSK.php">Otázky</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profileSK.php">Môj Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="keyInputSK.php">Zadanie Kódu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Odhlásenie</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container cont justify-content-center align-items-center">
    <div class="col-sm-10 col-md-10 col-lg-8">
        <div class="card bg-dark">
            <div class="col-12 d-flex flex-column flex-sm-row justify-content-between align-items-center">
                <div class="mr-2 mb-2">
                    <h1 class="mt-3 mb-1">Modifikácia</br> hesla</h1>
                    <h5 class="card-title">Používateľské meno: <?php echo $_SESSION['user']['username']; ?></h5>
                </div>
                <img src="../img/profile/1.png" alt="avatar" class="avatar img-fluid mt-responsive ml-3 mr-3">
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Heslo:</label>
                        <input type="password" name="password" id="password" class="form-control" autocomplete="off"
                               placeholder="*******" required oninput="isValidPassword(this)">
                    </div>
                    <button type="submit" class="btn btn-primary">Odoslať</button>
                </form>
            </div>
        </div>
    </div>
</div>

<footer class="page-footer font-small bg-dark">
    <div class="container">
        <div class="text-center py-3 text-light">
            &copy; WebTech 2 - Final Zadanie
        </div>
    </div>
</footer>
<script>
    let form = document.querySelector('form');
    form.addEventListener('submit', checkForm);
</script>
</body>
</html>
