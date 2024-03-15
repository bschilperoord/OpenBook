<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php
include 'header.php';

$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");

// Retrieve the search query from the form submission
$searchQuery = $_POST['search_query'] ?? '';

// Validate and sanitize the search query
$searchQuery = trim($searchQuery);
$searchQuery = mysqli_real_escape_string($conn, $searchQuery);

if (!empty($searchQuery)) {
    // Perform the search query on the users table
    $query = "SELECT * FROM users WHERE username LIKE '%$searchQuery%'";
    $result = mysqli_query($conn, $query);

    // Display the search results
    if (mysqli_num_rows($result) > 0) {
        echo "<h2>Search Results</h2>";
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($result)) {
            $username = htmlspecialchars($row['username']);
            echo "<li>" . $username . "</li>";
        }
        echo "</ul>";
    } else {
        echo "No results found. Going back to My Friends page.";
        header("Refresh:5; url=friends.php");
    }
} else {
    echo "Invalid search query. Going back to My Friends page.";
    header("Refresh:5; url=friends.php");
}

// Close the database connection
mysqli_close($conn);
?>

</body>
</html>