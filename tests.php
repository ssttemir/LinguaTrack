<?php
require_once 'includes/header.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
checkLogin();

// Test ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['test_type'];
    $score = $_POST['score'];
    $evaluation = $_POST['evaluation'];

    $stmt = $conn->prepare("INSERT INTO Test (user_id, test_type, score, evaluation, test_date) VALUES (?, ?, ?, ?, CURDATE())");
    $stmt->bind_param("isis", $_SESSION['user_id'], $type, $score, $evaluation);
    $stmt->execute();
    $stmt->close();
    header("Location: tests.php");
    exit();
}

// Test geçmişi
$stmt = $conn->prepare("SELECT * FROM Test WHERE user_id = ? ORDER BY test_date DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
    <h2 class="text-center mb-4">🧪 Test Sonuçları</h2>

    <form method="POST" class="card p-4 mb-5 shadow-sm">
        <h5 class="mb-3">📌 Yeni Test Sonucu Ekle</h5>
        <div class="mb-3">
            <label class="form-label">Test Türü</label>
            <select class="form-select" name="test_type" required>
                <option value="Kelime">Kelime</option>
                <option value="Dilbilgisi">Dilbilgisi</option>
                <option value="Seviye">Seviye</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Puan</label>
            <input type="number" class="form-control" name="score" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Değerlendirme (isteğe bağlı)</label>
            <input type="text" class="form-control" name="evaluation">
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </div>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <div class="card p-4 shadow-sm">
            <h5 class="mb-3">📄 Geçmiş Testler</h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Tür</th>
                            <th>Puan</th>
                            <th>Not</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d.m.Y', strtotime($row['test_date'])) ?></td>
                                <td><?= htmlspecialchars($row['test_type']) ?></td>
                                <td><?= htmlspecialchars($row['score']) ?></td>
                                <td><?= htmlspecialchars($row['evaluation']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
