<?php
include 'header.php';

if (!isset($_SESSION["loggedinuser"])) {
    header("location: user.php");
    exit; // Stop further execution of the code
}

// Establish the database connection
$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");

// Check if the connection was successful
if ($conn->connect_error) {
    die("Failed to connect to the database: " . $conn->connect_error);
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
    <script src="blogpost.js"></script>
    <script type="text/javascript">
                
    </script>
</head>
<body>
    <?php if($theme == 'light'){?>
    <a id="themeswitcher" href="switchtheme.php?theme=dark">Switch theme</a>
    <?php } else {?>
    <a id="themeswitcher" href="switchtheme.php?theme=light">Switch theme</a>
    <?php } ?>
    <div id="header">
        
        <h1>Write your blogpost</h1>
        <input type="file" id="image-input">
        <button onclick="changeBackground()">Change Background</button>
        <button onclick="saveContent()">Save</button>
        <div id="editable-content" contenteditable="true"></div>
        <img src="" alt="image" id="profanitycheck" style="display: none;" />
    </div>

    <div id="body">
        <h1>Bestaande blogposts</h1>
        <?php
        // SQL SELECT statement to retrieve all blog posts
        $sql = "SELECT * FROM blogposts";
        
        // Prepare the query
        $stmt = $conn->prepare($sql);
        
        // Execute the query
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();
        
        // Check if there are any rows returned
        if ($result->num_rows > 0) {
            // Loop through each row
            while ($row = $result->fetch_assoc()) {
                // Access the columns of each row
                $id = $row['id'];
                $content = $row['content'];
                $author = $row['author'];
                $created_at = $row['created_at'];
                $updated_at = $row['updated_at'];
        
                // Display the blog post information
                echo "<div style='background-image: url(" . htmlspecialchars($row['image']) . "); background-size: cover; background-position: center; background-repeat: no-repeat; height: 500px;'> ID: " . $id . "<br>";
                echo "<div style='background-color: black; opacity: 0.7;'>";
                echo "" . htmlspecialchars($content) . "<br>";
                echo "Author: " . htmlspecialchars($author) . "<br>";
                echo "Created At: " . htmlspecialchars($created_at) . "<br>";
                echo "Updated At: " . htmlspecialchars($updated_at) . "<br>";
                echo "<br>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "No blog posts found.";
        }
        
        // Close the prepared statement
        $stmt->close();

        ?>
    </div>

    <?php
    include 'footer.php';
    ?>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>
