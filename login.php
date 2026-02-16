<?php
session_start();
require_once 'db_connect.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    // Basic Validation
    if (empty($phone) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif (strlen($phone) != 10 || !ctype_digit($phone)) {
        $error = "Phone number must be 10 digits.";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch();

        if ($user) {
            // User exists, verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: verification.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            // Register new user (Simplification for this demo)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (phone, password) VALUES (?, ?)");
            try {
                $stmt->execute([$phone, $hashed_password]);
                $_SESSION['user_id'] = $pdo->lastInsertId();
                header("Location: verification.php");
                exit;
            } catch (PDOException $e) {
                $error = "Error creating account: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EZpay - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header-gradient">
        <h1>Welcome Back</h1>
        <p>Login to continue</p>
    </div>
    
    <div class="container" style="margin-top: -20px;">
        <div class="card">
            <div class="text-center mb-3">
                <div class="logo-circle">
                    <span style="font-size: 24px; font-weight: bold; color: var(--primary-color);">EZ</span>
                </div>
                <h3>Safe, simple and efficient</h3>
            </div>

            <?php if ($error): ?>
                <div style="color: red; text-align: center; margin-bottom: 15px; font-size: 0.9em;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <div class="input-group">
                        <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-light);">+91</span>
                        <input type="tel" name="phone" class="form-control" style="padding-left: 50px;" placeholder="Enter mobile number" required pattern="[0-9]{10}" maxlength="10">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                        <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">SIGN IN</button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
            } else {
                input.type = "password";
            }
        }
    </script>
</body>
</html>
