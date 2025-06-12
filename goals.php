<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';
checkLogin();

$user_id = $_SESSION['user_id'];

// Hedef Ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $conn->real_escape_string($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $conn->query("INSERT INTO Goal (user_id, description, start_date, end_date) VALUES ($user_id, '$description', '$start_date', '$end_date')");
}

// Hedefleri Listele
$result = $conn->query("SELECT * FROM Goal WHERE user_id = $user_id ORDER BY start_date DESC");
?>

<div class="container py-5">
    <h2 class="mb-4 text-center" style="color: var(--highlight);">🎯 Hedeflerim</h2>

    <div class="card p-4 mb-5 shadow-sm">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Hedef Açıklaması</label>
                    <input type="text" name="description" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Başlangıç Tarihi</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Bitiş Tarihi</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">Hedef Ekle</button>
            </div>
        </form>
    </div>

    <?php if ($result->num_rows > 0): ?>
    <div class="card p-4 shadow-sm">
        <h5 class="mb-3">📌 Kayıtlı Hedefler</h5>
        <ul class="list-group list-group-flush">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?php echo htmlspecialchars($row['description']); ?></strong><br>
                        <small><?php echo $row['start_date']; ?> → <?php echo $row['end_date']; ?></small>
                    </div>
                    <span class="badge 
                        <?php
                            switch ($row['status']) {
                                case 'Tamamlandı': echo 'badge-success'; break;
                                case 'İptal': echo 'badge-danger'; break;
                                default: echo 'badge-warning';
                            }
                        ?>">
                        <?php echo $row['status']; ?>
                    </span>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <?php else: ?>
        <p class="text-muted text-center">Henüz bir hedef eklenmedi.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
