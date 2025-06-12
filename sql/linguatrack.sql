-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 12 Haz 2025, 23:43:40
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `linguatrack`
--

DELIMITER $$
--
-- Yordamlar
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddGoal` (IN `p_user_id` INT, IN `p_description` TEXT, IN `p_start_date` DATE, IN `p_end_date` DATE)   BEGIN
    INSERT INTO Goal (user_id, description, start_date, end_date)
    VALUES (p_user_id, p_description, p_start_date, p_end_date);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddStudyLog` (IN `p_user_id` INT, IN `p_duration_minutes` INT, IN `p_skill` ENUM('Kelime','Dilbilgisi','Okuma','Yazma','Dinleme','Konuşma'), IN `p_note` TEXT)   BEGIN
    INSERT INTO StudyLog (user_id, date, duration_minutes, skill, note)
    VALUES (p_user_id, CURDATE(), p_duration_minutes, p_skill, p_note);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddTestResult` (IN `p_user_id` INT, IN `p_test_type` ENUM('Kelime','Dilbilgisi','Seviye'), IN `p_score` INT, IN `p_evaluation` VARCHAR(100))   BEGIN
    INSERT INTO Test (user_id, test_type, test_date, score, evaluation)
    VALUES (p_user_id, p_test_type, CURDATE(), p_score, p_evaluation);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddVocabulary` (IN `p_user_id` INT, IN `p_word` VARCHAR(100), IN `p_meaning` VARCHAR(200), IN `p_example_sentence` TEXT)   BEGIN
    INSERT INTO Vocabulary (user_id, word, meaning, example_sentence, added_date)
    VALUES (p_user_id, p_word, p_meaning, p_example_sentence, CURDATE());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserStatistics` (IN `p_user_id` INT)   BEGIN
    SELECT 
        u.name,
        u.target_language,
        u.current_level,
        u.total_study_time,
        u.total_words,
        CalculateStudyStreak(p_user_id) as current_streak,
        (SELECT COUNT(*) FROM Goal WHERE user_id = p_user_id AND status = 'Tamamlandı') as completed_goals,
        (SELECT COUNT(*) FROM Test WHERE user_id = p_user_id) as total_tests,
        (SELECT AVG(score) FROM Test WHERE user_id = p_user_id) as average_test_score
    FROM `User` u
    WHERE u.user_id = p_user_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegisterUser` (IN `p_name` VARCHAR(100), IN `p_email` VARCHAR(100), IN `p_password` VARCHAR(100), IN `p_target_language` VARCHAR(50), IN `p_current_level` VARCHAR(10), IN `p_goal_level` VARCHAR(10))   BEGIN
    INSERT INTO `User` (name, email, password, target_language, current_level, goal_level, registration_date)
    VALUES (p_name, p_email, p_password, p_target_language, p_current_level, p_goal_level, CURDATE());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateVocabularyStatus` (IN `p_vocab_id` INT, IN `p_status` ENUM('Öğreniliyor','Ezberlendi','Unutuldu'))   BEGIN
    UPDATE Vocabulary 
    SET status = p_status,
        last_review_date = CURDATE()
    WHERE vocab_id = p_vocab_id;
END$$

--
-- İşlevler
--
CREATE DEFINER=`root`@`localhost` FUNCTION `CalculateStudyStreak` (`user_id_param` INT) RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE streak INT DEFAULT 0;
    DECLARE last_study_date DATE;

    SELECT MAX(date) INTO last_study_date
    FROM StudyLog
    WHERE user_id = user_id_param;

    IF last_study_date IS NULL THEN
        RETURN 0;
    END IF;

    WHILE EXISTS (
        SELECT 1 FROM StudyLog 
        WHERE user_id = user_id_param 
        AND date = last_study_date
    ) DO
        SET streak = streak + 1;
        SET last_study_date = DATE_SUB(last_study_date, INTERVAL 1 DAY);
    END WHILE;

    RETURN streak;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `goal`
--

CREATE TABLE `goal` (
  `goal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Devam Ediyor','Tamamlandı','İptal') DEFAULT 'Devam Ediyor',
  `progress` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `goal`
--

