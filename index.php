<?php
require_once 'telephones.php';

// Gestion de la recherche globale par nom de téléphone ou marque
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$filtered_telephones = $telephones;
if ($search !== '') {
    $filtered_telephones = array_filter($telephones, function ($tel) use ($search) {
        return (stripos($tel['modele'], $search) !== false) || 
               (stripos($tel['marque'], $search) !== false) ||
               (stripos($tel['couleur'] ?? '', $search) !== false);
    });
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachir Apple | Vitrine & Téléphonie Premium</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- BARRE DE NAVIGATION COMMUNE -->
    <header class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">
                <span class="brand-logo-icon"></span>
                <span>Bachir <span class="highlight">Apple</span></span>
            </a>

            <!-- Barre de recherche globale par nom -->
            <form action="index.php" method="GET" class="nav-search-form">
                <div class="search-input-wrapper">
                    <span class="search-icon">🔍</span>
                    <input 
                        type="text" 
                        name="q" 
                        value="<?= htmlspecialchars($search) ?>" 
                        placeholder="Rechercher un modèle (iPhone, S25, Xiaomi...)" 
                        class="nav-search-input"
                        autocomplete="off"
                    >
                </div>
            </form>

            <nav>
                <ul class="nav-links">
                    <li><a href="index.php" class="nav-link active">Accueil</a></li>
                    <li><a href="boutique.php" class="nav-link">Boutique & Filtres</a></li>
                    <li><a href="boutique.php" class="nav-cta-btn">Voir le catalogue</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- BANNIÈRE HERO ÉPURÉE (STYLE APPLE) -->
    <section class="hero">
        <div class="hero-badge">⚡ La référence du smartphone d'exception en Algérie</div>
        <h1 class="hero-title">L'excellence technologique. <span>Épurée.</span></h1>
        <p class="hero-subtitle">
            Découvrez notre sélection rigoureuse de smartphones neufs et occasions certifiées. 
            Livraison sur les 58 Wilayas sans inscription.
        </p>

        <div class="hero-actions">
            <a href="boutique.php" class="btn-primary">
                <span>Accéder à la boutique</span>
                <span>→</span>
            </a>
            <a href="#catalogue" class="btn-secondary">
                <span>Explorer la vitrine</span>
            </a>
        </div>

        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-value">58 Wilayas</span>
                <span class="stat-label">Livraison partout en Algérie</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">Garantie Incluse</span>
                <span class="stat-label">Jusqu'à 1 an selon modèle</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">Batteries Testées</span>
                <span class="stat-label">Santé affichée pour l'occasion</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">Paiement Réception</span>
                <span class="stat-label">Vérifiez avant de payer</span>
            </div>
        </div>
    </section>

    <!-- SECTION CATALOGUE PRINCIPAL -->
    <main class="main-wrapper" id="catalogue">
        <div class="section-header">
            <div>
                <h2 class="section-title">Catalogue des Téléphones</h2>
                <p class="section-desc">Tous nos smartphones prêts à être livrés immédiatement.</p>
            </div>
            <div class="results-count">
                <strong><?= count($filtered_telephones) ?></strong> <?= count($filtered_telephones) > 1 ? 'appareils disponibles' : 'appareil disponible' ?>
            </div>
        </div>

        <!-- Alerte en cas de recherche active -->
        <?php if ($search !== ''): ?>
            <div class="search-notice">
                <p>Résultats de recherche pour : <strong>"<?= htmlspecialchars($search) ?>"</strong></p>
                <a href="index.php" class="clear-search-btn">✕ Effacer la recherche</a>
            </div>
        <?php endif; ?>

        <!-- GRILLE DE TOUS LES PRODUITS -->
        <?php if (!empty($filtered_telephones)): ?>
            <div class="product-grid">
                <?php foreach ($filtered_telephones as $tel): ?>
                    <?php
                        $has_real_image = !empty($tel['image']) && file_exists(__DIR__ . '/' . $tel['image']);
                        $placeholder_svg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600"><rect width="600" height="600" fill="%2312161f"/><rect x="160" y="60" width="280" height="480" rx="36" fill="%23161b22" stroke="%2330363d" stroke-width="2"/><text x="300" y="260" fill="%23f0f6fc" font-family="sans-serif" font-size="20" font-weight="bold" text-anchor="middle">' . rawurlencode($tel['modele']) . '</text><text x="300" y="300" fill="%232f81f7" font-family="sans-serif" font-size="15" font-weight="600" text-anchor="middle">' . rawurlencode($tel['couleur'] ?? '') . '</text><text x="300" y="345" fill="%238b949e" font-family="sans-serif" font-size="13" text-anchor="middle">📁 En attente photo</text></svg>';
                        $display_image = $has_real_image ? htmlspecialchars($tel['image']) : $placeholder_svg;
                    ?>
                    <article class="product-card">
                        <div class="product-image-container">
                            <span class="badge-etat <?= strtolower($tel['etat']) ?>">
                                <?= htmlspecialchars($tel['etat']) ?>
                            </span>

                            <span class="badge-marque">
                                <?= htmlspecialchars($tel['marque']) ?>
                            </span>

                            <img 
                                src="<?= $display_image ?>" 
                                alt="<?= htmlspecialchars($tel['modele']) ?>" 
                                class="product-image"
                                loading="lazy"
                            >
                        </div>

                        <div class="product-content">
                            <span class="product-brand"><?= htmlspecialchars($tel['marque']) ?></span>
                            <h3 class="product-title"><?= htmlspecialchars($tel['modele']) ?></h3>

                            <div class="product-specs">
                                <div class="spec-item">
                                    <span class="spec-icon">🎨</span>
                                    <span>Couleur : <span class="spec-value"><?= htmlspecialchars($tel['couleur']) ?></span></span>
                                </div>

                                <div class="spec-item">
                                    <span class="spec-icon">🛡️</span>
                                    <span>Garantie : <span class="spec-value"><?= htmlspecialchars($tel['garantie']) ?></span></span>
                                </div>

                                <?php if ($tel['etat'] === 'Occasion' && !empty($tel['batterie'])): ?>
                                    <div class="spec-item battery-spec">
                                        <span class="spec-icon">🔋</span>
                                        <span>Batterie : <span class="spec-value"><?= htmlspecialchars($tel['batterie']) ?>%</span></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="product-footer">
                                <div class="product-price-wrapper">
                                    <span class="price-label">Prix officiel</span>
                                    <div class="product-price">
                                        <?= number_format($tel['prix'], 0, ',', ' ') ?> <span class="currency">DA</span>
                                    </div>
                                </div>
                                <a 
                                    href="commande.php?id=<?= $tel['id'] ?>" 
                                    class="btn-buy"
                                    title="Commander avec livraison sur votre Wilaya"
                                >
                                    <span>Commander</span>
                                    <span>→</span>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">📱</div>
                <h3 class="empty-title">Aucun téléphone trouvé</h3>
                <p class="empty-desc">Aucun modèle ne correspond à votre recherche "<?= htmlspecialchars($search) ?>".</p>
                <a href="index.php" class="btn-primary">Réinitialiser la recherche</a>
            </div>
        <?php endif; ?>
    </main>

    <!-- PIED DE PAGE -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <h4>Bachir Apple</h4>
                <p>
                    Boutique spécialisée dans la vente de smartphones haut de gamme en Algérie. 
                    Transparence, qualité garantie et service client réactif.
                </p>
            </div>
            <div class="footer-col">
                <h5>Navigation</h5>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="boutique.php">Boutique & Filtres</a></li>
                    <li><a href="boutique.php?etat=Neuf">Modèles Neufs</a></li>
                    <li><a href="boutique.php?etat=Occasion">Modèles Occasion</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Marques Phares</h5>
                <ul>
                    <li><a href="boutique.php?marque[]=Apple">Apple iPhone</a></li>
                    <li><a href="boutique.php?marque[]=Samsung">Samsung Galaxy</a></li>
                    <li><a href="boutique.php?marque[]=Xiaomi">Xiaomi</a></li>
                    <li><a href="boutique.php?marque[]=Honor">Honor</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> Bachir Apple - Tous droits réservés.</span>
            <span>Conçu pour XAMPP local & PHP pur.</span>
        </div>
    </footer>

</body>
</html>
