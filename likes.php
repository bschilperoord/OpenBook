<?php
include 'header.php';
header('Content-Type: application/json'); // Set appropriate response header

$conn = new mysqli($servernamesql, $usernamesql, $passwordsql, $databasesql);

$zero = 1; // Initialize likeCount variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'] ?? 0;
    $type = $_POST['type'];
    
    // Validate and sanitize $postId and $type (add your validation here)

    if (!empty($postId)) {
        // Check if the post already has a like (value of 0), then update it by one
        // Retrieve the current value of postid from the database
        $selectQuery = "SELECT postid, itemid FROM likes WHERE itemid = ? and type = ?";
        $selectStmt = mysqli_prepare($conn, $selectQuery);
        mysqli_stmt_bind_param($selectStmt, 'is', $postId, $type);
        mysqli_stmt_execute($selectStmt);
        mysqli_stmt_store_result($selectStmt);
        mysqli_stmt_bind_result($selectStmt, $likesId, $itemId);

        // Check the number of rows returned
        $numRows = mysqli_stmt_num_rows($selectStmt);

        // If the row with the given postid exists, increment the value
        if ($numRows > 0) {
            $selectStmt->fetch(); // Fetch the results
            $totallikes = $likesId + 1;
            // Update the database
            $updateQuery = "UPDATE likes SET postid = ? WHERE itemid = ? and type = ?";
            $updateStmt = mysqli_prepare($conn, $updateQuery);

            if ($updateStmt) {
                mysqli_stmt_bind_param($updateStmt, 'iis', $totallikes, $itemId, $type);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);
                $likeCount = $totallikes; // Set the updated like count
            } else {
                // Handle the case where mysqli_prepare failed.
                echo json_encode(array('error' => 'Error preparing statement: ' . mysqli_error($conn)));
                exit;
            }
        } else {
            // Insert a new like record into the 'likes' table
            $insertQuery = "INSERT IGNORE INTO likes (itemid, userid, postid, type) VALUES (?, ?, ?, ?)";
            $insertStmt = mysqli_prepare($conn, $insertQuery);
            $userId = $_SESSION['userid'];

            mysqli_stmt_bind_param($insertStmt, 'iiis', $postId, $userId, $zero, $type);
            mysqli_stmt_execute($insertStmt);
            mysqli_stmt_close($insertStmt);
            
            // Since it's a new like, set likeCount to 1
            $likeCount = 1;
        }
        
        $response = array('likecount' => $likeCount);
        echo json_encode($response);
    } else {
        echo json_encode(array('error' => 'Invalid post ID'));
    }
} else {
    echo json_encode(array('error' => 'Invalid request method'));
}

// Close the database connection
mysqli_close($conn);
?>