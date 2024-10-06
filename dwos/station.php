<?php
include('dwos.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get top selling stations based on order count
$sqlTopSelling = "
    SELECT s.station_name, u.address AS user_address, u.phone_number AS user_phone, COUNT(o.order_id) AS total_orders
    FROM users u
    JOIN stations s ON u.user_id = s.owner_id
    JOIN orders o ON u.user_id = o.user_id
    WHERE u.user_type = 'O'
    GROUP BY u.user_id
    ORDER BY total_orders DESC
    LIMIT 3"; // Limit to 3 for initial display
$resultTopSelling = $conn->query($sqlTopSelling);

// Prepare an array to hold top selling stations
$topSellingStations = [];
if ($resultTopSelling->num_rows > 0) {
    while ($row = $resultTopSelling->fetch_assoc()) {
        $topSellingStations[] = $row;
    }
}

// Query to get all top selling stations for "Show All"
$sqlAllTopSelling = "
    SELECT s.station_name, u.address AS user_address, u.phone_number AS user_phone, COUNT(o.order_id) AS total_orders
    FROM users u
    JOIN stations s ON u.user_id = s.owner_id
    JOIN orders o ON u.user_id = o.user_id
    WHERE u.user_type = 'O'
    GROUP BY u.user_id
    ORDER BY total_orders DESC";
$resultAllTopSelling = $conn->query($sqlAllTopSelling);

// Prepare an array to hold all top selling stations
$allTopSellingStations = [];
if ($resultAllTopSelling->num_rows > 0) {
    while ($row = $resultAllTopSelling->fetch_assoc()) {
        $allTopSellingStations[] = $row;
    }
}

// Query to get newly added stations based on station_id
$sqlNewStations = "
    SELECT s.station_name
    FROM stations s
    JOIN users u ON s.owner_id = u.user_id
    WHERE u.user_type = 'O'
    ORDER BY s.station_id DESC
    LIMIT 3"; // Limit to 3 for initial display
$resultNewStations = $conn->query($sqlNewStations);

// Prepare an array to hold new stations
$newStations = [];
if ($resultNewStations->num_rows > 0) {
    while ($row = $resultNewStations->fetch_assoc()) {
        $newStations[] = $row;
    }
}

// Query to get all newly added stations for "Show All"
$sqlAllNewStations = "
    SELECT s.station_name
    FROM stations s
    JOIN users u ON s.owner_id = u.user_id
    WHERE u.user_type = 'O'
    ORDER BY s.station_id DESC";
$resultAllNewStations = $conn->query($sqlAllNewStations);

// Prepare an array to hold all new stations
$allNewStations = [];
if ($resultAllNewStations->num_rows > 0) {
    while ($row = $resultAllNewStations->fetch_assoc()) {
        $allNewStations[] = $row;
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="station.css">
    <title>Water Stations</title>
</head>
<body>
    
    <?php include 'adminnavbar.php'; ?>

    <div class="header">
        <h1>Water Stations</h1>
    </div>

    <div class="home-container">
        <section id="top-selling" class="top-selling-container">
            <h2>Top Selling Stations</h2>
            <?php if (!empty($topSellingStations)): ?>
                <ul>
                    <?php 
                    $count = 1; // Initialize a counter for row numbers
                    foreach ($topSellingStations as $station): ?>
                        <li home-id="top-selling-<?php echo $count; ?>" class="station-item">
                            <span class="station-number"><?php echo $count; ?></span>
                            <span><?php echo $station['station_name']; ?></span><br>
                            Address: <?php echo $station['user_address']; ?><br>
                            Phone: <?php echo $station['user_phone']; ?><br>
                            Total Orders: <?php echo $station['total_orders']; ?>
                        </li>
                    <?php 
                    $count++; // Increment the counter for the next station
                    endforeach; ?>
                    
                    <button id="show-all-top-selling" class="show-all-button" onclick="showAllTopSelling()">Show All</button>
                </ul>
            <?php else: ?>
                <p class="centered">No top selling stations found.</p>
            <?php endif; ?>
        </section>

        <section id="new-stations" class="new-stations-container">
            <h2>Newly Added Stations</h2>
            <?php if (!empty($newStations)): ?>
                <ul>
                    <?php 
                    $count = 1; // Reset counter for new stations
                    foreach ($newStations as $station): ?>
                        <li home-id="new-station-<?php echo $count; ?>" class="station-item">
                            <span class="station-number"><?php echo $count; ?>.</span>
                            <span><?php echo $station['station_name']; ?></span>
                        </li>
                    <?php 
                    $count++; // Increment the counter for each new station
                    endforeach; ?>
                    
                    <button id="show-all-new-stations" class="show-all-button" onclick="showAllNewStations()">Show All</button>
                </ul>
            <?php else: ?>
                <p class="centered">No newly added stations found.</p>
            <?php endif; ?>
        </section>
    </div>

    <!-- Modal for Top Selling Stations -->
    <div id="top-selling-modal" class="modal hidden">
        <div class="modal-content">
            <span class="close" onclick="closeTopSellingModal()">&times;</span>
            <h2>All Top Selling Stations</h2>
            <ul id="modal-top-selling-list">
                <?php foreach ($allTopSellingStations as $index => $station): ?>
                    <li class="station-item">
                        <span class="station-number"><?php echo $index + 1; ?></span>
                        <span><?php echo $station['station_name']; ?></span><br>
                        Address: <?php echo $station['user_address']; ?><br>
                        Phone: <?php echo $station['user_phone']; ?><br>
                        Total Orders: <?php echo $station['total_orders']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Modal for Newly Added Stations -->
    <div id="new-stations-modal" class="modal hidden">
        <div class="modal-content">
            <span class="close" onclick="closeNewStationsModal()">&times;</span>
            <h2>All Newly Added Stations</h2>
            <ul id="modal-new-stations-list">
                <?php foreach ($allNewStations as $index => $station): ?>
                    <li class="station-item">
                        <span class="station-number"><?php echo $index + 1; ?></span>
                        <span><?php echo $station['station_name']; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        function showAllTopSelling() {
            document.getElementById('top-selling-modal').classList.remove('hidden');
        }

        function closeTopSellingModal() {
            document.getElementById('top-selling-modal').classList.add('hidden');
        }

        function showAllNewStations() {
            document.getElementById('new-stations-modal').classList.remove('hidden');
        }

        function closeNewStationsModal() {
            document.getElementById('new-stations-modal').classList.add('hidden');
        }
    </script>
</body>
</html>
