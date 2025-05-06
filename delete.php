<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit; 
}

include "connect.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $result = $conn->query("SELECT image FROM users WHERE id=$id");
    $row = $result->fetch_assoc();
    if ($row['image'] && file_exists("uploads/" . $row['image'])) {
        unlink("uploads/" . $row['image']);
    }
    
    $conn->query("DELETE FROM users WHERE id=$id");
}

header("Location: users.php");
exit;
?>