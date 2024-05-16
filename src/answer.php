<?php

require_once 'config.php';
$conn = getDatabaseConnection();
// get number from url
if(!isset($_GET['key'])){
    directBackToIndex();
}
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

switch ($type) {
    case 'one_answer':
        $sql = "SELECT * FROM answers WHERE question_id = " . $question['id'];
        $result = $conn->query($sql);
        $answers = [];
        while ($row = $result->fetch_assoc()) {
            $answers[] = $row;
        }
        break;
    case 'abc_answer':
        $sql = "SELECT * FROM abc_answers WHERE question_id = " . $question['id'];
        $result = $conn->query($sql);
        $answers = [];
        $vote_count = 0;
        while ($row = $result->fetch_assoc()) {
            $vote_count += $row['count'];
            $answers[] = $row;
        }
        break;
    default:
        directBackToIndex();
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
    header('Location: ../index.php');
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
    <link rel="stylesheet" href="../styles/base.css">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../css/customSwitch.css">
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
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Registration</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container cont justify-content-center align-items-center">
    <div class="card bg-dark" style="width: 100%">
        <div class="card-body" style="display: flex; flex-direction: column; align-items: center; width: 100%">
            <h1 class="text-center mb-4" id="Question_text"></h1>
            <div class="container" id="answers"></div>
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
    $(document).ready(function () {
        let question = <?php echo json_encode($question); ?>;
        let type = '<?php echo $type; ?>';
        $('#Question_text').text(question.question);

        switch (type) {
            case 'one_answer':
                $('#answers').append(`<ul class="list-group" id="answersList"></ul>`);
                // append with the answers in a ul
                <?php foreach ($answers as $answer):?>
                $('#answersList').append(`
                <li class="list-group-item" style="color:black;">
                    <div>
                        <h1><?php echo $answer['answer']; ?></h1>
                    </div>
                </li>
                `);
                <?php endforeach; ?>
                break;
            case 'abc_answer':
                $('#answers').append(`<div class="span6" id="answersList"></div>`);
                // append with the answers in a ul
                <?php foreach ($answers as $answer):?>
                $('#answersList').append(`
                <strong><?php echo $question[strtolower($answer['answer'])]; ?></strong><span style="float:right;"><?php echo $answer['count']; ?></span>
                <div class="progress active" style="height:2rem;">
                    <div class="progress-bar" role="progressbar" style="<?php if($answer['answer'] == $question['answer']){echo "background-color:#25c525;";} ?>width: <?php echo ($answer['count'] / $vote_count) * 100; ?>%" aria-valuenow="<?php echo $answer['count']; ?>" aria-valuemin="0" aria-valuemax="1"></div>
                </div>
                <br>
                `);
                <?php endforeach; ?>
                break;
        }
    });




</script>
</body>
</html>



