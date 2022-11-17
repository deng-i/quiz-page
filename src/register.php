<?php
	session_start();
	if(empty($_POST)){
		echo(register_form());
	}
	else{
		register_user();
	}

function register_form(){
	return '<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<title>Register</title>
	</head>
	<body>
		<h1>Register</h1>

		<form method="POST">

		  <div class="mb-3">
		    <label for="username" class="form-label">Username</label>
		    <input type="text" class="form-control" name="username" required="required" maxlength="255">
		  </div>

		  <div class="mb-3">
		    <label for="password" class="form-label">Password</label>
		    <input type="password" class="form-control" name="password" required="required">
		  </div>

		  <div class="mb-3">
		    <label for="first-name" class="form-label">First name</label>
		    <input type="text" class="form-control" name="first-name" required="required" onkeypress="return /[a-z]/i.test(event.key)" maxlength="255">
		  </div>

		  <div class="mb-3">
		    <label for="last-name" class="form-label">Last name</label>
		    <input type="text" class="form-control" name="last-name" required="required" onkeypress="return /[a-z]/i.test(event.key)" maxlength="255">
		  </div>

		  <div class="mb-3">
		    <label for="type" class="form-check-label">Check this if you are a teacher</label>
		    <input type="checkbox" class="form-check-input" name="type">
		  </div>

		  <input type="submit" value="Submit" class="btn btn-primary">
		</form>
		<a href="index.php" class="link-primary">Click here to login</a>

	</body>
	</html>';
}

function register_user(){
	// get the values from POST
	$un = $_POST["username"];
	$pw = $_POST["password"];
	$pw = password_hash($pw, PASSWORD_DEFAULT);
	$fname = $_POST["first-name"];
	$lname = $_POST["last-name"];
	if(isset($_POST["type"])){
		$type = "Teacher";
	}
	else{
		$type = "Student";
	}

	// connect to DB
	$pdo = new pdo("mysql:host=localhost;dbname=cw2", "root", "");
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	$stmt = $pdo->prepare("SELECT username FROM user WHERE username = :userid");
	$stmt->execute([
		"userid" => $un]);
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$user = $stmt->fetch();

	// if the username is not taken then register
	if(empty($user)){
		$sql = "INSERT INTO user (username, forename, surname, type, password)
				VALUES (:userid, :userfname, :userlname, :usertype, :userpassword)";

		$stmt = $pdo->prepare($sql);
		$stmt->execute([
						"userid" => $un,
						"userfname" => $fname,
						"userlname" => $lname,
						"usertype" => $type,
						"userpassword" => $pw]);
		$_SESSION['username'] = $un;
		$_SESSION['type'] = $type;
		header("Location: main.php");
	}
	else{
		echo("Username taken, you will be taken back to the registration page shortly");
		header("Refresh:4");
	}
}
?>