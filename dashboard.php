<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';
checkLogin();

$user_id = $_SESSION['user_id'];

// Hedef sayısı
$goal_result = $conn->query("SELECT COUNT(*) AS total FROM Goal WHERE user_id = $user_id");
$goal_count = ($goal_result && $goal_result->num_rows > 0) ? $goal_result->fetch_assoc()['total'] : 0;

// Kelime sayısı
$vocab_result = $conn->query("SELECT COUNT(*) AS total FROM Vocabulary WHERE user_id = $user_id");
$vocab_count = ($vocab_result && $vocab_result->num_rows > 0) ? $vocab_result->fetch_assoc()['total'] : 0;

// Toplam çalışma süresi
$study_result = $conn->query("SELECT SUM(duration_minutes) AS total FROM StudyLog WHERE user_id = $user_id");
$study_time = ($study_result && $study_result->num_rows > 0) ? $study_result->fetch_assoc()['total'] : 0;

// Test sayısı
$test_result = $conn->query("SELECT COUNT(*) AS total FROM Test WHERE user_id = $user_id");
$test_count = ($test_result && $test_result->num_rows > 0) ? $test_result->fetch_assoc()['total'] : 0;
?>

<div class="container py-5">
    <h2 class="mb-4 text-center" style="color: var(--highlight);">📊 Genel Durum</h2>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">🎯 Hedefler</h5>
                    <p class="display-6 fw-bold"><?php echo $goal_count; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">📚 Kelimeler</h5>
                    <p class="display-6 fw-bold"><?php echo $vocab_count; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">⏱️ Süre</h5>
                    <p class="display-6 fw-bold"><?php echo $study_time ?? 0; ?> dk</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">🧪 Testler</h5>
                    <p class="display-6 fw-bold"><?php echo $test_count; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
