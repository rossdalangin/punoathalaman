<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="hero-card" style="max-width: 450px; margin: 40px auto; text-align: left;">
    <h2 class="hero-title" style="font-size: 1.5rem; text-align: center;">Mag-login (Login)</h2>
    <form action="/api/auth/login" method="POST">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required placeholder="admin@punoathalaman.ph">
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Login</button>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
