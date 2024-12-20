<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "root";
$dbname = "dolphin_crm";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$filter = 'All Contacts'; 

if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
}

if ($filter == 'Sales Leads') {
    $sql = "SELECT * FROM Contacts WHERE type = 'Sales Lead'";
} elseif ($filter == 'Support') {
    $sql = "SELECT * FROM Contacts WHERE type = 'Support'";
} elseif ($filter == 'Assigned to me') {
    $sql = "SELECT * FROM Contacts WHERE assigned_to = " . $_SESSION['user_id']; 
} else {
    $sql = "SELECT * FROM Contacts";  
}

$result = $conn->query($sql);


$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax) {
   
    ob_start();
    $content = ob_get_clean();
    echo json_encode(['content' => $content]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - Dashboard</title>
    <link rel="stylesheet" href="Home.css">
</head>
<body>
    <div class="dashboard">
        <h1>Dashboard</h1>

        <div class="filters">
            <span>Filter by:</span>
            <a href="dashboard.php?filter=All Contacts">All Contacts</a> |
            <a href="dashboard.php?filter=Sales Leads">Sales Leads</a> |
            <a href="dashboard.php?filter=Support">Support</a> |
            <a href="dashboard.php?filter=Assigned to me">Assigned to me</a>
        </div>

        <div class="content-box">
            <div class="header-row">
                <h2>Contact List</h2>
                <a href="dashboard.php?page=new_contact">
                    <button class="new-user-button">+ Add Contact</button></a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Type of Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td><?= htmlspecialchars($row['company']); ?></td>
                        <td><?= htmlspecialchars($row['type']); ?></td>
                        <td><a href="dashboard.php?page=contact_detail&id=<?= $row['id']; ?>">View</a></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6">No contacts found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php $conn->close(); ?>
    <script src="home.js"></script>
</body>
</html>
