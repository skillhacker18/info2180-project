<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="layout.css">
    <?php
    // Include page-specific CSS if provided
    if (isset($pageStyles)) {
        echo "<link rel='stylesheet' href='$pageStyles'>";
    }
    ?>
    <title>Dolphin CRM</title>
</head>
<body>
    <header>
        <span class="header-img">🐬</span>
        Dolphin CRM
    </header>
    <!-- Include the sidebar -->
    <div class="sidebar">
        <a href="dashboard.php">Home</a>
        <a href="new_contact.php">New Contact</a>
        <a href="users.php">Users</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <!-- Main content will be included here -->
        <?php include $content; ?>
    </div>
</body>
</html>

