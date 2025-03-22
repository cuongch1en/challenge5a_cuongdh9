<?php
    session_start();
    @include '../inc/config.php';
    @include './check_admin.php';
    @include '../logout.php';
    // if (isset($_POST['submit'])){
    //     $username = mysqli_real_escape_string($conn, $_POST['username']);
    //     $_SESSION['delete_user'] = $username;
    //     $select = "SELECT * FROM user WHERE username = '$username'";
    //     $result = mysqli_query($conn, $select); 
    //     if(mysqli_num_rows($result) == 0){
    //         $error[] = 'This username doesn\'t exist!';
    //     }else{
    //         $user = mysqli_fetch_all($result, MYSQLI_ASSOC);
    //     }
    // } 
    // if (isset($_POST['delete'])){
    //     $username = $_SESSION['delete_user'];
    //     $query = "DELETE FROM user WHERE username='$username';";
    //     mysqli_query($conn, $query);
    //     $success[] = 'Delete successfully!';
    //     unset($_SESSION['delete_user']);
    // }

    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $_SESSION['delete_user'] = $username;
    
        // Prepare the SQL statement to select user
        $select = "SELECT * FROM user WHERE username = ? and type = 'user'";
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
    
    if (isset($_POST['delete'])) {
        $username = $_SESSION['delete_user'];
    
        // Prepare the SQL statement to delete user
        $query = "DELETE FROM user WHERE username = ? and type ='user'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            $success[] = "Delete successfully!";
        } else {
            $error[] = "Error deleting user: " . $stmt->error;
        }
    
        unset($_SESSION['delete_user']);
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
                            <form class="text-start" method='POST'>
                                <h3 class="text-center">Delete user</h3>
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
                                    <label for="username" class="form-label">Enter username:</label>
                                    <input type="text" name='username' class="form-control" id="username" required>
                                </div>
                                
                                <div class="d-flex flex-row-reverse" style="margin-top: 1rem;">
                                    <button type="submit" name='submit' class="btn btn-primary">Check</button>
                                </div>

                                <?php if (isset($user)): ?>
                                    <div style="margin-bottom: 1rem";>Available user:</div>
                                        <?php echo $user[0]['username'] . ' - ' . $user[0]['email']  . ' - ' . $user[0]['type'] . '<br>'; ?> 
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div style="margin-top: 0.5rem">Are you sure to delete this user?</div>
                                            <form class="text-start" method='POST'>
                                                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
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