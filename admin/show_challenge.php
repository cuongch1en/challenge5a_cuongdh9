<?php
session_start();
@include '../inc/config.php';
@include './check_admin.php';
@include '../logout.php';

// Function to delete a challenge
// function deleteChallenge($conn, $challenge_id) {
//     $query = "DELETE FROM challenges WHERE id = '$challenge_id'";
//     mysqli_query($conn, $query);
// }

// // Fetch challenges from the database
// $query = "SELECT * FROM challenges";
// $result = mysqli_query($conn, $query);
// $challenges = mysqli_fetch_all($result, MYSQLI_ASSOC);

// // Check if delete button is clicked
// if (isset($_GET['delete'])) {
//     $challenge_id = $_GET['delete'];
//     deleteChallenge($conn, $challenge_id);
//     header("Location: show_challenge.php");
// }

function deleteChallenge($conn, $challenge_id)
{
    // Prepare the SQL statement to delete a challenge
    $query = "DELETE FROM challenges WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $challenge_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch challenges from the database
$query = "SELECT * FROM challenges";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$challenges = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Check if delete button is clicked
if (isset($_GET['delete'])) {
    $challenge_id = $_GET['delete'];
    deleteChallenge($conn, $challenge_id);
    header("Location: show_challenge.php");
    exit();
}


?>

<?php @include '../inc/admin/header.php'; ?>
<section class="p-5">
    <div class="container">
        <div>
            <div class="d-flex col-md justify-content-center">
                <div class="card bg-light text-dark" style="width: 70rem;">
                    <div class="card-body text-center">
                        <h3 class="text-center">Challenges List</h3>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Challenge Name</th>
                                        <th scope="col">Hint</th>
                                        <th scope="col">Answer</th>
                                        <th scope="col">Uploader</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($challenges as $key => $challenge): ?>
                                        <tr>
                                            <th scope="row"><?php echo $key + 1; ?></th>
                                            <td><?php echo $challenge['challenge_name']; ?></td>
                                            <td><?php echo $challenge['hint']; ?></td>
                                            <td><?php echo $challenge['answer']; ?></td>
                                            <td><?php echo $challenge['uploader']; ?></td>
                                            <td>
                                                <a href="update_challenge.php?id=<?php echo $challenge['id']; ?>"
                                                    class="btn btn-primary btn-sm">Update</a>
                                                <a href="show_challenge.php?delete=<?php echo $challenge['id']; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this challenge?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
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