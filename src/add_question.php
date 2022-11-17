<?php 
	session_start();

	$type = $_SESSION['type'];
	if(empty($_POST)){
		if($type == "Teacher"){
			echo(teacher_option());
		}
	}
	else{
		process_question();
	}


	function process_question(){
		// selects the correct answer
		foreach ($_POST as $key => $value) {
			if($key == "answer"){
				$answer = $previous;
			}
			$previous = $value;
		}
		$question = $_POST["question"];
		$quiz_id = $_GET["id"];

		$pdo = new pdo("mysql:host=localhost;dbname=cw2", "root", "");
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$sql = "INSERT INTO question (quiz_id, question, options, answer)
				VALUES (:quizid, :question, :options, :answer)";

		foreach ($_POST as $key => $value) {
			if(str_contains($key, "option")){
				$stmt = $pdo->prepare($sql);
				$stmt->execute([
								"quizid" => $quiz_id,
								"question" => $question,
								"options" => $value,
								"answer" => $answer]);
			}
		}
		echo ("<script type='text/javascript'>alert('Question successfully added');</script>");
		header("Refresh: 0; url=main.php");
	}

	function teacher_option(){
		return '<!DOCTYPE html>
				<html>
				<head>
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
					<title>Quiz</title>
				</head>
				<body>
					<h1>Add question to quiz</h1>

					<form method="POST">

					  <div class="mb-3">
					    <label for="question" class="form-label">Question</label>
					    <input type="text" class="form-control" name="question" required="required" maxlength="500">
					  </div>

					  <div class="mb-3">
					    <label for="option1" class="form-label">Option 1</label>
					    <input type="text" class="form-control" name="option1" required="required" maxlength="200">
					    <label for="1" class="form-label">Correct answer</label>
					    <input type="radio" class="form-check-input" name="answer" id="1" value="1" checked="checked">
					  </div>

					  <div class="mb-3">
					    <label for="option2" class="form-label">Option 2</label>
					    <input type="text" class="form-control" name="option2" required="required" maxlength="200">
					    <label for="2" class="form-label">Correct answer</label>
					    <input type="radio" class="form-check-input" name="answer" id="2" value="2">
					  </div>

					  <div class="mb-3">
					    <label for="option3" class="form-label">Option 3</label>
					    <input type="text" class="form-control" name="option3" required="required" maxlength="200">
					    <label for="3" class="form-label">Correct answer</label>
					    <input type="radio" class="form-check-input" name="answer" id="3" value="3">
					  </div>

					  <div class="mb-3">
					    <label for="option4" class="form-label">Option 4</label>
					    <input type="text" class="form-control" name="option4" required="required" maxlength="200">
					    <label for="4" class="form-label">Correct answer</label>
					    <input type="radio" class="form-check-input" name="answer" id="4" value="4">
					  </div>

					  <input type="submit" value="Add question" class="btn btn-primary">
					</form>
					<a href="main.php" class="link-primary">Go back to main page</a>

				</body>
				</html>';
	}
?>