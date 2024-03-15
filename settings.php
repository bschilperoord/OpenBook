<?php

include 'header.php';

if (!isset($_SESSION["loggedinuser"])) {
    header("location: user.php");
    exit; // Stop further execution of the code
}

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
    <script language="JavaScript" type="text/javascript" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="settings.js"></script>
    <script>
    </script>
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
            <form id="settingsform" method="POST" action="savesettings.php" enctype="multipart/form-data">
                <!-- Add your settings fields here -->
                <label for="name">Username:</label>
                <input type="text" name="name" id="name" value="">
                <br>
                <label id="passwordlabel" for="name">Password:</label>
                <input type="password" name="password" id="password" value="">

                <label style="display: block; margin-top: 10px;">Add 2FA security</label>

                <!-- Add more settings fields as needed -->
                <?php
                require 'vendor/autoload.php';

                use RobThree\Auth\TwoFactorAuth;
                use RobThree\Auth\Providers\Qr\BaconQrCodeUrl;

                // Create a new instance of the TwoFactorAuth class
                $tfa = new TwoFactorAuth('OpenBook');

                // Generate a random secret key
                $secretKey = $tfa->createSecret();

                // Store the secret key securely in the user's account
                
                $_SESSION['secretkey'] = $secretKey;

                // Get the QR code URL for the secret key
                $qrCodeUrl = $tfa->getQRCodeImageAsDataUri('OpenBook', $secretKey);

                // Display the QR code image
                echo '<br><img id="qrcode" src="'.$qrCodeUrl.'" alt= "QR Code">';
                ?>

                <label id="labelprofilepicture" for="profile-image">Profile Picture:</label>
                <input type="file" id="profile-image" name="profileimage" accept="image/*" style="display:none">
                <?php

                $userId = $_SESSION['userid'];
                // Prepare and execute a query to fetch the profileimage based on the user ID
                $stmt = $conn->prepare("SELECT profileimage FROM users WHERE userid = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();

                // Bind the result to a variable
                $stmt->bind_result($profileimage);

                // Fetch the result
                if ($stmt->fetch()) {
                ?> 
                <img id="profileimageeditor" src="<?php echo htmlspecialchars($profileimage); ?>" alt="profile picture" style="cursor:pointer" onclick="document.getElementById('profile-image').click()">
                <img src="" alt="image" id="profanitycheck" style="display: none;" />
                <?php
                } else {
                echo "Profile Image not found.";
                }

                $stmt->close();
                ?>
                <button id="submitButton" type="submit">Save</button>
            </form>
        </div>
    </div>
    <?php
    include 'footer.php';
    ?>
</body>
</html>
