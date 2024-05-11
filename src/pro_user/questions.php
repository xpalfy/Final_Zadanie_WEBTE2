<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../checkType.php';
check(['1']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Questions</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/questions.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

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
                    <a class="nav-link" href="menu.php">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="questions.php">Questions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="cont" style="width: 80%; margin: 60px auto;">
    <table id="questionsTable" class="display nowrap" style="width:100%">
        <thead>
        <tr>
            <th>Question</th>
            <th>Category</th>
            <th>Type</th>
            <th>Active</th>
            <th>Change</th>
            <th>Delete</th>
            <th>Activate</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="d-flex justify-content-center align-items-center">
        <div style="width: 80%;">
            <button class="btn btn-primary mt-3 btn-block" data-toggle="modal" data-target="#addQuestionModal">Add
                Question
            </button>
        </div>
    </div>
</div>
<div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog" aria-labelledby="addQuestionModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuestionModalLabel">Add New Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addQuestionForm">
                    <div class="form-group">
                        <label for="questionType">Select question type:</label>
                        <select class="form-control" id="questionType" name="questionType" required>
                            <option value="1" selected>One answer</option>
                            <option value="2">Multiple Choice</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="questionText">Question Text</label>
                        <textarea class="form-control" id="questionText" name="questionText" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="questionCategory">Category</label>
                        <input type="text" class="form-control" id="questionCategory" name="questionCategory" required>
                    </div>
                    <div id="multipleChoiceOptions" style="display:none;">
                        <div class="form-group">
                            <label for="optionA">Option A:</label>
                            <input type="text" class="form-control" id="optionA" name="optionA">
                        </div>
                        <div class="form-group">
                            <label for="optionB">Option B:</label>
                            <input type="text" class="form-control" id="optionB" name="optionB">
                        </div>
                        <div class="form-group">
                            <label for="optionC">Option C:</label>
                            <input type="text" class="form-control" id="optionC" name="optionC">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Question</button>
                    </div>
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
        $('#addQuestionModal').on('hidden.bs.modal', function () {
            clearModal();
        });
        $('#questionType').change(function () {
            let isMultipleChoice = $(this).val() === '2';
            $('#multipleChoiceOptions').toggle(isMultipleChoice);
            $('#optionA, #optionB, #optionC').prop('required', isMultipleChoice);
        });

        $('#addQuestionForm').on('submit', function (event) {
            event.preventDefault();

            let formData = {};
            $(this).serializeArray().forEach(function (item) {
                formData[item.name] = item.value;
            });

            if ($('#questionType').val() === '1') {
                delete formData.optionA;
                delete formData.optionB;
                delete formData.optionC;
            }

            $.ajax({
                type: 'POST',
                url: './questions/addQuestion.php',
                contentType: 'application/json', // Specifying the content type as JSON
                data: JSON.stringify(formData), // Convert formData to JSON string
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        toastr.success('Question added successfully!');
                        $('#addQuestionModal').modal('hide');
                        $('#questionsTable').DataTable().clear().destroy();
                        fetchQuestions();
                        clearModal();
                    } else {
                        toastr.error(data.message || 'Error adding question. Please try again.');
                    }
                },
                error: function () {
                    toastr.error('Failed to connect to server. Please check your connection.');
                }
            });
        });

        function clearModal() {
            $('#questionType').val('1');
            $('#questionText').val('');
            $('#questionCategory').val('');
            $('#optionA').val('');
            $('#optionB').val('');
            $('#optionC').val('');
            $('#multipleChoiceOptions').hide();
        }

        function fetchQuestions() {
            $.ajax({
                url: './questions/fetchQuestions.php',
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#questionsTable').DataTable({
                        responsive: true,
                        ajax: './questions/fetchQuestions.php',
                        columns: [
                            {data: 'question', title: 'Question'},
                            {data: 'category', title: 'Category'},
                            {data: 'type', title: 'Type'},
                            {data: 'active', title: 'Active Status'},
                            {
                                data: 'id',
                                title: 'Change',
                                render: function (data, type, row) {
                                    return `<button onclick="changeQuestion('${row.id}')" class="btn btn-primary btn-sm" style="min-width: 80%;">Change</button>`;
                                },
                                orderable: false
                            },
                            {
                                data: 'id',
                                title: 'Delete',
                                render: function (data, type, row) {
                                    return `<button onclick="deleteQuestion('${row.id}')" class="btn btn-danger btn-sm" style="min-width: 80%;">Delete</button>`;
                                },
                                orderable: false
                            },
                            {
                                data: 'id',
                                title: 'Activate',
                                render: function (data, type, row) {
                                    return `<button onclick="activateQuestion('${row.id}')" class="btn btn-${row.active ? 'secondary' : 'success'} btn-sm" style="min-width: 80%;">${row.active ? 'Deactivate' : 'Activate'}</button>`;
                                },
                                orderable: false
                            }
                        ],
                        createdRow: function(row, data, dataIndex) {
                            $('td', row).css('text-align', 'center');
                        }
                    });
                },
                error: function () {
                    toastr.error('Failed to load questions. Please try again.');
                }
            });
        }

        fetchQuestions();
    });

</script>
</body>
</html>