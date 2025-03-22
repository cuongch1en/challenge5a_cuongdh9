<?php

// $conn = mysqli_connect('localhost','root','','myproject');
$conn = new mysqli('mysql', 'root', '', 'myproject');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>