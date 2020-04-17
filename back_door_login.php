<?php
	include 'file_path.php'
?>
<html>
<!-- THIS DOES NOT TAKE IN USERNAME OR PASSWORD -- USES REDIRECT -->
<head>
	<title>Schedule-it</title>
	<link rel="stylesheet" type="text/css"	href="./assets/css/style.css">
</head>

<body>
	<div id="frm">
		<form action="process.php" method="POST">
			<p>
				<label>Username:</label>
				<input type="text" id="user" name="user"	value="<?php echo $DEV_ONID ?>"/>
			</p>
			<p>
				<label>Password:</label>
				<input type="password" id="pass" name="pass"	/>
			</p>
			<p hidden>
				<label>Backdoor:</label>
				<input type="text" id="backdoor" name="backdoor" value="True"/>
			</p>
			<p>
				<input type="submit" id="btn" value="Login"	/>
			</p>
		</form>
	</div>
</body>
</html> 