<?php
include 'header.php';

if (!isset($_SESSION["loggedinuser"])) {
    header("location: user.php");
    exit; // Stop further execution of the code
}

$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chatbox</title>
    <?php if($theme == 'light'){?>
    <link rel="stylesheet" href="style.css">
    <?php } else { ?>
    <link rel="stylesheet" href="styledark.css">
    <?php } ?>
    <script src="jquery-3.7.0.min.js"></script>
    <script type="text/javascript" src="home.js"></script>
    <script type="text/javascript" src="chatbox.js"></script>
    <script type="text/javascript">
        // Get the required elements
    </script>
    <style>
        #emoji-popup {
            display: none;
            /* Additional styles for positioning, background, and border */
            text-align: center;
            align-items: center;
            justify-content: center;
        }

        #emoji-popup .emoji {
            /* Additional styles for each emoji element */
            display: inline-block;
            /* Add any necessary padding, margin, or other styling */
        }
    </style>
</head>
<body>
<?php if($theme == 'light'){?>
<a id="themeswitcher" href="switchtheme.php?theme=dark">Switch theme</a>
<?php } else {?>
<a id="themeswitcher" href="switchtheme.php?theme=light">Switch theme</a>
<?php } ?>

<iframe id="winretro" src="https://www.winretro.com/" title="WinRetro"></iframe>

