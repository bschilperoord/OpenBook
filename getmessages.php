<?php

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

// Get the conversation ID from the request
$conversationId = $_GET['conversationId'] ?? null; // Assuming it is passed as a query parameter

// Validate and sanitize the conversation ID
$conversationId = filter_var($conversationId, FILTER_VALIDATE_INT);

if ($conversationId === false) {
    // Invalid conversation ID
    $response = array(
        'success' => false,
        'error' => 'Invalid conversation ID'
    );

    // Set the response header to JSON
    header('Content-Type: application/json');

    // Send the JSON response
    echo json_encode($response);
    exit();
}

// Get the conversation ID from the request
$recipient = $_GET['recipient'] ?? null; // Assuming it is passed as a query parameter

// Validate and sanitize the conversation ID
$recipient = htmlspecialchars($recipient, ENT_QUOTES, 'UTF-8');

if ($recipient === false) {
    // Invalid conversation ID
    $response = array(
        'success' => false,
        'error' => 'Invalid conversation ID'
    );

    // Set the response header to JSON
    header('Content-Type: application/json');

    // Send the JSON response
    echo json_encode($response);
    exit();
}

// Prepare the SQL statement
$query = "SELECT 
content,
sender_id,
recipient_id,
sender_username,
receiver_username,
message_id,
conversation_id,
timestamp
FROM
(SELECT 
    m.content,
    m.sender_id,
    m.recipient_id,
    u1.username AS sender_username,
    u2.username AS receiver_username,
    m.message_id,
    m.conversation_id,
    m.timestamp
FROM 
    privatemessages AS m
INNER JOIN users AS u1 ON m.sender_id = u1.userid
INNER JOIN users AS u2 ON m.recipient_id = u2.userid
WHERE 
    (m.sender_id = ? OR m.recipient_id = ? OR m.sender_id = ? OR m.recipient_id = ?) 
    AND m.conversation_id = ?
) AS subquery
ORDER BY
message_id DESC;";

$stmt = $conn->prepare($query);

// Bind the parameters
$stmt->bind_param('iisii', $_SESSION['userid'], $conversationId, $recipient, $_SESSION['userid'], $conversationId);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if any messages were found
if ($result->num_rows > 0) {
    // Fetch messages as an associative array
    $messages = $result->fetch_all(MYSQLI_ASSOC);

    // Return the messages as JSON response
    $response = array(
        'success' => true,
        'messages' => $messages
    );
} else {
    // No messages found
    $response = array(
        'success' => false,
        'error' => 'No messages found'
    );
}

// Close the statement
$stmt->close();

// Close the database connection
$conn->close();

// Set the response header to JSON
header('Content-Type: application/json');

// Send the JSON response
echo json_encode($response);
?>
