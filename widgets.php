<?php
session_start();

include 'header.php';

// Establish database connection
$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the setting already exists for the user
$userID = $_SESSION['userid'];
$settingwidget = $_POST['settingwidget'];

$parts = explode(", ", $settingwidget);
$one = $parts[0];

// Define the setting name
$name = 'widget';

// Prepare and execute a SELECT query to check if the setting exists
$selectQuery = "SELECT Value FROM settings WHERE name = ? AND userid = ? AND (Value LIKE ?)";
$selectStmt = $conn->prepare($selectQuery);
$selectValue = $one . '%'; // Adding a wildcard (%) to match values starting with $one
$selectStmt->bind_param('sis', $name, $userID, $selectValue);
$selectStmt->execute();
$selectResult = $selectStmt->get_result();
$selectStmt->close();

// Check if a row exists for the setting
if ($selectResult->num_rows == 1) {
    // The setting exists, update its value
    $row = $selectResult->fetch_assoc();
    $currentValue = $row['Value'];
    
    // Toggle between 'rss, 0' and 'rss, 1', and 'weather, 0' and 'weather, 1'
    if ($currentValue === 'rss, 1') {
        $newValue = 'rss, 0';
    } elseif ($currentValue === 'rss, 0') {
        $newValue = 'rss, 1';
    } elseif ($currentValue === 'weather, 1') {
        $newValue = 'weather, 0';
    } elseif ($currentValue === 'weather, 0') {
        $newValue = 'weather, 1';
    } else {
        $newValue = $currentValue;
    }
    
    $updateQuery = "UPDATE settings
    SET Value = ?
    WHERE name = ?
    AND userid = ?
    AND Value = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('ssis', $newValue, $name, $userID, $currentValue);
    if ($updateStmt->execute()) {
        echo "Setting 'widget' updated successfully for user ID: " . $userID;
    } else {
        echo "Error updating setting: " . $updateStmt->error;
    }
    $updateStmt->close();
} else {
    if($settingwidget != ''){
        // The setting does not exist, insert a new row
        $insertQuery = "INSERT INTO settings (name, Value, userid) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param('ssi', $name, $settingwidget, $userID);
        if ($insertStmt->execute()) {
            echo "Setting 'widget' inserted successfully for user ID: " . $userID;
        } else {
            echo "Error inserting setting: " . $insertStmt->error;
        }
        $insertStmt->close();
    }
}

$conn->close();
?>
