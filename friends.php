<?php
session_start();
include 'header.php';

// Check if the user is not logged in, then redirect to the login page
if (!isset($_SESSION['loggedinuser'])) {
    header("location: user.php");
    exit;
}

// Include the PHP code to establish a database connection
$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve pending friend requests for the receiver using prepared statements
$query = "SELECT f.request_id, f.status, u_receiver.username AS receiver, u_sender.username AS sender 
FROM friends f 
LEFT JOIN users u_receiver ON f.receiver_id = u_receiver.userid 
LEFT JOIN users u_sender ON f.sender_id = u_sender.userid 
WHERE (u_sender.username = ? OR u_receiver.username = ?) AND f.status = 'pending';";

$stmt = $conn->prepare($query);
$stmt->bind_param('ss', $_SESSION['loggedinuser'],$_SESSION['loggedinuser']);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Friends Page</title>
    <?php if($theme == 'light'){?>
    <link rel="stylesheet" href="style.css">
    <?php } else { ?>
    <link rel="stylesheet" href="styledark.css">
    <?php } ?>
</head>
<body>
    <?php if($theme == 'light'){?>
    <a id="themeswitcher" href="switchtheme.php?theme=dark">Switch theme</a>
    <?php } else {?>
    <a id="themeswitcher" href="switchtheme.php?theme=light">Switch theme</a>
    <?php } ?>
    <div id="header">
        <img id="friendslogo" src="friends.png">
        <h1 style="margin-bottom: 10px;">My Friends</h1>

        <?php
        if ($result->num_rows != 0) { 
            // Display the friend requests
            while($row = $result->fetch_assoc()) { 
            
                echo "There is pending friend request from: " . htmlspecialchars($row['sender'], ENT_QUOTES, 'UTF-8');
                
                if($row['sender'] != $_SESSION['loggedinuser']){
                echo "<form action='friendrequest.php' method='post'>";
                echo "<button type='submit' name='status' value='accepted'>Accept</button>";
                echo "<button type='submit' name='status' value='rejected'>Reject</button>";
                echo "<input type='hidden' name='request_id' value='" . $row['request_id'] . "'>";
                echo "</form>";
                }
            }
        }
        // Retrieve the accepted friend requests from the friend_requests table using prepared statements
        $query2 = "SELECT fr.request_id, fr.status,
        CASE
            WHEN u.username = ? THEN v.username
            ELSE u.username
            END AS opposite_username
        FROM friends fr
        LEFT JOIN users u ON fr.sender_id = u.userid
        LEFT JOIN users v ON fr.receiver_id = v.userid
        WHERE (u.username = ? OR v.username = ?)
        AND fr.status = 'accepted';";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bind_param('sss', $_SESSION['loggedinuser'], $_SESSION['loggedinuser'], $_SESSION['loggedinuser']);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        echo "<p style='color: white;'>Current friends:</p>";

        // Display the accepted friend requests
        if ($result2->num_rows > 0) {
            echo "<ul style='list-style-position: outside; list-style-type: none;'>";
            while ($row = $result2->fetch_assoc()) {
                echo "<li style='color: white; display: inline-block; margin-right: 10px;'>" . htmlspecialchars($row['opposite_username'], ENT_QUOTES, 'UTF-8') . "</li><a id='sendpm' style='color: white; display: block; border: solid black 5px; border-radius: 15px; padding: 5px; text-decoration: none;' href='privatemessaging.php?user=". $row['opposite_username'] ."'>Send PM</a><br>";
            }
            echo "</ul>";
        } else {
            echo "No accepted friend requests.";
        }

        ?>

        <!-- Add a form to search for friends -->
        <form action="searchfriends.php" method="post">
            <input type="text" name="search_query" placeholder="Search for friends" required>
            <button type="submit">Search</button>
        </form>

        <!-- Add a form to send friend requests -->
        <form action="friendrequest.php?makefriendrequest=yes" method="post">
            <input style="margin-left: 91px;" type="text" name="receiver_username" placeholder="Username of friend" required>
            <button type="submit">Send Friend Request</button>
        </form>
    </div>
    <?php
    include 'footer.php';
    ?>
</body>
</html>

<?php
// Close the prepared statements and the database connection
$stmt->close();
$stmt2->close();
$conn->close();
?>
