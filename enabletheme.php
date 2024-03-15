<?php

if (!isset($_SESSION["loggedinuser"])) {
    header("location: user.php");
    exit; // Stop further execution of the code
}

include 'header.php';

$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");



?>