<?php
session_start();
@include '../inc/config.php';
@include './check_admin.php';
@include '../logout.php';

function updateChallenge($conn, $challenge_id, $name, $hint, $file_name, $file_tmp) {
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    if ($file_extension !== 'txt') {
        $errors[] = "You can only upload .txt files";
    } else {
        $answer = pathinfo($file_name, PATHINFO_FILENAME);
        $upload_folder = "../challenges/";
        $file_path = $upload_folder . $file_name;
        move_uploaded_file($file_tmp, $file_path);

        // Prepare the SQL statement to update the challenge
        $query = "UPDATE challenges SET challenge_name = ?, hint = ?, file_name = ?, file_path = ?, answer = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $name, $hint, $file_name, $file_path, $answer, $challenge_id);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['submit'])) {
    $challenge_id = $_GET['id'];
    $name = $_POST['name'];
    $hint = $_POST['hint'];
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    updateChallenge($conn, $challenge_id, $name, $hint, $file_name, $file_tmp);
    $successes[] = "Update challenge successfully!";
}

$challenge_id = $_GET['id'];

// Prepare the SQL statement to select the challenge
$query = "SELECT * FROM challenges WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $challenge_id);
$stmt->execute();
$result = $stmt->get_result();
$challenge = $result->fetch_assoc();
$stmt->close();

?>

<?php @include '../inc/admin/header.php'; ?>
<section class="p-5">
    <div class="container">
        <div>
            <div class="d-flex col-md justify-content-center">
                <div class="card bg-light text-dark" style="width: 50rem;">
                    <div class="card-body text-center">
                        <h3 class="text-center">Update Challenge</h3>
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
                        <form class="text-start" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Challenge name:</label>
                                <input class="form-control" type="text" name="name" value="<?php echo $challenge['challenge_name']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Hint:</label>
                                <textarea class="form-control" name="hint" rows="3" required><?php echo $challenge['hint']; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label">Upload file:</label>
                                <input class="form-control" type="file" name="file" accept=".txt">
                            </div>
                            <div class="d-flex justify-content-between" style="margin-top: 1rem;">
                                <a href="show_challenge.php" class="btn btn-secondary">Back</a>
                                <button type="submit" name='submit' class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php @include '../inc/footer.php'; ?>
