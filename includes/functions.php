<?php
// Sadece ilk kez yüklensin
if (!function_exists('checkLogin')) {
    function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit();
        }
    }
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function displayAlert($message, $type = 'success') {
    return "<div class='alert alert-$type alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
}
?>
