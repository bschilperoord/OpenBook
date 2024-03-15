<?php

include 'header.php';

if (!isset($_SESSION["loggedinuser"])) {
    header("location: user.php");
    exit; // Stop further execution of the code
}

?>