<?php
session_start();
@include '../inc/config.php';
@include './check_user.php';
@include '../logout.php';

$student_username = $_SESSION['username'];

$query = "SELECT DISTINCT assignments.*, assigned_assignments.student_username,
              (SELECT COUNT(*) FROM submitted_assignments WHERE submitted_assignments.assignment_id = assignments.id AND submitted_assignments.uploader = ?) AS submitted
              FROM assignments
              INNER JOIN assigned_assignments ON assignments.id = assigned_assignments.assignment_id
              WHERE assigned_assignments.student_username = ?";

// Initialize the prepared statement
$stmt = $conn->prepare($query);

// Bind the parameters
$stmt->bind_param("ss", $student_username, $student_username);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();
?>

<?php @include '../inc/user/header.php'; ?>
<section class="p-5">
    <div class="container">
        <div>
            <div class="d-flex col-md justify-content-center">
                <div class="card bg-light text-dark" style="width: 80rem;">
                    <div class="card-body text-center">
                        <h3 class="text-center">My Assignments</h3>
                        <div class="mb-3">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Assignment Title</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Deadline</th>
                                        <th scope="col">Completed</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td><a href='assignment_page.php?id={$row['id']}'>{$row['title']}</a></td>";
                                        echo "<td>{$row['description']}</td>";
                                        echo "<td>{$row['due_date']}</td>";
                                        if ($row['submitted'] > 0) {
                                            echo "<td>Yes</td>";
                                        } else {
                                            echo "<td>No</td>";
                                        }
                                        echo "<td><a href='assignment_page.php?id={$row['id']}' class='btn btn-primary'>Submit</a></td>"; // Link to upload_assignment.php
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php @include '../inc/footer.php'; ?>