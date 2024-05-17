<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../checkType.php';
check(['0']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Users</title>
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
                    <a class="nav-link" href="users.php">Users</a>
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
<div class="cont" style="width: 80%; margin: 60px auto;">
    <table id="userTable" class="display nowrap" style="width:100%">
        <thead>
        <tr>
            <th>Username</th>
            <th>Change</th>
            <th>Delete</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="d-flex justify-content-center align-items-center">
        <div style="width: 80%;">
            <button class="btn btn-primary mt-3 btn-block" data-toggle="modal" data-target="#addUserModal">Add
                User
            </button>
        </div>
    </div>
</div>
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addQuestionForm">
                    <div class="form-group">
                        <label for="userType">Select user type:</label>
                        <select class="form-control" id="addUserTypeModal" name="addUserTypeModal" required>
                            <option value="1" selected>Admin</option>
                            <option value="2">Pro User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required
                               oninput="isValidInput(this)">
                    </div>
                    <div class="form-group">
                        <label for="userPassword">Password</label>
                        <input type="password" class="form-control" id="userPassword" name="userPassword"
                               autocomplete="off" required oninput="isValidPassword(this)">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save User</button>
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
                <h5 class="modal-title" id="changeUserModalLabel">Change User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="changeUserForm">
                    <input type="hidden" id="changeUserID" name="userId">
                    <div class="form-group">
                        <label for="changeQuestionType">Select user type:</label>
                        <select class="form-control" id="changeQuestionType" name="questionType" required>
                            <option value="0">Admin</option>
                            <option value="1">Pro User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="changeUsername">Username</label>
                        <input type="text" class="form-control" id="changeUsername" name="username" required
                               oninput="isValidInput(this)">
                    </div>
                    <div class="form-group">
                        <label for="changeQuestionCategory">Password</label>
                        <input type="password" class="form-control" id="changeUserPassword" name="changeUserPassword"
                               autocomplete="off" placeholder="*******" oninput="isValidPassword(this)">
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
                            title: 'Change',
                            render: function (data, type, row) {
                                return `<button onclick="changeUser('${row.id}')" class="btn btn-primary btn-sm" style="min-width: 80%;">Change</button>`;
                            },
                            orderable: false
                        },
                        {
                            data: 'id',
                            title: 'Delete',
                            render: function (data, type, row) {
                                return `<button onclick="deleteUser('${row.id}')" class="btn btn-danger btn-sm" style="min-width: 80%;">Delete</button>`;
                            },
                            orderable: false
                        }
                    ]
                });
            },
            error: function () {
                toastr.error('Failed to load questions. Please try again.');
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
                    toastr.success('User added successfully.');
                } else {
                    toastr.error(data.message);
                }
            },
            error: function () {
                toastr.error('Failed to add the user. Please try again.');
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
                    toastr.error('Failed to load the user. Please try again.');
                }
            },
            error: function () {
                toastr.error('Failed to load the user. Please try again.');
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
                    toastr.success('User changed successfully.');
                } else {
                    toastr.error(data.message);
                }
            },
            error: function () {
                toastr.error('Failed to change the user. Please try again.');
            }
        });
    });

    function deleteUser(id, type) {
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
                    url: './users/deleteUser.php',
                    contentType: 'application/json',
                    data: JSON.stringify({id: id, type: type}),
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            $('#userTable').DataTable().clear().destroy();
                            fetchUsers();
                            toastr.success('User deleted successfully.');
                        } else {
                            toastr.error('Failed to delete the question. Please try again.');
                        }
                    },
                    error: function () {
                        toastr.error('Failed to delete the question. Please try again.');
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