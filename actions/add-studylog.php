<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $duration = intval($_POST['duration']);
    $skill = sanitizeInput($_POST['skill']);
    $note = sanitizeInput($_POST['note']);
    $userId = $_SESSION['user_id'];

    $sql = "INSERT INTO StudyLog (user_id, date, duration_minutes, skill, note)
            VALUES (?, CURDATE(), ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $userId, $duration, $skill, $note);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Çalışma kaydı eklendi!";
    } else {
        $_SESSION['error'] = "Hata oluştu: " . $stmt->error;
    }

    $stmt->close();
    header("Location: ../studylog.php");
    exit();
}
?>
