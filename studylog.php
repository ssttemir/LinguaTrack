<?php
require_once 'includes/header.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
checkLogin();

// Çalışma kaydı ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $duration = $_POST['duration'];
    $skill = $_POST['skill'];
    $note = $_POST['note'];

    $stmt = $conn->prepare("CALL InsertStudyLog(?, ?, ?, ?)");
    $stmt->bind_param("isis", $_SESSION['user_id'], $date, $duration, $notes);
    $stmt->execute();
    $stmt->close();
    header("Location: studylog.php");
    exit();
}

// Geçmiş çalışma kayıtları
$stmt = $conn->prepare("SELECT * FROM StudyLog WHERE user_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
    <h2 class="text-center mb-4">📝 Çalışma Kayıtları</h2>

    <form method="POST" class="card p-4 mb-5 shadow-sm">
        <h5 class="mb-3">📌 Yeni Çalışma Ekle</h5>
        <div class="mb-3">
            <label class="form-label">Süre (dakika)</label>
            <input type="number" class="form-control" name="duration" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Beceri</label>
            <select class="form-select" name="skill" required>
                <option value="Kelime">Kelime</option>
                <option value="Dilbilgisi">Dilbilgisi</option>
                <option value="Okuma">Okuma</option>
                <option value="Yazma">Yazma</option>
                <option value="Dinleme">Dinleme</option>
                <option value="Konuşma">Konuşma</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Not</label>
            <textarea class="form-control" name="note" rows="2"></textarea>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </div>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <div class="card p-4 shadow-sm">
            <h5 class="mb-3">📄 Geçmiş Kayıtlar</h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Süre</th>
                            <th>Beceri</th>
                            <th>Not</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d.m.Y', strtotime($row['date'])) ?></td>
                                <td><?= htmlspecialchars($row['duration_minutes']) ?> dk</td>
                                <td><?= htmlspecialchars($row['skill']) ?></td>
                                <td><?= htmlspecialchars($row['note']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
