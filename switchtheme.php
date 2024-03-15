<?php
include 'header.php';

$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");

// Retrieve the user ID
$queryForId = "SELECT userid, password FROM users WHERE username = ?";
$stmt2 = mysqli_prepare($conn, $queryForId);
mysqli_stmt_bind_param($stmt2, 's', $username);
mysqli_stmt_execute($stmt2);
$result = mysqli_stmt_get_result($stmt2);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $userId = $row['userid'];
    $dbPassword = $row['password'];
    mysqli_stmt_close($stmt2);
    $_SESSION["userid"] = $userId;
} else {
    // Handle the case when no matching row is found

}

// Check if the setting already exists for the user
$userID = $_SESSION['userid'];
$query = "SELECT COUNT(*) FROM settings WHERE name = 'theme' AND userid = ?";
$stmt = $conn->prepare($query);

if ($stmt) {
    // Bind the user ID to the prepared statement
    $stmt->bind_param("i", $userID);

    // Execute the query
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count === 0) {
        // Prepare the insert query
        $insertQuery = "INSERT INTO settings (name, value, userid) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);

        // Set the values for the insert query
        $name = 'theme';
        $value = 'default'; // Change this to the desired default theme value

        // Bind the values to the prepared statement
        $insertStmt->bind_param("ssi", $name, $value, $userID);

        // Execute the insert query
        if ($insertStmt->execute()) {
            echo "Setting 'theme' inserted successfully for user ID: " . $userID;
            echo '<br><a href="index.php">Go back to the index page</a>';
        } else {
            echo "Error inserting setting: " . $insertStmt->error;
        }

        $insertStmt->close();
    } else {
        echo "Login successful: " . $userID;
        echo '<br><a href="index.php">Go back to the index page</a>';
    }
} else {
    echo "Error preparing query: " . $conn->error;
}

// switchtheme.php

// Assuming you have established a database connection using MySQLi
// $conn is the database connection object

// Retrieve the submitted user ID and theme value
$userID = $_SESSION['userid'];
$theme = $_GET['theme'];

// Validate the theme value (optional)
$allowedThemes = ['dark', 'light']; // Define the allowed theme values
if (!in_array($theme, $allowedThemes)) {
    exit;
}

// Prepare the update query
$query = "UPDATE settings SET value = ? WHERE name = 'theme' AND userid = ?";
$stmt = $conn->prepare($query);

// Bind the values to the prepared statement
$stmt->bind_param("si", $theme, $userID);

// Execute the query
if ($stmt->execute()) {
    echo "Theme changed successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();

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

// Check if the referring page is different from the current page
if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== $_SERVER['REQUEST_URI']) {
    // Store the current page as the previous page in the session
    $_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];

    // Parse the referring URL
    $referrerParts = parse_url($_SERVER['HTTP_REFERER']);

    // Check if the referring URL is valid
    if ($referrerParts && isset($referrerParts['scheme']) && isset($referrerParts['host']) && isset($referrerParts['path'])) {
        // Construct the redirect URL with the correct protocol, host, and path
        $redirectUrl = $referrerParts['scheme'] . '://' . $referrerParts['host'] . $referrerParts['path'];

        // Redirect to the referring page
        header('Location: ' . $redirectUrl);
        exit;
    }
}

// Check if there is a previous page stored in the session
if (isset($_SESSION['previous_page'])) {
    // Construct the redirect URL with the correct protocol, host, and path
    $redirectUrl = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SESSION['previous_page'];

    // Redirect to the stored previous page
    header('Location: ' . $redirectUrl);
    exit;
}

// If no previous page is available, redirect to a default page
header('Location: index.php');
exit;
?>