<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- ADD THIS -->
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>Login Here </header>
  <div class="login-container">
    <!-- Left Image Section -->
    <div class="image-section">
      <img src="images/login-illustration.svg" alt="Login Illustration">
    </div>

    <!-- Right Login Form Section -->
    <div class="form-section">
      <form action="login.php" method="POST" class="login-form">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
      </form>
    </div>
  </div>
  <footer>&copy; 2025 Your Blog. All rights reserved.</footer>

</body>
</html>