<?php
require_once 'telephones.php';

// 1. Liste des marques strictement autorisées selon le cahier des charges
$marques_autorisees = ['Apple', 'Samsung', 'Honor', 'Xiaomi', 'Oppo', 'Realme'];

// 2. Récupération et validation du filtre 'État'
$etat_filtre = isset($_GET['etat']) ? trim($_GET['etat']) : 'Tous';
if (!in_array($etat_filtre, ['Tous', 'Neuf', 'Occasion'], true)) {
    $etat_filtre = 'Tous';
}

// 3. Récupération et validation du filtre 'Marques'
$marques_filtre = [];
if (isset($_GET['marque'])) {
    if (is_array($_GET['marque'])) {
        $marques_filtre = array_values(array_intersect($_GET['marque'], $marques_autorisees));
    } elseif (in_array($_GET['marque'], $marques_autorisees, true)) {
        $marques_filtre = [$_GET['marque']];
    }
}

// 4. Recherche par mot-clé
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

// 5. Filtrage PHP côté serveur (garantit le bon fonctionnement direct sous XAMPP sans JS)
$telephones_filtres = array_filter($telephones, function ($tel) use ($etat_filtre, $marques_filtre, $search, $marques_autorisees) {
    // Condition État
    if ($etat_filtre !== 'Tous' && $tel['etat'] !== $etat_filtre) {
        return false;
    }

    // Condition Marque (limitée aux marques sélectionnées ou aux autorisées)
    if (!empty($marques_filtre)) {
        if (!in_array($tel['marque'], $marques_filtre, true)) {
            return false;
        }
    } else {
        if (!in_array($tel['marque'], $marques_autorisees, true)) {
            return false;
        }
    }

    // Condition Recherche textuelle
    if ($search !== '') {
        $dansModele = stripos($tel['modele'], $search) !== false;
        $dansMarque = stripos($tel['marque'], $search) !== false;
        $dansCouleur = stripos($tel['couleur'] ?? '', $search) !== false;
        if (!$dansModele && !$dansMarque && !$dansCouleur) {
            return false;
        }
    }

    return true;
});

// Compteurs globaux
$total_tous = count($telephones);
$total_neuf = count(array_filter($telephones, fn($t) => $t['etat'] === 'Neuf'));
$total_occasion = count(array_filter($telephones, fn($t) => $t['etat'] === 'Occasion'));

