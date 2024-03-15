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

// Retrieve list of conversations for the current user from the database
$currentUserId = $_SESSION['userid'] ?? null; // Replace with your actual logic to get the current user ID

// Validate and sanitize the current user ID
$currentUserId = filter_var($currentUserId, FILTER_VALIDATE_INT);

if ($currentUserId === false) {
    // Invalid user ID
    $response = array(
        'success' => false,
        'error' => 'Invalid user ID'
    );

    // Set the response header to JSON
    header('Content-Type: application/json');

    // Send the JSON response
    echo json_encode($response);
    exit();
}

$query = "SELECT DISTINCT
c.conversation_id,
CASE
    WHEN c.recipient_id = ? THEN u_sender.username
    WHEN c.sender_id = ? THEN u_recipient.username
END AS other_username,
'accepted' AS status,
u_recipient.username as recipient_username,
c.created_at
FROM
conversations AS c
LEFT JOIN friends AS f ON (
(c.recipient_id = ? AND f.receiver_id = ? AND f.status = ?) OR
(c.sender_id = ? AND f.sender_id = ? AND f.status = ?)
)
INNER JOIN users AS u_recipient ON c.recipient_id = u_recipient.userid
INNER JOIN users AS u_sender ON c.sender_id = u_sender.userid
WHERE BINARY
(c.recipient_id = ? OR c.sender_id = ?)
ORDER BY
recipient_username ASC;
";

$status = "accepted";
$username = $_SESSION['loggedinuser'];

$stmt = $conn->prepare($query);

// Bind the parameters with the correct number of placeholders
$stmt->bind_param('iiiisiisii', 
    $currentUserId, $currentUserId, $currentUserId, 
    $currentUserId, $status, $currentUserId, $currentUserId,
    $status, $currentUserId, $currentUserId
);

// Print the filled-in query (for debugging purposes)
//echo "Filled-in Query: " . $query . " with parameters: " . $currentUserId . ', ' . $currentUserId . ', ' . $currentUserId . ', ' . $currentUserId . ', ' . $currentUserId . ', ' . $currentUserId . ', ' . $currentUserId . ', ' . $currentUserId . ', ' . $username . ', ' . $currentUserId . ', ' . $username . ', ' . $currentUserId . ', ' . $status;

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if any conversations were found
if ($result->num_rows > 0) {
    // Fetch conversations as an associative array
    $conversations = $result->fetch_all(MYSQLI_ASSOC);

    // Return the conversations as a JSON response
    $response = array(
        'success' => true,
        'conversations' => $conversations,
    );
} else {
    // No conversations found
    $response = array(
        'success' => false
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