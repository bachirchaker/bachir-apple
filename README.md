#  Bachir Apple - Vitrine & E-Commerce Premium

Site web vitrine et e-commerce moderne, rapide et épuré dédié à la vente de smartphones neufs et d'occasion en Algérie.
Conçu pour fonctionner sous **XAMPP avec PHP**, sans base de données SQL complexe, sans chatbot IA et sans inscription obligatoire pour les acheteurs.

---

## ✨ Fonctionnalités Clés

- **Design Dark Mode Haut de Gamme :** Inspiré de l'esthétique Apple (`#0d1117`, cartes `#161b22`, accents bleu électrique, typographie soignée).
- **Catalogue Centralisé (`telephones.php`) :** Gestion simplifiée de tous les modèles dans un fichier PHP structuré.
- **Barre de Recherche Globale :** Recherche instantanée par nom de modèle, marque ou couleur.
- **Boutique & Sidebar de Filtres Dynamiques (`boutique.php`) :**
  - Filtre par état : *Tous*, *Neuf* (sous blister), *Occasion* (certifiée).
  - Filtre par marques strictement limitées à : **Apple, Samsung, Honor, Xiaomi, Oppo, Realme**.
  - Filtrage instantané côté client (sans rechargement de page) et compatible méthode native PHP GET.
- **Transparence Batterie :** La santé de la batterie (ex. *91%*) s'affiche explicitement sur les modèles d'occasion et est masquée sur les modèles neufs.
- **Tunnel de Commande Sans Inscription (`commande.php`) :**
  - Formulaire rapide : Nom & Prénom, Téléphone, et sélection déroulante des **58 Wilayas d'Algérie**.
  - Message de confirmation animé : **« Attends, ta commande est en cours d'exécution ! »**.
  - Suivi immédiat via WhatsApp et sauvegarde autonome dans `commandes.json`.

---

## 📁 Architecture du Projet

```text
bachir-apple/
├── telephones.php       # Catalogue centralisé des smartphones (PHP Array)
├── wilayas.php          # Nomenclature officielle des 58 Wilayas d'Algérie
├── index.php            # Page d'accueil vitrine avec recherche globale
├── boutique.php         # Page boutique avec sidebar de filtres dynamiques
├── commande.php         # Formulaire de commande directe et confirmation
├── commandes.json       # Journal de stockage des commandes reçues
├── style.css            # Feuille de style complète Dark Mode & Responsive
├── uploads/             # Dossier réservé aux photos des smartphones
│   └── .gitkeep
└── README.md
```

---

## 🚀 Installation & Exécution Locale (XAMPP)

1. **Cloner le projet** dans le dossier `htdocs` de XAMPP :
   ```bash
   cd C:/xampp/htdocs
   git clone https://github.com/bachirchaker/bachir-apple.git
   ```

2. **Démarrer Apache** depuis le panneau de configuration XAMPP (*XAMPP Control Panel*).

3. **Accéder au site** dans votre navigateur :
   - Accueil : `http://localhost/bachir-apple/`
   - Boutique : `http://localhost/bachir-apple/boutique.php`

---

## 📷 Gestion des Photos

Pour afficher vos propres photos de smartphones, copiez simplement vos fichiers d'images dans le sous-dossier `uploads/` avec les noms correspondants :
- `iphone16promax.jpg` (iPhone 16 Pro Max - Titane Désert)
- `iphone15pro.jpg` (iPhone 15 Pro - Titane Naturel)
- `samsungs25ultra.jpg` (Samsung Galaxy S25 Ultra - Gris Titane)
- `xiaomi14pro.jpg` (Xiaomi 14 Pro - Noir)
- `iphone13.jpg` (iPhone 13 - Bleu Minuit)
- `opporeno11.jpg` (Oppo Reno 11 Pro - Gris Argent)
- `realmegt6.jpg` (Realme GT 6 - Argent Racing)
- `honor200pro.jpg` (Honor 200 Pro - Argent Titane)

*(Un système de placeholder élégant prend le relais si un fichier n'est pas encore présent).*

---

## 📄 Licence

Projet développé pour **Bachir Apple** © <?= date('Y') ?> - Tous droits réservés.
