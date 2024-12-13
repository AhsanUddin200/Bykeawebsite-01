<?php
// signup.php
require_once 'db.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Please fill all fields.";
    } else {
        // Check if user already exists
        $checkStmt = $conn->prepare("SELECT id FROM bykea_users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            // Hash the password
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO bykea_users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashed]);
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Bykea</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bykea-primary: #00a85a;
            --bykea-secondary: #0066cc;
            --bykea-dark: #2d3436;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .signup-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .signup-container .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 14px;
        }

        .btn-signup {
            background-color: var(--bykea-primary);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-signup:hover {
            background-color: 00a93b;
            transform: translateY(-3px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .login-link {
            color: var(--bykea-primary);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <img src="data:image/png;base64,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" alt="Bykea Logo" class="logo">
        <h2 class="mb-4">Create Your Account</h2>
        
        <?php if(!empty($error)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="mb-3">
                <input type="text" class="form-control" name="name" placeholder="Full Name" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email Address" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Create Password" required>
            </div>
            <button type="submit" class="btn btn-signup w-100">Sign Up</button>
        </form>
        
        <p class="mt-3">
            Already have an account? 
            <a href="login.php" class="login-link">Login here</a>
        </p>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>