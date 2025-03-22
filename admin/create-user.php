<?php
    session_start();
    @include '../inc/config.php';
    @include 'check_admin.php';
    @include '../logout.php';
    if (isset($_POST['submit'])){
    

    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pass = md5($_POST['password']);
    $cpass = md5($_POST['cpassword']);
    $type = $_POST['type'];


    $query = "SELECT * FROM user WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $error[] = 'This username or email has already been used!';
    } else {
        if ($pass != $cpass) {
            $error[] = 'Password doesn\' match';
        } else {
            if (isset($_FILES['avatar'])) {
                $file_name = $_FILES['avatar']['name'];
                $file_tmp = $_FILES['avatar']['tmp_name'];
                $file_type = $_FILES['avatar']['type'];
                $file_size = $_FILES['avatar']['size'];
                $file_error = $_FILES['avatar']['error'];

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
            $stmt->close();
            $insert_query = 'INSERT INTO user (username, name, email, phone, password, type, avatar) VALUES (?,?,?,?,?,?,?)';
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param('sssssss', $username, $name, $email, $phone, $pass, $type, $avatar_path);
            $stmt->execute();
            $stmt->close();
            $success[] = "Successfully registered!";
        }
    }
}
?>

<?php @include '../inc/admin/header.php'; ?>

<section class="p-5">
        <div class="container">
            <div>
                <div class="d-flex col-md justify-content-center">
                    <div class="card bg-light text-dark" style="width: 40rem;">
                        <div class="card-body text-center">
                            <form class="text-start" method='POST' enctype="multipart/form-data">
                                <h3 class="text-center">Create user</h3>
                                <?php
                                    if (isset($error)){
                                        foreach($error as $error){
                                            echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                        }
                                    }
                                    if (isset($success)){
                                        foreach($success as $success){
                                            echo '<div class="alert alert-success" role="alert">'.$success.'</div>';
                                        }
                                    }
                                ?>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Enter Username:</label>
                                    <input type="text" name='username' required class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Enter Name:</label>
                                    <input type="text" name='name' required class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Enter email:</label>
                                    <input type="email" name='email' required class="form-control" id="email" aria-describedby="emailHelp">
                                    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Enter Phone number:</label>
                                    <input type="text" name='phone' required class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Enter password:</label>
                                    <input type="password" name='password' required class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Re-enter password:</label>
                                    <input type="password" name='cpassword' required class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Choose Avatar:</label>
                                    <input type="file" name="avatar" accept="image/*" class="form-control" id="avatar">
                                </div>
                                <label for="utype" class="form-label">Select user type:</label>
                                <select class="form-select" name='type'>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <div class="d-flex justify-content-between align-items-center float-end" style="margin-top: 1rem;">
                                    <button type="submit" name='submit' class="btn btn-primary">Sign up</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
    @include '../inc/footer.php';
?>