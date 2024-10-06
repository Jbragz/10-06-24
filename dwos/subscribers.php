<?php
include('dwos.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch subscribers
function fetchSubscribers($conn, $subscriptionType) {
    $subscribers = [];
    $stmt = $conn->prepare("SELECT u.user_name FROM subscriptions s JOIN users u ON s.user_id = u.user_id WHERE s.subscription_type = ?");
    if ($stmt === false) {
        die("MySQL prepare error: " . $conn->error);
    }
    $stmt->bind_param("s", $subscriptionType);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $subscribers[] = $row;
        }
    }

    $stmt->close();
    return $subscribers;
}

// Fetch owners and customers
$owners = fetchSubscribers($conn, 'O');
$customers = fetchSubscribers($conn, 'C');

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribers</title>
    <link rel="stylesheet" href="subscribers.css">    
</head>
<body>

<?php include 'adminnavbar.php'; ?>

<div class="container">
    <h1>Subscribers</h1>
    
    <div class="subscribers-section">
        <div class="owners-container">
            <h2>Station Owners</h2>
            <ul id="owners-list">
                <?php foreach ($owners as $index => $owner): ?>
                    <li class="card <?php echo ($index >= 3) ? 'hidden' : ''; ?>">
                        <div class="card-id"><?php echo ($index + 1); ?></div>
                        <h3><?php echo htmlspecialchars($owner['user_name']); ?></h3>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if (count($owners) > 3): ?>
                <button id="show-owners-btn">Show All</button>
            <?php endif; ?>
        </div>

        <!-- Customers Section -->
                <div class="customers-container">
                    <h2>Customers</h2>
                     <?php if (count($customers) > 0): ?>
                        <ul id="customers-list">
                          <?php foreach ($customers as $index => $customer): ?>
                            <li class="card <?php echo ($index >= 3) ? 'hidden' : ''; ?>">
                              <div class="card-id"><?php echo ($index + 1); ?></div>
                             <h3><?php echo htmlspecialchars($customer['user_name']); ?></h3>
                            </li>
                            <?php endforeach; ?>
                              </ul>
                         <?php if (count($customers) > 3): ?>
                         <button id="show-customers-btn">Show All</button>
                         <?php endif; ?>
                         <?php else: ?>
                         <p>No customer subscribers yet.</p>
                         <?php endif; ?>
                </div>

    </div>
</div>

<!-- Modal for Owners -->
<div id="owners-modal" class="modal">
    <div class="modal-content">
        <span class="close" id="close-owners-modal">&times;</span>
        <h2>All Station Owners</h2>
        <ul id="all-owners-list">
            <?php foreach ($owners as $index => $owner): ?>
                <li class="card">
                    <div class="card-id"><?php echo ($index + 1); ?></div>
                    <h3><?php echo htmlspecialchars($owner['user_name']); ?></h3>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Modal for Customers -->
<div id="customers-modal" class="modal">
    <div class="modal-content">
        <span class="close" id="close-customers-modal">&times;</span>
        <h2>All Customers</h2>
        <ul id="all-customers-list">
            <?php foreach ($customers as $index => $customer): ?>
                <li class="card">
                    <div class="card-id"><?php echo ($index + 1); ?></div>
                    <h3><?php echo htmlspecialchars($customer['user_name']); ?></h3>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Place script inside a DOMContentLoaded event listener -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = "block";
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = "none";
    }

    // Event Listeners for Show All buttons
    document.getElementById('show-owners-btn')?.addEventListener('click', function() {
        openModal('owners-modal');
    });

    document.getElementById('show-customers-btn')?.addEventListener('click', function() {
        openModal('customers-modal');
    });

    // Event Listeners for close buttons
    document.getElementById('close-owners-modal')?.addEventListener('click', function() {
        closeModal('owners-modal');
    });

    document.getElementById('close-customers-modal')?.addEventListener('click', function() {
        closeModal('customers-modal');
    });

    // Close modal when clicking outside of the modal
    window.onclick = function(event) {
        const ownersModal = document.getElementById('owners-modal');
        const customersModal = document.getElementById('customers-modal');
        if (event.target == ownersModal) {
            closeModal('owners-modal');
        }
        if (event.target == customersModal) {
            closeModal('customers-modal');
        }
    }
});
</script>

</body>
</html>
