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
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: dashboard.php?page=dashboard');
    exit;
}

$response = [
    'status' => 'error',
    'message' => 'Invalid credentials',
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, email, role, password FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['loggedin'] = true;

            $response = [
                'status' => 'success',
                'message' => 'Login successful',
                'redirect' => 'dashboard.php?page=dashboard'
            ];
        } else {
            $response['message'] = 'Invalid email or password.';
        }
    } else {
        $response['message'] = 'No user found.';
    }

    $stmt->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="loginpage.css">
    <title>Dolphin CRM</title>
</head>
<body>
    <header>
        <span class="header-img">🐬</span>
        Dolphin CRM
    </header>
    <form action="login.php" method="POST">
        <div class="head">
            <h3>Login</h3>
            <p>Login with your email and password</p>
        </div>
        <div class="main">
            <input type="email" placeholder="Email address" name="email" required>
            <br><br>
            <input type="password" placeholder="Password" name="password" required>
            <div class="subcontainer">
                <label>
                    <input type="checkbox" checked="checked" name="remember"> Remember me
                </label>
            </div>
            <button type="submit">
                <i class="fas fa-lock"></i> Login
            </button>
            <div class="message"></div>
        </div>
    </form>
    <footer>
        <br>
        Copyright &copy; 2024 Dolphin CRM
    </footer>
    <script src="login.js"></script>
</body>
</html>