<?php
	// Define user ID safely (assuming it's an integer)
	$userId = isset($_SESSION['userid']) ? intval($_SESSION['userid']) : 0;

	// Query to retrieve sidebar coordinates
	$queryForId = "SELECT * FROM `settings` WHERE name = 'sidebarcoordinates' and userid = ?";
	$stmt = $conn->prepare($queryForId);
	$stmt->bind_param("i", $userId);
	$stmt->execute();
	$result = $stmt->get_result();

	// Check if the user exists in the database
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$coordinates = $row['Value'];
		$coordinatessplit = explode(", ", $coordinates);

		$x = $coordinatessplit[0];
		$y = $coordinatessplit[1];

		// Query to retrieve widget settings
		$queryForId2 = "SELECT * FROM `settings` WHERE name = 'widget' and userid = ?";
		$stmt2 = $conn->prepare($queryForId2);
		$stmt2->bind_param("i", $userId);
		$stmt2->execute();
		$result2 = $stmt2->get_result();

		if ($result2->num_rows > 0) {
			$row2 = $result2->fetch_assoc();
			$widgets = $row2['Value'];

			// Fetch the next row if it exists
			$row3 = $result2->fetch_assoc();

			// Output the sidebar with widget visibility classes
			?>
			<div id="sidebar" style="left: <?php echo $x; ?>px; top: <?php echo $y; ?>px;">
				<div class="<?php echo ($widgets == 'weather, 0') ? 'hidden' : (($widgets == 'weather, 1') ? 'visible' : ''); ?>"></div>
				<div class="<?php echo ($row3['Value'] == 'rss, 0') ? 'hidden2' : (($row3['Value'] == 'rss, 1') ? 'visible2' : ''); ?>"></div>
				<form action="addwidget.php" id="optionsForm">
					<label for="options">Toggle widget:</label>
					<select id="options" name="options">
						<option value="rss">RSS</option>
						<option value="weather">Weather</option>
					</select>
					<br><br>
					<input type="submit" value="Toggle">
				</form>
			</div>

            <iframe id="winretro" src="https://www.winretro.com" title="WinRetro"></iframe> 

			<?php
		}
	} else {
		echo "No results found";
	}

     ?>
    <div id="header">
        
        <h1 style="margin-bottom: 25px;">OpenBook</h1>
        <?php

        $userId = $_SESSION['userid'];
        // Prepare and execute a query to fetch the profileimage based on the user ID
        $stmt = $conn->prepare("SELECT profileimage FROM users WHERE userid = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        // Bind the result to a variable
        $stmt->bind_result($profileimage);

        // Fetch the result
        if ($stmt->fetch()) {
        ?> 
            <img id="profile" src="<?php echo htmlspecialchars($profileimage); ?>"> <?php
            // The profileimage value is stored in the $profileimage variable
        } else {
            echo "Profile Image not found.";
        }

        // Close the statement
        $stmt->close();
        ?>
        <h3>Hello, <?php echo htmlspecialchars($_SESSION["loggedinuser"]); ?> &#x1F600;</h3>
        <p></p>
        <h3>Timeline</h3>
        <div id="posts">
            <h2 style="margin-top: 50px;">Write a post</h2>
            <form method="POST" action="chatbox.php?sendingmessage=true">
                <input value="<?php echo htmlspecialchars($_SESSION["loggedinuser"]); ?>" type="text" name="gebruikersnaam" id="gebruikersnaam" placeholder="Gebruikersnaam" required>
                <input type="text" name="bericht" id="bericht" placeholder="I'm feeling great &#x1F600;" required>
                <button onclick="fetchData()">Verstuur</button>
            </form>

            <button id="emoji-button">Select Emoji</button>
            <div style="display: none;" id="emoji-popup">
                <!-- Emoji content will be added dynamically here -->
            </div>
        </div>
    </div>

    <div id="chatbox">
        <div id="radius" style="display: block; border-radius: 25px; background-color: black; opacity: 0.7;">
        <!-- Here the chat messages will be displayed -->
        </div>
    </div>

    <div id="mijnTemplate" style="display: none;">
        <div style="padding: 5px; display: flex;">
            <div id="hoverDiv" style="flex: 25%;">
                FLD_NAAM:
                <div id="popupContent" style="color: lightblue; flex: 25%;">
                <img id="profileimagetable" src="FLD_IMG" width="48">
                </div>
            </div>
            <div style="flex: 33%; overflow-wrap: break-word; inline-size: 150px; padding-right: 15px;"> "FLD_BERICHT"</div>
            <div style="flex: 33%; color: darkgrey;">FLD_TIMESTAMP</div>
            <button class="like-btn-chatbox" data-post-id="FLD_POSTID">👍</button>
            <span class="like-count">FLD_LIKES</span>
            
        </div>
    </div>

    <div id="body">
        <h1>Blogposts</h1>
        <?php
        // Assuming you have established a MySQL connection

        // Prepare the SQL SELECT statement to retrieve all blog posts
        $sql = "SELECT blogposts.*, COUNT(likes.postid) AS like_count 
        FROM blogposts 
        LEFT JOIN likes ON blogposts.id = likes.itemid 
        GROUP BY blogposts.id;";

        // Execute the query
        $result = $conn->query($sql);

        // Check if there are any rows returned
        if ($result->num_rows > 0) {
            // Loop through each row
            while ($row = $result->fetch_assoc()) {
                // Access the columns of each row
                $id = htmlspecialchars($row['id']);
                //$title = htmlspecialchars($row['title']);
                $content = htmlspecialchars($row['content']);
                $author = htmlspecialchars($row['author']);
                $created_at = htmlspecialchars($row['created_at']);
                $updated_at = htmlspecialchars($row['updated_at']);
                $like_count = htmlspecialchars($row['like_count']);

                // Display the blog post information
                echo "<div style='background-image: url(" . htmlspecialchars($row['image']) . "); background-size: cover; background-position: center; background-repeat: no-repeat; height: 500px;'> ID: " . $id . "<br>";
                echo "<div style='background-color: black; opacity: 0.7;'>";
                //echo "Title: " . $title . "<br>";
                echo "" . htmlspecialchars($content) . "<br>";
                echo "Author: " . $author . "<br>";
                echo "Created At: " . $created_at . "<br>";
                echo "Updated At: " . $updated_at . "<br>";
                echo "<br>";
                echo '<button class="like-btn-blogposts" data-post-id="'.$id.'">👍</button>';
                echo '<span class="like-count">' .$like_count. '</span>';
                echo "</div>";
                echo "</div>";      
            }
        } else {
            echo "No blog posts found.";
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>
    <script>
    
    </script>

    <?php
    include 'footer.php';
    ?>
</body>
</html>