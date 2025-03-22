<?php
@include './inc/config.php';

session_unset();
session_start();
if (isset($_POST['submit'])) {
    
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? and password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password using password_hash (instead of md5)

        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['type'];

        if ($_SESSION['role'] === 'admin') {
            header('location:./admin/page_admin.php');
        } elseif ($_SESSION['role'] === 'user') {
            header('location:./user/page_user.php');
        }

    } else {
        $error[] = 'Incorrect username or password';
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <!-- Link bootstrap icon ở trên -->
    <link rel="stylesheet" href="./source/css/main.css">
    <title>Student Management System</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark py-3 fixed-top">
        <div class="container">
            <a href="index.php" class="navbar-brand">CUONGDH9</a>
            <button class="btn btn-primary btn-large" type="button" data-bs-toggle="modal"
                data-bs-target="#sign-in">Login</button>
        </div>
    </nav>

    <section class="p-5">
        <div class="container">
            <div>
                <div class="d-flex col-md justify-content-center">
                    <div class="card bg-dark text-light" style="width: 30rem;">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <img src="./source/img/logo1.jpg" alt="">
                            </div>
                            <h6 class="card-title mb-3">
                                Student Management System
                            </h6>
                            <p class="card-text">
                                WELCOME TO MY WEBSITE.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="sign-in" tabindex="-1" aria-labelledby="enrollLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="enrollLabel">Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <?php
                    if (isset($error)) {
                        foreach ($error as $error) {
                            echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
                        }
                    }
                    ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="col-form-label">Username</label>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="col-form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="Password">
                        </div>
                    </div>
                    <div class="modal-footer" style="justify-content: space-between;">
                        <p style="text-align: left;">Don't have an account? <a href="register.php">Sign up here</a></p>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="submit" class="btn btn-primary">Login</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>