<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>LinguaTrack</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- 🌸 Stilize edilmiş üst kutu -->
<div class="container mt-3">
    <div class="d-flex justify-content-between align-items-center px-4 py-3 rounded shadow-sm" style="background: white;">
        <div class="fw-bold fs-4" style="color: var(--highlight);">
            🌸 LinguaTrack
        </div>
        <div class="d-flex gap-3">
            <a href="dashboard.php" class="text-decoration-none text-dark" title="Anasayfa"><i class="fas fa-home fa-lg"></i></a>
            <a href="goals.php" class="text-decoration-none text-dark" title="Hedefler"><i class="fas fa-bullseye fa-lg"></i></a>
            <a href="vocabulary.php" class="text-decoration-none text-dark" title="Kelimeler"><i class="fas fa-book fa-lg"></i></a>
            <a href="studylog.php" class="text-decoration-none text-dark" title="Çalışma Kaydı"><i class="fas fa-edit fa-lg"></i></a>
            <a href="tests.php" class="text-decoration-none text-dark" title="Testler"><i class="fas fa-vial fa-lg"></i></a>
           <a href="/linguatrack/logout.php" class="text-decoration-none text-danger" title="Çıkış">
    <i class="fas fa-sign-out-alt fa-lg"></i>
</a>

        </div>
    </div>
</div>
