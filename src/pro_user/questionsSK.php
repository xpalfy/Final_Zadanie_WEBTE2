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
    <title>Otázky</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/questions.css">
    <link rel="stylesheet" href="../css/customSwitch.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <a class="nav-link" href="questions.php">Anglická Verzia</a>
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
<div class="cont" style="width: 80%; margin: 60px auto;">
    <div class="filter-bar">
        <div class="form-group">
            <label for="filterCategory">Filtrovanie podľa kategórie:</label>
            <select id="filterCategory" class="form-control">
                <option value="">Všetky kategórie</option>
            </select>
        </div>
        <div class="form-group">
            <label for="filterTime">Filtrovanie podľa dátumu:</label>
            <select id="filterTime" class="form-control">
                <option value="">Všetky dátumy</option>
            </select>
        </div>
    </div>
    <table id="questionsTable" class="display nowrap" style="width:100%">
        <thead>
        <tr>
            <th>Otázka</th>
            <th>Kategória</th>
            <th>Dátum</th>
            <th>Modifikácia</th>
            <th>Zmazanie</th>
            <th>Aktivovanie</th>
            <th>QR Kód</th>
            <th>Archivovanie</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="d-flex justify-content-center align-items-center">
        <div style="width: 80%;">
            <button class="btn btn-primary mt-3 btn-block" data-toggle="modal" data-target="#addQuestionModal">Pridať
                Otázku
            </button>
        </div>
    </div>
