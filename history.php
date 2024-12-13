<?php
// history.php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's all bookings ordered by most recent
$stmt = $conn->prepare("SELECT * FROM bykea_bookings WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bykea Booking History</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bykea-primary: #00a85a;  /* Bykea's signature green */
            --bykea-secondary: #0066cc;
            --bykea-dark: #2d3436;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            color: #333;
            padding-bottom: 60px; /* To prevent footer overlap */
        }

        .bykea-header {
            background: linear-gradient(to right, var(--bykea-primary), var(--bykea-secondary));
            color: white;
            padding: 15px 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .bykea-header .logo {
            height: 50px;
            margin-right: 15px;
        }

        .bykea-header h1 {
            font-weight: 700;
            margin: 0;
            font-size: 24px;
            display: inline-block;
            vertical-align: middle;
        }

        .bykea-header .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
        }

        .bykea-header .nav-links a:hover {
            text-decoration: underline;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .btn-bykea {
            background-color: var(--bykea-primary);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-bykea:hover {
            background-color: var(--bykea-secondary);
            transform: scale(1.05);
        }

        .table thead {
            background-color: var(--bykea-primary);
            color: white;
        }

        .bykea-footer {
            background: var(--bykea-dark);
            color: white;
            padding: 15px 0;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .bykea-header h1 {
                font-size: 20px;
            }
            .bykea-header .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bykea-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRVSeUTCOLNGvXKfTdcCsVKeUHyuxO9mBBc37egDcmZlJFzOG_WYEbqllkaR6bE6Ha2MOA&usqp=CAU" alt="Bykea Logo" class="logo">
                <h1>Bykea Booking History</h1>
            </div>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="history.php">Your Bookings</a>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container mt-4 mb-5">
        <div class="row">
            <!-- Booking History -->
            <div class="col-md-12">
                <div class="card p-4">
                    <h4 class="mb-3">Your Bookings</h4>
                    <?php if(count($bookings) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Service</th>
                                    <th>Pickup</th>
                                    <th>Drop-off</th>
                                    <th>Fare (PKR)</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bookings as $b): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($b['id']); ?></td>
                                    <td><?php echo htmlspecialchars($b['service_type']); ?></td>
                                    <td><?php echo htmlspecialchars($b['pickup_location']); ?></td>
                                    <td><?php echo htmlspecialchars($b['dropoff_location']); ?></td>
                                    <td><?php echo htmlspecialchars($b['fare']); ?></td>
                                    <td><?php echo htmlspecialchars($b['payment_method']); ?></td>
                                    <td>
                                        <?php 
                                            $status = htmlspecialchars($b['status'] ?? 'Pending');
                                            if($status === 'Pending'){
                                                echo '<span class="badge bg-warning text-dark">Pending</span>';
                                            } elseif($status === 'Completed'){
                                                echo '<span class="badge bg-success">Completed</span>';
                                            } elseif($status === 'Cancelled'){
                                                echo '<span class="badge bg-danger">Cancelled</span>';
                                            } else {
                                                echo '<span class="badge bg-secondary">N/A</span>';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($b['created_at'] ?? date('Y-m-d H:i:s')); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-center text-muted">No bookings found. Start your first ride!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bykea-footer">
        &copy; <?php echo date("Y"); ?> Bykea. All rights reserved.
    </footer>

    <!-- Leaflet JS (Not needed here as there's no map) -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" crossorigin=""></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
    // No map on history page, so no JS needed here
    </script>
</body>
</html>
