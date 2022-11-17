<?php 
	session_start();

	$pdo = new pdo("mysql:host=localhost;dbname=cw2", "root", "");
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	if(empty($_POST)){
		echo(questions($pdo));
	}
	else{
		process_attempt($pdo);
	}

	function process_attempt($pdo){
		$quiz_id = $_GET['id'];
		$username = $_SESSION['username'];
		$date = date("Y/m/d");
		$score = 0;
		// counts how many correct answers
		foreach ($_POST as $key => $value) {
			$value_exploded = explode(".:.:.", $value);
			if($value_exploded[0] == $value_exploded[1]){
				$score = $score + 1;
			}
		}

		// adds to the attempt table the score and the date for the last attempt
		$sql = "INSERT INTO attempt (quiz_id, username, last_date, score)
				VALUES (:quizid, :username, :last_date, :score)";

		$stmt = $pdo->prepare($sql);
		if($stmt->execute([
						"quizid" => $quiz_id,
						"username" => $username,
						"last_date" => $date,
						"score" => $score])){
		}
		// if the user already tried this quiz, then the existing record will be updated not a new record created
		else{
			$sql = "UPDATE attempt SET last_date = :last_date, score = :score
					WHERE quiz_id = :quizid AND username = :username";
			$stmt = $pdo->prepare($sql);
			$stmt->execute([
							"last_date" => $date,
							"score" => $score,
							"quizid" => $quiz_id,
							"username" => $username]);
		}


		echo ("<script type='text/javascript'>alert('Quiz submitted');</script>");
		header("Refresh: 0; url=main.php");
	}

	function questions($pdo){
		$start = html_start();
		$middle = "";
		$end = html_end();
		$previous_question = "";
		$quiz_id = $_GET['id'];

		$sql = "SELECT * FROM question
				WHERE quiz_id = $quiz_id";

		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		$i = 0;
		// shows the questions and the options until there are no more questions
		while ($row = $stmt->fetch()) {
			$question = $row['question'];
			$option = $row['options'];
			$answer = $row['answer'];
			if($question != $previous_question){
				$i = $i + 1;
				if($_SESSION['type'] == "Teacher"){
					$middle .= "<div class='mb-3'>
									<h2>$i. $question</h2>
									<a href='edit_question.php?id=$quiz_id&question=$question'>Edit/Delete Question</a>
								</div>";
				}
				else{
					$middle .= "<div class='mb-3'>
									<h2>$i. $question</h2>
								</div>";
				}
			}
			$middle .= "<div class='mb-3'>
						    <label for='4' class='form-label'>$option</label>
						    <input type='radio' class='form-check-input' name='$i' id='4' value='$option.:.:.$answer' checked='checked'>
					  	</div>";
			$previous_question = $question;
		}
		return $start . $middle . $end;
	}

	function html_start(){
		$duration = $_GET['duration'];
		return '<!DOCTYPE html>
				<html>
				<head>
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
					<title>Quiz questions</title>
				</head>
				<body>
					<h1>Quiz questions</h1>
					<h2>Duration: ' . $duration . ' minutes</h2>

					<form method="POST">';
	}

	function html_end(){
		$duration = $_GET['duration'];
		// after the duration is over the submit button will be clicked automatically
		return '<script type="text/javascript">
					setTimeout(function() {document.getElementById("submit").click();},' . intval($duration) * 60 *1000 . ');
				</script>
				<input type="submit" value="Submit" class="btn btn-primary" id="submit">
				</form>
				<a href="main.php" class="link-primary">Go back to main page</a>

			</body>
			</html>';
	}

?>

