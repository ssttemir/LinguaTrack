<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>LinguaTrack - Giriş/Kayıt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow rounded-lg">
                <div class="card-header text-center bg-soft-pink">
                    <h3 class="fw-bold" style="color: var(--highlight);">🌸 LinguaTrack</h3>
                    <p class="mb-0 text-muted">Dil öğrenme yolculuğunuza hoş geldiniz!</p>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills nav-justified mb-4" id="authTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="pill" data-bs-target="#login">Giriş Yap</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="register-tab" data-bs-toggle="pill" data-bs-target="#register">Kayıt Ol</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="authTabsContent">
                        <div class="tab-pane fade show active" id="login">
                            <form action="auth/login.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">E-posta</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Şifre</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Giriş Yap</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="register">
                            <form action="auth/register.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Ad Soyad</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">E-posta</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Şifre</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Hedef Dil</label>
                                    <select class="form-select" name="target_language" required>
                                        <option value="İngilizce">İngilizce</option>
                                        <option value="Japonca">Japonca</option>
                                        <option value="İspanyolca">İspanyolca</option>
                                        <option value="Almanca">Almanca</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mevcut Seviye</label>
                                    <select class="form-select" name="current_level" required>
                                        <option value="A1">A1</option>
                                        <option value="A2">A2</option>
                                        <option value="B1">B1</option>
                                        <option value="B2">B2</option>
                                        <option value="C1">C1</option>
                                        <option value="C2">C2</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Hedef Seviye</label>
                                    <select class="form-select" name="goal_level" required>
                                        <option value="A2">A2</option>
                                        <option value="B1">B1</option>
                                        <option value="B2">B2</option>
                                        <option value="C1">C1</option>
                                        <option value="C2">C2</option>
                                    </select>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Kayıt Ol</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>