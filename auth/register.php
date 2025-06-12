<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $target_language = sanitizeInput($_POST['target_language']);
    $current_level = sanitizeInput($_POST['current_level']);
    $goal_level = sanitizeInput($_POST['goal_level']);

    // E-posta zaten kayıtlı mı kontrol et
    $stmt = $conn->prepare("SELECT COUNT(*) FROM User WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $_SESSION['error'] = "Bu e-posta adresi zaten kayıtlı!";
        header("Location: ../index.php");
        exit();
    }

    // Yeni kullanıcı ekle
    $stmt = $conn->prepare("INSERT INTO User (name, email, password, target_language, current_level, goal_level, registration_date) VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
    $stmt->bind_param("ssssss", $name, $email, $password, $target_language, $current_level, $goal_level);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Kayıt başarılı! Şimdi giriş yapabilirsiniz.";
    } else {
        $_SESSION['error'] = "Kayıt sırasında bir hata oluştu: " . $stmt->error;
    }

    $stmt->close();
    header("Location: ../index.php");
    exit();
}
?>
