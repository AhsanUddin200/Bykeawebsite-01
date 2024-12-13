<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .image-container {
            width: 300px;
            height: 300px;
            background-color: #ddd;
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .buttons {
            margin-top: 20px;
            display: flex;
            gap: 15px;
        }
        .buttons a {
            text-decoration: none;
            font-size: 16px;
            color: #fff;
            background: linear-gradient(45deg, #00a93b, #0056b3);
            padding: 12px 25px;
            border-radius: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .buttons a:hover {
            background: linear-gradient(45deg, #0056b3, #003f8a);
            transform: scale(1.1);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

<?php
    $imageSrc = "data:image/png;base64,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"; // Replace with your image URL
    echo "<div class='image-container' style='background-image: url($imageSrc);'></div>";
?>

<div class="buttons">
    <a href="signup.php">Signup</a>
    <a href="login.php">Login</a>
</div>

</body>
</html>
