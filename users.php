<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit; 
}

include "connect.php";

if (isset($_POST['add'])) {
    $name = $_POST['name']; 
    $email = $_POST['email'];  
    
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $image = uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $image;
            
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = '';
            }
        }
    }
    
    $conn->query("INSERT INTO users (name, email, image) VALUES ('$name', '$email', '$image')");
}

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h3>User Management</h3>
    
    <form method="POST" class="row g-2 mt-2" enctype="multipart/form-data">
        <div class="col-auto">
            <input type="text" name="name" placeholder="Name" class="form-control" required>
        </div>
        <div class="col-auto">
            <input type="email" name="email" placeholder="Email" class="form-control" required>
        </div>
        <div class="col-auto">
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        <div class="col-auto">
            <button class="btn btn-success" name="add">Add User</button>
        </div>
    </form>
    
    <table class="table table-bordered table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td>
                        <?php if ($row['image']): ?>
                            <img src="uploads/<?= $row['image'] ?>" class="user-image" alt="User Image">
                        <?php else: ?>
                            <div class="user-image bg-secondary"></div>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>