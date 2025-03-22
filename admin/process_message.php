<?php
session_start();
@include '../inc/config.php';
@include './check_admin.php';
@include '../logout.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id_query = "SELECT id FROM user WHERE username = ?";
    $stmt_sender = $conn->prepare($sender_id_query);
    $stmt_sender->bind_param("s", $_SESSION['username']);
    $stmt_sender->execute();
    $result_sender = $stmt_sender->get_result();
    $sender_id = $result_sender->fetch_assoc()['id'];

    $receiver_id = $_POST['recipient_id'];
    $message = $_POST['message'];

    $insert_query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);

    if ($stmt->execute()) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false, "message" => "Can't send your message!"));
    }

    $stmt_sender->close();
    $stmt->close();
    $conn->close();
    exit();
}
?>