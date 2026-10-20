<?php
require_once 'telephones.php';
require_once 'wilayas.php';

// Fichier de stockage des commandes
$commandes_file = __DIR__ . '/commandes.json';

// Récupération du téléphone sélectionné
$tel_id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 1;
$selected_tel = null;
foreach ($telephones as $t) {
    if ($t['id'] === $tel_id) {
        $selected_tel = $t;
        break;
    }
}
if (!$selected_tel) {
    $selected_tel = $telephones[0];
}

$success = false;
$errors = [];
$commande_data = null;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'passer_commande') {
    $nom = trim($_POST['nom'] ?? '');
    $telephone_client = trim($_POST['telephone'] ?? '');
    $wilaya_code = trim($_POST['wilaya'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');

    // Validation
    if (empty($nom)) {
        $errors[] = 'Veuillez renseigner votre nom et prénom.';
    }

    if (empty($telephone_client)) {
        $errors[] = 'Veuillez saisir votre numéro de téléphone.';
    } elseif (!preg_match('/^(0[5672][0-9]{8}|[0-9]{9,10})$/', str_replace([' ', '-', '.'], '', $telephone_client))) {
        $errors[] = 'Veuillez saisir un numéro de téléphone algérien valide (ex: 0550 12 34 56).';
    }

    if (empty($wilaya_code) || !isset($wilayas[$wilaya_code])) {
        $errors[] = 'Veuillez sélectionner votre Wilaya parmi les 58 Wilayas d\'Algérie.';
    }

    if (empty($errors)) {
        $wilaya_nom = $wilayas[$wilaya_code];
        $commande_id = 'BA-' . date('ymd') . '-' . rand(1000, 9999);

        $commande_data = [
            'id' => $commande_id,
            'date' => date('d/m/Y H:i:s'),
            'telephone' => [
                'id' => $selected_tel['id'],
                'marque' => $selected_tel['marque'],
                'modele' => $selected_tel['modele'],
                'couleur' => $selected_tel['couleur'],
                'etat' => $selected_tel['etat'],
                'prix' => $selected_tel['prix'],
                'garantie' => $selected_tel['garantie'],
                'batterie' => $selected_tel['batterie'],
                'image' => $selected_tel['image']
            ],
            'client' => [
                'nom' => $nom,
                'telephone' => $telephone_client,
                'wilaya_code' => $wilaya_code,
                'wilaya_nom' => $wilaya_nom,
                'adresse' => $adresse
            ],
            'statut' => 'En cours d\'exécution'
        ];

        // Sauvegarde dans un fichier JSON sans base SQL complexe
        $historique = [];
        if (file_exists($commandes_file)) {
            $contenu = file_get_contents($commandes_file);
            $historique = json_decode($contenu, true) ?: [];
        }
        $historique[] = $commande_data;
        file_put_contents($commandes_file, json_encode($historique, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $success = true;
    }
}

// Calcul de l'image ou du placeholder
$has_real_image = !empty($selected_tel['image']) && file_exists(__DIR__ . '/' . $selected_tel['image']);
$placeholder_svg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600"><rect width="600" height="600" fill="%2312161f"/><rect x="160" y="60" width="280" height="480" rx="36" fill="%23161b22" stroke="%2330363d" stroke-width="2"/><text x="300" y="260" fill="%23f0f6fc" font-family="sans-serif" font-size="20" font-weight="bold" text-anchor="middle">' . rawurlencode($selected_tel['modele']) . '</text><text x="300" y="300" fill="%232f81f7" font-family="sans-serif" font-size="15" font-weight="600" text-anchor="middle">' . rawurlencode($selected_tel['couleur'] ?? '') . '</text><text x="300" y="345" fill="%238b949e" font-family="sans-serif" font-size="13" text-anchor="middle">📁 En attente photo</text></svg>';
$display_image = $has_real_image ? htmlspecialchars($selected_tel['image']) : $placeholder_svg;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $success ? "Commande en cours d'exécution" : "Commander " . htmlspecialchars($selected_tel['modele']) ?> | Bachir Apple</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">
                <span class="brand-logo-icon"></span>
                <span>Bachir <span class="highlight">Apple</span></span>
            </a>

            <nav>
                <ul class="nav-links">
                    <li><a href="index.php" class="nav-link">Accueil</a></li>
                    <li><a href="boutique.php" class="nav-link">Boutique & Filtres</a></li>
                    <li><a href="boutique.php" class="nav-cta-btn">Changer d'appareil</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-wrapper">
        <?php if ($success && $commande_data): ?>
            <!-- ÉCRAN DE SUCCÈS : ATTENDS, TA COMMANDE EST EN COURS D'EXÉCUTION -->
            <section class="order-execution-card">
                <div class="execution-badge">
                    <span class="pulse-dot"></span>
                    <span>Traitement en direct</span>
                </div>

                <div class="execution-icon-wrapper">
                    <div class="pulse-ring"></div>
                    <span class="execution-icon">⏳</span>
                </div>

                <h1 class="execution-title">Attends, ta commande est en cours d'exécution !</h1>
                
                <p class="execution-desc">
                    Merci <strong><?= htmlspecialchars($commande_data['client']['nom']) ?></strong> ! Votre demande d'achat a été transmise instantanément au service expédition de Bachir Apple.
                    Nous vous appellerons sous peu au <strong><?= htmlspecialchars($commande_data['client']['telephone']) ?></strong> pour confirmer la livraison.
                </p>

                <div class="order-recap-box">
                    <div class="recap-header">
                        <span>Récapitulatif de votre commande</span>
                        <span class="order-ref">N° <?= htmlspecialchars($commande_data['id']) ?></span>
                    </div>

                    <div class="recap-item">
                        <span class="recap-label">📱 Téléphone commandé</span>
                        <span class="recap-val"><strong><?= htmlspecialchars($commande_data['telephone']['modele']) ?></strong></span>
                    </div>

                    <div class="recap-item">
                        <span class="recap-label">🎨 Couleur officielle</span>
                        <span class="recap-val"><span class="color-dot"></span> <?= htmlspecialchars($commande_data['telephone']['couleur']) ?></span>
                    </div>

                    <div class="recap-item">
                        <span class="recap-label">✨ État</span>
                        <span class="recap-val"><?= htmlspecialchars($commande_data['telephone']['etat']) ?></span>
                    </div>

                    <?php if ($commande_data['telephone']['etat'] === 'Occasion' && !empty($commande_data['telephone']['batterie'])): ?>
                        <div class="recap-item">
                            <span class="recap-label">🔋 Santé batterie</span>
                            <span class="recap-val" style="color:#58a6ff; font-weight:600;"><?= htmlspecialchars($commande_data['telephone']['batterie']) ?>%</span>
                        </div>
                    <?php endif; ?>

                    <div class="recap-item">
                        <span class="recap-label">🛡️ Garantie</span>
                        <span class="recap-val"><?= htmlspecialchars($commande_data['telephone']['garantie']) ?></span>
                    </div>

                    <div class="recap-item">
                        <span class="recap-label">📍 Destination (Wilaya)</span>
                        <span class="recap-val"><?= htmlspecialchars($commande_data['client']['wilaya_nom']) ?></span>
                    </div>

                    <?php if (!empty($commande_data['client']['adresse'])): ?>
                        <div class="recap-item">
                            <span class="recap-label">🏠 Adresse / Commune</span>
                            <span class="recap-val"><?= htmlspecialchars($commande_data['client']['adresse']) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="recap-item total-row">
                        <span class="recap-label">Montant à régler à la livraison</span>
                        <span class="recap-price"><?= number_format($commande_data['telephone']['prix'], 0, ',', ' ') ?> DA</span>
                    </div>
                </div>

                <div class="execution-actions">
                    <a 
                        href="https://wa.me/213555000000?text=<?= urlencode("Bonjour Bachir Apple, je viens de passer la commande " . $commande_data['id'] . " pour le " . $commande_data['telephone']['modele'] . " (" . $commande_data['telephone']['couleur'] . ") vers la Wilaya de " . $commande_data['client']['wilaya_nom']) ?>" 
                        target="_blank" 
                        class="btn-primary"
                    >
                        <span>💬 Suivre sur WhatsApp</span>
                    </a>

                    <a href="boutique.php" class="btn-secondary">
                        <span>← Retourner à la boutique</span>
                    </a>
                </div>
            </section>

        <?php else: ?>

            <!-- FORMULAIRE DE COMMANDE DIRECTE SANS INSCRIPTION -->
            <div class="checkout-layout">
                <aside class="checkout-product-card">
                    <div class="checkout-image-wrapper">
                        <span class="badge-etat <?= strtolower($selected_tel['etat']) ?>">
                            <?= htmlspecialchars($selected_tel['etat']) ?>
                        </span>
                        <span class="badge-marque">
                            <?= htmlspecialchars($selected_tel['marque']) ?>
                        </span>

                        <img 
                            src="<?= $display_image ?>" 
                            alt="<?= htmlspecialchars($selected_tel['modele']) ?>" 
                            class="checkout-image"
                        >
                    </div>

                    <div class="checkout-product-details">
                        <span class="product-brand"><?= htmlspecialchars($selected_tel['marque']) ?></span>
                        <h2 class="checkout-title"><?= htmlspecialchars($selected_tel['modele']) ?></h2>

                        <div class="product-color-row">
                            <span class="color-indicator"></span>
                            <span>Couleur : <strong><?= htmlspecialchars($selected_tel['couleur']) ?></strong></span>
                        </div>

                        <div class="product-specs">
                            <div class="spec-item">
                                <span class="spec-icon">🛡️</span>
                                <span>Garantie : <span class="spec-value"><?= htmlspecialchars($selected_tel['garantie']) ?></span></span>
                            </div>

                            <?php if ($selected_tel['etat'] === 'Occasion' && !empty($selected_tel['batterie'])): ?>
                                <div class="spec-item battery-spec">
                                    <span class="spec-icon">🔋</span>
                                    <span>Batterie : <span class="spec-value"><?= htmlspecialchars($selected_tel['batterie']) ?>%</span></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="checkout-price-box">
                            <span class="price-label">Prix Total (Paiement à la livraison)</span>
                            <div class="product-price">
                                <?= number_format($selected_tel['prix'], 0, ',', ' ') ?> <span class="currency">DA</span>
                            </div>
                        </div>

                        <div class="checkout-trust-badges">
                            <div class="trust-badge-item">✓ Téléphone 100% Original</div>
                            <div class="trust-badge-item">✓ Vérification avant paiement</div>
                            <div class="trust-badge-item">✓ Livraison rapide 58 Wilayas</div>
                        </div>
                    </div>
                </aside>

                <section class="checkout-form-container">
                    <div class="form-header">
                        <span class="step-badge">Étape 2 / 2</span>
                        <h1 class="form-title">Finaliser votre commande</h1>
                        <p class="form-subtitle">Remplissez vos coordonnées ci-dessous pour lancer la livraison immédiate. Aucune inscription requise.</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="form-errors-box">
                            <strong>Veuillez corriger les erreurs suivantes :</strong>
                            <ul>
                                <?php foreach ($errors as $err): ?>
                                    <li><?= htmlspecialchars($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="commande.php" method="POST" class="order-form">
                        <input type="hidden" name="action" value="passer_commande">
                        <input type="hidden" name="id" value="<?= $selected_tel['id'] ?>">

                        <!-- 1) NOM ET PRÉNOM -->
                        <div class="form-group">
                            <label for="nom" class="form-label">
                                <span>1. Nom et Prénom</span>
                                <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="nom" 
                                name="nom" 
                                class="form-input" 
                                placeholder="Ex: Mohamed Benali"
                                value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                                required
                            >
                        </div>

                        <!-- 2) NUMÉRO DE TÉLÉPHONE -->
                        <div class="form-group">
                            <label for="telephone" class="form-label">
                                <span>2. Numéro de téléphone</span>
                                <span class="required">*</span>
                            </label>
                            <input 
                                type="tel" 
                                id="telephone" 
                                name="telephone" 
                                class="form-input" 
                                placeholder="Ex: 0550 12 34 56 ou 0661 ..."
                                value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>"
                                required
                            >
                            <small class="form-hint">Nous vous appellerons sur ce numéro pour la confirmation d'envoi.</small>
                        </div>

                        <!-- 3) WILAYA EN ALGÉRIE (58 WILAYAS DÉROULANTES) -->
                        <div class="form-group">
                            <label for="wilaya" class="form-label">
                                <span>3. Wilaya de destination (58 Wilayas d'Algérie)</span>
                                <span class="required">*</span>
                            </label>
                            <select id="wilaya" name="wilaya" class="form-select" required>
                                <option value="">-- Sélectionnez votre Wilaya --</option>
                                <?php foreach ($wilayas as $code => $nom_wilaya): ?>
                                    <option 
                                        value="<?= htmlspecialchars($code) ?>"
                                        <?= (isset($_POST['wilaya']) && $_POST['wilaya'] === $code) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($nom_wilaya) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 4) COMMUNE / ADRESSE -->
                        <div class="form-group">
                            <label for="adresse" class="form-label">
                                <span>4. Commune / Adresse de livraison (Optionnel)</span>
                            </label>
                            <input 
                                type="text" 
                                id="adresse" 
                                name="adresse" 
                                class="form-input" 
                                placeholder="Ex: Bab Ezzouar, Cité 2068 logts"
                                value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <span>Couleur confirmée du smartphone</span>
                            </label>
                            <div class="color-confirm-pill">
                                <span class="color-dot"></span>
                                <span><?= htmlspecialchars($selected_tel['couleur']) ?></span>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit-order">
                            <span>🚀 Valider et Commander</span>
                            <span>(<?= number_format($selected_tel['prix'], 0, ',', ' ') ?> DA)</span>
                        </button>
                    </form>
                </section>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <h4>Bachir Apple</h4>
                <p>Commandes sécurisées avec paiement en espèces à la livraison partout en Algérie.</p>
            </div>
            <div class="footer-col">
                <h5>Navigation</h5>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="boutique.php">Boutique & Filtres</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Assistance</h5>
                <ul>
                    <li><a href="https://wa.me/213555000000" target="_blank">Contact WhatsApp</a></li>
                    <li><a href="index.php#catalogue">Catalogue complet</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> Bachir Apple - Tous droits réservés.</span>
            <span>Paiement à la réception sur les 58 Wilayas.</span>
        </div>
    </footer>

</body>
</html>
