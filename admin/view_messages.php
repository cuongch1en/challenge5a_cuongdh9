<?php
session_start();
include '../inc/config.php';
include './check_admin.php';
include '../logout.php';

$username = $_SESSION['username'];
$query = "SELECT id FROM user WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$currentUserId = $row['id'];
$stmt->close();

// Prepare the SQL statement to get messages
$messagesQuery = "SELECT * FROM messages WHERE receiver_id = ? AND sender_id != ?";
$stmt = $conn->prepare($messagesQuery);
$stmt->bind_param("ii", $currentUserId, $currentUserId);
$stmt->execute();
$messagesResult = $stmt->get_result();

// Close the statement
$stmt->close();
?>

<?php include '../inc/admin/header.php'; ?>

<section class="p-5">
    <div class="container">
        <div>
            <h2>Received Messages</h2>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sender</th>
                            <th>Message</th>
                            <th>Sent At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($messagesResult)) {
                            $senderId = $row['sender_id'];
                            $message = $row['message'];
                            $sentAt = $row['sent_at'];

                            $senderQuery = "SELECT username FROM user WHERE id = $senderId";
                            $senderResult = mysqli_query($conn, $senderQuery);
                            $senderRow = mysqli_fetch_assoc($senderResult);
                            $senderUsername = $senderRow['username'];

                            echo "<tr>";
                            echo "<td>$senderUsername</td>";
                            echo "<td>$message</td>";
                            echo "<td>$sentAt</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include '../inc/footer.php'; ?>