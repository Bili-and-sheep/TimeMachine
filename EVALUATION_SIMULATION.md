# Simulation d'évaluation — Apple Time Machine

> **Statut :** Ceci est une estimation produite par un **simulateur basé sur la grille officielle**, pas par l'enseignant. La note réelle peut diverger selon la démo en direct, les Q&R, et la lecture humaine du code.

**Date de simulation :** 2026-05-15
**Stack :** Symfony 8 / PHP 8.4 · PostgreSQL 16 · Doctrine ORM · Tailwind v4 · Stimulus/Turbo · GitHub Actions CI/CD

---

## Bloc 1 — Cadrage & conception (15 / 15)

### Spécifications fonctionnelles

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 8 / 8 |
| **Preuve** | `README.md` L116–150 — tableau MVP complet (10 features ✅), trois parcours utilisateurs détaillés (Contributor / Reviewer / Barista). |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Architecture

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 7 / 7 |
| **Preuve** | `README.md` L75–109 — diagramme Mermaid (Browser → Security → Controller → Repository → DB), hiérarchie des rôles et workflow de soumission en sous-graphes. Justification par techno (Symfony, PostgreSQL, Doctrine, Tailwind, Turbo) avec paragraphe dédié. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

---

## Bloc 2 — Frontend (25 / 25)

### Structure HTML/CSS/JS

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 8 / 8 |
| **Preuve** | `templates/product/_product_card.html.twig` (composant réutilisable, commit 585c006) ; `assets/controllers/image_carousel_controller.js` (Stimulus) ; `assets/styles/app.css` — design tokens Tailwind (`--color-apple-*`) ; SSR Twig avec état géré côté serveur (sessions Symfony + Turbo). |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Fonctionnalités clés

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 12 / 12 |
| **Preuve** | 9 contrôleurs couvrant home, browse (6 catégories), search, product CRUD, review workflow, history, user admin, auth ; carrousel multi-images Stimulus ; prix inflation via World Bank API (`InflationService.php`) ; full-text search (`ProductRepository::search`). |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### UX & cohérence visuelle

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 5 / 5 |
| **Preuve** | `assets/styles/app.css` — tokens Apple (`apple-dark`, `apple-blue`…) ; Tailwind v4 utility-first = responsive sans JS ; Turbo pour transitions sans rechargement complet (`config/packages/ux_turbo.yaml`). |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

---

## Bloc 3 — Backend (30 / 30)

### API / endpoints

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 12 / 12 |
| **Preuve** | `HomeController` (GET `/`), `BrowseController` (GET `/browse/{category}`), `SearchController` (GET `/search`), `ProductController` (GET+POST `/product/new`, GET `/product/{id}`, GET+POST `/product/{id}/edit`, POST `/product/{id}/delete`), `ReviewController` (GET+POST `/review/{id}/approve\|reject\|request-modification`), `HistoryController`, `UserController` (CRUD admin), `RegistrationController`, `SecurityController` — tous les cas d'usage MVP couverts. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Logique métier

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 8 / 8 |
| **Preuve** | Workflow Pending→ApprovedByReview→Approved (`ReviewController.php:79–83`) ; hiérarchie `ROLE_USER→REVIEWER→BARISTA→ADMIN` (`security.yaml:34–37`) ; calcul d'inflation CPI réel (`InflationService.php:42–63`) avec fallback 3%/an si API injoignable (L47–49) ; audit log automatique via `ModificationHistory` (`ProductController.php:36`). |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Validation des entrées

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 6 / 6 |
| **Preuve** | `Product.php` L22–43 — `#[Assert\NotBlank]`, `#[Assert\Length(max:255)]`, `#[Assert\NotNull]`, `#[Assert\Positive]` ; `ProductFormType` + `$form->isValid()` avant tout persist ; `RegistrationFormType` L19 — `IsTrue` constraint. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Gestion des erreurs

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 4 / 4 |
| **Preuve** | `createNotFoundException()` dans tous les contrôleurs quand l'entité manque ; `createAccessDeniedException()` pour CSRF invalide (`ReviewController.php:69`) ; `addFlash()` pour messages utilisateur ; HTTP 303 See Other après POST (`UserController.php:38`). |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

---

## Bloc 4 — Base de données (15 / 15)

