CREATE TABLE IF NOT EXISTS audit(
	staff_id VARCHAR(255),
	quiz_id INT UNSIGNED,
	date_and_time DATETIME
);

DELIMITER //
CREATE TRIGGER log_stuff
	AFTER DELETE ON quiz FOR EACH ROW
	BEGIN
        INSERT INTO audit
        SET staff_id = OLD.author_username,
            quiz_id = OLD.id,
            date_and_time = NOW();
	END //
DELIMITER ;


DELIMITER //
CREATE PROCEDURE less_than_40()
BEGIN
	SELECT surname, forename, score, name AS quiz_name, COUNT(DISTINCT question.question) AS max_score FROM user, attempt, quiz, question
	WHERE attempt.quiz_id = quiz.id
	AND attempt.username = user.username
	AND quiz.id = question.quiz_id
	HAVING (attempt.score / COUNT(DISTINCT question.question)) < 0.4;
END //
DELIMITER ;