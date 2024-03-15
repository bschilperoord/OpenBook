<?php
$dev = false;

if ($dev === true) {
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $filename = "./.env_developer";
} else {
    $filename = "./.env";
}

$setting = parse_ini_file($filename);

$servernamesql = $setting["servernamesql"] ?? null;
$usernamesql = $setting["usernamesql"] ?? null;
$passwordsql = $setting["passwordsql"] ?? null;
$databasesql = $setting["databasesql"] ?? null;

session_start();

if (!isset($_SESSION['themevalue'])) {
    $theme = 'light';
} else if ($_SESSION['themevalue'] == 'dark') {
    $theme = 'dark';
} else if ($_SESSION['themevalue'] == 'light') {
    $theme = 'light';
}

$date = date('Y-m-d H:i:s');
setcookie("datecookie", $date, time() + 3600);
setcookie("theme", $theme, time() + 3600 * 24 * 365 * 10);
?>
