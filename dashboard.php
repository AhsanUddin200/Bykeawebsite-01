<?php
// booking.php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle new booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pickup'], $_POST['dropoff'], $_POST['service_type'], $_POST['payment_method'])) {
    $pickup = trim($_POST['pickup']);
    $dropoff = trim($_POST['dropoff']);
    $service_type = trim($_POST['service_type']);
    $payment_method = trim($_POST['payment_method']);

    if (!empty($pickup) && !empty($dropoff) && !empty($service_type) && !empty($payment_method)) {
        // Calculate distance using a mock function or implement actual distance calculation
        // Here, we're using a random distance for demonstration
        $distance = rand(2, 10); 
        if ($service_type === 'Ride') {
            $fare = $distance * 50;
        } else {
            // Delivery
            $fare = $distance * 30;
        }

        $stmt = $conn->prepare("INSERT INTO bykea_bookings (user_id, pickup_location, dropoff_location, fare, service_type, payment_method, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $pickup, $dropoff, $fare, $service_type, $payment_method, 'Pending']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bykea Booking</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" crossorigin=""/>
    
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

        #map {
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            height: 400px;
            width: 100%;
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

        .fare-estimate {
            font-weight: bold;
            color: var(--bykea-primary);
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
                <h1>Bykea Booking</h1>
            </div>
            <nav class="nav-links">
                <a href="dashboard.php">Booking</a>
                <a href="history.php">Your Bookings</a>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container mt-4 mb-5">
        <div class="row">
            <!-- Booking Form -->
            <div class="col-md-6">
                <div class="card p-4 mb-4">
                    <h4 class="mb-3">Book a Ride/Delivery</h4>
                    <form method="post" action="" onsubmit="return validateForm();">
                        <div class="mb-3">
                            <label for="service_type" class="form-label">Service Type</label>
                            <select name="service_type" id="service_type" class="form-select">
                                <option value="Ride">Ride</option>
                                <option value="Delivery">Delivery</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="pickup" class="form-label">Pickup Location</label>
                            <input type="text" name="pickup" id="pickup" class="form-control" placeholder="e.g. Lucky One Mall" required>
                        </div>
                        <div class="mb-3">
                            <label for="dropoff" class="form-label">Drop-off Location</label>
                            <input type="text" name="dropoff" id="dropoff" class="form-control" placeholder="e.g. NIPA" required>
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-select">
                                <option value="Cash">Cash</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Mobile Wallet">Mobile Wallet</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-secondary mb-3" onclick="onGetFareEstimate()">Get Fare Estimate</button>
                        <p id="fareEstimate" class="text-muted fare-estimate"></p>
                        <button type="submit" class="btn btn-bykea w-100">Book Now</button>
                    </form>
                </div>
            </div>
            
            <!-- Live Tracking Section -->
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="mb-3">Live Order Tracking</h4>
                    <p class="text-muted">This map shows the approximate driver/delivery partner's location in real-time (demo). When you get a fare estimate, your pickup and drop-off locations will appear on the map with detailed routes.</p>
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bykea-footer">
        &copy; <?php echo date("Y"); ?> Bykea. All rights reserved.
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" crossorigin=""></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
    // Initialize map (Karachi as example center)
    var map = L.map('map').setView([24.8607, 67.0011], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    // Custom Icons
    var bikeIcon = L.icon({
        iconUrl: 'https://img.icons8.com/color/48/000000/motorcycle.png',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    });

    var carIcon = L.icon({
        iconUrl: 'https://img.icons8.com/color/48/000000/car.png',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    });

    // Initialize driver marker with bike icon
    var driverMarker = L.marker([24.8607, 67.0011], {icon: bikeIcon}).addTo(map).bindPopup("Driver Location");

    // Variables to hold pickup & dropoff markers and route line
    var pickupMarker = null;
    var dropoffMarker = null;
    var routeLine = null;

    // Mock geocoding function
    function mockGeocode(address) {
        // Lowercase for matching
        address = address.toLowerCase().trim();
        // Known locations (just examples)
        if(address === 'lucky one mall') {
            return {lat:24.9320, lng:67.0899}; // Approx coords for Lucky One Mall
        } else if(address === 'nipa') {
            return {lat:24.9134, lng:67.1116}; // Approx coords near NIPA
        } else {
            // If unknown, pick a random point near Karachi
            var latOffset = (Math.random() - 0.5) * 0.05;
            var lngOffset = (Math.random() - 0.5) * 0.05;
            return {lat:24.8607 + latOffset, lng:67.0011 + lngOffset};
        }
    }

    // Function to calculate fare
    function calculateFare() {
        const serviceType = document.getElementById('service_type').value;
        // Mock distance
        const distance = Math.floor(Math.random()*10)+2;
        let fare = 0;
        if (serviceType === 'Ride') {
            fare = distance * 50;
        } else {
            // Delivery
            fare = distance * 30;
        }
        return {fare, distance};
    }

    // Function to handle fare estimate and map plotting
    function onGetFareEstimate(){
        var pickup = document.getElementById('pickup').value.trim();
        var dropoff = document.getElementById('dropoff').value.trim();
        if(pickup === '' || dropoff === ''){
            alert('Please enter both pickup and drop-off locations.');
            return;
        }

        // Calculate fare
        var {fare, distance} = calculateFare();
        document.getElementById('fareEstimate').innerText = `Estimated Fare: ${fare} PKR (Approx. ${distance} km)`;

        // Geocode pickup and dropoff
        var pickupCoords = mockGeocode(pickup);
        var dropoffCoords = mockGeocode(dropoff);

        // Clear existing markers/line if any
        if(pickupMarker) { map.removeLayer(pickupMarker); }
        if(dropoffMarker) { map.removeLayer(dropoffMarker); }
        if(routeLine) { map.removeLayer(routeLine); }

        // Add markers
        pickupMarker = L.marker([pickupCoords.lat, pickupCoords.lng], {title:'Pickup'}).addTo(map)
                        .bindPopup("Pickup: " + pickup).openPopup();
        dropoffMarker = L.marker([dropoffCoords.lat, dropoffCoords.lng], {title:'Drop-off'}).addTo(map)
                        .bindPopup("Drop-off: " + dropoff).openPopup();

        // Fetch route from OSRM API and draw polyline
        fetchRoute(pickupCoords, dropoffCoords);
    }

    // Function to fetch route from OSRM API
    function fetchRoute(pickupCoords, dropoffCoords) {
        var url = `https://router.project-osrm.org/route/v1/driving/${pickupCoords.lng},${pickupCoords.lat};${dropoffCoords.lng},${dropoffCoords.lat}?geometries=geojson&overview=full`;

        fetch(url)
        .then(response => response.json())
        .then(data => {
            if(data.code !== 'Ok') {
                alert('Routing error: ' + data.message);
                return;
            }

            var route = data.routes[0];
            var routeCoords = route.geometry.coordinates.map(coord => [coord[1], coord[0]]); // Convert to [lat, lng]

            // Draw the polyline that runs on roads
            routeLine = L.polyline(routeCoords, {color:'blue'}).addTo(map);

            // Fit map bounds to the route
            map.fitBounds(routeLine.getBounds(), {padding:[50,50]});
        })
        .catch(error => {
            console.error('Error fetching route:', error);
            alert('An error occurred while fetching the route.');
        });
    }

    // Update driver location periodically (mock)
    function updateDriverLocation() {
        fetch('tracking.php')
        .then(response => response.json())
        .then(data => {
            const lat = data.latitude;
            const lng = data.longitude;
            const vehicle = data.vehicle;

            // Choose icon based on vehicle type
            var icon = vehicle === 'car' ? carIcon : bikeIcon;

            driverMarker.setIcon(icon);
            driverMarker.setLatLng([lat, lng]).bindPopup("Driver Location").openPopup();
        })
        .catch(error => console.error('Error fetching location:', error));
    }
    setInterval(updateDriverLocation, 5000);

    // Form validation
    function validateForm(){
        var pickup = document.getElementById('pickup').value.trim();
        var dropoff = document.getElementById('dropoff').value.trim();
        if(pickup === '' || dropoff === ''){
            alert('Please enter both pickup and drop-off locations.');
            return false;
        }
        return true;
    }
    </script>
</body>
</html>
