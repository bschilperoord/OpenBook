<?php

include 'header.php';

// Establish the database connection
$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");

?>

<!DOCTYPE html>
<html>
<head>
    <?php if($theme == 'light'){?>
    <link rel="stylesheet" href="style.css">
    <?php } else { ?>
    <link rel="stylesheet" href="styledark.css">
    <?php } ?>
    <title>Settings</title>
</head>
<body>
    <?php if($theme == 'light'){?>
    <a id="themeswitcher" href="switchtheme.php?theme=dark">Switch theme</a>
    <?php } else {?>
    <a id="themeswitcher" href="switchtheme.php?theme=light">Switch theme</a>
    <?php } ?>
    <div id="header">
        <div id="settingsdiv">
            <h1>Settings</h1>
            <form method="POST" action="savesettings.php">
                <!-- Add your settings fields here -->
                <label for="name">Username:</label>
                <input type="text" name="name" id="name" value="">
                <br>
                <label id="passwordlabel" for="name">Password:</label>
                <input type="text" name="password" id="password" value="">

                <!-- Add more settings fields as needed -->

                <button type="submit">Save</button>
            </form>

            <?php
            require 'vendor/autoload.php';

            use RobThree\Auth\TwoFactorAuth;
            use RobThree\Auth\Providers\Qr\BaconQrCodeUrl;

            // Create a new instance of the TwoFactorAuth class
            $tfa = new TwoFactorAuth('Your App Name');

            // Generate a random secret key
            $secretKey = $tfa->createSecret();

            // Store the secret key securely in the user's account

            // Get the QR code URL for the secret key
            $qrCodeUrl = $tfa->getQRCodeImageAsDataUri('Your App Name', $secretKey);

            // Display the QR code image
            echo '<img src="'.$qrCodeUrl.'" alt="QR Code">';
            ?>


        </div>
    </div>
    <?php
    include 'footer.php';
    ?>
</body>
</html>
