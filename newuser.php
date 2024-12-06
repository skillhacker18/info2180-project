<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'Admin') {
    header('Location: login.html');
    exit;
}

$host = "localhost";
$username = "root";
$password = "password123";
$dbname = "dolphin_crm";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error){
    die("connection failed: " . $conn->connect_error);
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $conn->real_escape_string(trim($_POST['firstname']));
    $lastname = $conn->real_escape_string(trim($_POST['lastname']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    $role = $conn->real_escape_string(trim($_POST['role']));

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Invalid email format';
        exit;
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password)) { 
        echo 'Password must be at least 8 characters long and include at least one number, one letter, and one capital letter.'; 
        exit;
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO Users (firstname, lastname, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $firstname, $lastname, $email, $hashed_password, $role); 
    if ($stmt->execute()) { 
        echo 'New user added successfully.'; 
    } else { 
        echo 'Error: ' . $stmt->error; 
    } 
    $stmt->close();
}
$conn->close();

$content = 'newuser.html';
include 'layout.html';
?>
