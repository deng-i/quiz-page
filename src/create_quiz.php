<?php 
	session_start();
	if(empty($_POST)){
		echo(new_quiz());
	}
	else{
		process_quiz();
	}

	function new_quiz(){
		return '<!DOCTYPE html>
				<html>
				<head>
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
					<title>Create Quiz</title>
				</head>
				<body>
					<h1>Create a new quiz here</h1>

					<form method="POST">

					  <div class="mb-3">
					    <label for="quiz-name" class="form-label">Quiz name</label>
					    <input type="text" class="form-control" name="quiz-name" required="required" maxlength="255">
					  </div>

					  <div class="mb-3">
					    <label for="duration" class="form-label">Duration(in minutes)</label>
					    <input type="number" class="form-control" name="duration" required="required">
					  </div>

					  <div class="mb-3">
					    <label for="available" class="form-check-label">Check this if this quiz should be available</label>
					    <input type="checkbox" class="form-check-input" name="available">
					  </div>

					  <input type="submit" value="Create" class="btn btn-primary">
					</form>
					<a href="main.php" class="link-primary">Go back to main page</a>

				</body>
				</html>';
	}

	function process_quiz(){
		// get the values from POST
		$name = $_POST["quiz-name"];
		$duration = $_POST["duration"];
		if(isset($_POST["available"])){
			$available = 1;
		}
		else{
			$available = 0;
		}
		$un = $_SESSION['username'];

		// connect to DB
		$pdo = new pdo("mysql:host=localhost;dbname=cw2", "root", "");
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$sql = "INSERT INTO quiz (name, author_username, available, duration)
				VALUES (:name, :author_username, :available, :duration)";

		$stmt = $pdo->prepare($sql);
		$stmt->execute([
						"name" => $name,
						"author_username" => $un,
						"available" => $available,
						"duration" => $duration]);
		echo ("<script type='text/javascript'>alert('Quiz successfully created');</script>");
		header("Refresh: 0; url=main.php");
	}
?>