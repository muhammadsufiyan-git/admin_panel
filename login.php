<?php
session_start();
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = md5($_POST['password']);

    $checkUser = "SELECT * FROM admins WHERE username='$user'";
    $userResult = $conn->query($checkUser);

    if ($userResult->num_rows > 0) {
        $sql = "SELECT * FROM admins WHERE username='$user' AND password='$pass'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $_SESSION['admin'] = $user;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Wrong password.";
        }
    } else {
        $insert = "INSERT INTO admins (username, password) VALUES ('$user', '$pass')";
        if ($conn->query($insert)) {
            $_SESSION['admin'] = $user;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Registration failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box {
            background: #fff;
            padding: 30px 25px;
            border-radius: 10px;
            box-shadow: 0px 10px 20px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-box h3 {
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
            color: #2c3e50;
        }
        .form-control {
            border-radius: 8px;
        }
        .btn-primary {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h3>Admin Login</h3>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required placeholder="Enter username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Enter password">
            </div>
            <button type="submit" class="btn btn-primary w-100">Login / Register</button>
        </form>
    </div>
</body>
</html>
