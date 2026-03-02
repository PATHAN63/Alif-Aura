<?php
$pageTitle = 'Contact';
require_once 'includes/header.php';

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    if ($name && $email && $message) {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $success = true;
    }
}
?>

<div class="section fade-in">
    <h1 class="section-title">Contact Us</h1>
    <?php if ($success): ?>
        <p style="text-align:center;color:#D4AF37">Thank you! We will get back to you soon.</p>
    <?php endif; ?>
    <div style="max-width:600px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:2rem">
        <div>
            <h3 style="color:#D4AF37;margin-bottom:1rem">Get in Touch</h3>
            <p><strong>Email:</strong> hello@alifaura.com</p>
            <p><strong>Phone:</strong> +92 300 1234567</p>
        </div>
        <form method="POST" style="grid-column:span 2">
            <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Subject</label><input type="text" name="subject"></div>
            <div class="form-group"><label>Message</label><textarea name="message" rows="5" required></textarea></div>
            <button type="submit" class="btn-gold">Send Message</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
