<?php
    if ($_SESSION['role'] !== 'admin') {
       // Kiểm tra xem có quyền admin không ?
        header("Location: ../error_page.php");
        exit; 
    }
?>
