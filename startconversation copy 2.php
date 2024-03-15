<?php
include 'header.php';

// Establish the database connection
$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Assuming you have already established a database connection

// Get the sender ID, recipient username, and message content from the request
$senderId = $_SESSION['userid']; // Assuming you have the sender ID stored in the session
$recipientUsername = $_POST['recipient']; // Assuming it is passed as a POST parameter

// Get the recipient ID based on the recipient username
$query = "SELECT users.userid, conversations.sender_id, conversations.recipient_id
FROM users
LEFT JOIN conversations ON users.userid = conversations.sender_id
WHERE users.username = ?;";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $recipientUsername);
$stmt->execute();
$result = $stmt->get_result();

// Check if the recipient exists
if ($result->num_rows > 0) {
    // Fetch the recipient ID
    $row = $result->fetch_assoc();
    $recipientId = $row['userid'];

    // Check if the conversation already exists
    $query = "SELECT conversation_id FROM conversations WHERE sender_id = ? OR recipient_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $senderId, $recipientId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        // Conversation already exists
        $response = array(
            'success' => false
        );
    } else {
        // Insert the message into the database
        $query = "INSERT INTO conversations (sender_id, recipient_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $senderId, $recipientId);
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
} else {
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
