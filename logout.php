<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
</head>
</html>

<?php

include 'header.php';

if (!isset($_SESSION["loggedinuser"])) {
    header("location: user.php");
    exit; // Stop further execution of the code
}

session_regenerate_id(); // Regenerate session ID to prevent session fixation
session_destroy();

header("location: user.php")

?>