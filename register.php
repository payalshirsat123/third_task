<?php
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Basic validation
    if (strlen($username) < 3 || strlen($password) < 6) {
        $error = "Username must be at least 3 chars and password at least 6 chars.";
    } else {
        // Check if username exists
        $check = "SELECT id FROM users WHERE username=?";
        $stmt = mysqli_prepare($conn, $check);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Username already exists. Please choose another one.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $username, $hashed);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<header>Register</header>

<div class="login-container">
    <div class="image-section">
        <img src="images/register-img.svg" alt="Register Illustration" />
    </div>
    <div class="form-section">
        <form class="login-form" method="post" action="">
    <h2>Register</h2>

    <?php if (!empty($error)): ?>
        <div class="error-message" style="color:red;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required />

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required />

    <button type="submit">Register</button>

    <p style="margin-top: 10px;">Already have an account? <a href="login.php">Login here</a>.</p>
</form>

    </div>
</div>

<footer>&copy; 2025 Your Blog. All rights reserved.</footer>

</body>
</html>
