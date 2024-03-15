<?php

include 'header.php';

?>

<!DOCTYPE html>
<html>
<head>
    <?php if($theme == 'light'){?>
    <link rel="stylesheet" href="style.css">
    <?php } else { ?>
    <link rel="stylesheet" href="styledark.css">
    <?php } ?>
    <title>Private Messaging System</title>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="blogpost.js"></script>
    <script type="text/javascript">
                
    </script>
</head>
<body>
    <?php if($theme == 'light'){?>
    <a id="themeswitcher" href="switchtheme.php?theme=dark">Switch theme</a>
    <?php } else {?>
    <a id="themeswitcher" href="switchtheme.php?theme=light">Switch theme</a>
    <?php } ?>
    <div id="header">
    </div>

    <div id="body">
        <?php
            // Create a database connection
            $conn = new mysqli($servernamesql, $usernamesql, $passwordsql, $databasesql);

            // Check the connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Get the token from the URL
            $token = trim($_GET["token"]);

            // Verify the token and update user account
            $sql = "UPDATE users SET is_confirmed = 1 WHERE confirmation_token = '$token'";

            if ($conn->query($sql) === TRUE) {
                echo "Email confirmation successful. You can now log in.";
                echo '<a href="index.php">Go to OpenBook</a>';
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            $conn->close();
        ?>
    </div>

    <?php
    include 'footer.php';
    ?>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>