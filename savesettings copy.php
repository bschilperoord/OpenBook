<?php
// save_settings.php

session_start();

// Retrieve the submitted form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$profileimage = isset($_FILES['profile-image']) ? $_FILES['profile-image'] : null;
$trimmedpassword = trim($password);
$hashedPassword = hash("sha512", $trimmedpassword);

$twoFactorCode = isset($_SESSION['secretkey']) ? $_SESSION['secretkey'] : '';

// Perform validation and processing as needed
$errors = array();

// Validate name
if (empty($name)) {
    $errors[] = "Name is required.";
}

// Validate password
if (empty($password)) {
    $errors[] = "Password is required.";
}

// Validate 2FA code
if (empty($twoFactorCode)) {
    $errors[] = "Two-Factor Authentication code is required.";
}

// If there are validation errors, display them and redirect back to the settings page
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo htmlspecialchars($error) . "<br>"; // Sanitize error message
    }
    echo '<a href="settings.php">Go back</a>';
    exit;
}

// Save the settings (e.g., update database or configuration file)
// Modify this part to fit your specific needs

// For example, if you want to update a database record using MySQLi:
// 1. Establish a database connection
// 2. Prepare and execute the update queries

// Assuming you have a MySQL database, here's an example:

include 'header.php';

// 1. Establish a database connection using MySQLi
$conn = new mysqli($servernamesql, $usernamesql, $passwordsql, $databasesql);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Prepare and execute the update queries
$stmtUsername = $conn->prepare("UPDATE users SET username = ? WHERE userid = ?");
$stmtUsername->bind_param("si", $name, $_SESSION['userid']);

$stmtPassword = $conn->prepare("UPDATE users SET password = ? WHERE userid = ?");
$stmtPassword->bind_param("si", $hashedPassword, $_SESSION['userid']);

$stmtTwoFactor = $conn->prepare("UPDATE users SET `2FA` = ? WHERE userid = ?");
$stmtTwoFactor->bind_param("si", $twoFactorCode, $_SESSION['userid']);

// Update the profile image if it was uploaded
if ($profileimage !== null && $profileimage['error'] === UPLOAD_ERR_OK) {
    $profileImageName = $profileimage['name'];
    $profileImageTmp = $profileimage['tmp_name'];
    $profileImageDestination = 'profile_images/' . $profileImageName;
    if (move_uploaded_file($profileImageTmp, $profileImageDestination)) {
        $_SESSION['profileimage'] = $profileImageDestination;
        $stmtProfileImage = $conn->prepare("UPDATE users SET profile_image = ? WHERE userid = ?");
        $stmtProfileImage->bind_param("si", $profileImageDestination, $_SESSION['userid']);
        $updateProfileImage = $stmtProfileImage->execute();
        $stmtProfileImage->close();
    } else {
        $errors[] = "Error moving profile image. Please try again.";
    }
}

$updateUsername = $stmtUsername->execute();
$updatePassword = $stmtPassword->execute();
$updateTwoFactor = $stmtTwoFactor->execute();

if ($updateUsername && $updatePassword && $updateTwoFactor && (!$profileimage || $updateProfileImage)) {
    echo "Settings saved successfully!";
} else {
    echo "Error: Unable to save settings.";
}

$stmtUsername->close();
$stmtPassword->close();
$stmtTwoFactor->close();
$conn->close();

// Redirect back to the settings page after a delay
header("refresh:2; url=settings.php");
?>