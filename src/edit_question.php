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
		$pdo = new pdo("mysql:host=localhost;dbname=cw2", "root", "");
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$quiz_id = $_GET["id"];
		$question = $_GET['question'];

		// getting the options for the question so later it can be used
		$sql = "SELECT options FROM question
				WHERE quiz_id = :quizid AND question = :question";

		$stmt = $pdo->prepare($sql);
		$stmt->execute([
						"quizid" => $quiz_id,
						"question" => $question]);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$options = [];
		while ($row = $stmt->fetch()){
			array_push($options, $row['options']);
		}

		// If the user checked the delete checkbox
		if(isset($_POST['delete'])){
			$sql = "DELETE FROM question
					WHERE quiz_id = :quizid AND question = :question AND options = :options";
			$i = 0;
			foreach ($_POST as $key => $value) {
				if(str_contains($key, "option")){
					$stmt = $pdo->prepare($sql);
					$stmt->execute([
									"quizid" => $quiz_id,
									"question" => $question,
									"options" => $options[$i]]);
					$i++;
				}
			}
			echo ("<script type='text/javascript'>alert('Question successfully deleted');</script>");
			header("Refresh: 0; url=main.php");
		}
		// the DB will be updated with the new values
		else{
			foreach ($_POST as $key => $value) {
				if($key == "answer"){
					$answer = $previous;
				}
				$previous = $value;
			}
			$new_question = $_POST['question'];

			$sql = "UPDATE question SET question = :new_question, options = :new_options, answer = :answer
					WHERE quiz_id = :quizid AND question = :question AND options = :options";

			$i = 0;
			foreach ($_POST as $key => $value) {
				if(str_contains($key, "option")){
					$stmt = $pdo->prepare($sql);
					$stmt->execute([
									"quizid" => $quiz_id,
									"new_question" => $new_question,
									"new_options" => $value,
									"answer" => $answer,
									"question" => $question,
									"options" => $options[$i]]);
					$i++;
				}
			}
			echo ("<script type='text/javascript'>alert('Question successfully edited');</script>");
			header("Refresh: 0; url=main.php");
		}
	}

	function teacher_option(){
		// needed to populate the form with the current data
		$pdo = new pdo("mysql:host=localhost;dbname=cw2", "root", "");
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$sql = "SELECT options FROM question
				WHERE quiz_id = :quizid AND question = :question";
		$quiz_id = $_GET['id'];
		$question = $_GET['question'];

		$stmt = $pdo->prepare($sql);
		$stmt->execute([
						"quizid" => $quiz_id,
						"question" => $question]);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$options = [];
		while ($row = $stmt->fetch()){
			array_push($options, $row['options']);
		}

		return "<!DOCTYPE html>
				<html>
				<head>
					<meta charset='utf-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1'>
					<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC' crossorigin='anonymous'>
					<title>Quiz</title>
				</head>
				<body>
					<h1>Edit/Delete question</h1>

					<form method='POST'>

					  <div class='mb-3'>
					    <label for='question' class='form-label'>Question</label>
					    <input type='text' class='form-control' name='question' required='required' value='$question'>
					  </div>

					  <div class='mb-3'>
					    <label for='option1' class='form-label'>Option 1</label>
					    <input type='text' class='form-control' name='option1' required='required' value='$options[0]'>
					    <label for='1' class='form-label'>Correct answer</label>
					    <input type='radio' class='form-check-input' name='answer' id='1' value='1' checked='checked'>
					  </div>

					  <div class='mb-3'>
					    <label for='option2' class='form-label'>Option 2</label>
					    <input type='text' class='form-control' name='option2' required='required' value='$options[1]'>
					    <label for='2' class='form-label'>Correct answer</label>
					    <input type='radio' class='form-check-input' name='answer' id='2' value='2'>
					  </div>

					  <div class='mb-3'>
					    <label for='option3' class='form-label'>Option 3</label>
					    <input type='text' class='form-control' name='option3' required='required' value='$options[2]'>
					    <label for='3' class='form-label'>Correct answer</label>
					    <input type='radio' class='form-check-input' name='answer' id='3' value='3'>
					  </div>

					  <div class='mb-3'>
					    <label for='option4' class='form-label'>Option 4</label>
					    <input type='text' class='form-control' name='option4' required='required' value='$options[3]'>
					    <label for='4' class='form-label'>Correct answer</label>
					    <input type='radio' class='form-check-input' name='answer' id='4' value='4'>
					  </div>

					  <div class='mb-3'>
					    <label for='delete' class='form-check-label'>Check this to delete the question</label>
					    <input type='checkbox' class='form-check-input' name='delete'>
					  </div>

					  <input type='submit' value='Edit/Delete question' class='btn btn-primary'>
					</form>
					<a href='main.php' class='link-primary'>Go back to main page</a>

				</body>
				</html>";
	}
?>