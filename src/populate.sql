-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2021 at 01:25 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cw2`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `less_than_40` ()  BEGIN
	SELECT surname, forename, score, name AS quiz_name, COUNT(DISTINCT question.question) AS max_score FROM user, attempt, quiz, question
	WHERE attempt.quiz_id = quiz.id
	AND attempt.username = user.username
	AND quiz.id = question.quiz_id
	HAVING (attempt.score / COUNT(DISTINCT question.question)) < 0.4;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `attempt`
--

CREATE TABLE `attempt` (
  `quiz_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `last_date` date DEFAULT NULL,
  `score` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `attempt`
--

INSERT INTO `attempt` (`quiz_id`, `username`, `last_date`, `score`) VALUES
(16, 'student', '2021-12-02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `audit`
--

CREATE TABLE `audit` (
  `staff_id` varchar(255) DEFAULT NULL,
  `quiz_id` int(10) UNSIGNED DEFAULT NULL,
  `date_and_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `audit`
--

INSERT INTO `audit` (`staff_id`, `quiz_id`, `date_and_time`) VALUES
('teacher2', 10, '2021-12-02 16:31:04'),
('teacher', 17, '2021-12-02 18:02:17');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `quiz_id` int(10) UNSIGNED NOT NULL,
  `question` varchar(500) NOT NULL,
  `options` varchar(200) NOT NULL,
  `answer` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`quiz_id`, `question`, `options`, `answer`) VALUES
(13, 'What is the largest animal', 'elephant', 'elephant'),
(13, 'What is the largest animal', 'frog', 'elephant'),
(13, 'What is the largest animal', 'mouse', 'elephant'),
(13, 'What is the largest animal', 'snake', 'elephant'),
(16, 'Red Vines is a popular brand of which candy?', 'Gum', 'Licorice'),
(16, 'Red Vines is a popular brand of which candy?', 'i dont know', 'Licorice'),
(16, 'Red Vines is a popular brand of which candy?', 'Licorice', 'Licorice'),
(16, 'Red Vines is a popular brand of which candy?', 'Lollipop', 'Licorice'),
(16, 'The art of paper folding is called:', 'Collage', 'Origami'),
(16, 'The art of paper folding is called:', 'Creasing', 'Origami'),
(16, 'The art of paper folding is called:', 'Mixed Media', 'Origami'),
(16, 'The art of paper folding is called:', 'Origami', 'Origami'),
(16, 'What is 64/8 reduced to its simplest form?', '16/4', '8'),
(16, 'What is 64/8 reduced to its simplest form?', '32/4', '8'),
(16, 'What is 64/8 reduced to its simplest form?', '4', '8'),
(16, 'What is 64/8 reduced to its simplest form?', '8', '8'),
(16, 'Which of the following is not one of Earths three layers?', 'Core', 'Shell V power'),
(16, 'Which of the following is not one of Earths three layers?', 'Crust', 'Shell V power'),
(16, 'Which of the following is not one of Earths three layers?', 'Mantle', 'Shell V power'),
(16, 'Which of the following is not one of Earths three layers?', 'Shell V power', 'Shell V power'),
(16, 'Which type of cookie usually predicts your future with a sheet of paper inside it?', 'Fortune cookie', 'Fortune cookie'),
(16, 'Which type of cookie usually predicts your future with a sheet of paper inside it?', 'Ice cream cookie', 'Fortune cookie'),
(16, 'Which type of cookie usually predicts your future with a sheet of paper inside it?', 'Pizzelle', 'Fortune cookie'),
(16, 'Which type of cookie usually predicts your future with a sheet of paper inside it?', 'Sugar cookie', 'Fortune cookie');

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `author_username` varchar(255) DEFAULT NULL,
  `available` tinyint(4) NOT NULL,
  `duration` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`id`, `name`, `author_username`, `available`, `duration`) VALUES
(9, '1st quiz', 'teacher', 1, 1),
(13, 'animals', 'teacher2', 1, 5),
(16, 'test quiz 1', 'teacher2', 1, 5);

--
-- Triggers `quiz`
--
DELIMITER $$
CREATE TRIGGER `log_stuff` AFTER DELETE ON `quiz` FOR EACH ROW BEGIN
        INSERT INTO audit
        SET staff_id = OLD.author_username,
            quiz_id = OLD.id,
            date_and_time = NOW();
	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `username` varchar(255) NOT NULL,
  `forename` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `forename`, `surname`, `type`, `password`) VALUES
('student', 'asd', 'asd', 'Student', '$2y$10$3zuTGDJ3vJABkxLshC/A1e1xEjEM8/JpxuRmQGriZS5qSuIkoGdza'),
('student2', 'asd', 'asd', 'Student', '$2y$10$lXyF82GB.pc303kneylZ7uhfR9qtmgovPUyhlXFySxuZHcNwfxl.2'),
('teacher', 'asd', 'asd', 'Teacher', '$2y$10$k0XzdvgT3HUMVltQWnFNX.eoH6jp5TL3ui3ick5Co3muHDK2ATKWW'),
('teacher2', 'joh', 'jm', 'Teacher', '$2y$10$si5gDPEjUGlYd1bChmfWzeYJCnd/IriLz9ktLSoNBBDE2xTReaf0m'),
('teacher3', 'teach', 'er', 'Teacher', '$2y$10$i4XdR90rVFbukThiL.YGNe85D0.qwSFGdioOUtxg8lp/FK3AahAJS');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attempt`
--
ALTER TABLE `attempt`
  ADD PRIMARY KEY (`quiz_id`,`username`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`quiz_id`,`question`,`options`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_username` (`author_username`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attempt`
--
ALTER TABLE `attempt`
  ADD CONSTRAINT `attempt_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attempt_ibfk_2` FOREIGN KEY (`username`) REFERENCES `user` (`username`) ON DELETE CASCADE;

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`author_username`) REFERENCES `user` (`username`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
