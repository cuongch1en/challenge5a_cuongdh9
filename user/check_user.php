<?php
if ($_SESSION['role'] !== 'user') {
    // Điều hướng đến trang lỗi error_page.php
    header("Location: ../error_page.php");
    exit;
}
?>
