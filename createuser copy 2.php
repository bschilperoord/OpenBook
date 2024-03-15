<?php
session_start();
include 'header.php';

require_once 'vendor/autoload.php';
use RobThree\Auth\TwoFactorAuth;

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

    // Retrieve the user data from the database
    $queryForId = "SELECT userid, password, 2FA FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $queryForId);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        // Handle database query error
        header("Location: user.php?error=database_error");
        exit;
    }

    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        // User not found in the database
        header("Location: user.php?error=user_not_found");
        exit;
    }

    $userId = $row['userid'];
    $dbPassword = $row['password'];
    $db2fa = $row['2FA'];
    mysqli_stmt_close($stmt);

    $hashedpw = hash('sha512', "$password");

    // Verify password
    if ($hashedpw != $dbPassword) {
        header("Location: user.php?error=invalid_credentials");
        exit;
    }

    // Verify Two-Factor Authentication code
    $tfa = new TwoFactorAuth('OpenBook'); // Replace 'OpenBook' with your application name
    $isValidOTP = $tfa->verifyCode($db2fa, $twofactorauthcode);

    echo $db2fa;
    echo "<br>";
    echo $twofactorauthcode;
    echo "<br>";

    if ($isValidOTP) {
        // The one-time password is valid
        echo "Valid one-time password!";
        $_SESSION["loggedinuser"] = $username;
        $_SESSION["justcreatedauser"] = true;
        $_SESSION["userid"] = $userId;
        header("Location: index.php");
    } else {
        // The one-time password is invalid
        echo "Invalid one-time password!";
        header("Location: user.php?error=invalid_2fa_code");
    }
    
    // Close the connection
    mysqli_close($conn);

    // Redirect to the appropriate theme page
    if ($_SESSION['themevalue'] === 'light') {
        header("Location: switchtheme.php?theme=light");
        exit;
    } else if ($_SESSION['themevalue'] === 'dark') {
        header("Location: switchtheme.php?theme=dark");
        exit;
    }

} else {
    // If the form was not submitted, redirect to the index page
    header("Location: index.php");
    exit;
}
?>
