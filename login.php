<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

$host = "localhost";
$username = "root";
$password = "password123";
$dbname = "dolphin_crm";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['loggedin'] = true;

            // Redirect to the dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            echo 'Invalid email or password';
        }
    } else {
        echo 'Invalid email or password';
    }

    $stmt->close();
}

$conn->close();

?>
