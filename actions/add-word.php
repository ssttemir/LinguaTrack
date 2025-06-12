<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $word = sanitizeInput($_POST['word']);
    $meaning = sanitizeInput($_POST['meaning']);
    $example = sanitizeInput($_POST['example_sentence']);
    $userId = $_SESSION['user_id'];

    $sql = "INSERT INTO Vocabulary (user_id, word, meaning, example_sentence, added_date)
            VALUES (?, ?, ?, ?, CURDATE())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $userId, $word, $meaning, $example);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Kelime başarıyla eklendi!";
    } else {
        $_SESSION['error'] = "Bir hata oluştu: " . $stmt->error;
    }

    $stmt->close();
    header("Location: ../vocabulary.php");
    exit();
}
?>


