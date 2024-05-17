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

    if ($_SESSION['user']['type'] == 0) {
        if ($_SESSION['slovak'] === true) {
            header('Location: ./admin/keyInputSK.php');
        } else {
            header('Location: ./admin/keyInput.php');
        }
    } else if ($_SESSION['user']['type'] == 1) {
        if ($_SESSion['slovak'] === true) {
            header('Location: ./pro_user/keyInputSK.php');
        } else {
            header('Location: ./pro_user/keyInput.php');
        }
    } else {
        header('Location: index.php');
    }
    
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
    <link rel="stylesheet" href="css/customSwitch.css">
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
    <div>
        <div class="card bg-dark" style="width: fit-content">
            <div class="card-body" style="display: flex; flex-direction: column; align-items: center; width: fit-content">
                <h1 class="text-center mb-4" id="Question_text"></h1>
                <form action="" method="post" id="answerForm">
                    <div class="form-group" id="Answers"></div>
                    <button type="submit" class="btn btn-primary btn-block" id="submitBtn">Submit</button>
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
    $(document).ready(function () {
        let question = <?php echo json_encode($question); ?>;
        let type = '<?php echo $type; ?>';
        $('#Question_text').text(question.question);
        switch (type) {
            case 'one_answer':
                $('#Answers').append(`
                <div class="form-group">
                    <input type="hidden" name="question_id" value="${question.id}">
                    <input type="hidden" name="type" value="one_answer">
                    <input type="hidden" name="qr_code" value="${question.qr_code}">
                    <input type="text" class="form-control" name="answer" placeholder="Answer" required>
                </div>
            `);
                break;
            case 'abc_answer':
                let correctAnswer = question.answer;
                $('#Answers').append(`
                <div class="form-group">
                <input type="hidden" name="question_id" value="${question.id}">
                <input type="hidden" name="type" value="abc_answer">
                <input type="hidden" name="qr_code" value="${question.qr_code}">
                <input type="hidden" name="correct_answer" value="${correctAnswer}">
                <div class="row" style="align-items: center; flex-wrap: nowrap">
                    <div class="col">
                        <h5>A: ${question.a}</h5>
                    </div>
                    <div class="" style=" align-items: center;">
                        <label class="switch">
                            <input type="checkbox" id="OptionASwitch" name="optionASwitch" value="true">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="row" style="align-items: center; flex-wrap: nowrap">
                    <div class="col">
                        <h5>B: ${question.b}</h5>
                    </div>
                    <div class="" style=" align-items: center;">
                        <label class="switch">
                            <input type="checkbox" id="ptionBSwitch" name="optionBSwitch" value="true">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="row" style="align-items: center; flex-wrap: nowrap;">
                    <div class="col">
                        <h5>C: ${question.c}</h5>
                    </div>
                    <div class="" style=" align-items: center;">
                        <label class="switch">
                            <input type="checkbox" id="OptionASwitch" name="optionCSwitch" value="true">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            `);
                break;
        }

        $('#answerForm').on('submit', function (e) {
          e.preventDefault();
          let formData = {};
          $(this).serializeArray().forEach(function (item) {
              formData[item.name] = item.value;
          });
            if(!formData['answer']){
                formData['answer'] = '';
            }

            if(formData.optionASwitch){
                formData['answer'] += 'A';
                delete formData.optionASwitch;
            }
            if(formData.optionBSwitch){
                formData['answer'] += 'B';
                delete formData.optionBSwitch;
            }
            if(formData.optionCSwitch){
                formData['answer'] += 'C';
                delete formData.optionCSwitch;
            }
          $.ajax({
              type: 'POST',
              url: 'addAnswer.php',
              contentType: 'application/json',
              data: JSON.stringify(formData),
              dataType: 'json',
              success: function (data) {
                  if (data.success) {
                      toastr.success('Answer added successfully!');
                      $('#submitBtn').prop('disabled', true);
                        setTimeout(function () {
                            window.location.href = 'answer.php/?key=<?php echo $question['qr_code']; ?>';
                        }, 1000);
                  } else {
                      toastr.error(data.message || 'Error adding answer. Please try again.');
                  }
              },
              error: function () {
                  toastr.error('Failed to connect to server. Please check your connection.');
              }
          });
    });
});

</script>
</body>
</html>



