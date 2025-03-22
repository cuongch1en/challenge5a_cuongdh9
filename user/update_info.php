<?php
session_start();
@include '../inc/config.php';
@include './check_user.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$email = $_POST['email'];
$phone = $_POST['phone'];

$username = $_SESSION['username'];


$stmt = $conn->prepare("UPDATE user SET email = ?, phone = ? WHERE username = ?");
$stmt->bind_param("sss", $email, $phone, $username);
$stmt->execute();
$stmt->close();


?>
