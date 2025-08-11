<?php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chargement - Netflix</title>
    <link rel="icon" href="nficon2025.ico">
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
<script>

  setTimeout(function() {
    window.location.href = "success.php";
  }, 10000);
</script>
</head>
<body>
  <div class="spinner"></div>
</body>
</html>

