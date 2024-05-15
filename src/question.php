<?php
require_once 'config.php';
$conn = getDatabaseConnection();
// get number from url
$qr_code = $_GET['key'];
// get question from database
$sql = "SELECT * FROM questions WHERE qr_code = '$qr_code'";
$result = $conn->query($sql);
// if question not exists in type in question check in abc questions
if ($result->num_rows == 0) {
    $sql = "SELECT * FROM abc_questions WHERE qr_code = '$qr_code'";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        directBackToIndex();
    } else {
      // check if question is active
        $question = $result->fetch_assoc();
        if ($question['active'] == 0) {
            directBackToIndex();
        }
    }
    $type = 'abc_answer';
} else {
    $type = 'one_answer';
    // check if question is active
    $question = $result->fetch_assoc();
    if ($question['active'] == 0) {
        directBackToIndex();
    }
}
function directBackToIndex()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Invalid key'
    ];
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Question</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/index.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="js/regex.js"></script>
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
<div class="container cont justify-content-center align-items-center">
    <div class="col-sm-10 col-md-10 col-lg-8">
        <div class="card bg-dark">
            <div class="card-body">
                <h1 class="text-center mb-4" id="Question_text"></h1>
                <form action="" method="post">
                    <div class="form-group" id="Answers">

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

<?php
// make question text
echo "<script>document.getElementById('Question_text').innerText = '" . $question['question'] . "';</script>";


