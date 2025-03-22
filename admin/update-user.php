<?php
session_start();
@include '../inc/config.php';
@include 'check_admin.php';
@include '../logout.php';
if (isset($_POST['submit'])) {

    $username = $_POST['username'];
    $_SESSION['update_user'] = $username;

    // Prepare the SQL statement to select user
    $select = "SELECT * FROM user WHERE username = ? AND type = 'user'";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $error[] = "This username doesn't exist!";
    } else {
        $user = $result->fetch_all(MYSQLI_ASSOC);
    }

    $stmt->close();

}
if (isset($_POST['update'])) {

    $update_user = $_SESSION['update_user'];
    $nusername = $_POST['nusername'];
    $nname = $_POST['nname'];
    $nemail = $_POST['nemail'];
    $nphone = $_POST['nphone'];
    $npass = md5($_POST['npassword']);
    $avatar_path = '';

    if (isset($_FILES['navatar']) && $_FILES['navatar']['size'] > 0) {
        $file_name = $_FILES['navatar']['name'];
        $file_tmp = $_FILES['navatar']['tmp_name'];
        $file_type = $_FILES['navatar']['type'];
        $file_size = $_FILES['navatar']['size'];
        $file_error = $_FILES['navatar']['error'];

        if ($file_error === 0) {
            if ($file_size > 5242880) { // 5MB
                $error[] = 'File size must be less than 5 MB';
            } else {
                $file_destination = '../avatar_user/' . $file_name;
                move_uploaded_file($file_tmp, $file_destination);
                $avatar_path = $file_name;
            }
        } else {
            $error[] = 'Error uploading file';
        }
    }

    // Prepare the SQL statement to update user
    $query = "UPDATE user SET username = ?, name = ?, email = ?, phone = ?, password = ?, type = 'user'";
    if (!empty($avatar_path)) {
        $query .= ", avatar = ?";
    }
    $query .= " WHERE username = ?";

    $stmt = $conn->prepare($query);
    if (!empty($avatar_path)) {
        $stmt->bind_param("sssssss", $nusername, $nname, $nemail, $nphone, $npass, $avatar_path, $update_user);
    } else {
        $stmt->bind_param("ssssss", $nusername, $nname, $nemail, $nphone, $npass, $update_user);
    }
    $stmt->execute();
    $success[] = 'Update successfully!';
    unset($_SESSION['update_user']);
    $stmt->close();

}
?>

<?php @include '../inc/admin/header.php'; ?>
<section class="p-5">
    <div class="container">
        <div>
            <div class="d-flex col-md justify-content-center">
                <div class="card bg-light text-dark" style="width: 50rem;">
                    <div class="card-body text-center">
                        <form class="text-start" method='POST' enctype="multipart/form-data">
                            <h3 class="text-center">Update user</h3>
                            <?php
                            if (isset($error)) {
                                foreach ($error as $error) {
                                    echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
                                }
                            }
                            if (isset($success)) {
                                foreach ($success as $success) {
                                    echo '<div class="alert alert-success" role="alert">' . $success . '</div>';
                                }
                            }
                            ?>
                            <div class="mb-3">
                                <label for="username" class="form-label">Enter username:</label>
                                <input type="text" name='username' class="form-control" id="username">
                            </div>

                            <div class="d-flex flex-row-reverse" style="margin-top: 1rem;">
                                <button type="submit" name='submit' class="btn btn-primary">Check</button>
                            </div>

                            <?php if (isset($user)): ?>
                                <div style="margin-bottom: 1rem" ;>Available user:</div>
                                <?php
                                echo $user[0]['username'] . ' - ' . $user[0]['email'] . ' - ' . $user[0]['type'] . '<br>';
                                echo '<div style="margin-top: 1rem;">Update information:</div>';
                                ?>
                                <div class="d-flex flex-column col-md justify-content-center" style="margin-top: 1rem;">
                                    <form class="text-start" method='POST' enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Enter new username:</label>
                                            <input type="text" name='nusername' class="form-control" id="username"
                                                value="<?php echo $user[0]['username']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Enter new name:</label>
                                            <input type="text" name='nname' class="form-control" id="name"
                                                value="<?php echo $user[0]['name']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Enter new email:</label>
                                            <input type="email" name='nemail' class="form-control" id="email"
                                                aria-describedby="emailHelp" value="<?php echo $user[0]['email']; ?>"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Enter new phone:</label>
                                            <input type="text" name='nphone' class="form-control" id="phone"
                                                value="<?php echo $user[0]['phone']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Enter new password:</label>
                                            <input type="password" name='npassword' class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="avatar" class="form-label">Choose Avatar:</label>
                                            <input type="file" name="navatar" accept="image/*" class="form-control"
                                                id="avatar">
                                        </div>
                                        <!-- <label for="utype" class="form-label">Select user type:</label>
                                        <select class="form-select" name='ntype'>
                                            <option value="user">User</option>
                                            <option value="admin">Admin</option>
                                        </select> -->
                                        <div class="d-flex flex-row-reverse">
                                            <button type="submit" name='update' class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php @include '../inc/footer.php'; ?>