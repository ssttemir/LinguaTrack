<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = sanitizeInput($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    try {
        $stmt = $pdo->prepare("INSERT INTO Goal (user_id, description, start_date, end_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $description, $start_date, $end_date]);

        $_SESSION['success'] = "Hedef başarıyla eklendi!";
        header("Location: ../goals.php");
        exit();
    } catch(PDOException $e) {
        $_SESSION['error'] = "Hedef eklenirken hata oluştu: " . $e->getMessage();
        header("Location: ../goals.php");
        exit();
    }
}
?>
