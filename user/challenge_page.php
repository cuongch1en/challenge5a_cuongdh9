<?php
session_start();
@include '../inc/config.php';
@include './check_user.php';
@include '../logout.php';

if(isset($_GET['id'])) {
    $challenge_id = $_GET['id'];
    // $query = "SELECT * FROM challenges WHERE id = '$challenge_id'";
    // $result = mysqli_query($conn, $query);
    // $challenge = mysqli_fetch_assoc($result);
    $stmt= $conn->prepare('SELECT * FROM challenges WHERE id = ?');
    $stmt->bind_param('i', $challenge_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $challenge = $result->fetch_assoc();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_answer = $_POST['answer'];
    $correct_answer = $challenge['answer'];
    
    if($user_answer == $correct_answer) {
        $response = array(
            'status' => 'correct',
            'content' => $challenge['content']
        );
    } else {
        $response = array('status' => 'incorrect');
    }
    echo json_encode($response);
    exit;
}
?>

<?php @include '../inc/user/header.php'; ?>
<section class="p-5">
    <div class="container">
        <div>
            <div class="d-flex col-md justify-content-center">
                <div class="card bg-light text-dark" style="width: 40rem;">
                    <div class="card-body text-center">
                        <h3 class="text-center">Challenge: <?php echo $challenge['challenge_name']; ?></h3>
                        <p>Hint: <?php echo $challenge['hint']; ?></p>
                        <div id="response" style="white-space: pre-line; line-height: 1.5;"></div>
                        <form id="challengeForm" method="post">
                            <div class="form-group">
                                <label for="answer">Your Answer:</label>
                                <input type="text" class="form-control" id="answer" name="answer">
                            </div>
                            <p></p>
                            <div class="form-group">
                                <a href="show_challenge.php" class="btn btn-secondary float-start">Back</a>
                                <button type="submit" class="btn btn-primary mr-2 float-end">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('challengeForm').addEventListener('submit', function(event) {
    event.preventDefault();
    var formData = new FormData(this);
    fetch('challenge_page.php?id=<?php echo $challenge_id; ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'correct') {
            document.getElementById('response').innerHTML = '<p><strong>Correct!! This is file content: </strong></p><p>' + data.content + '</p>';
        } else {
            document.getElementById('response').innerHTML = '<p><strong>Incorrect answer! Please try again!!</strong></p>';
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>

<?php @include '../inc/footer.php'; ?>
