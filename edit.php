<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit; 
}

include "connect.php";

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE id=$id");
$user = $result->fetch_assoc();

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name']; 
    $email = $_POST['email'];
    
    $image = $_POST['existing_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if ($image && file_exists("uploads/" . $image)) {
                unlink("uploads/" . $image);
            }
            
            $image = uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $image;
            
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }
    }
    
    $conn->query("UPDATE users SET name='$name', email='$email', image='$image' WHERE id=$id");
    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h3>Edit User</h3>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <input type="hidden" name="existing_image" value="<?= $user['image'] ?>">
        
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?= $user['name'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= $user['email'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Image</label>
            <?php if ($user['image']): ?>
                <div class="mb-2">
                    <img src="uploads/<?= $user['image'] ?>" class="user-image" alt="Current Image">
                </div>
            <?php endif; ?>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        
        <button type="submit" name="update" class="btn btn-primary">Update User</button>
        <a href="users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>