INSERT INTO `goal` (`goal_id`, `user_id`, `description`, `start_date`, `end_date`, `status`, `progress`) VALUES
(1, 1, 'prepositions of time', '2025-06-11', '2025-06-18', 'Devam Ediyor', 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `studylog`
--

CREATE TABLE `studylog` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `skill` enum('Kelime','Dilbilgisi','Okuma','Yazma','Dinleme','Konuşma') NOT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `studylog`
--

INSERT INTO `studylog` (`log_id`, `user_id`, `date`, `duration_minutes`, `skill`, `note`) VALUES
(1, 1, '2025-06-11', 30, 'Konuşma', '');

--
-- Tetikleyiciler `studylog`
--
DELIMITER $$
CREATE TRIGGER `after_studylog_insert` AFTER INSERT ON `studylog` FOR EACH ROW BEGIN
    UPDATE `User` 
    SET total_study_time = total_study_time + NEW.duration_minutes
    WHERE user_id = NEW.user_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `test`
--

CREATE TABLE `test` (
  `test_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_type` enum('Kelime','Dilbilgisi','Seviye') NOT NULL,
  `test_date` date NOT NULL,
  `score` int(11) NOT NULL,
  `evaluation` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `test`
--

INSERT INTO `test` (`test_id`, `user_id`, `test_type`, `test_date`, `score`, `evaluation`) VALUES
(1, 1, 'Kelime', '2025-06-11', 80, 'Eksikler gözden geçirilmeli'),
(2, 1, 'Kelime', '2025-06-12', 90, ''),
(3, 1, 'Kelime', '2025-06-12', 95, 'Eksikler gözden geçirilmeli');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `target_language` varchar(50) NOT NULL,
  `current_level` varchar(10) NOT NULL,
  `goal_level` varchar(10) NOT NULL,
  `registration_date` date NOT NULL,
  `total_study_time` int(11) DEFAULT 0,
  `total_words` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `target_language`, `current_level`, `goal_level`, `registration_date`, `total_study_time`, `total_words`) VALUES
(1, 'Sena', 'ssttemir@icloud.com', '$2y$10$9EvgOZPuY5A4gIarOPgDk.RMj99fgpxyRRkgbJ7jBarSB9GvF/IB2', 'İngilizce', 'B1', 'C2', '2025-06-11', 60, 1),
(2, 'Giyuu', 'ssttemir@gmail.com', '$2y$10$8/qcEVDtlJydvY1r4acZUeobURR.LqWBpYRHZt7ruSEqJ11pdM9Gu', 'İngilizce', 'A1', 'A2', '2025-06-12', 0, 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `vocabulary`
--

CREATE TABLE `vocabulary` (
  `vocab_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `word` varchar(100) NOT NULL,
  `meaning` varchar(200) NOT NULL,
  `example_sentence` text DEFAULT NULL,
  `status` enum('Öğreniliyor','Ezberlendi','Unutuldu') DEFAULT 'Öğreniliyor',
  `added_date` date NOT NULL,
  `last_review_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `vocabulary`
--

INSERT INTO `vocabulary` (`vocab_id`, `user_id`, `word`, `meaning`, `example_sentence`, `status`, `added_date`, `last_review_date`) VALUES
(1, 1, 'blind', 'kör', 'she is color blind', 'Öğreniliyor', '2025-06-11', NULL);

--
-- Tetikleyiciler `vocabulary`
--
DELIMITER $$
CREATE TRIGGER `after_vocabulary_status_update` AFTER UPDATE ON `vocabulary` FOR EACH ROW BEGIN
    IF NEW.status = 'Ezberlendi' AND OLD.status != 'Ezberlendi' THEN
        UPDATE `User` 
        SET total_words = total_words + 1
        WHERE user_id = NEW.user_id;
    ELSEIF NEW.status != 'Ezberlendi' AND OLD.status = 'Ezberlendi' THEN
        UPDATE `User` 
        SET total_words = total_words - 1
        WHERE user_id = NEW.user_id;
    END IF;
END
$$
DELIMITER ;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `goal`
--
ALTER TABLE `goal`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `idx_goal_user` (`user_id`);

--
-- Tablo için indeksler `studylog`
--
ALTER TABLE `studylog`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_studylog_user` (`user_id`);

--
-- Tablo için indeksler `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `idx_test_user` (`user_id`);

--
-- Tablo için indeksler `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `vocabulary`
--
ALTER TABLE `vocabulary`
  ADD PRIMARY KEY (`vocab_id`),
  ADD KEY `idx_vocab_user` (`user_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `goal`
--
ALTER TABLE `goal`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `studylog`
--
ALTER TABLE `studylog`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `test`
--
ALTER TABLE `test`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `vocabulary`
--
ALTER TABLE `vocabulary`
  MODIFY `vocab_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `goal`
--
ALTER TABLE `goal`
  ADD CONSTRAINT `goal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `studylog`
--
ALTER TABLE `studylog`
  ADD CONSTRAINT `studylog_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `test_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `vocabulary`
--
ALTER TABLE `vocabulary`
  ADD CONSTRAINT `vocabulary_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
