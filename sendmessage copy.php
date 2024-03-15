<?php

include 'header.php';

// Establish the database connection
$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the sender ID, recipient username, and message content from the request
$senderId = $_SESSION['userid']; // Assuming you have the sender ID stored in the session
$recipientUsername = $_POST['recipient']; // Assuming it is passed as a POST parameter
$conversationId = $_POST['conversationId'];
$messageContent = $_POST['messageContent']; // Assuming it is passed as a POST parameter

// Get the recipient ID based on the recipient username
$query = "SELECT users.userid
FROM users
WHERE users.username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $recipientUsername);
$stmt->execute();
$result = $stmt->get_result();

// Check if the recipient exists
if ($result->num_rows > 0) {
    // Fetch the recipient ID
    $row = $result->fetch_assoc();
    $recipientId = $row['userid'];
    
    // Insert the message into the database
    $query = "INSERT INTO privatemessages (conversation_id, sender_id, recipient_id, content)
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiis', $conversationId, $senderId, $recipientId, $messageContent);
    $stmt->execute();

    // Check if the message was inserted successfully
    if ($stmt->affected_rows > 0) {
        $response = array(
            'success' => true
        );
    } else {
        $response = array(
            'success' => false
        );
    }
}
else
{
    // Recipient does not exist
    $response = array(
        'success' => false
    );
}

// Close the database connection
$stmt->close();
$conn->close();

// Set the response header to JSON
header('Content-Type: application/json');

// Send the JSON response
echo json_encode($response);
?>
