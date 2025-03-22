<?php
session_start();
@include '../inc/config.php';
@include './check_user.php';
@include '../logout.php';


if (isset($_GET['id'])) {
    $assignment_id = $_GET['id'];

    // Prepare the SQL statement to get assignment details
    $stmt = $conn->prepare("SELECT * FROM assignments WHERE id = ?");
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Calculate the remaining time
    $due_date = strtotime($row['due_date']);
    $current_time = time();
    $time_left = $due_date - $current_time;

    // If the remaining time is negative, disable upload
    if ($time_left <= 0) {
        $time_left = 0;
        $upload_disabled = true;
    } else {
        $upload_disabled = false;
    }

    $days_left = floor($time_left / (60 * 60 * 24));
    $hours_left = floor(($time_left % (60 * 60 * 24)) / (60 * 60));
    $minutes_left = floor(($time_left % (60 * 60)) / 60);

    // Check if the user has already submitted the assignment
    $username = $_SESSION['username'];
    $stmt_check_submission = $conn->prepare("SELECT * FROM submitted_assignments WHERE assignment_id = ? AND uploader = ?");
    $stmt_check_submission->bind_param("is", $assignment_id, $username);
    $stmt_check_submission->execute();
    $result_check_submission = $stmt_check_submission->get_result();
    $has_submitted = $result_check_submission->num_rows > 0;

    // If the user has submitted, get the submission details
    $submitted_assignment_info = null;
    if ($has_submitted) {
        $submitted_assignment_info = $result_check_submission->fetch_assoc();
    }

    // Close the statements
    $stmt->close();
    $stmt_check_submission->close();
} else {
    // If no ID is provided, redirect the user back to the assignment list page
    header("Location: show_assignment.php");
    exit();
}


if (isset($_POST['submit']) && !$upload_disabled && !$has_submitted) {
    if (isset($_FILES['submission'])) { 
        
        $allowed_ext = ['pdf', 'doc', 'docx', 'jpg', 'png', 'txt'];
        if (!empty($_FILES['submission']['name'])) {
            $name = $_FILES['submission']['name'];
            $size = $_FILES['submission']['size'];
            $type = $_FILES['submission']['type'];
            $file_tmp = $_FILES['submission']['tmp_name'];
            $target_dir = "../submissions/";
            $file_ext = explode('.', $name);
            $file_ext = strtolower(end($file_ext));

            // Validate file extension
            if (in_array($file_ext, $allowed_ext)) {
                // Validate the size
                if ($size <= 5000000) { // <= 5MB
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $target_file = $target_dir . "${name}";
                    move_uploaded_file($file_tmp, $target_file);
                    $uploader = $_SESSION['username'];

                    // Insert into database using prepared statement
                    $stmt = $conn->prepare("INSERT INTO submitted_assignments (assignment_id, uploader, file_name, file_size, file_type, upload_time) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP())");
                    $stmt->bind_param("isssi", $assignment_id, $uploader, $name, $size, $type);
                    $stmt->execute();
                    $stmt->close();

                    $successes[] = "File uploaded!";
                } else {
                    $errors[] = "File is too big!";
                }
            } else {
                $errors[] = "Invalid file type!";
            }
        } else {
            $errors[] = "No files chosen!";
        }   
    } 
}
?>

<?php @include '../inc/user/header.php'; ?>
<section class="p-5">
    <div class="container">
        <div class="d-flex col-md justify-content-center">
            <div class="card bg-light text-dark" style="width: 40rem;">
                <div class="card-body">
                    <h3 class="card-title"><?php echo $row['title']; ?></h3>
                    <?php
                    if (isset($errors)){
                        foreach($errors as $error){
                            echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                        }
                    }
                    if (isset($successes)){
                        foreach($successes as $success){
                            echo '<div class="alert alert-success" role="alert">'.$success.'</div>';
                        }
                    }
                    ?>
                    <p class="card-text"><strong>Description:</strong> <?php echo $row['description']; ?></p>
                    <p class="card-text"><strong>Deadline:</strong> <?php echo $row['due_date']; ?></p>
                    <p class="card-text"><strong>Document:</strong>
                        <?php
                        if (!empty($row['file_name'])) {
                            echo '<a href="' . $row['file_path'] . '" download>' . $row['file_name'] . '</a>';
                        } else {
                            echo 'No file uploaded';
                        }
                        ?>
                    </p>
                    <p class="card-text"><strong>Time Left:</strong> <span id="timeLeft">
                        <?php 
                        if ($time_left > 0) {
                            echo "$days_left days, $hours_left hours, $minutes_left minutes";
                        } else {
                            echo "Time's up! You can't submit your assignment!";
                        }
                        ?>
                    </span></p>

                    <?php if(!$upload_disabled && !$has_submitted): ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="submission" class="form-label"><strong>Submit Assignment:</strong></label>
                                <input class="form-control" type="file" name="submission">
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary float-end">Submit</button>
                        </form>
                    <?php elseif ($has_submitted): ?>
                        <p class="text-success"><strong>You have already submitted this assignment!!!</strong></p>
                        <p><strong>Submitted File:</strong> <a href="../submissions/<?php echo $submitted_assignment_info['file_name']; ?>" download><?php echo $submitted_assignment_info['file_name']; ?></a></p>
                        <p><strong>Submitted Time:</strong> <?php echo $submitted_assignment_info['upload_time']; ?></p>
                    <?php endif; ?>

                    <a href="show_assignment.php" class="btn btn-secondary">Back</a>
                    <!--đếm ngược thời gian -->
                    <script>
                        function updateTime() {
                            var timeLeftSpan = document.getElementById("timeLeft");
                            var daysLeft = <?php echo $days_left; ?>;
                            var hoursLeft = <?php echo $hours_left; ?>;
                            var minutesLeft = <?php echo $minutes_left; ?>;

                            if (minutesLeft > 0) {
                                minutesLeft--;
                            } else {
                                if (hoursLeft > 0) {
                                    hoursLeft--;
                                    minutesLeft = 59;
                                } else {
                                    if (daysLeft > 0) {
                                        daysLeft--;
                                        hoursLeft = 23;
                                        minutesLeft = 59;
                                    } else {
                                        clearInterval(timer);
                                        timeLeftSpan.textContent = "Time's up!";
                                    }
                                }
                            }

                            if (daysLeft >= 0 || hoursLeft >= 0 || minutesLeft >= 0) {
                                timeLeftSpan.textContent = daysLeft + " days, " + hoursLeft + " hours, " + minutesLeft + " minutes";
                            } else {
                                timeLeftSpan.textContent = "Time's up!";
                            }
                        }

                        var timer = setInterval(updateTime, 60000);
                    </script>
                </div>
            </div>
        </div>
    </div>
</section>

<?php @include '../inc/footer.php'; ?>
