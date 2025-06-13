<?php
require_once 'includes/header.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
checkLogin();

// Kelime ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $word = sanitizeInput($_POST['word']);
    $meaning = sanitizeInput($_POST['meaning']);
    $example = sanitizeInput($_POST['example']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("CALL AddNewWord(?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $_SESSION['user_id'], $word, $meaning, $example, $status);
    $stmt->execute();
    $stmt->close();

    header("Location: vocabulary.php");
    exit();
}

// Kelimeleri çek
$stmt = $conn->prepare("SELECT * FROM Vocabulary WHERE user_id = ? ORDER BY added_date DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
    <div class="row">
        <!-- 📝 Form -->
        <div class="col-md-6 mb-4">
            <div class="card p-4">
                <h4 class="mb-3">🌸 Yeni Kelime Ekle</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Kelime</label>
                        <input type="text" class="form-control" name="word" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Anlamı</label>
                        <input type="text" class="form-control" name="meaning" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Örnek Cümle</label>
                        <textarea class="form-control" name="example" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select class="form-select" name="status" required>
                            <option value="Öğreniliyor">Öğreniliyor</option>
                            <option value="Ezberlendi">Ezberlendi</option>
                            <option value="Unutuldu">Unutuldu</option>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 📚 Liste -->
        <div class="col-md-6 mb-4">
            <div class="card p-4">
                <h4 class="mb-3">📖 Kelimelerim</h4>
                <?php if ($result->num_rows > 0): ?>
                    <ul class="list-group">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                            $badgeClass = match($row['status']) {
                                'Ezberlendi' => 'badge-success',
                                'Unutuldu' => 'badge-danger',
                                default => 'badge-warning'
                            };
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold"><?= htmlspecialchars($row['word']) ?></div>
                                    <?= htmlspecialchars($row['meaning']) ?><br>
                                    <small class="text-muted"><?= nl2br(htmlspecialchars($row['example_sentence'])) ?></small>
                                </div>
                                <span class="badge <?= $badgeClass ?>"><?= $row['status'] ?></span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Henüz kelime eklenmedi.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
