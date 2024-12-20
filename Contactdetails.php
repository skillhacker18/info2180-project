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
    die("Database connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid contact ID.");
}
$contact_id = intval($_GET['id']);

$contact = null;
$query = "
    SELECT Contacts.*, 
           CONCAT(users.firstname, ' ', users.lastname) AS assigned_to_name, 
           created_by_user.firstname AS created_by_firstname, 
           created_by_user.lastname AS created_by_lastname 
    FROM Contacts 
    LEFT JOIN Users AS users ON Contacts.assigned_to = users.id 
    LEFT JOIN Users AS created_by_user ON Contacts.created_by = created_by_user.id 
    WHERE Contacts.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $contact_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $contact = $result->fetch_assoc();
} else {
    die("Contact not found.");
}

$notes = [];
$notes_query = "
    SELECT Notes.*, 
           CONCAT(users.firstname, ' ', users.lastname) AS user_name 
    FROM Notes 
    LEFT JOIN Users AS users ON Notes.created_by = users.id 
    WHERE Notes.contact_id = ? 
    ORDER BY Notes.created_at ASC";
$stmt = $conn->prepare($notes_query);
$stmt->bind_param("i", $contact_id);
$stmt->execute();
$notes_result = $stmt->get_result();

if ($notes_result && $notes_result->num_rows > 0) {
    while ($row = $notes_result->fetch_assoc()) {
        $notes[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['assign_to_me'])) {
        $assigned_to = $_SESSION['user_id'];
        $update_query = "UPDATE Contacts SET assigned_to = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ii", $assigned_to, $contact_id);
        if (!$stmt->execute()) {
            echo json_encode(['error' => 'Error updating contact.']);
        }
    }

    
    elseif (isset($_POST['switch_to_sales_lead'])) {
        $update_query = "UPDATE Contacts SET type = 'Sales Lead', updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $contact_id);
        if (!$stmt->execute()) {
            echo json_encode(['error' => 'Error updating contact.']);
        }
    }

    
    elseif (isset($_POST['add_note']) && !empty(trim($_POST['note']))) {
        $note = $conn->real_escape_string(trim($_POST['note']));
        $user_id = $_SESSION['user_id'];
        $insert_note_query = "INSERT INTO Notes (contact_id, created_by, comment, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($insert_note_query);
        $stmt->bind_param("iis", $contact_id, $user_id, $note);

        if ($stmt->execute()) {
            
            $update_contact_query = "UPDATE Contacts SET updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($update_contact_query);
            $stmt->bind_param("i", $contact_id);
            $stmt->execute();

            
            $notes_query = "
                SELECT Notes.*, 
                       CONCAT(users.firstname, ' ', users.lastname) AS user_name 
                FROM Notes 
                LEFT JOIN Users AS users ON Notes.created_by = users.id 
                WHERE Notes.contact_id = ? 
                ORDER BY Notes.created_at DESC";
            $stmt = $conn->prepare($notes_query);
            $stmt->bind_param("i", $contact_id);
            $stmt->execute();
            $notes_result = $stmt->get_result();

            $notes = [];
            if ($notes_result && $notes_result->num_rows > 0) {
                while ($row = $notes_result->fetch_assoc()) {
                    $notes[] = $row;
                }
            }

            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                ob_start();
                include 'content/contactContent.php';  // Use the updated notes content in the response
                $content = ob_get_clean();
                echo $content;  
            }
        } else {
            echo json_encode(['error' => 'Error adding note.']);
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/contactDetails.css">
    <title>Contact Details - Dolphin CRM</title>
</head>
<body>
    <div class="contact-details">
        <div class="header">
            <h1>Contact Details</h1>
            <div class="actions">
                <form method="post">
                    <button type="submit" name="assign_to_me" class="action-btn">Assign to me</button>
                    <button type="submit" name="switch_to_sales_lead" class="action-btn">Switch to Sales Lead</button>
                </form>
            </div>
        </div>

        <div class="contact-info">
            <i class="fas fa-user"></i>
            <div class="contact-details-text">
                <h3><?= htmlspecialchars($contact['title']) ?><strong> . </strong><?= htmlspecialchars($contact['firstname'] . ' ' . $contact['lastname']) ?></h3>
                <div class="contact-meta">
                    <p><strong>Created on:</strong> <?= htmlspecialchars(date("F d, Y", strtotime($contact['created_at']))) ?>
                    <strong> by </strong> <?= isset($contact['assigned_to_name']) && $contact['assigned_to_name'] !== null ? htmlspecialchars($contact['assigned_to_name']) : '<em>Unknown</em>' ?></p>

                    <p><strong>Updated on:</strong> <?= htmlspecialchars(date("F d, Y", strtotime($contact['updated_at']))) ?></p>
                </div>
            </div>
        </div>

        <div class="border-box">
            <div class="email-tele-field">
                <label for="email">Email:</label>
                <p id="email"><?= htmlspecialchars($contact['email']) ?></p>

                <label for="telephone">Telephone:</label>
                <p id="telephone"><?= htmlspecialchars(preg_replace("/(\d{3})(\d{3})(\d{4})/", "$1-$2-$3", $contact['telephone'])) ?></p>
            </div>

            <div class="company-assign-field">
                <label for="company">Company:</label>
                <p id="company"><?= htmlspecialchars($contact['company']) ?></p>

                <label for="assigned_to">Assigned to:</label>
                <p id="assigned_to"><?= htmlspecialchars($contact['assigned_to_name']) ?></p>
            </div>
        </div>

        <form method="post" class="note-form" action="dashboard.php?page=contact_detail&id=<?= $contact_id ?>">

            <h2><i class="fas fa-sticky-note"></i> Notes</h2>

            <?php if (count($notes) > 0): ?>
                <ul class="notes-list">
                    <?php foreach ($notes as $note): ?>
                        <li class="note-item">
                            <p><strong><?= htmlspecialchars($note['user_name']) ?>:</strong></p>
                            <p><?= htmlspecialchars($note['comment']) ?></p>
                            <p><em><?= htmlspecialchars(date("F d, Y", strtotime($note['created_at']))) ?> at <?= htmlspecialchars(date("g:i A", strtotime($note['created_at']))) ?></em></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No notes found.</p>
            <?php endif; ?>

            <p class="note-header">Add a Note about <?= htmlspecialchars($contact['firstname'] . ' ' . $contact['lastname']) ?>:</p>
            <textarea name="note" placeholder="Enter details here" required></textarea>
            <button type="submit" name="add_note" class="action-btn">Add Note</button>
        </form>
    </div>

    <script src="js/contactDetails.js"></script>
</body>
</html>
