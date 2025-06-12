<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
checkLogin();

if (isset($_GET['id'])) {
    $vocabId = intval($_GET['id']);
    $userId = $_SESSION['user_id'];

    // Önce kelimenin o kullanıcıya ait olup olmadığını kontrol et
    $sql = "SELECT user_id FROM Vocabulary WHERE vocab_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vocabId);
    $stmt->execute();
    $stmt->bind_result($ownerId);
    $stmt->fetch();
    $stmt->close();

    if ($ownerId == $userId) {
        // Silme işlemi
        $del = $conn->prepare("DELETE FROM Vocabulary WHERE vocab_id = ?");
        $del->bind_param("i", $vocabId);
        $del->execute();
        $del->close();

        $_SESSION['success'] = "Kelime silindi.";
    } else {
        $_SESSION['error'] = "Bu kelimeyi silmeye yetkin yok.";
    }
} else {
    $_SESSION['error'] = "Geçersiz istek.";
}

header("Location: ../vocabulary.php");
exit();
?>
