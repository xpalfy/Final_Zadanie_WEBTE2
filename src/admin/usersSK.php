<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../checkType.php';
check(['0']);
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Používatelia</title>
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
                    <a class="nav-link" href="users.php">Anglická Verzia</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="menuSK.php">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="usersSK.php">Používatelia</a>
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
    <table id="userTable" class="display nowrap" style="width:100%">
        <thead>
        <tr>
            <th>Používateľské meno</th>
            <th>Modifikácia</th>
            <th>Zmazanie</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="d-flex justify-content-center align-items-center">
        <div style="width: 80%;">
            <button class="btn btn-primary mt-3 btn-block" data-toggle="modal" data-target="#addUserModal">
                Pridať používateľa
            </button>
        </div>
    </div>
</div>
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Pridanie používateľa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addQuestionForm">
                    <div class="form-group">
                        <label for="userType">Vyberte typ používateľa:</label>
                        <select class="form-control" id="addUserTypeModal" name="addUserTypeModal" required>
                            <option value="0" selected>Admin</option>
                            <option value="1">Pro User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="username">Používateľské meno</label>
                        <input type="text" class="form-control" id="username" name="username" required
                               oninput="isValidInput(this)">
                    </div>
                    <div class="form-group">
                        <label for="userPassword">Heslo</label>
                        <input type="password" class="form-control" id="userPassword" name="userPassword"
                               autocomplete="off" required oninput="isValidPassword(this)">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvoriť okno</button>
                        <button type="submit" class="btn btn-primary">Uložiť používateľa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="changeUserModal" tabindex="-1" role="dialog" aria-labelledby="changeUserModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeUserModalLabel">Modifikácia používateľa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="changeUserForm">
                    <input type="hidden" id="changeUserID" name="userId">
                    <div class="form-group">
                        <label for="changeQuestionType">Vyberte typ používateľa:</label>
                        <select class="form-control" id="changeQuestionType" name="questionType" required>
                            <option value="0">Admin</option>
                            <option value="1">Pro User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="changeUsername">Používateľské meno</label>
                        <input type="text" class="form-control" id="changeUsername" name="username" required
                               oninput="isValidInput(this)">
                    </div>
                    <div class="form-group">
                        <label for="changeQuestionCategory">Heslo</label>
                        <input type="password" class="form-control" id="changeUserPassword" name="changeUserPassword"
                               autocomplete="off" placeholder="*******" oninput="isValidPassword(this)">
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
<footer class="page-footer font-small bg-dark">
    <div class="container">
        <div class="text-center py-3 text-light">
            &copy; WebTech 2 - Final Zadanie
        </div>
    </div>
</footer>
<script>
    function fetchUsers() {
        $.ajax({
            url: './users/fetchUsers.php',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#userTable').DataTable({
                    responsive: true,
                    data: data.data,
                    columns: [
                        {data: 'username'},
                        {
                            data: 'id',
                            title: 'Modifikácia',
                            render: function (data, type, row) {
                                return `<button onclick="changeUser('${row.id}')" class="btn btn-primary btn-sm" style="min-width: 80%;">Modifikácia</button>`;
                            },
                            orderable: false
                        },
                        {
                            data: 'id',
                            title: 'Zmazanie',
                            render: function (data, type, row) {
                                return `<button onclick="deleteUser('${row.id}')" class="btn btn-danger btn-sm" style="min-width: 80%;">Zmazanie</button>`;
                            },
                            orderable: false
                        }
                    ]
                });
            },
            error: function () {
                toastr.error('Nepodarilo sa načítať otázky. Skúste to prosím znova.');
            }
        });
    }

    function addUser() {
        let username = $('#username').val();
        let password = $('#userPassword').val();
        let type = $('#addUserTypeModal').val();
        $.ajax({
            type: 'POST',
            url: './users/addUser.php',
            contentType: 'application/json',
            data: JSON.stringify({username: username, password: password, type: type}),
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#addUserModal').modal('hide');
                    $('#userTable').DataTable().clear().destroy();
                    clearAddModal();
                    fetchUsers();
                    toastr.success('Používateľ bol úspešne pridaný.');
                } else {
                    toastr.error(data.message);
                }
            },
            error: function () {
                toastr.error('Nepodarilo sa pridať používateľa. Skúste to prosím znova.');
            }
        });
    }

    function changeUser(id) {
        $.ajax({
            type: 'POST',
            url: './users/fetchUser.php',
            contentType: 'application/json',
            data: JSON.stringify({id: id}),
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#changeUserID').val(data.data.id);
                    $('#changeUsername').val(data.data.username);
                    $('#changeQuestionType').val(data.data.type);
                    $('#changeUserModal').modal('show');
                } else {
                    toastr.error('Nepodarilo sa načítať používateľa. Skúste to prosím znova.');
                }
            },
            error: function () {
                toastr.error('Nepodarilo sa načítať používateľa. Skúste to prosím znova.');
            }
        });
    }
    $("#changeUserForm").on('submit', function (e) {
        e.preventDefault();
        if (!checkChangeUser()) {
            return;
        }
        let id = $('#changeUserID').val();
        let username = $('#changeUsername').val();
        let password = $('#changeUserPassword').val();
        let type = $('#changeQuestionType').val();
        $.ajax({
            type: 'POST',
            url: './users/changeUser.php',
            contentType: 'application/json',
            data: JSON.stringify({id: id, username: username, password: password, type: type}),
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#changeUserModal').modal('hide');
                    $('#userTable').DataTable().clear().destroy();
                    clearChangeModal();
                    fetchUsers();
                    toastr.success('Používateľ bol úspešne modifikovaný.');
                } else {
                    toastr.error(data.message);
                }
            },
            error: function () {
                toastr.error('Nepodarilo sa zmeniť používateľa. Skúste to prosím znova.');
            }
        });
    });

    function deleteUser(id, type) {
        Swal.fire({
            title: 'Ste si istí?',
            text: "Toto sa nedá vrátiť späť!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Áno, vymažte ho!',
            cancelButtonText: 'Vrátiť späť'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: './users/deleteUser.php',
                    contentType: 'application/json',
                    data: JSON.stringify({id: id, type: type}),
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            $('#userTable').DataTable().clear().destroy();
                            fetchUsers();
                            toastr.success('Používateľ bol úspešne odstránený.');
                        } else {
                            toastr.error('Nepodarilo sa vymazať otázku. Skúste to prosím znova.');
                        }
                    },
                    error: function () {
                        toastr.error('Nepodarilo sa vymazať otázku. Skúste to prosím znova.');
                    }
                });
            }
        });
    }

    function clearAddModal() {
        removeError('username');
        removeError('userPassword');
        removeSuccess('username');
        removeSuccess('userPassword');
        $('#username').val('');
        $('#userPassword').val('');
    }
    function clearChangeModal() {
        removeError('changeUsername');
        removeError('changeUserPassword');
        removeSuccess('changeUsername');
        removeSuccess('changeUserPassword');
        $('#changeUsername').val('');
        $('#changeUserPassword').val('');
    }

    $(document).ready(function () {
        fetchUsers();

        $('#addUserModal').on('hidden.bs.modal', function () {
            clearAddModal();
        });
        $('#changeUserModal').on('hidden.bs.modal', function () {
            clearChangeModal();
        });

        $('#addUserModal').on('submit', function (e) {
            e.preventDefault();
            if (!checkAddUser()) {
                return;
            }
            addUser();
        });
    });
</script>