<?php
session_start();
@include '../inc/config.php';
@include './check_admin.php';
@include '../logout.php';


if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $hint = $_POST['hint'];
    $uploader = $_SESSION['username'];

    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    $file_extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

    if ($file_extension !== 'txt') {
        $errors[] = "You can only upload .txt files";
    } else {
        $answer = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
        $upload_folder = "../challenges/";
        $file_path = $upload_folder . $file_name;
        move_uploaded_file($file_tmp, $file_path);

        $file_content = file_get_contents($file_path);
        $file_content = mysqli_real_escape_string($conn, $file_content);

        // Prepare the SQL statement to insert the challenge
        $query = "INSERT INTO challenges (challenge_name, hint, uploader, file_name, file_path, answer, content) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssss", $name, $hint, $uploader, $file_name, $file_path, $answer, $file_content);
        $stmt->execute();


        $successes[] = "Add challenge successfully!";


        $stmt->close();
    }
}

?>

<?php @include '../inc/admin/header.php'; ?>
<section class="p-5">
    <div class="container">
        <div>
            <div class="d-flex col-md justify-content-center">
                <div class="card bg-light text-dark" style="width: 50rem;">
                    <div class="card-body text-center">
                        <form class="text-start" method="POST" enctype="multipart/form-data">
                            <h3 class="text-center">Add Challenge</h3>
                            <?php
                            if (isset($errors)) {
                                foreach ($errors as $error) {
                                    echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
                                }
                            }
                            if (isset($successes)) {
                                foreach ($successes as $success) {
                                    echo '<div class="alert alert-success" role="alert">' . $success . '</div>';
                                }
                            }
                            ?>
                            <div class="mb-3">
                                <label for="title" class="form-label">Challenge name:</label>
                                <input class="form-control" type="text" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Hint:</label>
                                <textarea class="form-control" name="hint" rows="3" required></textarea>
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