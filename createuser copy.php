<?php
session_start();
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize user input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $twofactorauthcode = trim($_POST['2facode']);

    if (empty($username) || empty($password)) {
        header("Location: user.php?error=empty_fields");
        exit;
    }

    // Create a connection
    $conn = mysqli_connect($servernamesql, $usernamesql, $passwordsql, $databasesql);

    // Check the connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare and execute the query to insert the user
    $insertQuery = "INSERT IGNORE INTO users (username, password, profileimage) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);
    $defaultProfileImage = 'profile.png';
    $hashedPassword = hash("sha512", $password);
    mysqli_stmt_bind_param($stmt, 'sss', $username, $hashedPassword, $defaultProfileImage);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Retrieve the user ID
    $queryForId = "SELECT userid, password, 2FA FROM users WHERE username = ?";
    $stmt2 = mysqli_prepare($conn, $queryForId);
    mysqli_stmt_bind_param($stmt2, 's', $username);
    mysqli_stmt_execute($stmt2);
    $result = mysqli_stmt_get_result($stmt2);
    $row = mysqli_fetch_assoc($result);
    $userId = $row['userid'];
    $dbPassword = $row['password'];
    $db2fa = $row['2FA'];
    mysqli_stmt_close($stmt2);

    require_once 'vendor/robthree/twofactorauth/lib/TwoFactorAuth.php';
    require_once 'vendor/robthree/twofactorauth/lib/Providers/Qr/IQRCodeProvider.php';
    require_once 'vendor/robthree/twofactorauth/lib/Providers/Qr/BaconQrCodeProvider.php';

    function getUserSecretKey($username)
    {
        $dev = false;

        if ($dev === true) {
            // ini_set('display_errors', 1);
            // ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            $filename = "./.env_developer";
        } else {
            $filename = "./.env";
        }

        $setting = parse_ini_file($filename);

        $servernamesql = $setting["servernamesql"] ?? null;
        $usernamesql = $setting["usernamesql"] ?? null;
        $passwordsql = $setting["passwordsql"] ?? null;
        $databasesql = $setting["databasesql"] ?? null;

        // Establish a database connection using MySQLi
        // Create a connection
        $conn2 = mysqli_connect($servernamesql, $usernamesql, $passwordsql, $databasesql);

        // Check the connection
        if ($conn2->connect_error) {
            die("Connection failed: " . $conn2->connect_error);
        }

        // Prepare and execute a query to fetch the user's secret key based on the username
        $stmt = $conn2->prepare("SELECT 2FA FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        // Retrieve the result
        $stmt->bind_result($secretKey);
        $stmt->fetch();

        // Close the statement and database connection
        $stmt->close();
        $conn2->close();

        // Return the secret key
        return $secretKey;
    }

    // Retrieve the user's secret key from the database or wherever it is stored securely
    $secretKey = getUserSecretKey($username);

    // Function to verify TOTP code using the secret key
    function verifyTOTPCode($secretKey, $twofactorauthcode)
    {
        $timeWindow = 30; // Time window in seconds for TOTP validity
        $timestamp = time();

        // Generate TOTP codes for the current and previous time windows
        $currentCode = generateTOTPCode($secretKey, $timestamp);
        $previousCode = generateTOTPCode($secretKey, $timestamp - $timeWindow);

        // Check if the entered code matches either the current or previous code
        return $twofactorauthcode === $currentCode || $twofactorauthcode === $previousCode;
    }

    // Function to generate TOTP code based on the secret key and timestamp
    function generateTOTPCode($secretKey, $timestamp)
    {
        $timeWindow = 30; // Time window in seconds for TOTP validity
        $codeLength = 6; // Length of the TOTP code

        $counter = floor($timestamp / $timeWindow);

        // Generate the HMAC-SHA1 hash using the secret key and counter
        $hash = hash_hmac('sha1', pack('J', $counter), $secretKey, true);

        // Get the offset bits from the hash
        $offset = ord(substr($hash, -1)) & 0x0F;

        // Get the 4 bytes starting from the offset
        $codeBytes = substr($hash, $offset, 4);

        // Convert the code bytes to an integer value
        $code = unpack('N', $codeBytes)[1];

        // Apply a modulo operation to get the desired code length
        $code = $code % pow(10, $codeLength);

        // Convert the code to a string with leading zeros if necessary
        $code = str_pad($code, $codeLength, '0', STR_PAD_LEFT);

        return $code;
    }

    // Verify the 2FA code
    if (!verifyTOTPCode($secretKey, $twofactorauthcode)) {
        echo "Invalid Two-Factor Authentication code.";
        echo '<a href="user.php">Go back</a>';
        exit;
    }

    // Validate the user ID and password
    if ($hashedPassword !== $dbPassword) {
        header("Location: user.php?password=incorrect");
        exit;
    }

    // Store session data
    $_SESSION["loggedinuser"] = $username;
    $_SESSION["justcreatedauser"] = true;
    $_SESSION["profileimage"] = 'profile.png';
    $_SESSION["userid"] = $userId;

    // Close the connection

    // Check if the user ID is set in the session
    if (isset($_SESSION['userid'])) {
        $userID = $_SESSION['userid'];

        // Prepare the select query
        $query = "SELECT value FROM settings WHERE name = 'theme' AND userid = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Bind the user ID to the prepared statement
            $stmt->bind_param("i", $userID);

            // Execute the query
            if ($stmt->execute()) {
                $stmt->bind_result($themeValue);

                if ($stmt->fetch()) {
                    // Return the theme value
                    echo $themeValue;
                    $_SESSION['themevalue'] = $themeValue;
                    $theme = $themeValue;
                } else {
                    echo "No theme value found for the user.";
                }
            } else {
                echo "Error executing the query: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error preparing the query: " . $conn->error;
        }
    } else {
        echo "User ID not found in session.";
    }

    $conn->close();

    // Redirect to the index page
    if($_SESSION['themevalue'] == 'light')
    {
        header("Location: switchtheme.php?theme=light");
        exit;
    } else if($_SESSION['themevalue'] == 'dark')
    {
        header("Location: switchtheme.php?theme=dark");
        exit;
    }
    else{
        header("Location: switchtheme.php?theme=light");
        exit;
    }
    
} else {
    // If the form was not submitted, redirect to the index page
    header("Location: index.php");
    exit;
}
?>
