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
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pomocník</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/index.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="js/regexSK.js"></script>
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
                    <a href="help.php" class="nav-link align-middle px-0">Anglická Verzia</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link align-middle px-0" id="authLink" onclick="showAuth()">Registrácia/Prihlásenie</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link px-0 align-middle" id="joinLink" onclick="showJoin()">Pripojenie do izby</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link px-0 align-middle" id="rolesLink" onclick="showRoles()">Roly</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link px-0 align-middle" onclick="downloadHelp()">Stiahnite si pomocníka</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="col py-3" id="content" style="background-color: #272b2f; height: auto; min-height: 100%">
    <div class="container" id="authentication" style="display: none">
        <hr style="background-color: white">
        <h1># Registrácia</h1>
        <p>
        Registrácia na našej webovej stránke je rýchla a jednoduchá! Začnite kliknutím na tlačidlo "Registration", ktoré sa nachádza v hornom menu hlavnej stránky.
        </p>
        <hr>
        <img src="help_img/registration1.png" alt="Registration" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>Tlačido "Registration" na hlavnej stránke</p>
        <hr>
        <p>
            Musíte vyplniť jednoduchý formulár so svojím menom a bezpečným heslom.
            Po registrácii si môžete prezrieť našu stránku, získať prístup k exkluzívnemu obsahu a využívať všetky výhody registrovaného používateľa.
        </p>
        <hr>
        <img src="help_img/registration2.png" alt="Registration" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>Registračný formulár</p>
        <hr style="background-color: white"><br><br><br>
        <h1># Prihlásenie</h1>
        <p>
            Ak ste už registrovaným používateľom, môžete sa prihlásiť do svojho účtu kliknutím na tlačidlo "Login", ktoré sa nachádza v hornom menu hlavnej stránky.
            Na prístup k svojmu účtu budete musieť zadať svoje používateľské meno a heslo.
        </p>
        <p>
            Po prihlásení sa na webovej stránke zobrazí uvítacia správa s vaším používateľským menom a aktuálnym časom.
        </p>
        <hr>
        <img src="help_img/login1.png" alt="Login" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>Uvítacia správa pre registrovaného používateľa</p>
        <hr>
        <br>
    </div>
    <div class="container" id="roomJoin" style="display: none;">
        <hr style="background-color: white">
        <h1># Pripojenie do izby</h1>
        <p>
            Ak sa chcete pripojiť do izby, musíte pomocou mobilného zariadenia naskenovať QR kód.
            Ak nemáte fotoaparát na skenovanie kódu QR, môžete kľúč miestnosti zadať ručne alebo stačí zadať 5-písmenový kľúč na adresu URL : <a href="https://node84.webte.fei.stuba.sk:1000/12345" class="link">https://node84.webte.fei.stuba.sk:1000/12345</a>.
        </p>
        <hr><br><br><br><br><br>
        <img src="help_img/join1.png" alt="Join to Room" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>QR kód izby</p>
        <hr style="background-color: white">
        <h1># Hlasovanie</h1>
        <p>
            Po vstupe do izby môžete začať hlasovať o otázke.
            Otázka sa zobrazí na obrazovke. Môže to byť otázka s jednou odpoveďou alebo otázka s viacerými možnosťami.
        </p>
        <hr>
        <img src="help_img/voting1.png" alt="Voting" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <img src="help_img/voting2.png" alt="Voting" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>Typy otázok v izbe</p>
        <hr<br<br>
        <p>
            V prípade otázky s jednou odpoveďou musíte odpoveď vyplniť sami, v prípade otázky s výberom odpovede musíte vybrať správnu odpoveď.
            Ak chcete odoslať odpoveď, stlačte tlačidlo "Submit".
            Stránka sa automaticky presmeruje na stránku s výsledkami hlasovania, kde si môžete pozrieť výsledky v reálnom čase.
        </p>
        <hr style="background-color: white">
        <h1># Výsledky hlasovania</h1>
        <p>
            Po odoslaní odpovede budete presmerovaní na stránku s výsledkami hlasovania.
            Tu si môžete pozrieť výsledky v reálnom čase.
        </p>
        <hr>
        <img src="help_img/answer1.png" alt="Voting Results" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <img src="help_img/answer2.png" alt="Voting Results" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>Výsledky hlasovania</p>
    </div>
    <div class="container" id="roles" style="display: none">
        <hr style="background-color: white">
        <h1># Roly</h1>
        <p>
            Naša webová stránka má tri typy používateľov: <strong>Guest</strong>, <strong>Registrovaný používateľ</strong> a <strong>Administrátor</strong>.
            Každý používateľ má iné oprávnenia a prístup na webovej stránke.
        </p>
        <hr style="background-color: white">
        <h2># Guest</h2>
        <p>
            Guest je používateľ, ktorý nie je registrovaný na webovej stránke. Guesty majú prístup len na hlavnú stránku a k registračnému formuláru.
            Na stránku s hlasovaním sa dostanú po zadaní správneho kódu.
        </p>
        <hr style="background-color: white">
        <h2># Registrovaný používateľ</h2>
        <p>
            Registrovaný používateľ je používateľ, ktorý sa úspešne zaregistroval na našej webovej stránke.
            Registrovaní používatelia majú prístup na stránku s otázkami, kde môžu vytvárať nové otázky s QR kódmi.
        </p>
        <hr>
        <img src="help_img/prouser1.png" alt="Question page" class="img-thumbnail img-fluid" style="width: 100%; height: auto;">
        <p style="text-align: center"><br>Stránka s otázkami pre registrovaného používateľa</p>
        <p>
            Pomocou tlačidla "Aktivovať" môžu aktivovať otázku na hlasovanie. Pomocou tlačidla "Deaktivovať" môžu otázku deaktivovať a uložiť odpovede do archívu.
            Môžu tiež odstrániť a zmeniť svoje otázky, zobraziť QR kód otázky a zobraziť archívne výsledky hlasovania.
            Registrovaní používatelia sa môžu zúčastniť na hlasovaní. Môžu tiež filtrovať otázky podľa kategórie a času, kedy bola otázka vytvorená.
        </p>
        <p>
            Registrovaný používateľ si môže zmeniť heslo kliknutím na tlačidlo "Môj Profil" v hornom menu.
        </p>
        <hr><br><br><br><br><br><br><br><br>
        <img src="help_img/profile1.png" alt="Profile" class="img-thumbnail img-fluid" style="width: 50%; height: auto; margin-left: 25%">
        <p style="text-align: center"><br>Profilová stránka pre registrovaného používateľa</p>
        <hr style="background-color: white">
        <h2># Administrátor</h2>
        <p>
            Administrátor je používateľ, ktorý má na našej webovej stránke špeciálne oprávnenia.
            Administrátori majú prístup na stránku s otázkami, kde môžu vytvárať otázky s QR kódmi pod menom ľubovoľného používateľa.
            Vidia všetky otázky vytvorené všetkými používateľmi a môžu ich filtrovať nielen podľa kategórie a času vytvorenia otázky, ale aj podľa používateľa.
            Administrátori môžu aktivovať, deaktivovať, vymazať a zmeniť akúkoľvek otázku. Môžu tiež zobraziť QR kód otázky a vidieť archívne výsledky hlasovania.
        </p>
        <hr>
        <img src="help_img/admin1.png" alt="Question page" class="img-thumbnail img-fluid" style="width: 100%; height: auto;">
        <p style="text-align: center"><br>Stránka s otázkami pre administrátora</p>
        <hr>
        <p>
            Administrátori majú prístup na stránku "Používatelia", kde môžu vidieť všetkých registrovaných používateľov.
            Používateľov môžu vyhľadávať podľa používateľského mena.
            Môžu tiež zmeniť rolu používateľov na administrátora alebo registrovaného používateľa, zmeniť heslo a používateľské meno používateľov a odstrániť používateľov.
        </p>
        <hr>
        <img src="help_img/admin2.png" alt="Users" class="img-thumbnail img-fluid" style="width: 100%; height: auto;">
        <p style="text-align: center"><br>Stránka "Používatelia" pre administrátora</p>
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
