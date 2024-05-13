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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <div class="filter-bar">
        <div class="form-group">
            <label for="filterCategory">Filter by Category:</label>
            <select id="filterCategory" class="form-control">
                <option value="">All Categories</option>
            </select>
        </div>
        <div class="form-group">
            <label for="filterTime">Filter by Time:</label>
            <select id="filterTime" class="form-control">
                <option value="">All Time</option>
            </select>
        </div>
    </div>
    <table id="questionsTable" class="display nowrap" style="width:100%">
        <thead>
        <tr>
            <th>Question</th>
            <th>Category</th>
            <th>Time</th>
            <th>Change</th>
            <th>Delete</th>
            <th>Activate</th>
            <th>QR Code</th>
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
                        <textarea class="form-control" id="questionText" name="questionText" required
                                  oninput="isValidQuestion(this)"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="questionCategory">Category</label>
                        <input type="text" class="form-control" id="questionCategory" name="questionCategory" required
                               oninput="isValidText(this)">
                    </div>
                    <div id="multipleChoiceOptions" style="display:none;">
                        <div class="form-group">
                            <label for="optionA">Option A:</label>
                            <input type="text" class="form-control" id="optionA" name="optionA"
                                   oninput="isValidText(this)">
                        </div>
                        <div class="form-group">
                            <label for="optionB">Option B:</label>
                            <input type="text" class="form-control" id="optionB" name="optionB"
                                   oninput="isValidText(this)">
                        </div>
                        <div class="form-group">
                            <label for="optionC">Option C:</label>
                            <input type="text" class="form-control" id="optionC" name="optionC"
                                   oninput="isValidText(this)">
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
<div class="modal fade" id="changeQuestionModal" tabindex="-1" role="dialog" aria-labelledby="changeQuestionModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeQuestionModalLabel">Change Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="changeQuestionForm">
                    <input type="hidden" id="changeQuestionId" name="questionId">
                    <div class="form-group">
                        <label for="changeQuestionType">Select question type:</label>
                        <select class="form-control" id="changeQuestionType" name="questionType" required>
                            <option value="1">One answer</option>
                            <option value="2">Multiple Choice</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="changeQuestionText">Question Text</label>
                        <textarea class="form-control" id="changeQuestionText" name="questionText" required
                                  oninput="isValidQuestion(this)"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="changeQuestionCategory">Category</label>
                        <input type="text" class="form-control" id="changeQuestionCategory" name="questionCategory"
                               required oninput="isValidInput(this)">
                    </div>
                    <div id="changeMultipleChoiceOptions" style="display:none;">
                        <div class="form-group">
                            <label for="changeOptionA">Option A:</label>
                            <input type="text" class="form-control" id="changeOptionA" name="optionA"
                                   oninput="isValidInput(this)">
                        </div>
                        <div class="form-group">
                            <label for="changeOptionB">Option B:</label>
                            <input type="text" class="form-control" id="changeOptionB" name="optionB"
                                   oninput="isValidInput(this)">
                        </div>
                        <div class="form-group">
                            <label for="changeOptionC">Option C:</label>
                            <input type="text" class="form-control" id="changeOptionC" name="optionC"
                                   oninput="isValidInput(this)">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
    function loadCategories() {
        $.ajax({
            url: './questions/fetchCategories.php',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#filterCategory').empty().append('<option value="">All Categories</option>');
                    data.categories.forEach(function (category) {
                        $('#filterCategory').append($('<option>').text(category).val(category));
                    });
                } else {
                    toastr.error(data.message || 'Error loading categories.');
                }
            },
            error: function () {
                toastr.error('Failed to load categories.');
            }
        });
    }

    function loadTime() {
        $.ajax({
            url: './questions/fetchTime.php',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#filterTime').empty().append('<option value="">All Time</option>');
                    data.created_dates.forEach(function (date) {
                        $('#filterTime').append($('<option>').text(date).val(date));
                    });
                } else {
                    toastr.error(data.message || 'Error loading time.');
                }
            },
            error: function () {
                toastr.error('Failed to load time.');
            }
        });
    }

    function fetchQuestions() {
        let currentCategory = $('#filterCategory').val();
        let currentTime = $('#filterTime').val();
        $.ajax({
            url: './questions/fetchQuestions.php',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#questionsTable').DataTable({
                    data: data.data,
                    responsive: true,
                    columns: [
                        {data: 'question', title: 'Question'},
                        {data: 'category', title: 'Category',visible: false},
                        {data: 'time', title: 'Time',visible: false},
                        {
                            data: 'id',
                            title: 'Change',
                            render: function (data, type, row) {
                                return `<button onclick="changeQuestion('${row.id}','${row.type}')" class="btn btn-primary btn-sm" style="min-width: 80%;">Change</button>`;
                            },
                            orderable: false
                        },
                        {
                            data: 'id',
                            title: 'Delete',
                            render: function (data, type, row) {
                                return `<button onclick="deleteQuestion('${row.id}','${row.type}')" class="btn btn-danger btn-sm" style="min-width: 80%;">Delete</button>`;
                            },
                            orderable: false
                        },
                        {
                            data: 'id',
                            title: 'Activate',
                            render: function (data, type, row) {
                                return `<button onclick="activateQuestion('${row.id}','${row.type}')" class="btn btn-${row.active === 'true' ? 'secondary' : 'success'} btn-sm" style="min-width: 80%;">${row.active === 'true' ? 'Deactivate' : 'Activate'}</button>`;
                            },
                            orderable: false
                        },
                        {
                            data: 'id',
                            title: 'QR Code',
                            render: function (data, type, row) {
                                return `<button onclick="generateQRCode('${row.qr_code}', '${row.type}')" class="btn btn-primary btn-sm" style="min-width: 80%;">QR Code</button>`;
                            },
                            orderable: false
                        }
                    ],
                    createdRow: function (row) {
                        $('td', row).css('text-align', 'center');
                    },
                    initComplete: function () {
                        var api = this.api();

                        $('#filterCategory').on('change', function () {
                            api.column(1).search(this.value).draw();
                        });

                        $('#filterTime').on('change', function () {
                            api.column(2).search(this.value).draw();
                        });

                        if ($('#filterCategory option[value="' + currentCategory + '"]').length > 0) {
                            $('#filterCategory').val(currentCategory);
                        } else {
                            $('#filterCategory').val(''); // Revert to default if not found
                        }

                        // Check and set 'Time'
                        if ($('#filterTime option[value="' + currentTime + '"]').length > 0) {
                            $('#filterTime').val(currentTime);
                        } else {
                            $('#filterTime').val(''); // Revert to default if not found
                        }

                        api.column(1).search($('#filterCategory').val()).draw(); // Adjust column index if necessary
                        api.column(2).search($('#filterTime').val()).draw();      // Adjust column index if necessary
                    }
                });
            },
            error: function () {
                toastr.error('Failed to load questions. Please try again.');
            }
        });
    }

    function generateQRCode(qrCode) {
        let fullUrl = `https://node84.webte.fei.stuba.sk:1000/question.php?qr_code=${qrCode}`;
        Swal.fire({
            title: 'QR Code',
            text: 'Scan the QR code to view the question',
            imageUrl: `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${fullUrl}`,
            imageWidth: 150,
            imageHeight: 150,
            imageAlt: 'QR Code',
            showCloseButton: true,
            showConfirmButton: false
        });
    }

    function changeQuestion(id, type) {
        $.ajax({
            url: './questions/fetchQuestionDetails.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({id: id, type: type}),
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#changeQuestionId').val(data.question.id);
                    $('#changeQuestionText').val(data.question.question);
                    $('#changeQuestionCategory').val(data.question.category);

                    if (type === 'Multiple Choice') {
                        $('#changeQuestionType').empty().append($('<option>', {
                            value: '2',
                            text: 'Multiple Choice'
                        })).val('2');
                        $('#changeMultipleChoiceOptions').show();
                        $('#changeOptionA').val(data.question.options.a);
                        $('#changeOptionB').val(data.question.options.b);
                        $('#changeOptionC').val(data.question.options.c);
                    } else {
                        $('#changeQuestionType').empty()
                            .append($('<option>', {value: '1', text: 'One answer'}))
                            .val('1');
                        $('#changeMultipleChoiceOptions').hide();
                        $('#changeOptionA').val('');
                        $('#changeOptionB').val('');
                        $('#changeOptionC').val('');
                    }

                    $('#changeQuestionModal').modal('show');
                } else {
                    toastr.error(data.message || 'Error fetching question details.');
                }
            },
            error: function () {
                toastr.error('Failed to fetch question details.');
            }
        });
    }

    $('#changeQuestionForm').on('submit', function (event) {
        event.preventDefault();
        if (!checkFormChange()) {
            return;
        }
        let formData = $(this).serializeArray().reduce(function (obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});
        if ($('#changeQuestionType').val() === '1') {
            delete formData.optionA;
            delete formData.optionB;
            delete formData.optionC;
        }
        $.ajax({
            type: 'POST',
            url: './questions/updateQuestion.php',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#changeQuestionModal').modal('hide');
                    toastr.success('Question updated successfully!');
                    $('#questionsTable').DataTable().clear().destroy();
                    loadCategories();
                    loadTime();
                    clearChangeModal();
                    fetchQuestions();
                } else {
                    toastr.error(data.message || 'Error updating question.');
                }
            },
            error: function () {
                toastr.error('Failed to update question.');
            }
        });
    });

    function deleteQuestion(id, type) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: './questions/deleteQuestion.php',
                    contentType: 'application/json',
                    data: JSON.stringify({id: id, type: type}),
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            $('#questionsTable').DataTable().clear().destroy();
                            loadCategories();
                            loadTime();
                            fetchQuestions();
                            $('#filterType').val('');
                            toastr.success('Question deleted successfully!');
                        } else {
                            toastr.error(data.message || 'Error deleting question.');
                        }
                    },
                    error: function () {
                        toastr.error('Failed to delete question.');
                    }
                });
            }
        });
    }

    function activateQuestion(id, type) {
        $.ajax({
            type: 'POST',
            url: './questions/activateQuestion.php',
            contentType: 'application/json',
            data: JSON.stringify({id: id, type: type}),
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#questionsTable').DataTable().clear().destroy();
                    fetchQuestions();
                    if (data.active) {
                        toastr.success('Question activated successfully!');
                    } else {
                        toastr.success('Question deactivated successfully!');
                    }
                } else {
                    toastr.error(data.message || 'Error changing question status. Please try again.');
                }
            },
            error: function () {
                toastr.error('Failed to connect to server. Please check your connection.');
            }
        });
    }

    function clearAddModal() {
        removeError('questionType');
        removeError('questionText');
        removeError('questionCategory');
        removeError('optionA');
        removeError('optionB');
        removeError('optionC');
        removeSuccess('questionType');
        removeSuccess('questionText');
        removeSuccess('questionCategory');
        removeSuccess('optionA');
        removeSuccess('optionB');
        removeSuccess('optionC');
        $('#questionType').val('1');
        $('#questionText').val('');
        $('#questionCategory').val('');
        $('#optionA').val('');
        $('#optionB').val('');
        $('#optionC').val('');
        $('#multipleChoiceOptions').hide();
    }
    function clearChangeModal() {
        removeError('changeQuestionType');
        removeError('changeQuestionText');
        removeError('changeQuestionCategory');
        removeError('changeOptionA');
        removeError('changeOptionB');
        removeError('changeOptionC');
        removeSuccess('changeQuestionType');
        removeSuccess('changeQuestionText');
        removeSuccess('changeQuestionCategory');
        removeSuccess('changeOptionA');
        removeSuccess('changeOptionB');
        removeSuccess('changeOptionC');
        $('#changeQuestionType').val('1');
        $('#changeQuestionText').val('');
        $('#changeQuestionCategory').val('');
        $('#changeOptionA').val('');
        $('#changeOptionB').val('');
        $('#changeOptionC').val('');
        $('#changeMultipleChoiceOptions').hide();
    }

    $(document).ready(function () {
        loadCategories();
        loadTime();
        $('#addQuestionModal').on('hidden.bs.modal', function () {
            clearAddModal();
        });
        $('#changeQuestionModal').on('hidden.bs.modal', function () {
            clearChangeModal();
        });
        $('#questionType').change(function () {
            let isMultipleChoice = $(this).val() === '2';
            $('#multipleChoiceOptions').toggle(isMultipleChoice);
            $('#optionA, #optionB, #optionC').prop('required', isMultipleChoice);
        });

        $('#addQuestionForm').on('submit', function (event) {
            event.preventDefault();
            if(!checkFormAdd()){
                return;
            }
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
                contentType: 'application/json',
                data: JSON.stringify(formData),
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        toastr.success('Question added successfully!');
                        $('#addQuestionModal').modal('hide');
                        $('#questionsTable').DataTable().clear().destroy();
                        clearAddModal();
                        loadCategories();
                        loadTime();
                        fetchQuestions();
                    } else {
                        toastr.error(data.message || 'Error adding question. Please try again.');
                    }
                },
                error: function () {
                    toastr.error('Failed to connect to server. Please check your connection.');
                }
            });
        });

        fetchQuestions();
    });
</script>
</body>
</html>