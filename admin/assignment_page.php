<?php
session_start();
@include '../inc/config.php';
@include './check_admin.php';
@include '../logout.php';

if (isset($_GET['id'])) {
    $assignment_id = $_GET['id'];

    // Prepare the SQL statement to get assignment details
    $query = "SELECT * FROM assignments WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    // Calculate the remaining time
    $due_date = strtotime($row['due_date']);
    $current_time = time();
    $time_left = $due_date - $current_time;

    if ($time_left < 0) {
        $time_left = 0;
    }

    $days_left = floor($time_left / (60 * 60 * 24));
    $hours_left = floor(($time_left % (60 * 60 * 24)) / (60 * 60));
    $minutes_left = floor(($time_left % (60 * 60)) / 60);

    // Prepare the SQL statement to get the list of users who have submitted the assignment
    $submitted_users_query = "SELECT DISTINCT user.username FROM user INNER JOIN submitted_assignments ON user.username = submitted_assignments.uploader WHERE submitted_assignments.assignment_id = ?";
    $stmt = $conn->prepare($submitted_users_query);
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $submitted_users_result = $stmt->get_result();

    $submitted_users = [];
    while ($submitted_user_row = $submitted_users_result->fetch_assoc()) {
        $submitted_users[] = $submitted_user_row['username'];
    }

    $stmt->close();


} else {
    // Nếu không có ID được truyền, chuyển hướng người dùng trở lại trang danh sách bài tập
    header("Location: show_assignment.php");
    exit();
}
?>

<?php @include '../inc/admin/header.php'; ?>
<section class="p-5">
    <div class="container">
        <div class="d-flex col-md justify-content-center">
            <div class="card bg-light text-dark" style="width: 40rem;">
                <div class="card-body">
                    <h3 class="card-title"><?php echo $row['title']; ?></h3>
                    <p class="card-text"><strong>Description:</strong> <?php echo $row['description']; ?></p>
                    <p class="card-text"><strong>Deadline:</strong> <?php echo $row['due_date']; ?></p>
                    <!-- Hiển thị tệp đã tải lên -->
                    <p>
                    <div class="card-text"><strong>Document:</strong>
                        <?php
                        if (!empty($row['file_name'])) {
                            echo '<a href="' . $row['file_path'] . '" download>' . $row['file_name'] . '</a>';
                        } else {
                            echo 'No file uploaded';
                        }
                        ?>
                    </div>
                    </p>
                    <!-- Kết thúc phần hiển thị tệp -->
                    <p class="card-text"><strong>Assigned Students:</strong>
                        <?php
                        // Truy vấn để lấy danh sách sinh viên được giao bài tập
                        $assign_query = "SELECT student_username FROM assigned_assignments WHERE assignment_id = '$assignment_id'";
                        $assign_result = mysqli_query($conn, $assign_query);
                        $assigned_students = [];
                        while ($assign_row = mysqli_fetch_assoc($assign_result)) {
                            $assigned_students[] = $assign_row['student_username'];
                        }
                        $assigned_students_str = implode(", ", $assigned_students);
                        echo $assigned_students_str;
                        ?>
                    </p>
                    <!-- Hiển thị danh sách người dùng đã nộp bài -->
                    <p class="card-text"><strong>Submitted Students:</strong>
                        <?php
                        if (!empty($submitted_users)) {
                            echo implode(", ", $submitted_users);
                        } else {
                            echo 'No students submitted yet';
                        }
                        ?>
                    </p>
                    <!-- Kết thúc phần hiển thị danh sách người dùng đã nộp bài -->
                    <!-- Hiển thị thời gian còn lại -->
                    <p class="card-text"><strong>Time Left:</strong> <span id="timeLeft">
                            <?php
                            if ($time_left > 0) {
                                echo "$days_left days, $hours_left hours, $minutes_left minutes";
                            } else {
                                echo "Time's up!";
                            }
                            ?>
                        </span>
                    </p>


                    <div class="d-flex justify-content-between">
                        <a href="show_assignment.php" class="btn btn-secondary">Back</a>
                        <a href="view_submitted.php?id=<?php echo $assignment_id; ?>" class="btn btn-primary">View
                            Submitted</a>
                        <div>
                            <a href="delete_assignment.php?id=<?php echo $assignment_id; ?>"
                                class="btn btn-danger">Delete</a>
                            <a href="update_assignment.php?id=<?php echo $assignment_id; ?>"
                                class="btn btn-primary">Update</a>
                        </div>
                    </div>
                    <!-- Script JavaScript để đếm ngược thời gian -->
                    <script>
                        // Hàm cập nhật thời gian còn lại
                        function updateTime() {
                            var timeLeftSpan = document.getElementById("timeLeft");
                            var daysLeft = <?php echo $days_left; ?>;
                            var hoursLeft = <?php echo $hours_left; ?>;
                            var minutesLeft = <?php echo $minutes_left; ?>;

                            // Giảm thời gian còn lại
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
                                        // Nếu hết thời gian, dừng đếm ngược
                                        clearInterval(timer);
                                        timeLeftSpan.textContent = "Time's up!";
                                    }
                                }
                            }

                            // Cập nhật nội dung thẻ span
                            if (daysLeft >= 0 || hoursLeft >= 0 || minutesLeft >= 0) {
                                timeLeftSpan.textContent = daysLeft + " days, " + hoursLeft + " hours, " + minutesLeft + " minutes";
                            } else {
                                timeLeftSpan.textContent = "Time's up!";
                            }
                        }

                        // Gọi hàm updateTime mỗi phút
                        var timer = setInterval(updateTime, 60000);
                    </script>
                </div>
            </div>
        </div>
    </div>
</section>

<?php @include '../inc/footer.php'; ?>