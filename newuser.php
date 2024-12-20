<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$username = "root";
$password = "root";
$dbname = "dolphin_crm";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("connection failed: " . $conn->connect_error);
}


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax'])) {
    $firstname = $conn->real_escape_string(trim($_POST['firstname']));
    $lastname = $conn->real_escape_string(trim($_POST['lastname']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    $role = $conn->real_escape_string(trim($_POST['role']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password)) { 
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long and include at least one number, one letter, and one capital letter.']);
        exit;
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO Users (firstname, lastname, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $firstname, $lastname, $email, $hashed_password, $role); 
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User created successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/newuser.css">
    <title>Dolphin CRM</title>
</head>
<body>
    <div class="form-container">
        <h1>New User</h1>
        <form id="new-user-form">
            <div class="user">
                <div class="name-fields">
                    <div class="field">
                        <label for="firstname">First Name</label>
                        <input type="text" placeholder=" Jane" id="firstname" name="firstname" required>
                    </div>
                    <div class="field">
                        <label for="lastname">Last Name</label>
                        <input type="text" placeholder=" Doe" id="lastname" name="lastname" required>
                    </div>
                </div>
                <div class="email-password-fields">
                    <div class="field">
                        <label for="email">Email</label>
                        <input type="text" placeholder=" something@gmail.com" id="email" name="email" required>
                    </div>
                    <div class="field">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="Admin">Admin</option>
                    <option value="Member">Member</option>
                </select>
                <button type="submit">Save</button>
            </div>
        </form>
        <div id="response-message"></div>
    </div>

    <script src="js/newUser.js"></script>
</body>
</html>
