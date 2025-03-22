<?php
session_start();
@include '../inc/config.php';
@include './check_admin.php';
@include '../logout.php';

if (isset($_GET['id'])) {
    $assignment_id = $_GET['id'];

    // Prepare the SQL statement to delete the assignment
    $delete_assignment_query = "DELETE FROM assignments WHERE id = ?";
    $stmt = $conn->prepare($delete_assignment_query);
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $stmt->close();

    // Prepare the SQL statement to delete assigned assignments
    $delete_assigned_query = "DELETE FROM assigned_assignments WHERE assignment_id = ?";
    $stmt = $conn->prepare($delete_assigned_query);
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to the show_assignment page
    header("Location: show_assignment.php");
    exit();
} else {
    // Redirect to the show_assignment page if no assignment ID is provided
    header("Location: show_assignment.php");
    exit();
}

?>