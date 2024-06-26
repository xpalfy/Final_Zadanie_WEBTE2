<?php
$keyNotSet = false;
$notActive = false;
$doesntExist = false;

require_once 'config.php';
$conn = getDatabaseConnection();
// get number from url
if(!isset($_GET['key'])){
    $keyNotSet = true;
    directBackToIndex($keyNotSet, $notActive, $doesntExist);
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
        $doesntExist = true;
        directBackToIndex($keyNotSet, $notActive, $doesntExist);
    } else {
        // check if question is active
        $question = $result->fetch_assoc();
        if ($question['active'] == 0) {
            $notActive = true;
            directBackToIndex($keyNotSet, $notActive, $doesntExist);
        }
    }
    $type = 'abc_answer';
} else {
    $type = 'one_answer';
    // check if question is active
    $question = $result->fetch_assoc();
    if ($question['active'] == 0) {
        $notActive = true;
        directBackToIndex($keyNotSet, $notActive, $doesntExist);
    }
}

function directBackToIndex($keyNotSet, $notActive, $doesntExist)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if ($keyNotSet) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Invalid key!'
        ];
    } elseif ($notActive) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Question is not active!'
        ];
    } elseif ($doesntExist) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => "Question doesn't exist!"
        ];
    }
    header('Location: ../login.php');
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userType = $_SESSION['user']['type'] ?? null;
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
<style>
    .answer{
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        border-radius: 10px;
        margin: 5px;
    }
    .answer p:hover{
        color: #cccccc;
        animation-name: colorChangeInAnswer;
        animation-duration: 2s;
        animation-iteration-count: infinite;
    }

    @keyframes colorChangeInAnswer {
        0% {
            color: #007bff;
        }
        25% {
            color: #28a745;
        }
        50% {
            color: #ffc107;
        }
        75% {
            color: #dc3545;
        }
        100% {
            color: #007bff;
        }
    }



</style>
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

        <?php if ($userType === 0): ?>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        <?php elseif ($userType === 1): ?>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        <?php else: ?>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Menu</a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
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
    const ws = new WebSocket("wss://node116.webte.fei.stuba.sk/wss");

    ws.onopen = function() {
        getAnswers();
    }

    ws.onmessage = function(event) {
        const data = JSON.parse(event.data);
        updateAnswers(data.data);
    };

    function organizeAnswersRandomPlaces() {
        let parent = document.getElementById('answersList');
        for (let i = parent.children.length; i >= 0; i--) {
            parent.appendChild(parent.children[Math.random() * i | 0]);
        }
        let answers = document.getElementsByClassName('answer');
        let answerCount = answers.length;
        // set random grid rows and cols for parent
        parent.style.display = 'grid';
        let i = 0
        while (i < answerCount) {
            let good = true;
            let row = Math.floor(Math.random() * answerCount) + 1;
            let col = Math.floor(Math.random() * answerCount) + 1;
            // check if grid is occupied
            for (let j = 0; j < i; j++) {
                if (answers[j].style.gridRowStart == row && answers[j].style.gridColumnStart == col) {
                    good = false;
                }
            }
            if (good) {
                answers[i].style.gridRow = row;
                answers[i].style.gridColumn = col;
                i++;
            }
        }
    }

    function getAnswers(){
        $.ajax({
            url: '../getAnswers.php',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                question_id: <?php echo $question['id']; ?>,
                type: '<?php echo $type; ?>'
            }),
            success: function (data) {
                let question = <?php echo json_encode($question); ?>;
                let answers = data.answers;
                let vote_count = data.vote_count;
                ws.send(JSON.stringify({data: { question: question, answers: answers, vote_count: vote_count } }));
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    function updateAnswers(data) {
        let question = data.question;
        let answers = data.answers;
        let vote_count = data.vote_count;
        let type = '<?php echo $type; ?>';

        $('#answers').empty();

        switch (type) {
            case 'one_answer':
                $('#answers').append('<div class="container" id="answersList"></div>');
                for (let i = 0; i < answers.length; i++) {
                    $('#answersList').append(`<div class="answer"><p style="margin-bottom:0;font-size:${answers[i].count * 2 / vote_count * 5}vw">${answers[i].answer}</p></div>`);
                }
                organizeAnswersRandomPlaces();
                break;
            case 'abc_answer':
                $('#answers').append('<div class="span6" id="answersList"></div>');
                let widths = [];
                for (let i = 0; i < answers.length; i++) {
                    let correct = false;
                    let answerText = '';
                    widths.push((answers[i].count / vote_count) * 100);
                    switch (answers[i].answer) {
                        case 'A':
                            correct = (question.answer === 'A')
                            answerText = question.a;
                            break;
                        case 'B':
                            correct = (question.answer === 'B')
                            answerText= question.b;
                            break;
                        case 'C':
                            correct = (question.answer === 'C')
                            answerText = question.c;
                            break;
                    }
                    let color = correct ? 'bg-success' : 'bg-danger';
                    $('#answersList').append(`<strong>${answerText}</strong><span style="float:right;">${answers[i].count}</span>
                        <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="height: 2rem">
                            <div class="progress-bar ${color} progress-bar-striped progress-bar-animated" style="width: 0"></div>
                        </div>
                        <br>
                    `);
                }
                setTimeout(() => {
                    setWidths(widths);
                }, 500);
                break;
        }
    }

    function setWidths(widths) {
        let progressBars = document.getElementsByClassName('progress-bar');
        for (let i = 0; i < progressBars.length; i++) {
            progressBars[i].style.width = `${widths[i]}%`;
            progressBars[i].innerText = `${widths[i].toFixed(2)}%`;
        }
    }

    $(document).ready(function () {
        let question = <?php echo json_encode($question); ?>;
        $('#Question_text').text(question.question);
    });




</script>
</body>
</html>