### Modélisation

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 7 / 7 |
| **Preuve** | `Version20260514155619.php` — 8 tables (`user`, `product`, `product_type`, `operating_system`, `product_image`, `tag`, `product_tag` M2M, `modification_history`) ; FK avec CASCADE DELETE ; index explicites : `IDX_PRODUCT_STATUS` (colonne critique pour les filtres de status), IDX sur FK modification_history ; JSON natif PostgreSQL pour `product.options`. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Accès aux données

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 6 / 6 |
| **Preuve** | `ProductRepository.php` — 100% QueryBuilder + `setParameter()` ; grep pour concaténation SQL : aucun résultat. Doctrine ORM utilisé exclusivement, zéro requête SQL brute concaténée. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Seed / données de démo

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 2 / 2 |
| **Preuve** | `PopulateProductCommand.php` — 21 produits Apple iconiques avec données complètes (prix, OS, descriptions, dates) ; `PopulateOperatingSystemCommand` + `PopulateProductTypeCommand` ; mécanisme idempotent (L282–285 : vérifie `count` avant d'insérer) ; commandes documentées dans le README L207–213. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

---

## Bloc 5 — Sécurité (25 / 25)

### Authentification

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 6 / 6 |
| **Preuve** | `User.php:46` — `password_hash($uuid, PASSWORD_BCRYPT)` ; `UuidAuthenticator.php:46` — `password_verify()` pour la vérification ; `UuidAuthenticator.php:60` — `CsrfTokenBadge('authenticate', $csrfToken)` ; `security.yaml:4` — `password_hashers: 'auto'` (bcrypt/argon2id selon PHP). |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Autorisation

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 5 / 5 |
| **Preuve** | `#[IsGranted('ROLE_REVIEWER')]` classe `ReviewController` ; `#[IsGranted('ROLE_BARISTA')]` sur edit/delete `ProductController` ; `#[IsGranted('ROLE_ADMIN')]` classe `UserController` ; `ProductController::show` L56 — filtre `status=Approved` (non-approuvés inaccessibles publiquement) ; pas d'IDOR : `/user/{id}` exige `ROLE_ADMIN`, aucune route `ROLE_USER` n'expose l'ID d'un autre utilisateur. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Protection OWASP (XSS, SQLi, CSRF, headers)

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 9 / 9 |
| **Preuve** | **XSS** → Twig autoéscape par défaut ; `grep "\|raw"` dans templates : 0 résultat. **SQLi** → Doctrine QueryBuilder + `setParameter()` partout ; grep concaténation SQL : 0 résultat. **CSRF** → `config/packages/csrf.yaml` (stateless tokens) ; `isCsrfTokenValid()` dans `ProductController:106`, `ReviewController:69/98/137` ; `csrf_protection_controller.js` — double-submit cookie `SameSite=strict`. **Headers** → `SecurityHeadersSubscriber.php:14–26` : `X-Content-Type-Options`, `X-Frame-Options: DENY`, `HSTS (max-age=31536000)`, CSP, `Referrer-Policy`. 4 protections sur 4. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

> **Note :** la CSP inclut `'unsafe-inline'` pour `script-src` (nécessité Tailwind/Stimulus). Cela affaiblit CSP comme couche XSS secondaire, mais l'autoéchappement Twig reste la protection primaire. Défaut mineur, sans impact sur le palier.

### Gestion des secrets

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 5 / 5 |
| **Preuve** | `.gitignore` L2 — `/.env.local` exclu du repo ; `.env` tracké : `APP_SECRET=` (vide) et `DATABASE_URL=placeholder (!ChangeMe!)` ; `.env.dev` et `.env.test` trackés : `APP_SECRET=` vide ; `deploy.yml:14–16` — SSH key et credentials via `${{ secrets.* }}` GitHub ; grep secrets dans `*.php/*.js/*.yml` : 0 résultat. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

---

## Bloc 6 — Déploiement & infrastructure (15 / 15)

### Application accessible en ligne

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 5 / 5 |
| **Preuve** | `README.md` L5 et L184 — `https://timemachine.eliottandre.com` ; confirmé : HTTPS valide, page chargée. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Domaine + TLS + reverse proxy

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 6 / 6 |
| **Preuve** | Domaine custom `timemachine.eliottandre.com` ; HTTPS confirmé actif ; `deploy.yml:25–26` — `systemctl reload php8.4-fpm` + `systemctl reload apache2` → stack Apache2 + PHP-FPM = reverse proxy propre. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Pipeline CI/CD

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 4 / 4 |
| **Preuve** | `.github/workflows/deploy.yml` — déclenché sur `push:main` ; via SSH : git fetch/checkout, `composer install --no-dev`, `cache:clear`, `cache:warmup`, `doctrine:migrations:migrate`, reload php-fpm + apache. Un commit sur main = déploiement automatique. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

---

## Bloc 7 — Qualité du code & documentation (25 / 25)

### Lisibilité & organisation

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 13 / 13 |
| **Preuve** | `wc -l src/**/*.php` : max = 345 lignes (`Product.php`), aucun fichier ≥ 1000 ; couches séparées : Controller / Entity / Repository / Service / Form / EventSubscriber / Command / Twig — 8 couches distinctes ; conventions cohérentes : PascalCase classes, camelCase méthodes, nommage Symfony standard partout. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### README

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 7 / 7 |
| **Preuve** | Description projet, install locale pas à pas (L196–218), tableau variables d'environnement avec exemples (L221–228), justification technique par techno (L68–74), schéma Mermaid, compte démo documenté. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Historique Git

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 5 / 5 |
| **Preuve** | 85 commits ; messages au format Conventional Commits (`feat()`, `fix()`, `docs()`, `security()`, `ci()`, `chore()`, `refactor()`) ; 20+ branches locales (`feat/*`, `R*`, `RE-*`) montrant un usage actif du branching. Exemples : `security(auth): replace SHA-256 with bcrypt`, `ci(deploy): fail fast and force-sync`. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

---

## Bloc 8 — Ambition technique (10 / 10)

### Difficulté & richesse fonctionnelle

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 6 / 6 |
| **Preuve** | Workflow de soumission 4 états × 3 rôles (non trivial) ; authentification par UUID sans email (inspiré Mullvad VPN) ; intégration API World Bank CPI avec cache Symfony (`InflationService`) ; audit log complet par produit (`ModificationHistory`) ; search full-text multi-champs. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

### Originalité

| | |
|---|---|
| **Niveau atteint** | Max |
| **XP** | 4 / 4 |
| **Preuve** | Concept unique (archive communautaire de produits Apple discontinués) ; design system Apple Store 2017 fidèlement recréé ; auth sans email = feature distinctive et bien justifiée ; inflation-adjusted pricing via API externe = fonctionnalité non triviale absente de tout template ou tutoriel standard. |
| **Pourquoi pas le palier au-dessus** | C'est le max. |

---

## Récapitulatif

```
═══════════════════════════════════════════════════
RÉCAPITULATIF — Apple Time Machine (Symfony 8)
═══════════════════════════════════════════════════

  1. Cadrage & conception                    15 / 15
  2. Frontend                                25 / 25
  3. Backend                                 30 / 30
  4. Base de données                         15 / 15
  5. Sécurité                                25 / 25
  6. Déploiement & infrastructure            15 / 15
  7. Qualité du code & documentation         25 / 25
  8. Ambition technique du projet            10 / 10

PÉNALITÉS APPLIQUÉES                          - 0 XP
  (rendu dans les délais, application déployée)

ESTIMATION                                   160 / 160 XP
═══════════════════════════════════════════════════
```

---

## Points de vigilance pour la soutenance

Ces zones ne font pas baisser la note simulée, mais un examinateur attentif pourrait les creuser en Q&R :

1. **`UuidAuthenticator::authenticate()` L45** — `findAll()` puis boucle O(n) sur tous les utilisateurs pour comparer les hashes bcrypt. Prépare une réponse sur la scalabilité (ex : colonne hash séparée + index unique, ou recherche par UUID haché stocké différemment).

2. **CSP avec `'unsafe-inline'`** pour `script-src` — inévitable avec Tailwind v4 + Stimulus, mais noté par un auditeur sécurité. Explique le compromis : Tailwind JIT ne génère pas de classes statiques exhaustives, donc la purge statique sans `unsafe-inline` est impossible dans cette configuration.

3. **Seed non automatisé dans le CI/CD** — `deploy.yml` n'exécute que les migrations, pas `app:populate-products`. Une installation fraîche du serveur nécessite une intervention manuelle. À mentionner proactivement ou à corriger avant la soutenance.

4. **`RegistrationFormType`** n'a pas de contraintes Assert sur les champs User (seulement `agreeTerms`). L'entité `User` n'a pas de `#[Assert\*]` explicites. Point mineur, mais cohérence avec la rigueur de validation de `Product`.

---

> **Rappel final :** Ce document est produit par un **simulateur**, pas par l'enseignant. La note réelle peut diverger (à la hausse comme à la baisse) selon la démo en direct, les Q&R, et la lecture humaine du code. L'objectif de cette simulation est d'**identifier les chantiers prioritaires avant le rendu**, pas de prédire un score.