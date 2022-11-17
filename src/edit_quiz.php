<?php 
	session_start();
	
	if(empty($_POST)){
		echo(current_quiz());
	}
	else{
		process_quiz();
	}

	function current_quiz(){
		$pdo = new pdo("mysql:host=localhost;dbname=cw2", "root", "");
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$sql = "SELECT name, duration, available FROM quiz	
				WHERE id = :quizid";
		$quiz_id = $_GET['id'];

		$stmt = $pdo->prepare($sql);
		$stmt->execute([
						"quizid" => $quiz_id]);
		$quiz = $stmt->fetch(PDO::FETCH_ASSOC);
		$quiz_name = $quiz['name'];
		$duration = $quiz['duration'];
		$available = $quiz['available'];
		if($available == 1){
			$available = "checked";
		}
		else{
			$available = "";
		}

		return "<!DOCTYPE html>
				<html>
				<head>
					<meta charset='utf-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1'>
					<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC' crossorigin='anonymous'>
					<title>Create Quiz</title>
				</head>
				<body>
					<h1>Create a new quiz here</h1>

					<form method='POST'>

					  <div class='mb-3'>
					    <label for='quiz-name' class='form-label'>Quiz name</label>
					    <input type='text' class='form-control' name='quiz-name' required='required' value='$quiz_name' maxlength='255'>
					  </div>

					  <div class='mb-3'>
					    <label for='duration' class='form-label'>Duration(in minutes)</label>
					    <input type='number' class='form-control' name='duration' required='required' value='$duration'>
					  </div>

					  <div class='mb-3'>
					    <label for='available' class='form-check-label'>Check this if this quiz should be available</label>
					    <input type='checkbox' class='form-check-input' name='available' $available>
					  </div>

					  <div class='mb-3'>
					    <label for='delete' class='form-check-label'>Check this to delete the quiz</label>
					    <input type='checkbox' class='form-check-input' name='delete'>
					  </div>

					  <input type='submit' value='Edit' class='btn btn-primary'>
					</form>
					<a href='main.php' class='link-primary'>Go back to main page</a>

				</body>
				</html>";
	}

	function process_quiz(){
		// connect to DB and get values needed
		$pdo = new pdo("mysql:host=localhost;dbname=cw2", "root", "");
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		$quiz_id = $_GET["id"];
		$un = $_SESSION['username'];

		// if the delete checkbox is checked then the quiz will be deleted
		if(isset($_POST['delete'])){
			//update so that the trigger works as intended
			$sql = "UPDATE quiz SET author_username = :username
					WHERE id = :quizid";
			$stmt = $pdo->prepare($sql);
			$stmt->execute([
							"quizid" => $quiz_id,
							"username" => $un]);

			$sql = "DELETE FROM quiz
					WHERE id = :quizid";
			$stmt = $pdo->prepare($sql);
			$stmt->execute([
							"quizid" => $quiz_id]);
			echo ("<script type='text/javascript'>alert('Quiz successfully deleted');</script>");
			header("Refresh: 0; url=main.php");

		}
		// edit some parameter of the quiz
		else{
			$name = $_POST["quiz-name"];
			$duration = $_POST["duration"];
			if(isset($_POST["available"])){
				$available = 1;
			}
			else{
				$available = 0;
			}

			$sql = "UPDATE quiz SET name = :name, author_username = :username, duration = :duration, available = :available
					WHERE id = :quizid";

			$stmt = $pdo->prepare($sql);
			$stmt->execute([
							"name" => $name,
							"username" => $un,
							"duration" => $duration,
							"available" => $available,
							"quizid" => $quiz_id]);
			echo ("<script type='text/javascript'>alert('Quiz successfully edited');</script>");
			header("Refresh: 0; url=main.php");
		}
	}
?>