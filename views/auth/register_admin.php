
<?php require __DIR__ . '/../partials/header.php'; ?>

<section class="auth-card">
    <h1>Create Admin User</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="/okaro/register_admin.php" class="form">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirm">Confirm Password</label>
            <input type="password" name="password_confirm" id="password_confirm" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Create Admin</button>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
