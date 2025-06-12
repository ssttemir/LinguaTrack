<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $testType = sanitizeInput($_POST['test_type']);
    $score = intval($_POST['score']);
    $evaluation = sanitizeInput($_POST['evaluation']);

    $sql = "INSERT INTO Test (user_id, test_date, test_type, score, evaluation)
            VALUES (?, CURDATE(), ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isis", $userId, $testType, $score, $evaluation);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Test sonucu başarıyla eklendi!";
    } else {
        $_SESSION['error'] = "Bir hata oluştu: " . $stmt->error;
    }

    $stmt->close();
    header("Location: ../tests.php");
    exit();
} else {
    $_SESSION['error'] = "Geçersiz istek!";
    header("Location: ../tests.php");
    exit();
}
?>