</div>
<div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog" aria-labelledby="addQuestionModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuestionModalLabel">Pridanie novej otázky</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addQuestionForm">
                    <div class="form-group">
                        <label for="questionType">Vyberte typ otázky:</label>
                        <select class="form-control" id="questionType" name="questionType" required>
                            <option value="1" selected>Jedna odpoveď</option>
                            <option value="2">Viaceré možnosti</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="questionText">Text otázky:</label>
                        <textarea class="form-control" id="questionText" name="questionText" required
                                  oninput="isValidQuestion(this)"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="questionCategory">Kategória:</label>
                        <input type="text" class="form-control" id="questionCategory" name="questionCategory" required
                               oninput="isValidText(this)">
                    </div>
                    <div id="multipleChoiceOptions" style="display:none;">
                        <div class="form-group">
                            <label for="optionA">Možnosť A:</label>
                            <div class="row">
                                <div class="col-9">
                                    <input type="text" class="form-control" id="optionA" name="optionA" oninput="isValidText(this)">
                                </div>
                                <div class="col-1" style=" align-items: center;">
                                    <label class="switch">
                                        <input type="checkbox" id="optionASwitch" name="optionASwitch" value="true">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="optionB">Možnosť B:</label>
                            <div class="row">
                                <div class="col-9">
                                    <input type="text" class="form-control" id="optionB" name="optionB" oninput="isValidText(this)">
                                </div>
                                <div class="col-1" style=" align-items: center;">
                                    <label class="switch">
                                        <input type="checkbox" id="optionBSwitch" name="optionBSwitch" value="true">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="optionC">Možnosť C:</label>
                            <div class="row">
                                <div class="col-9">
                                    <input type="text" class="form-control" id="optionC" name="optionC" oninput="isValidText(this)">
                                </div>
                                <div class="col-1" style=" align-items: center;">
                                    <label class="switch">
                                        <input type="checkbox" id="optionCSwitch" name="optionCSwitch" value="true">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvoriť okno</button>
                        <button type="submit" class="btn btn-primary">Uložiť otázku</button>
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
                <h5 class="modal-title" id="changeQuestionModalLabel">Modifikácia otázky</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="changeQuestionForm">
                    <input type="hidden" id="changeQuestionId" name="questionId">
                    <div class="form-group">
                        <label for="changeQuestionType">Vyberte typ otázky:</label>
                        <select class="form-control" id="changeQuestionType" name="questionType" required>
                            <option value="1">Jedna odpoveď</option>
                            <option value="2">Viaceré možnosti</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="changeQuestionText">Text otázky:</label>
                        <textarea class="form-control" id="changeQuestionText" name="questionText" required
                                  oninput="isValidQuestion(this)"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="changeQuestionCategory">Kategória:</label>
                        <input type="text" class="form-control" id="changeQuestionCategory" name="questionCategory"
                               required oninput="isValidInput(this)">
                    </div>
                    <div id="changeMultipleChoiceOptions" style="display:none;">
                        <div class="form-group">
                            <label for="changeOptionA">Možnosť A:</label>
                            <div class="row">
                                <div class="col-9">
                                    <input type="text" class="form-control" id="changeOptionA" name="optionA"
                                           oninput="isValidInput(this)">
                                </div>
                                <div class="col-1" style=" align-items: center;">
                                    <label class="switch">
                                        <input type="checkbox" id="changeOptionASwitch" name="optionASwitch" value="true">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="changeOptionB">Možnosť B:</label>
                            <div class="row">
                                <div class="col-9">
                                    <input type="text" class="form-control" id="changeOptionB" name="optionB"
                                           oninput="isValidInput(this)">
                                </div>
                                <div class="col-1" style=" align-items: center;">
                                    <label class="switch">
                                        <input type="checkbox" id="changeOptionBSwitch" name="optionBSwitch" value="true">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="changeOptionC">Možnosť C:</label>
                            <div class="row">
                                <div class="col-9">
                                    <input type="text" class="form-control" id="changeOptionC" name="optionC"
                                           oninput="isValidInput(this)">
                                </div>
                                <div class="col-1" style=" align-items: center;">
                                    <label class="switch">
                                        <input type="checkbox" id="changeOptionCSwitch" name="optionCSwitch" value="true">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvoriť okno</button>
                        <button type="submit" class="btn btn-primary">Uložiť modifikácie</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="archiveQuestionModal" tabindex="-1" role="dialog" aria-labelledby="archiveQuestionModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archiveQuestionModalLabel">Archivovať odpovede</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="filter-bar">
                    <div class="form-group">
                        <label for="filterDate">Filtrovanie podľa času:</label>
                        <select id="filterDate" class="form-control">
                        </select>
                    </div>
                </div>
                <table id="archiveTable" >
                    <thead>
                    <tr>
                        <th>Odpoveď</th>
                        <th>Počet</th>
                        <th>Čas</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal" onclick="downloadAnswers()">Exportovať ako .json</button>
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
                    $('#filterCategory').empty().append('<option value="">Všetky kategórie</option>');
                    data.categories.forEach(function (category) {
                        $('#filterCategory').append($('<option>').text(category).val(category));
                    });
                } else {
                    toastr.error(data.message || 'Chyba pri načítaní kategórií.');
                }
            },
            error: function () {
                toastr.error('Chyba pri načítaní kategórií.');
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
                    $('#filterTime').empty().append('<option value="">Všetky dátumy</option>');
                    data.created_dates.forEach(function (date) {
                        $('#filterTime').append($('<option>').text(date).val(date));
                    });
                } else {
                    toastr.error(data.message || 'Chyba pri načítavaní dátumu.');
                }
            },
            error: function () {
                toastr.error('Chyba pri načítavaní dátumu.');
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
                        {data: 'question', title: 'Otázka'},
                        {data: 'category', title: 'Kategória',visible: false},
                        {data: 'time', title: 'Dátum',visible: false},
                        {
                            data: 'id',
                            title: 'Modifikácia',
                            render: function (data, type, row) {
                                return `<button onclick="changeQuestion('${row.id}','${row.type}')" class="btn btn-primary btn-sm" style="min-width: 80%;">Modifikovať</button>`;
                            },
                            orderable: false
                        },
                        {
                            data: 'id',
                            title: 'Zmazanie',
                            render: function (data, type, row) {
                                return `<button onclick="deleteQuestion('${row.id}','${row.type}')" class="btn btn-danger btn-sm" style="min-width: 80%;">Zmazať</button>`;
                            },
                            orderable: false
                        },
                        {
                            data: 'id',
                            title: 'Aktivovanie',
                            render: function (data, type, row) {
                                return `<button onclick="activateQuestion('${row.id}','${row.type}')" class="btn btn-${row.active === 'true' ? 'success' : 'secondary'} btn-sm" style="min-width: 80%;">${row.active === 'true' ? 'Aktivovaná' : 'Deaktivovaná'}</button>`;
                            },
                            orderable: false
                        },
                        {
                            data: 'id',
                            title: 'QR Kód',
                            render: function (data, type, row) {
                                return `<button onclick="generateQRCode('${row.qr_code}', '${row.type}')" class="btn btn-primary btn-sm" style="min-width: 80%;">QR Kód</button>`;
                            },
                            orderable: false
                        },
                        {
                            data: 'id',
                            title: 'Archivovanie',
                            render: function (data, type, row) {
                                return `<button onclick="showArchiveQuestion('${row.id}','${row.type}')" class="btn btn-warning btn-sm" style="min-width: 80%;">Archivovať</button>`;
                            },
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
                toastr.error('Nepodarilo sa načítať otázky. Skúste to prosím znova.');
            }
        });
    }

    function generateQRCode(qrCode) {
        let fullUrl = `https://node84.webte.fei.stuba.sk:1000/question.php?key=${qrCode}`;
        Swal.fire({
            title: 'Kód izby: ' + qrCode,
            text: 'Naskenujte QR kód, aby ste si mohli zobraziť otázku',
            imageUrl: `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${fullUrl}`,
            imageWidth: 150,
            imageHeight: 150,
            imageAlt: 'QR Kód',
            showCloseButton: true,
            showConfirmButton: false
        });
    }

    function checkAnswers(answer) {
        if (answer.includes('A')) {
            $('#changeOptionASwitch').prop('checked', true);
        } else {
            $('#changeOptionASwitch').prop('checked', false);
        }
        if (answer.includes('B')) {
            $('#changeOptionBSwitch').prop('checked', true);
        } else {
            $('#changeOptionBSwitch').prop('checked', false);
        }
        if (answer.includes('C')) {
            $('#changeOptionCSwitch').prop('checked', true);
        } else {
            $('#changeOptionCSwitch').prop('checked', false);
        }
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
                            text: 'Viaceré možnosti'
                        })).val('2');
                        $('#changeMultipleChoiceOptions').show();
                        $('#changeOptionA').val(data.question.options.a);
                        $('#changeOptionB').val(data.question.options.b);
                        $('#changeOptionC').val(data.question.options.c);
                        checkAnswers(data.question.answer);
                    } else {
                        $('#changeQuestionType').empty()
                            .append($('<option>', {
                                value: '1',
                                text: 'One answer'
                        })).val('1');
                        $('#changeMultipleChoiceOptions').hide();
                        $('#changeOptionA').val('');
                        $('#changeOptionB').val('');
                        $('#changeOptionC').val('');
                    }

                    $('#changeQuestionModal').modal('show');
                } else {
                    toastr.error(data.message || 'Chyba pri načítavaní podrobností o otázke.');
                }
            },
            error: function () {
                toastr.error('Chyba pri načítavaní podrobností o otázke.');
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
            delete formData.optionASwitch;
            delete formData.optionBSwitch;
            delete formData.optionCSwitch;
        }else{
            formData['answer'] = '';
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
                    toastr.success('Otázka bola úspešne aktualizovaná!');
                    $('#questionsTable').DataTable().clear().destroy();
                    loadCategories();
                    loadTime();
                    clearChangeModal();
                    fetchQuestions();
                } else {
                    toastr.error(data.message || 'Chyba pri aktualizácii otázky.');
                }
            },
            error: function () {
                toastr.error('Chyba pri aktualizácii otázky.');
            }
        });
    });

    function deleteQuestion(id, type) {
        Swal.fire({
            title: 'Ste si istí?',
            text: "Toto už nebudete môcť vrátiť späť!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Áno, vymažte ho!',
            cancelButtonText:  'Vrátiť späť'
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
                            toastr.success('Otázka bola úspešne vymazaná!');
                        } else {
                            toastr.error(data.message || 'Chyba pri odstraňovaní otázky.');
                        }
                    },
                    error: function () {
                        toastr.error('Chyba pri odstraňovaní otázky.');
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
                        toastr.success('Otázka bola úspešne aktivovaná!');
                    } else {
                        toastr.success('Otázka bola úspešne deaktivovaná!');
                    }
                } else {
                    toastr.error(data.message || 'Chyba pri zmene stavu otázky. Skúste to prosím znova.');
                }
            },
            error: function () {
                toastr.error('Nepodarilo sa pripojiť k serveru. Skontrolujte prosím svoje pripojenie.');
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
        $('#optionASwitch, #optionBSwitch, #optionCSwitch').prop('checked', false);
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
        $('#changeOptionASwitch, #changeOptionBSwitch, #changeOptionCSwitch').prop('checked', false);
        $('#changeMultipleChoiceOptions').hide();
    }

    function showArchiveQuestion(id, type) {
        $.ajax({
            type: 'POST',
            url: './questions/fetchArchiveQuestion.php',
            contentType: 'application/json',
            data: JSON.stringify({id: id, type: type}),
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#archiveQuestionModal').modal('show');
                    $('#filterDate').on('change', function () {
                        if ($(this).val() === 'all') {
                            $('#archiveTable').DataTable().column(2).search('').draw();
                        } else {
                            $('#archiveTable').DataTable().column(2).search(this.value).draw();
                        }
                    });
                    $('#archiveTable').DataTable().clear().destroy();
                    $('#archiveTable').DataTable({
                        data: data.questions,
                        columns: [
                            {data: 'answer', title: 'Odpoveď', orderable: false},
                            {data: 'count', title: 'Počet', orderable: false},
                            {data: 'time', title: 'Čas', visible: false, orderable: false}
                        ],
                        createdRow: function (row) {
                            $('td', row).css('text-align', 'center');
                        },
                        order : [[1, 'desc']]
                    });
                    fillArchiveDates(data.questions);
                } else {
                    toastr.error(data.message || 'Nepodarilo sa archivovať otázku. Skúste to prosím znova.');
                }
            },
            error: function () {
                toastr.error('Nepodarilo sa pripojiť k serveru. Skontrolujte prosím svoje pripojenie.');
            }
        });

    }

    function fillArchiveDates(questions) {
        let dates = [];
        questions.forEach(function (question) {
            if (!dates.includes(question.time)) {
                dates.push(question.time);
            }
        });
        $('#filterDate').empty().append('<option value="all">Všetky časy</option>');
        dates.forEach(function (date) {
            $('#filterDate').append($('<option>').text(date).val(date));
        });
    }

    function downloadAnswers(){
        // get question id
        let questionId = $('#archiveTable').DataTable().data()[0].question_id;
        let type = $('#archiveTable').DataTable().data()[0].type;
        let time = $('#filterDate').val();
        $.ajax({
            type: 'POST',
            url: './questions/downloadAnswers.php',
            contentType: 'application/json',
            data: JSON.stringify({id: questionId, type: type, time: time}),
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    const modifiedResponse = {
                        "question": data.question,
                        "answers": data.answers.map(answer => {
                            return {
                                "answer": answer.answer,
                                "count": answer.count,
                                "time": answer.time
                            };
                        })
                    };
                    let json = JSON.stringify(modifiedResponse);
                    let blob = new Blob([json], {type: 'application/json'});
                    let url = URL.createObjectURL(blob);
                    let a = document.createElement('a');
                    a.href = url;
                    let time = data.answers[0].time === data.answers[data.answers.length - 1].time ? data.answers[0].time : 'all';
                    a.download = 'answers_' + time + '.json';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    toastr.success('Odpovede boli úspešne stiahnuté!');
                } else {
                    toastr.error(data.message || 'Chyba pri sťahovaní odpovedí. Skúste to prosím znova.');
                }
            },
            error: function () {
                toastr.error('Nepodarilo sa pripojiť k serveru. Skontrolujte prosím svoje pripojenie.');
            }
        });
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
                delete formData.optionASwitch;
                delete formData.optionBSwitch;
                delete formData.optionCSwitch;
            }
            else{
                formData['answer'] = '';
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
            }

            $.ajax({
                type: 'POST',
                url: './questions/addQuestion.php',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        toastr.success('Otázka bola úspešne pridaná!');
                        $('#addQuestionModal').modal('hide');
                        $('#questionsTable').DataTable().clear().destroy();
                        clearAddModal();
                        loadCategories();
                        loadTime();
                        fetchQuestions();
                    } else {
                        toastr.error(data.message || 'Chyba pri pridaní otázky. Skúste to prosím znova.');
                    }
                },
                error: function () {
                    toastr.error('Nepodarilo sa pripojiť k serveru. Skontrolujte prosím svoje pripojenie.');
                }
            });
        });

        fetchQuestions();
    });
</script>
</body>
</html>