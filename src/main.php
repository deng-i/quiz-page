<?php 
	session_start();
	$start = html_start();
	// if the user is teacher then they have more options
	if ($_SESSION['type'] == "Teacher") {
		$start .= "<th scope='col'>Score</th>
					<th scope='col'>Add</th>
					<th scope='col'>Edit/Delete</th>
					</tr>
					</thead>
					<tbody>";
	}
	else{
		$start .= "<th scope='col'>Score</th>
					</tr>
					</thead>
					<tbody>";
	}
	$middle = "";
	$end = html_end();

	$sql = "SELECT * FROM quiz";
	$sql2 = "SELECT score FROM attempt
			 WHERE quiz_id = :quizid AND username = :username";

	$pdo = new pdo("mysql:host=localhost;dbname=cw2", "root", "");
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	// creates links to new pages while there are available quizzes
	while ($row = $stmt->fetch()) {
		if($_SESSION['type'] == "Teacher"){
			$middle .= '<tr><td>';
			$middle .= $row['name'];
			$middle .= '</td><td><a href="quiz.php?id=';
			$middle .= $row['id'];
			$middle .= '&duration=';
			$middle .= $row['duration'];
			$middle .= '">Link to quiz</a></td><td>';
			$stmt2 = $pdo->prepare($sql2);
			$stmt2->execute([
							"quizid" => $row['id'],
							"username" => $_SESSION['username']]);
			$score = $stmt2->fetch(PDO::FETCH_ASSOC);
			if(!empty($score)){
				$score = $score['score'];
			}
			else{
				$score = "-";
			}
			$middle .= $score;
			$middle .= '</td><td><a href="add_question.php?id=';
			$middle .= $row['id'];
			$middle .= '">Add question</a>';
			$middle .= '</td><td><a href="edit_quiz.php?id=';
			$middle .= $row['id'];
			$middle .= '">Edit/Delete quiz</a>';
			$middle .= '</td></tr>';
		}
		else if($row['available'] == 1){
			$middle .= '<tr><td>';
			$middle .= $row['name'];
			$middle .= '</td><td><a href="quiz.php?id=';
			$middle .= $row['id'];
			$middle .= '&duration=';
			$middle .= $row['duration'];
			$middle .= '">Link to quiz</a></td><td>';
			$stmt2 = $pdo->prepare($sql2);
			$stmt2->execute([
							"quizid" => $row['id'],
							"username" => $_SESSION['username']]);
			$score = $stmt2->fetch(PDO::FETCH_ASSOC);
			if(!empty($score)){
				$score = $score['score'];
			}
			else{
				$score = "-";
			}
			$middle .= $score;
			$middle .= '</td></tr>';
		}
	}
	echo($start . $middle . $end);

	function html_start(){
		$text = '<!DOCTYPE html>
				<html>
				<head>
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
					<title>Quizzes</title>
				</head>
				<body>
					<h1>Available quizzes</h1>';
		if($_SESSION['type'] == "Teacher"){
			$text .= '<a href="create_quiz.php">Create new quiz</a>';
		}
		$text .='<table class="table">
					<thead>
					<tr>
						<th scope="col">Name</th>
						<th scope="col">Link</th>';
		return $text;
	}

	function html_end(){
		return '</tbody>
				</table>
				</body>
				</html>';
	}

?>
