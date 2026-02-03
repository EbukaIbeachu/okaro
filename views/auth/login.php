<?php require __DIR__ . '/../partials/header.php'; ?>

<section class="auth-card">
    <h1>Sign in</h1>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" action="login.php" class="form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Login</button>
        </div>

    </form>
    <div class="form-actions" style="margin-top: 1em; text-align: center;">
        <a class="btn-secondary" href="/okaro/register.php">Register</a>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
