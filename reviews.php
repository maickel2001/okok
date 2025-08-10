<?php
require_once 'includes/auth.php';
$auth = new Auth();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis Clients - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo ASSET_VERSION; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body><?php require_once __DIR__ . '/maintenance.php'; refund_banner(); ?>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="index.php" class="logo">
                    <i class="fas fa-rocket"></i>
                    <?php echo SITE_NAME; ?>
                </a>
                <ul class="nav-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="order.php">Commander</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="reviews.php" class="btn btn-secondary">Avis</a></li>
                    <?php if ($auth->isUserLoggedIn()): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="logout.php" class="btn btn-secondary">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn btn-secondary">Connexion</a></li>
                        <li><a href="register.php" class="btn btn-primary">S'inscrire</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <div>
                    <h1 style="margin-bottom: 0.5rem;"><i class="fas fa-star"></i> Avis Clients</h1>
                    <p style="color: var(--text-secondary)">Ils nous font confiance pour leurs résultats.</p>
                </div>
            </div>

            <div class="services-grid">
                <div class="card">
                    <div style="display:flex; gap:0.75rem; align-items:center; margin-bottom:0.5rem;">
                        <i class="fas fa-user-circle" style="font-size:1.6rem; color: var(--primary-color);"></i>
                        <strong>Inès</strong>
                    </div>
                    <p style="color: var(--text-secondary)">Livraison rapide et résultats au rendez-vous. Mon compte a vraiment décollé !</p>
                    <div style="color:#ffd60a">★★★★★</div>
                </div>
                <div class="card">
                    <div style="display:flex; gap:0.75rem; align-items:center; margin-bottom:0.5rem;">
                        <i class="fas fa-user-circle" style="font-size:1.6rem; color: var(--primary-color);"></i>
                        <strong>Alex</strong>
                    </div>
                    <p style="color: var(--text-secondary)">Support très réactif. Bon rapport qualité/prix, je recommande.</p>
                    <div style="color:#ffd60a">★★★★★</div>
                </div>
                <div class="card">
                    <div style="display:flex; gap:0.75rem; align-items:center; margin-bottom:0.5rem;">
                        <i class="fas fa-user-circle" style="font-size:1.6rem; color: var(--primary-color);"></i>
                        <strong>Samuel</strong>
                    </div>
                    <p style="color: var(--text-secondary)">Process simple, résultats conformes à l’annonce. Très satisfait.</p>
                    <div style="color:#ffd60a">★★★★★</div>
                </div>
            </div>

            <div class="card" style="margin-top:1.5rem;">
                <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;"><i class="fas fa-shield-alt"></i> Politique de remboursement</h3>
                <p style="color: var(--text-secondary)">Satisfait ou remboursé sous 7 jours selon conditions. Contactez-nous si un service ne correspond pas aux attentes.</p>
            </div>
        </div>
    </div>
</body>
</html>