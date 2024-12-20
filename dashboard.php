<?php
session_start();

$host = "localhost";
$username = "root";
$password = "root";
$dbname = "dolphin_crm";

$conn = new mysqli($host, $username, $password, $dbname,8889);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

if (($page === 'new_contact' || $page === 'add_user' || $page === 'new_user' || $page === 'contact_detail') && $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

$pageToFile = [
    'dashboard' => 'Home.php',
    'view_users' => 'ViewUser.php',
    'new_contact' => 'NewContact.php',
    'new_user' => 'NewUser.php', 
    'contact_detail' => 'Contactdetails.php'  
];

if (!array_key_exists($page, $pageToFile)) {

    die('Page not found.');
}

$content = $pageToFile[$page];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->

    <title>Dolphin CRM</title>
</head>
<body class="layout-page"> 

    <header>
        <span class="header-img">🐬</span>
        Dolphin CRM
    </header>

    <nav class="sidebar">
        <div class="menu-bar">
            <div class="menu">
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="dashboard.php?page=dashboard">
                            <i class="fas fa-home"></i>
                            <span class="text nav-text">Home</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="dashboard.php?page=new_contact">
                            <i class="fas fa-address-book"></i>
                            <span class="text nav-text">New Contact</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="dashboard.php?page=view_users">
                            <i class="fas fa-users"></i>
                            <span class="text nav-text">Users</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="bottom-content">

                <li class="logout">
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
                <li class="mode">
                    <div class="sun-moon">
                        <i class="fas fa-moon icon moon"></i>
                        <i class="fas fa-sun icon sun"></i>
                    </div>
                    <span class="mode-text text">Dark mode</span>

                    <div class="toggle-switch">
                        <span class="switch"></span>
                    </div>
                </li>
            </div>
        </div>
    </nav>

    <div class="content page-specific-content">
        <?php include $content; ?>
    </div>

    <script>
        const body = document.querySelector('body'),
              modeSwitch = body.querySelector(".toggle-switch"),
              modeText = body.querySelector(".mode-text");

              modeSwitch.addEventListener("click" , () =>{
                body.classList.toggle("dark");
    
                    if(body.classList.contains("dark")){
                    modeText.innerText = "Light mode";
                    }else{
                        modeText.innerText = "Dark mode";
        
                    }
                });
    </script>
</body>
</html>
