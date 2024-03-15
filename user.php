<?php
include 'header.php';

if (isset($_SESSION["loggedinuser"])) {
    header("location: index.php");
    exit; // Stop further execution of the code
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Auto-Refreshing Chatbox</title>
  <?php if($theme == 'light'){?>
  <link rel="stylesheet" href="style.css">
  <?php } else { ?>
  <link rel="stylesheet" href="styledark.css">
  <?php } ?>
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script type="text/javascript" src="home.js"></script>
</head>
<body>
	<div id="header">

		<h1>OpenBook</h1>
		<img id="profile" src="profile.png">
		<br>
		<div style="display: block; width: 280px; margin-left: auto; margin-right: auto; margin-top: 50px;">
			<form method="post" action="createuser.php">
				<label for="username">Username:</label>
				<input type="text" id="username" name="username" required>
				<label for="password">Password:</label>
				<input type="password" id="password" name="password" required>
				<label id="twofactorcodelabel" for="2facode">2FA code:</label>
				<input type="text" id="2facode" name="2facode" placeholder="optional 2FA code">
				<label id="emaillabel" for="email">E-mail:</label>
				<input type="email" id="email" name="email" placeholder="enter for signup only">
				<input style="font-weight: bold; display: block; margin-top: 25px;" type="submit" value="Login/Create Account">
			</form>
			<?php
			if (isset($_GET['password']) && $_GET['password'] == 'incorrect') {
				echo "<br>Password incorrect";
			}
			?>
		</div>
	</div>
</body>
</html>