$comptes_marques = [];
foreach ($marques_autorisees as $m) {
    $comptes_marques[$m] = count(array_filter($telephones, fn($t) => $t['marque'] === $m));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique & Filtres | Bachir Apple</title>
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

            <!-- Recherche synchronisée avec les filtres -->
            <form action="boutique.php" method="GET" class="nav-search-form" id="searchForm">
                <input type="hidden" name="etat" value="<?= htmlspecialchars($etat_filtre) ?>">
                <?php foreach ($marques_filtre as $mf): ?>
                    <input type="hidden" name="marque[]" value="<?= htmlspecialchars($mf) ?>">
                <?php endforeach; ?>
                <div class="search-input-wrapper">
                    <span class="search-icon">🔍</span>
                    <input 
                        type="text" 
                        name="q" 
                        id="globalSearchInput"
                        value="<?= htmlspecialchars($search) ?>" 
                        placeholder="Rechercher par modèle, marque ou couleur..." 
                        class="nav-search-input"
                        autocomplete="off"
                    >
                </div>
            </form>

            <nav>
                <ul class="nav-links">
                    <li><a href="index.php" class="nav-link">Accueil</a></li>
                    <li><a href="boutique.php" class="nav-link active">Boutique & Filtres</a></li>
                    <li><a href="index.php" class="nav-cta-btn">Tous les modèles</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-wrapper">
        <!-- Bouton Filtres sur Mobile -->
        <div class="mobile-filter-bar">
            <button type="button" class="mobile-filter-btn" id="mobileFilterToggle">
                <span>⚙️</span>
                <span>Afficher les options de filtres</span>
            </button>
        </div>

        <div class="shop-layout">
            <!-- BARRE LATÉRALE (SIDEBAR) DE FILTRES À GAUCHE -->
            <aside class="sidebar" id="filterSidebar">
                <div class="sidebar-header">
                    <h2 class="sidebar-title">
                        <span>⚡</span>
                        <span>Filtres</span>
                    </h2>
                    <a href="boutique.php" class="reset-filters-btn">Réinitialiser</a>
                </div>

                <form action="boutique.php" method="GET" id="filterForm">
                    <?php if ($search !== ''): ?>
                        <input type="hidden" name="q" value="<?= htmlspecialchars($search) ?>">
                    <?php endif; ?>

                    <!-- FILTRE 1 : ÉTAT -->
                    <div class="filter-group">
                        <div class="filter-group-title">État du téléphone</div>
                        <div class="filter-options">
                            <label class="filter-label <?= $etat_filtre === 'Tous' ? 'active' : '' ?>">
                                <div class="filter-checkbox-wrap">
                                    <input 
                                        type="radio" 
                                        name="etat" 
                                        value="Tous" 
                                        class="filter-input filter-radio-etat" 
                                        <?= $etat_filtre === 'Tous' ? 'checked' : '' ?>
                                    >
                                    <span>Tous les états</span>
                                </div>
                                <span class="filter-count"><?= $total_tous ?></span>
                            </label>

                            <label class="filter-label <?= $etat_filtre === 'Neuf' ? 'active' : '' ?>">
                                <div class="filter-checkbox-wrap">
                                    <input 
                                        type="radio" 
                                        name="etat" 
                                        value="Neuf" 
                                        class="filter-input filter-radio-etat" 
                                        <?= $etat_filtre === 'Neuf' ? 'checked' : '' ?>
                                    >
                                    <span>Neuf sous blister</span>
                                </div>
                                <span class="filter-count"><?= $total_neuf ?></span>
                            </label>

                            <label class="filter-label <?= $etat_filtre === 'Occasion' ? 'active' : '' ?>">
                                <div class="filter-checkbox-wrap">
                                    <input 
                                        type="radio" 
                                        name="etat" 
                                        value="Occasion" 
                                        class="filter-input filter-radio-etat" 
                                        <?= $etat_filtre === 'Occasion' ? 'checked' : '' ?>
                                    >
                                    <span>Occasion certifiée</span>
                                </div>
                                <span class="filter-count"><?= $total_occasion ?></span>
                            </label>
                        </div>
                    </div>

                    <!-- FILTRE 2 : MARQUES -->
                    <div class="filter-group">
                        <div class="filter-group-title">Marques sélectionnées</div>
                        <div class="filter-options">
                            <?php foreach ($marques_autorisees as $marque): ?>
                                <?php 
                                    $is_checked = in_array($marque, $marques_filtre, true); 
                                    $count = $comptes_marques[$marque] ?? 0;
                                ?>
                                <label class="filter-label <?= $is_checked ? 'active' : '' ?>">
                                    <div class="filter-checkbox-wrap">
                                        <input 
                                            type="checkbox" 
                                            name="marque[]" 
                                            value="<?= htmlspecialchars($marque) ?>" 
                                            class="filter-input filter-checkbox-marque"
                                            <?= $is_checked ? 'checked' : '' ?>
                                        >
                                        <span><?= htmlspecialchars($marque) ?></span>
                                    </div>
                                    <span class="filter-count"><?= $count ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="sidebar-submit-btn">
                        Appliquer les filtres
                    </button>
                </form>
            </aside>

            <!-- ZONE D'AFFICHAGE DYNAMIQUE DES PRODUITS -->
            <section class="products-area">
                <div class="section-header">
                    <div>
                        <h1 class="section-title">Boutique & Catalogue</h1>
                        <p class="section-desc">Filtrez les téléphones par marque et par état selon vos critères.</p>
                    </div>
                    <div class="results-count" id="resultsCount">
                        <strong id="countNumber"><?= count($telephones_filtres) ?></strong> appareil(s) trouvé(s)
                    </div>
                </div>

                <?php if ($etat_filtre !== 'Tous' || !empty($marques_filtre) || $search !== ''): ?>
                    <div class="active-filters-bar" id="activeFiltersBar">
                        <span style="font-size:0.85rem; color:var(--text-muted); margin-right:0.3rem;">Filtres actifs :</span>

                        <?php if ($etat_filtre !== 'Tous'): ?>
                            <span class="filter-tag">
                                État: <?= htmlspecialchars($etat_filtre) ?>
                                <a href="<?= 'boutique.php?' . http_build_query(array_merge($_GET, ['etat' => 'Tous'])) ?>" class="remove-tag">×</a>
                            </span>
                        <?php endif; ?>

                        <?php foreach ($marques_filtre as $mf): ?>
                            <?php 
                                $next_marques = array_diff($marques_filtre, [$mf]);
                                $new_params = $_GET;
                                if (empty($next_marques)) {
                                    unset($new_params['marque']);
                                } else {
                                    $new_params['marque'] = array_values($next_marques);
                                }
                            ?>
                            <span class="filter-tag">
                                <?= htmlspecialchars($mf) ?>
                                <a href="<?= 'boutique.php?' . http_build_query($new_params) ?>" class="remove-tag">×</a>
                            </span>
                        <?php endforeach; ?>

                        <?php if ($search !== ''): ?>
                            <?php 
                                $new_params = $_GET;
                                unset($new_params['q']);
                            ?>
                            <span class="filter-tag">
                                Recherche: "<?= htmlspecialchars($search) ?>"
                                <a href="<?= 'boutique.php?' . http_build_query($new_params) ?>" class="remove-tag">×</a>
                            </span>
                        <?php endif; ?>

                        <a href="boutique.php" class="reset-filters-btn" style="margin-left:auto;">Effacer tout</a>
                    </div>
                <?php endif; ?>

                <!-- GRILLE DES PRODUITS -->
                <div class="product-grid" id="productGrid">
                    <?php if (!empty($telephones_filtres)): ?>
                        <?php foreach ($telephones_filtres as $tel): ?>
                            <?php
                                $has_real_image = !empty($tel['image']) && file_exists(__DIR__ . '/' . $tel['image']);
                                $placeholder_svg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600"><rect width="600" height="600" fill="%2312161f"/><rect x="160" y="60" width="280" height="480" rx="36" fill="%23161b22" stroke="%2330363d" stroke-width="2"/><text x="300" y="260" fill="%23f0f6fc" font-family="sans-serif" font-size="20" font-weight="bold" text-anchor="middle">' . rawurlencode($tel['modele']) . '</text><text x="300" y="300" fill="%232f81f7" font-family="sans-serif" font-size="15" font-weight="600" text-anchor="middle">' . rawurlencode($tel['couleur'] ?? '') . '</text><text x="300" y="345" fill="%238b949e" font-family="sans-serif" font-size="13" text-anchor="middle">📁 En attente photo</text></svg>';
                                $display_image = $has_real_image ? htmlspecialchars($tel['image']) : $placeholder_svg;
                            ?>
                            <article 
                                class="product-card" 
                                data-id="<?= $tel['id'] ?>"
                                data-marque="<?= htmlspecialchars($tel['marque']) ?>"
                                data-etat="<?= htmlspecialchars($tel['etat']) ?>"
                                data-modele="<?= htmlspecialchars(strtolower($tel['modele'])) ?>"
                                data-couleur="<?= htmlspecialchars(strtolower($tel['couleur'])) ?>"
                            >
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
                                            <span class="price-label">Prix</span>
                                            <div class="product-price">
                                                <?= number_format($tel['prix'], 0, ',', ' ') ?> <span class="currency">DA</span>
                                            </div>
                                        </div>
                                        <a 
                                            href="commande.php?id=<?= $tel['id'] ?>" 
                                            class="btn-buy"
                                            title="Commander sur les 58 Wilayas"
                                        >
                                            <span>Commander</span>
                                            <span>→</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="empty-state" id="emptyState" style="<?= empty($telephones_filtres) ? 'display:block;' : 'display:none;' ?>">
                    <div class="empty-icon">🔍</div>
                    <h3 class="empty-title">Aucun produit correspondant</h3>
                    <p class="empty-desc">Aucun téléphone ne correspond à la combinaison de filtres sélectionnée.</p>
                    <a href="boutique.php" class="btn-primary">Réinitialiser tous les filtres</a>
                </div>
            </section>
        </div>
    </main>

    <!-- PIED DE PAGE -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <h4>Bachir Apple</h4>
                <p>
                    Catalogue de smartphones premium en Algérie. 
                    Livraison sur les 58 Wilayas et paiement à la réception.
                </p>
            </div>
            <div class="footer-col">
                <h5>Navigation</h5>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="boutique.php">Boutique & Filtres</a></li>
                    <li><a href="boutique.php?etat=Neuf">Tous les modèles Neufs</a></li>
                    <li><a href="boutique.php?etat=Occasion">Tous les modèles Occasion</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Marques Phares</h5>
                <ul>
                    <li><a href="boutique.php?marque[]=Apple">Apple</a></li>
                    <li><a href="boutique.php?marque[]=Samsung">Samsung</a></li>
                    <li><a href="boutique.php?marque[]=Xiaomi">Xiaomi</a></li>
                    <li><a href="boutique.php?marque[]=Honor">Honor</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> Bachir Apple - Tous droits réservés.</span>
            <span>Site e-commerce & vitrine optimisé pour XAMPP.</span>
        </div>
    </footer>

    <!-- SCRIPT JAVASCRIPT POUR MISE À JOUR DYNAMIQUE INSTANTANÉE -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productCards = document.querySelectorAll('.product-card');
            const countNumber = document.getElementById('countNumber');
            const emptyState = document.getElementById('emptyState');
            const mobileToggle = document.getElementById('mobileFilterToggle');
            const sidebar = document.getElementById('filterSidebar');
            const searchInput = document.getElementById('globalSearchInput');

            if (mobileToggle && sidebar) {
                mobileToggle.addEventListener('click', function () {
                    sidebar.classList.toggle('open');
                    this.textContent = sidebar.classList.contains('open') ? '✕ Fermer les filtres' : '⚙️ Afficher les options de filtres';
                });
            }

            function applyDynamicFilters() {
                const checkedEtat = document.querySelector('input[name="etat"]:checked');
                const selectedEtat = checkedEtat ? checkedEtat.value : 'Tous';

                const checkedMarques = Array.from(document.querySelectorAll('input[name="marque[]"]:checked')).map(el => el.value);
                const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';

                document.querySelectorAll('.filter-label').forEach(label => {
                    const input = label.querySelector('input');
                    if (input) {
                        if (input.checked) {
                            label.classList.add('active');
                        } else {
                            label.classList.remove('active');
                        }
                    }
                });

                let visibleCount = 0;

                productCards.forEach(card => {
                    const cardEtat = card.getAttribute('data-etat');
                    const cardMarque = card.getAttribute('data-marque');
                    const cardModele = card.getAttribute('data-modele') || '';
                    const cardCouleur = card.getAttribute('data-couleur') || '';

                    const matchEtat = (selectedEtat === 'Tous') || (cardEtat === selectedEtat);
                    const matchMarque = (checkedMarques.length === 0) || checkedMarques.includes(cardMarque);
                    const matchSearch = (searchTerm === '') || 
                                        cardModele.includes(searchTerm) || 
                                        cardMarque.toLowerCase().includes(searchTerm) ||
                                        cardCouleur.includes(searchTerm);

                    if (matchEtat && matchMarque && matchSearch) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (countNumber) {
                    countNumber.textContent = visibleCount;
                }

                if (emptyState) {
                    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
                }

                const params = new URLSearchParams();
                if (selectedEtat !== 'Tous') params.set('etat', selectedEtat);
                checkedMarques.forEach(m => params.append('marque[]', m));
                if (searchTerm !== '') params.set('q', searchTerm);

                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.replaceState({}, '', newUrl);
            }

            document.querySelectorAll('.filter-radio-etat, .filter-checkbox-marque').forEach(input => {
                input.addEventListener('change', applyDynamicFilters);
            });

            if (searchInput) {
                searchInput.addEventListener('input', applyDynamicFilters);
            }
        });
    </script>
</body>
</html>
