<?php
session_start();
include 'header.php';

if (!isset($_SESSION["loggedinuser"])) {
    header("location: user.php");
    exit; // Stop further execution of the code
}

// Establish the database connection
$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve sender and receiver IDs from the user table
$senderUsername = $_SESSION['loggedinuser'];
$receiverUsername = $_POST['receiver_username'];

// Prepare the statements to retrieve sender and receiver IDs
$senderQuery = "SELECT userid FROM users WHERE username = ?";
$receiverQuery = "SELECT userid FROM users WHERE username = ?";

$stmt1 = $conn->prepare($senderQuery);
$stmt1->bind_param('s', $senderUsername);
$stmt1->execute();
$senderResult = $stmt1->get_result();
$senderRow = $senderResult->fetch_assoc();
$senderId = $senderRow['userid'];

$stmt2 = $conn->prepare($receiverQuery);
$stmt2->bind_param('s', $receiverUsername);
$stmt2->execute();
$receiverResult = $stmt2->get_result();
$receiverRow = $receiverResult->fetch_assoc();
$receiverId = $receiverRow['userid'];

if ($_GET["makefriendrequest"] == 'yes') {
    if ($receiverId == $senderId) {
        echo "Can't send request to yourself. Redirecting in 5 seconds.";
        header("Refresh:5; url=friends.php");
        exit;
    } else {
        $_SESSION['receiverid'] = $receiverRow['userid'];

        if ($_SESSION['userid'] != $receiverRow['userid'] && $_SESSION['loggedinuser'] != $senderRow['userid']) {
            // Insert a new row in the friend requests table
            $insertQuery = "INSERT INTO friends (sender_id, receiver_id, status) VALUES (?, ?, 'pending')";
            $stmt3 = $conn->prepare($insertQuery);
            $stmt3->bind_param('ii', $senderId, $receiverId);
            $stmt3->execute();
            $stmt3->close();
            header("location: friends.php");
            exit;
        }
    }
}

if (isset($_POST['status'])) {
    $requestId = $_POST['request_id'];
    $status = $_POST['status'];

    // Update the status of the friend request in the friend requests table
    $updateQuery = "UPDATE friends SET status = ? WHERE request_id = ?";
    $stmt4 = $conn->prepare($updateQuery);
    $stmt4->bind_param('si', $status, $requestId);
    $stmt4->execute();
    $stmt4->close();
    header("location: friends.php");
    exit;
}

// Close the database connection
$conn->close();
?>