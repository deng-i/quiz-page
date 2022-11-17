<?php
	session_start();
	if(empty($_POST)){
		echo(login_form());
	}
	else{
		login_user();
	}

function login_form(){
	return '<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<title>Login</title>
	</head>
	<body>
		<h1>Login</h1>

		<form method="POST">

		  <div class="mb-3">
		    <label for="username" class="form-label">Username</label>
		    <input type="text" class="form-control" name="username" required="required" maxlength="255">
		  </div>

		  <div class="mb-3">
		    <label for="password" class="form-label">Password</label>
		    <input type="password" class="form-control" name="password" required="required">
		  </div>

		  <input type="submit" value="Login" class="btn btn-primary">
		</form>
		<a href="register.php" class="link-primary">Click here to register</a>

	</body>
	</html>';
}

function login_user(){
	$un = $_POST["username"];
	$pw = $_POST["password"];

	$sql = "SELECT username, password, type FROM user
			WHERE username = :userid";

	$pdo = new pdo("mysql:host=localhost;dbname=cw2", "root", "");
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	$stmt = $pdo->prepare($sql);
	$stmt->execute([
					"userid" => $un]);
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	while ($row = $stmt->fetch()) {
		if (password_verify($pw, $row["password"])){
			$_SESSION['username'] = $un;
			$_SESSION['type'] = $row["type"];
			header("Location: main.php");
		}		
	}
	echo("<br>Invalid credentials, you will be taken back to the login page shortly");
	header("Refresh:4");
}
?>