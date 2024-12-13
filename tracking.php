<?php
// tracking.php
header('Content-Type: application/json');

// Mock driver location near Karachi
$baseLat = 24.8607;
$baseLng = 67.0011;

// Generate random movement
$latOffset = (mt_rand(-1000, 1000) / 100000); // +/- 0.01 degrees
$lngOffset = (mt_rand(-1000, 1000) / 100000); // +/- 0.01 degrees

$latitude = $baseLat + $latOffset;
$longitude = $baseLng + $lngOffset;

// Randomly choose between bike and car
$vehicleType = mt_rand(0, 1) ? 'bike' : 'car';

echo json_encode([
    'latitude' => $latitude,
    'longitude' => $longitude,
    'vehicle' => $vehicleType
]);
?>
