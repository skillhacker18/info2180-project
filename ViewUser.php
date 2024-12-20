<?php
session_start();

$host = "localhost";
$username = "root";
$password = "root";
$dbname = "dolphin_crm";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['ajax'])) {
    $sql = "SELECT firstname, lastname, email, role, created_at FROM Users";
    $result = $conn->query($sql);

    $users = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    echo json_encode($users);
    exit;
}


$sql = "SELECT firstname, lastname, email, role, created_at FROM Users";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM</title>
    <link rel="stylesheet" href="viewuser.css">
</head>
<body>
    <div class="viewuser">
        <h1>Users</h1>
        <table id="users-table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Date Created</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <div class="button-container">
            <a href="dashboard.php?page=new_user">
                <button class="new-user-button">+ Add User</button>
            </a>
        </div>
    </div>

    <script src="viewUser.js"></script>
</body>
</html>


