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
        header('Location: index.php');
        exit();
    }

    $_SESSION['key'] = (int)$key;
    $link = 'question.php?key=' . $key;
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
    <title>Help</title>
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
    <script src="https://raw.githack.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>
</head>
<style>
    p {
        font-size: 1.5rem;
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
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav" style="width: 100%; justify-content: space-evenly">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Menu</a>
                </li>
                <li class="nav-item">
                    <a href="helpSK.php" class="nav-link align-middle px-0">Slovak Version</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link align-middle px-0" id="authLink" onclick="showAuth()">Registration/Login</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link px-0 align-middle" id="joinLink" onclick="showJoin()">Join to Room</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link px-0 align-middle" id="rolesLink" onclick="showRoles()">Roles</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link px-0 align-middle" onclick="downloadHelp()">Download Help</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="col py-3" id="content" style="background-color: #272b2f; height: auto; min-height: 100%">
    <div class="container" id="authentication" style="display: none">
        <hr style="background-color: white">
        <h1># Registration</h1>
        <p>
            Registration on our website is quick and easy! Start by clicking on the "Registration" button located in the top menu of the main page.
        </p>
        <hr>
        <img src="help_img/registration1.png" alt="Registration" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>Registration button on main page</p>
        <hr>
        <p>
            You will need to fill in a simple form with your name and a secure password.
            Once you're registered, you can explore our site, access exclusive content and enjoy all the benefits of being a registered member.
        </p>
        <hr>
        <img src="help_img/registration2.png" alt="Registration" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>Registration form</p>
        <hr style="background-color: white"><br><br><br>
        <h1># Login</h1>
        <p>
            If you are already a registered user, you can log in to your account by clicking on the "Login" button located in the top menu of the main page.
            You will need to enter your username and password to access your account.
        </p>
        <p>
            Once you're logged in, the website will show you a welcome message with your username and the current time.
        </p>
        <hr>
        <img src="help_img/login1.png" alt="Login" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>Welcome message for registered user</p>
        <hr>
        <br>
    </div>
    <div class="container" id="roomJoin" style="display: none;">
        <hr style="background-color: white">
        <h1># Join to Room</h1>
        <p>
            To join a room, you need to scan the QR code with your mobile device.
            If you don't have a camera to scan the QR code, you can enter the room key manually, or just type the 5 letter key to the URL : <a href="https://node84.webte.fei.stuba.sk:1000/12345" class="link">https://node84.webte.fei.stuba.sk:1000/12345</a>.
        </p>
        <hr><br><br><br><br><br>
        <img src="help_img/join1.png" alt="Join to Room" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>QR code for room</p>
        <hr style="background-color: white">
        <h1># Voting</h1>
        <p>
            Once you have joined the room, you can start voting on the question.
            The question will appear on the screen. It can be a self-answer question or a multiple-choice question.
        </p>
        <hr>
        <img src="help_img/voting1.png" alt="Voting" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <img src="help_img/voting2.png" alt="Voting" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>Question types for voting</p>
        <hr<br<br>
        <p>
            For a self-answer question you have to fill in the answer yourself, for a multiple choice question you have to choose the correct answer.
            To submit the answer, press the "Submit" button.
            The page will automatically redirect to the voting results page where you can see the results in real time.
        </p>
        <hr style="background-color: white">
        <h1># Voting Results</h1>
        <p>
            After submitting the answer, you will be redirected to the voting results page.
            Here you can see the results of the vote in real time.
        </p>
        <hr>
        <img src="help_img/answer1.png" alt="Voting Results" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <img src="help_img/answer2.png" alt="Voting Results" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>Voting results</p>
    </div>
    <div class="container" id="roles" style="display: none">
        <hr style="background-color: white">
        <h1># Roles</h1>
        <p>
            Our website has three types of users: <strong>Guest</strong>, <strong>Registered User</strong> and <strong>Admin</strong>.
            Every user has different permissions and access to the website.
        </p>
        <hr style="background-color: white">
        <h2># Guest</h2>
        <p>
            A guest is a user who is not registered on our website. Guests can only access the main page and the registration form.
            They can access the voting page with the correct code.
        </p>
        <hr style="background-color: white">
        <h2># Registered User</h2>
        <p>
            A registered user is a user who has successfully registered on our website.
            Registered users can access the question page where they can create questions with QR codes.
        </p>
        <hr>
        <img src="help_img/prouser1.png" alt="Question page" class="img-thumbnail img-fluid" style="width: 100%; height: auto;">
        <p style="text-align: center"><br>Question page for registered user</p>
        <p>
            With the Activate button, they can activate the question for voting. With the Deactivate button, they can deactivate the question and save the answers to archive.
            They can also Delete and Change their questions, show the QR code of the question and see the archive results of the voting.
            Registered users can participate in voting. They can also filter the questions by the Category and the Time when the question was created.
        </p>
        <p>
            A registered user can change their password by clicking on the "Profile" button in the top menu.
        </p>
        <hr><br><br><br><br><br><br><br><br>
        <img src="help_img/profile1.png" alt="Profile" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>Profile page for registered user</p>
        <hr style="background-color: white">
        <h2># Admin</h2>
        <p>
            An admin is a user who has special permissions on our website
            Admins can access the question page where they can create questions with QR codes under any users name.
            They see all the questions created by all users and can filter them by the Category and the Time when the question was created and by the User.
            Admins can activate, deactivate, delete and change any question. They can also show the QR code of the question and see the archive results of the voting.
        </p>
        <hr>
        <img src="help_img/admin1.png" alt="Question page" class="img-thumbnail img-fluid" style="width: 100%; height: auto;">
        <p style="text-align: center"><br>Question page for admin</p>
        <hr>
        <p>
            Admins can access the Users page where they can see all the registered users.
            They can search the users by the Username.
            They can also change the role of the users to Admin or Registered User, change the password and username of the users and delete the users.
        </p>
        <hr>
        <img src="help_img/admin2.png" alt="Users" class="img-thumbnail img-fluid" style="width: 100%; height: auto;">
        <p style="text-align: center"><br>Users page for admin</p>
        <hr>
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
    function hideEverything() {
        document.getElementById('authentication').style.display = 'none';
        document.getElementById('roomJoin').style.display = 'none';
        document.getElementById('roles').style.display = 'none';
        document.getElementById('authLink').style.color = 'rgba(255, 255, 255, 0.5)';
        document.getElementById('joinLink').style.color = 'rgba(255, 255, 255, 0.5)';
        document.getElementById('rolesLink').style.color = 'rgba(255, 255, 255, 0.5)';
    }

    function showEverything() {
        document.getElementById('authentication').style.display = 'block';
        document.getElementById('roomJoin').style.display = 'block';
        document.getElementById('roles').style.display = 'block';
    }

    function showAuth() {
        hideEverything();
        document.getElementById('authentication').style.display = 'block';
        document.getElementById('authLink').style.color = 'rgb(255,232,130)';
    }

    function showJoin() {
        hideEverything();
        document.getElementById('roomJoin').style.display = 'block';
        document.getElementById('joinLink').style.color = 'rgb(255,232,130)';
    }

    function showRoles() {
        hideEverything();
        document.getElementById('roles').style.display = 'block';
        document.getElementById('rolesLink').style.color = 'rgb(255,232,130)';
    }

    function downloadHelp() {
        showEverything();
        const element = document.getElementById('content');
        html2pdf()
            .from(element)
            .save()
            .then(() => {
                location.reload();
            });
    }

    showAuth();
</script>
</body>
</html>
