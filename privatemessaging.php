<?php

include 'header.php';

if (!isset($_SESSION["loggedinuser"])) {
    header("location: user.php");
    exit; // Stop further execution of the code
}

$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");

$recipientUsername = $_GET['user'];

$query = "SELECT userid FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $recipientUsername);
$stmt->execute();
$result = $stmt->get_result();

// Check if the recipient exists
if ($result->num_rows > 0) {
    // Fetch the recipient ID
    $row = $result->fetch_assoc();
    $recipientId = $row['userid'];
}

?>

<!DOCTYPE html>
<html>
<head>
    <?php if($theme == 'light'){?>
    <link rel="stylesheet" href="style.css">
    <?php } else { ?>
    <link rel="stylesheet" href="styledark.css">
    <?php } ?>
    <title>Private Messaging System</title>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="pm.js"></script>
</head>
<body>
    <?php if($theme == 'light'){?>
    <a id="themeswitcher" href="switchtheme.php?theme=dark">Switch theme</a>
    <?php } else {?>
    <a id="themeswitcher" href="switchtheme.php?theme=light">Switch theme</a>
    <?php } ?>
    <div id="header">
        
    <h1>Open/close a conversation (click on user)</h1>
        <div id="conversation-list">
            <!-- Display list of conversations -->
        </div>
        <div id="message-thread">
            <!-- Display message thread for the selected conversation -->
        </div>
        <form id="compose-form" method="POST" action="sendmessage.php">
            <input type="hidden" id="conversation-id" name="conversationId" value="<?php echo $recipientId; ?>">
            <input type="text" value="<?php echo isset($_GET['user']) ? htmlspecialchars($_GET['user']) : ''; ?>" id="recipient" name="recipient" placeholder="Recipient" required>
            <textarea id="message-content" name="messageContent" placeholder="Message" required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
    
    <?php
    include 'footer.php';
    ?>
</body>
</html>