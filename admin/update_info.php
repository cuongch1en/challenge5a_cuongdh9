<?php
session_start();
@include '../inc/config.php';
@include './check_admin.php';

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
