<?php
session_start();
@include '../inc/config.php';
@include './check_admin.php';
@include '../logout.php';

  $select = "SELECT * FROM user ORDER BY type ASC";
  $stmt = $conn->prepare($select);
// Execute the statement
$stmt->execute();
// Get the result
$result = $stmt->get_result();
// Fetch all data
$users = $result->fetch_all(MYSQLI_ASSOC);
// Close the statement
$stmt->close();
?>

<?php
@include '../inc/admin/header.php';
?>

<section class="p-5">
    <div class="container">
        <?php foreach ($users as $user): ?>
            <div class="d-flex col-md justify-content-center mb-4">
                <div class="card bg-dark text-light" style="min-width: 45rem;">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar" style="flex: 0 0 100px; margin-right: 40px;">
                            <img src="<?php echo '../avatar_user/' . $user['avatar']; ?>" alt="Avatar" style="width: 150px; height: 150px; border-radius: 40%;">
                        </div>
                        <div class="user-info flex-grow-1">
                            <div class="d-flex justify-content-around">
                                <div class="text-end" style="min-width: 10rem;">
                                    <h6 class="text-start card-title" style="margin-top: 0.1rem;">
                                        Username:
                                    </h6>
                                </div>
                                <div class="text-end" style="min-width: 10rem;">
                                    <?php echo $user['username']; ?>
                                </div>
                            </div>
                            <div class="d-flex justify-content-around">
                                <div class="text-end" style="min-width: 10rem;">
                                    <h6 class="text-start card-title" style="margin-top: 0.1rem;">
                                        Name:
                                    </h6>
                                </div>
                                <div class="text-end" style="min-width: 10rem;">
                                    <?php echo $user['name']; ?>
                                </div>
                            </div>
                            <div class="d-flex justify-content-around">
                                <div class="text-end" style="min-width: 10rem;">
                                    <h6 class="text-start card-title" style="margin-top: 0.1rem;">
                                        Email:
                                    </h6>
                                </div>
                                <div class="text-end" style="min-width: 10rem;">
                                    <?php echo $user['email']; ?>
                                </div>
                            </div>
                            <div class="d-flex justify-content-around">
                                <div class="text-end" style="min-width: 10rem;">
                                    <h6 class="text-start card-title" style="margin-top: 0.1rem;">
                                        Phone Number:
                                    </h6>
                                </div>
                                <div class="text-end" style="min-width: 10rem;">
                                    <?php echo $user['phone']; ?>
                                </div>
                            </div>
                            <div class="d-flex justify-content-around">
                                <div class="text-end" style="min-width: 10rem;">
                                    <h6 class="text-start card-title" style="margin-top: 0.1rem;">
                                        Role:
                                    </h6>
                                </div>
                                <div class="text-end" style="min-width: 10rem;">
                                    <?php echo $user['type']; ?>
                                </div>
                            </div>
                            <p></p>
                            <div class="d-flex justify-content-end">
                                <div class="text-end" style="min-width: 10rem;">
                                    <!-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#messageModal_<?php echo $user['id']; ?>">Give a message</button> -->
                                    <button class="btn btn-primary btn-give-message" data-bs-toggle="modal" data-bs-target="#messageModal_<?php echo $user['id']; ?>">Give a message</button>

                                </div>
                            </div>
                            <div class="modal fade" id="messageModal_<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="messageModalLabel"><strong style="color: black;">Give a message for <?php echo $user['username']; ?></strong></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="messageForm_<?php echo $user['id']; ?>" action="process_message.php" method="POST">
                                                <input type="hidden" name="recipient_id" value="<?php echo $user['id']; ?>">
                                                <textarea class="form-control" name="message" rows="3" placeholder="Write your message here"></textarea>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Send message</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php @include '../inc/footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".btn-give-message").forEach(button => {
            button.addEventListener("click", function() {
                const modalId = this.getAttribute("data-bs-target");
                const modal = document.querySelector(modalId);
                if (modal) {
                    const form = modal.querySelector("form");
                    if (form) {
                        form.addEventListener("submit", function(event) {
                            event.preventDefault();
                            const formData = new FormData(this);
                            fetch(this.action, {
                                method: this.method,
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert("Send message successfully!");
                                    // Close modal after sending message
                                    const modal = document.querySelector(modalId);
                                    if (modal) {
                                        const modalBS = bootstrap.Modal.getInstance(modal);
                                        modalBS.hide();
                                    }
                                } 
                                else {
                                    alert(data.message || "Can't send your message!");
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert("Can't send your message!");
                            });
                        });
                    }
                }
            });
        });
    });
</script>

