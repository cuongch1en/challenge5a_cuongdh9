<?php
session_start();
include '../inc/config.php';
include './check_user.php';
include '../logout.php';

$username = $_SESSION['username'];

// Prepare statement for getting user ID
$stmt1 = $conn->prepare("SELECT id FROM user WHERE username = ?");
$stmt1->bind_param("s", $username);  // "s" indicates string parameter
$stmt1->execute();
$result1 = $stmt1->get_result();
$row = $result1->fetch_assoc();
$currentUserId = $row['id'];

// Đếm số tin nhắn từ table messages qua id  trừ người gửi.
$stmt2 = $conn->prepare("SELECT COUNT(*) AS new_messages_count FROM messages WHERE receiver_id = ? AND sender_id != ?");
$stmt2->bind_param("ii", $currentUserId, $currentUserId);  // "ii" indicates two integer parameters
$stmt2->execute();
$result2 = $stmt2->get_result();
$countRow = $result2->fetch_assoc();
$newMessagesCount = $countRow['new_messages_count'];

// Close statements
$stmt1->close();
$stmt2->close();
?>

<?php include '../inc/user/header.php'; ?>
<section class="p-5">
    <div class="container">
        <div>
            <div class="d-flex col-md justify-content-center">
                <div class="card bg-dark text-light" style="width: 30rem;">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <img src="../source/img/user.jpg" alt="">
                        </div>
                        <h4 class="card-title mb-3">
                            Hello, <?php echo $_SESSION['username']; ?> !!
                        </h4>
                        <h6>You have <?php echo $newMessagesCount; ?> new messages.</h6>
                        <p></p>
                        <a href="view_messages.php" class="btn btn-primary">View Messages</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include '../inc/footer.php';
?>