<?php
// login.php
require_once 'db.php';

$error = "";
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM bykea_users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                // Correct password
                $_SESSION['user_id'] = $user['id'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = "Invalid credentials.";
            }
        } else {
            $error = "No user found with this email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Login - Bykea</title>
    <link href="data:image/png;base64,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" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            /* background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            padding: 30px;
            text-align: center;
        }
        .logo {
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
        }
        .login-form input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
        }
        .login-form button {
            width: 100%;
            padding: 12px;
            background: #00a93b;
            color: white;
            border: none;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .login-form button:hover {
            background: #00a93b;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .signup-link {
            margin-top: 15px;
            font-size: 14px;
        }
        .signup-link a {
            color: #00a93b;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMwAAADACAMAAAB/Pny7AAAAeFBMVEX///8Aq0EAqj4ApzXx+vXD5s1qv3c2sVMAqTr1/fkAoRUhqTkApSxVt2QApzIAoyHn9uzX7Nux3r4krUUnsVLQ6tfM6dOV06be8eNItV2N0aC94cVfv3fj8eVuwX1MuWd9x4us27Zcu22e1KpSvXGHzJZtxoc5tV2cIc01AAAGa0lEQVR4nO2Za4OqIBCG8VZYhpat2c3s/v//4VEGFMx2PWv77X2+rKsy8g7DMBBjAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABYGH7MVB50+PLbh1mQvDRIguXL1TDLkq7BvCwmZT5ShOKxnXbYLoqAnvmX6r+Nbze4Vg0mUsSmuirsblwvl51633+1TOb3VouvqScEn359Qssm4q4N59xLqbc3Uf0XrawG+Yy7XByOjAVxdRWVpqd3kecJdWcXdy0r85HZInQ8p8LzPiFm7jo9uF5a+e8oH4rCCoyb/PgsY+xUX7mR4dNz5NZtpet9j/dZrtua3tnEdDPejdcSzt98kfM9y+hhbMZFHjcdEtIPcStmFdGdsxQze2PZmZn2ZtqZ8efE8BZlXZyOfiqvxcOYNQ9OI1dJ4B0x+5RrNxhieBdvbZgLGsnCjuYxYraLhtRromEXyasoa95PKCyju/8iJpt6pPPuG2LcRZeDmQEPXhMKhw+JcdeBnyjy80VI697NZ+TrqGwWgjs5XyYfW8zXQjZzuZr/Sow4Jh2s78dN8LnTTtb8vRgzM+ZTCiV+ZHvVI51/k6185G1YV0x+oVH0dHYjMW707YK4qhvxW23VTc9/IYbtyV/xsU7Ocmh0qrk6tQBvkXfFJAcVkQ/t+UFiLrVvoqscb178iRg2a8R8SV2uoBBIZIi7zpV1xPhKS9ym8SFikjrDuGmwX1d/+XZsGfCNGNetxLC7VBPJuGJnGpiL3xVDA+jEtzbuh4i5krk8WfDWRx8WE8p+iEPds6UjZ8lM9q+o++ymKom2YrSWkzGHh4ihKLszVtQjLsq/EHOPVGquOcuhkQshLST8xCwxS/ZQ88XUMkTMcqoHRM5FfhpZoPWJWdHA3Cj8c6paeNU9EjnX64QWc4+aVN4j5puEu3MdJWE5lQE3Ms60mHOmK/TzqR4K7m11n1cyhsSZZXJgxEO3JTHujdZRfrLnr64A4pmJY+bfSW3Zk0lsIQOuHLfUqArAbT8ZyzX52e4wEjlT+Cm8xkZia8Q46o87DXrF2PCoVRM8ZdTKtE/J+ZmxMfQXmu7TrAX3lEA3z7rXcRsKuopTRBdrcX9TaBpbhk3tNj6VAr7mavj/Qsw9aCduWMqERrN/2rbtiHHi+wAx7YaCciNf0IdkTWtvjn4vptk80X/R2thgZrK+oeluDFkrhlMKsApfLaazNYuakQ3W0jsl/feQw5SOymd6zohIE9Ok4fzeqrnrbouJkWkbMeJESytPDcdqMZGFs2usXqUH0k2+rMivMs3Eo+JMZ7PVsmF/ikhfa9ifql1jZM7QZueTJslaeiA6tU9VahbZ0iRvnEF5pUo1W8nTtYNwjBhzeMNS9oO7reENbTuiu7kEKjEilecBNG02XTFvF83AURFtbwnH1Gf9tZlcJ82eMXqNW6mTOsBrLSykQHPnjaGfxFzbrYzBqDjrF5NQR9ZtR+g1p0dMTItr/qSpNtVtfhBzvHl9YvhpxLrZLyZcU3XZGv5GjGq6VwOlC4QfxGSUAI3sTpez48fFqPOkNoB/FhPeBUXdeYiYkHKZd9g0FJRDNr3vjxFDxzL/JYYtaaZ5i+UAMcmTd77Acko76afFZHF3yAeIYSt1MlUapzPvxGQqcRu3/PWLvk+I8enYwon/Z85UlOog5Br+KIa2DW0FXneF7kXlaDHmWdayUBuU06Bs1opJ1pQDpGu0mP7PUtuZVWgH8cto/UpMuludFddyS0uzdSY8SIw+1vFOSVPOeKsudRhlfTu3XM3UX9dnutBMW7gqXcTWKC2GiWFqTOuUpGuz9IUq3T1ow3ewYjBRd83Y+5UYo7B11B2rDhsoRp2D1zu4/qq5LpxniT7L6iz3K1VFjBXzAhfWb0LeQDEUP07V3/e/AsQr42DOakwL5/zXvwpO+6qKqvjrbILlYZJnb/PloQxP7V8CS3nT8Vn47K1XHDntZZFNZ1kGiYxS7/lbLSyYC++FdHvvlOJJKjwxP/fcu9p+9J/6xX2f5Qrx9Omrqe2vuklaPZ6POAjYF5MORXl+3VVkxaRYdcZ/+Xi9x/JyUpzDfssSuTWunhUvWirXVo33r7f/A7/Du9cG3mOtvK5l0/ybz4z+XQMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPsA/CI5ptaIYJI4AAAAASUVORK5CYII=" alt="Bykea Logo" class="logo">
        </div>
        <?php if(!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form class="login-form" method="post" action="">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </div>
    </div>
</body>
</html>