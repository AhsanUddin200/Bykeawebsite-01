<?php
// homepage.php - Display booking form and user’s bookings

require_once 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle new booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup = trim($_POST['pickup'] ?? '');
    $dropoff = trim($_POST['dropoff'] ?? '');
    
    if (!empty($pickup) && !empty($dropoff)) {
        // For simplicity, let's assume a simple fare calculation based on string length difference
        // In real scenario, integrate maps/distance APIs
        $distance = rand(2,10); // mock distance
        $fare = $distance * 50; // for example, 50 currency units per km
        
        $stmt = $conn->prepare("INSERT INTO bykea_bookings (user_id, pickup_location, dropoff_location, fare) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $pickup, $dropoff, $fare]);
    }
}

// Fetch user’s bookings
$stmt = $conn->prepare("SELECT * FROM bykea_bookings WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Homepage - Bykea</title>
<style>
body {
    font-family: Arial, sans-serif; 
    background:#f8f9fa; 
    margin:0; 
    padding:0;
}
.header {
    background:#343a40; 
    color:#fff; 
    padding:10px; 
    text-align:center;
}
.container {
    width:600px; 
    margin:20px auto; 
    background:#fff; 
    padding:20px; 
    border-radius:5px; 
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}
input[type=text] {
    width:100%; 
    padding:10px; 
    margin:10px 0; 
    border:1px solid #ccc; 
    border-radius:3px;
}
button {
    padding:10px 20px; 
    background:#28a745; 
    color:#fff; 
    border:none; 
    border-radius:3px; 
    cursor:pointer;
}
button:hover {
    background:#218838;
}
.table {
    width:100%; 
    border-collapse:collapse; 
    margin-top:20px;
}
.table th, .table td {
    border:1px solid #ccc; 
    padding:10px; 
    text-align:left;
}
.table th {
    background:#f1f1f1;
}
.logout-btn {
    float:right; 
    margin:-35px 10px 0 0; 
    background:#dc3545; 
    border:none; 
    padding:5px 10px; 
    border-radius:3px; 
    cursor:pointer; 
    color:#fff;
}
.logout-btn:hover {
    background:#c82333;
}
.fare-estimate {
    color: #555;
    font-size:14px;
}
</style>
<script>
function calculateFare() {
    // Simple mock: random calculation just for demonstration
    const distance = Math.floor(Math.random() * 10) + 2;
    const fare = distance * 50;
    document.getElementById('fareEstimate').innerText = "Estimated Fare: " + fare + " PKR (approx.)";
}
</script>
</head>
<body>
<div class="header">
    <h1>Bykea - Home</h1>
    <form style="display:inline;" method="post" action="logout.php">
      <button class="logout-btn">Logout</button>
    </form>
</div>
<div class="container">
    <h2>Book a Ride/Delivery</h2>
    <form method="post" action="" onsubmit="return validateForm();">
        <input type="text" name="pickup" id="pickup" placeholder="Pickup Location">
        <input type="text" name="dropoff" id="dropoff" placeholder="Drop-off Location">
        <button type="button" onclick="calculateFare()">Get Fare Estimate</button>
        <p class="fare-estimate" id="fareEstimate"></p><br>
        <button type="submit">Book Now</button>
    </form>
    <script>
    function validateForm(){
        var pickup = document.getElementById('pickup').value.trim();
        var dropoff = document.getElementById('dropoff').value.trim();
        if(pickup == '' || dropoff == ''){
            alert('Please enter both pickup and drop-off locations.');
            return false;
        }
        return true;
    }
    </script>

    <h2>Your Bookings</h2>
    <table class="table">
        <tr>
            <th>ID</th>
            <th>Pickup</th>
            <th>Drop-off</th>
            <th>Fare</th>
            <th>Status</th>
            <th>Time</th>
        </tr>
        <?php foreach($bookings as $b): ?>
        <tr>
            <td><?php echo htmlspecialchars($b['id']); ?></td>
            <td><?php echo htmlspecialchars($b['pickup_location']); ?></td>
            <td><?php echo htmlspecialchars($b['dropoff_location']); ?></td>
            <td><?php echo htmlspecialchars($b['fare']); ?></td>
            <td><?php echo htmlspecialchars($b['status']); ?></td>
            <td><?php echo htmlspecialchars($b['created_at']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
