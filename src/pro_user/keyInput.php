<?php
function redirectUser($key)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


    if (!preg_match('/^[a-zA-Z0-9]{5}$/', $key)) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Invalid key format'
        ];
        header('Location: https://node84.webte.fei.stuba.sk:1000/pro_user/keyInput.php');
        exit();
    }

    $_SESSION['key'] = (int)$key;
    $link = 'https://node84.webte.fei.stuba.sk:1000/question.php?key=' . $key;
    header('Location: ' . $link);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key = $_POST['key'];
    redirectUser($key);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KeyInput</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="../styles/base.css">
    <link rel="stylesheet" href="../styles/pro_user.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="../js/regex.js"></script>
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
                    <a class="nav-link" href="keyInputSK.php">Slovak Version</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="menu.php">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="questions.php">Questions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="keyInput.php">Key Input</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container cont justify-content-center align-items-center">
    <div class="col-sm-10 col-md-10 col-lg-8">
        <div class="card bg-dark">
            <div class="card-body">
                <h1 class="text-center mb-4">Question key</h1>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="key"><i class="fas fa-hashtag"></i> Key:</label>
                        <input type="text" class="form-control" id="number" name="key" required
                               oninput="isValidKey(this)">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
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
