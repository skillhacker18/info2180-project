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

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}


$users = []; 
$result = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) AS name FROM Users");


if (!$result) {
    die("Error with query: " . $conn->error); 
}

if ($result->num_rows > 0) {
    
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    
    echo "No users found in the database.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = trim($_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit;
    }

    
    $title = $conn->real_escape_string(trim($_POST['title']));
    $firstname = $conn->real_escape_string(trim($_POST['firstname']));
    $lastname = $conn->real_escape_string(trim($_POST['lastname']));
    $telephone = $conn->real_escape_string(trim($_POST['telephone']));
    $company = $conn->real_escape_string(trim($_POST['company']));
    $type = $conn->real_escape_string(trim($_POST['type']));
    $assigned_to = intval($_POST['assigned_to']);

    
    if (empty($firstname) || empty($lastname) || empty($telephone) || empty($company)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    
    $stmt = $conn->prepare(
        "INSERT INTO Contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at, updated_at) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
    );
    $stmt->bind_param(
        "sssssssii",
        $title,
        $firstname,
        $lastname,
        $email,
        $telephone,
        $company,
        $type,
        $assigned_to,
        $_SESSION['user_id']
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Contact created successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
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
    <link rel="stylesheet" href="css/NewContact.css">
    <title>Dolphin CRM</title>
</head>
<body>
    <div class="form-container">
        <h1>New Contact</h1>
        <form id="myForm" action="NewContact.php" method="post">
            <div class="contact">
                <div class="title-field">
                    <label for="title">Title</label>
                    <select id="title" name="title" required>
                        <option value="Mr">Mr</option>
                        <option value="Ms">Ms</option>
                        <option value="Dr">Dr</option>
                        <option value="Prof">Prof</option>
                    </select>
                </div>
                <div class="name-fields">
                    <div class="field">
                        <label for="firstname">First Name</label>
                        <input type="text" placeholder="Jane" id="firstname" name="firstname" required>
                    </div>
                    <div class="field">
                        <label for="lastname">Last Name</label>
                        <input type="text" placeholder="Doe" id="lastname" name="lastname" required>
                    </div>
                </div>
                <div class="email-telephone-fields">
                    <div class="field">
                        <label for="email">Email</label>
                        <input type="text" placeholder="something@gmail.com" id="email" name="email" required>
                    </div>
                    <div class="field">
                        <label for="telephone">Telephone</label>
                        <input type="text" id="telephone" name="telephone" required>
                    </div>
                </div>
                <div class="company-type-fields">
                    <div class="field">
                        <label for="company">Company</label>
                        <input type="text" id="company" name="company" placeholder="Company Name" required>
                    </div>
                    <div class="field">
                        <label for="type">Type</label>
                        <select id="type" name="type" required>
                            <option value="SALES LEAD">SALES LEAD</option>
                            <option value="SUPPORT">SUPPORT</option>
                        </select>
                    </div>
                </div>
                <label for="assigned_to">Assigned To</label>
                <select id="assigned_to" name="assigned_to" required>
                    
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['id']) ?>">
                                <?= htmlspecialchars($user['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No users available</option>
                    <?php endif; ?>
                </select>
                <button type="submit">Save</button>

            </div>
        </form>
    </div>
    <script src="js/newContact.js"></script>

</body>
</html>

