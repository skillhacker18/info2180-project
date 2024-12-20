<div class="contact-info">
    <i class="fas fa-user"></i>
    <div class="contact-details-text">
        <h3><?= htmlspecialchars($contact['title']) ?> <strong>.</strong> <?= htmlspecialchars($contact['firstname'] . ' ' . $contact['lastname']) ?></h3>
        <div class="contact-meta">
            <p><strong>Created on </strong><?= htmlspecialchars(date("F d, Y", strtotime($contact['created_at']))) ?>
                <strong> by </strong> <?= isset($contact['assigned_to_name']) && $contact['assigned_to_name'] !== null ? htmlspecialchars($contact['assigned_to_name']) : '<em>Unknown</em>' ?></p>
            <p><strong>Updated on </strong><?= htmlspecialchars(date("F d, Y", strtotime($contact['updated_at']))) ?></p>
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

<form method="post" class="note-form" action="dashboard.php?page=contact_detail?id=<?= $contact_id ?>">
    <h2><i class="fas fa-sticky-note"></i> Notes</h2>
    <?php if (count($notes) > 0): ?>
        <ul>
            <?php foreach ($notes as $note): ?>
                <li>
                    <p><?= htmlspecialchars($note['user_name']) ?><strong>: </strong></p>
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
    <button type="submit" name="add_note">Add Note</button>
</form>
