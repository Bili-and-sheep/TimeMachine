# Rapport d'évaluation simulée — Apple Time Machine
**Cours :** Infrastructure et Programmation Web — Bachelor 3 Oteria  
**Date du rapport :** 2026-05-13  
**Outil :** Simulateur d'évaluation (≠ note officielle de l'enseignant)

> ⚠️ Ce document est produit par un **simulateur**, pas par l'enseignant qui notera réellement le projet. La note réelle peut diverger à la hausse ou à la baisse selon la démo en live, les Q&R, et la lecture humaine du code. L'objectif est d'identifier les chantiers prioritaires avant le rendu.

---

## Sommaire

1. [Scores par critère](#scores-par-critère)
2. [Récapitulatif](#récapitulatif)
3. [Plan de remédiation — atteindre le score maximum](#plan-de-remédiation)

---

## Scores par critère

### 1. Cadrage & conception — 7 / 15

#### Spécifications fonctionnelles `4 / 8`

| | |
|---|---|
| **Niveau** | Intermédiaire |
| **Preuve** | `README.md` lignes 15–55 — sections "Features", "Product Pages", "Community", "Admin Panel" listées avec détail |
| **Manque** | Aucun parcours utilisateur formalisé, aucun MVP explicitement défini, aucune user story |

#### Architecture proposée `3 / 7`

| | |
|---|---|
| **Niveau** | Intermédiaire |
| **Preuve** | `README.md` lignes 59–65 — Symfony justifié en 1 paragraphe. Section "Tech Stack" présente |
| **Manque** | Aucun schéma des composants (ni Mermaid, ni image, ni ASCII art). PostgreSQL, Tailwind, Doctrine, Stimulus non justifiés |

---

### 2. Frontend — 15 / 25

#### Structure HTML/CSS/JS `4 / 8`

| | |
|---|---|
| **Niveau** | Intermédiaire |
| **Preuve** | `templates/base.html.twig` — héritage Twig, blocks. Tailwind v4 avec tokens Apple (`app.css:4-9`). Partials `user/_form.html.twig`, `user/_delete_form.html.twig` |
| **Manque** | Pas de macros Twig ni de composants UX. JS client = `console.log` uniquement (`assets/app.js:7`). La page profil (`mydrilla.html.twig`) mélange Tailwind v3/v4, a un `<div>` non fermé et du code commenté |

#### Fonctionnalités clés `6 / 12`

| | |
|---|---|
| **Niveau** | Intermédiaire |
| **Fonctionne** | Browse par catégorie (`BrowseController.php`), page produit complète (`product/show.html.twig`), soumission (`ProductController.php:23`), workflow review approve/reject (`ReviewController.php:63-148`) |
| **Absent** | **Recherche** : `grep -rn "search" src/ templates/` → 0 résultat. Le README promet "Full-text search" (lignes 25-27) |
| **Absent** | **Accueil** : `HomeController.php:18` → `findLastApproved()` = 1 seul produit. README promet "featured AND recently added products" |
| **Absent** | **Upload d'image** : `ProductFormType.php` — aucun champ image dans le formulaire de soumission |

#### UX & cohérence visuelle `5 / 5`

| | |
|---|---|
| **Niveau** | Maximum |
| **Preuve** | Design system Apple cohérent (tokens CSS `app.css:4-8`), `meta viewport` présent, classes responsive partout (`md:`, `lg:`, `sm:`), nav sticky + footer + flash messages uniformes |

---

### 3. Backend — 25 / 30

#### API / endpoints `12 / 12`

| | |
|---|---|
| **Niveau** | Maximum |
| **Preuve** | Tous les cas d'usage couverts : `GET /`, `/browse/{c}`, `/product/new`, `/product/{id}`, `/product/{id}/edit`, `/product/{id}/delete`, `/product/{id}/history`, `/review/*`, `/user/*`, `/login`, `/logout`, `/register`, `/mydrilla` |

#### Logique métier `8 / 8`

| | |
|---|---|
| **Niveau** | Maximum |
| **Preuve** | Prix inflation (`Product.php:153-162`), workflow 2 niveaux (`ReviewController.php:73-78`), hiérarchie de rôles (`security.yaml:40-43`), historique d'audit (`ModificationHistory`), compteur pending global (`AppExtension.php:13`) |

#### Validation des entrées `3 / 6`

| | |
|---|---|
| **Niveau** | Intermédiaire |
| **Preuve** | `ProductFormType.php` — `TextType`, `IntegerType`, `DateType`, `EntityType`. Validation manuelle du commentaire (`ReviewController.php:92`) |
| **Manque** | Aucune contrainte `#[Assert\*]` sur les entités ou FormTypes. Un prix négatif est accepté. Aucune longueur minimale sur le nom du produit |

#### Gestion des erreurs `2 / 4`

| | |
|---|---|
| **Niveau** | Intermédiaire |
| **Preuve** | `createNotFoundException()` dans tous les controllers. Flash messages success/error. Codes HTTP via exceptions Symfony (404, 403) |
| **Manque** | Pas de templates d'erreur custom (`templates/bundles/TwigBundle/Exception/` absent). Pas de `EventSubscriber` global |

---

### 4. Base de données — 10 / 15

#### Modélisation `3 / 7`

| | |
|---|---|
| **Niveau** | Intermédiaire |
| **Preuve** | 7 entités cohérentes avec relations ManyToOne/OneToMany/ManyToMany. `UniqueConstraint` sur `User.uuid` (`User.php:12-13`) |
| **Manque** | Dossier `migrations/` vide (seulement `.gitignore`). Pas d'index explicite sur `status`, `ProductType`. Schéma non reproductible proprement |

#### Accès aux données `6 / 6`

| | |
|---|---|
| **Niveau** | Maximum |
| **Preuve** | Doctrine QueryBuilder avec `setParameter()` systématique (`ProductRepository.php:20, 29`). Aucune concaténation SQL trouvée |

#### Seed `1 / 2`

| | |
|---|---|
| **Niveau** | Intermédiaire |
| **Preuve** | `PopulateOperatingSystemCommand.php` (200+ versions OS), `PopulateProductTypeCommand.php` (18 catégories) |
| **Manque** | Aucun seed de produits. Sur installation fraîche : archive vide, impossible de tester les fonctionnalités clés sans ajouter soi-même des produits |

---

### 5. Sécurité — 12 / 25

#### Authentification `3 / 6`

| | |
|---|---|
| **Niveau** | Intermédiaire |
| **Preuve** | `UuidAuthenticator.php:44` — `hash('sha256', $uuid)`. `SelfValidatingPassport` + `CsrfTokenBadge` (ligne 57) + `RememberMeBadge`. Approche Mullvad-like |
| **Manque** | SHA256 n'est pas un algorithme de hachage de mot de passe (pas de salt, pas de cost factor — bcrypt/argon2 attendus). La page profil ne permet pas de retrouver son UUID après connexion (code commenté dans `mydrilla.html.twig`) |

#### Autorisation `5 / 5`

| | |
|---|---|
| **Niveau** | Maximum |
| **Preuve** | `#[IsGranted]` sur toutes les routes sensibles. Hiérarchie `security.yaml:40-43`. Pas d'IDOR identifié (éditions réservées à `ROLE_BARISTA`) |

#### Protection OWASP `4 / 9`

| Protection | Statut | Preuve |
|---|---|---|
| XSS | ✅ | Twig auto-escape par défaut, aucun filtre `\|raw` dans les templates |
| SQLi | ✅ | Doctrine QueryBuilder + `setParameter()` partout |
| CSRF | ❌ | Formulaires approve/reject/request-modification dans `review/show.html.twig` **sans token CSRF**. `ReviewController.php` ne valide aucun token |
| Headers sécurité | ❌ | Aucun middleware de security headers (grep `CSP\|HSTS\|helmet` → 0 résultat) |

Score : 2/4 protections → palier intermédiaire (4 XP)

#### Gestion des secrets `0 / 5` 🚨

| | |
|---|---|
| **Niveau** | **ZÉRO — SECRET COMMITÉ** |
| **Preuve** | `git ls-files .env .env.dev` → les deux fichiers sont trackés. `.env:40` et `.env.dev:1` contiennent `APP_SECRET=<SECRET_REMOVED>` en clair |
| **Règle** | 1 secret en clair dans le repo = 0 XP sur ce critère, sans exception |

---

### 6. Déploiement & infrastructure — 0 / 15

#### Application accessible en ligne `0 / 5`

Aucune URL dans le README, aucune config d'hébergement dans le repo.

#### Domaine + TLS + reverse proxy `0 / 6`

`compose.yaml` en configuration locale uniquement. Aucun Dockerfile, aucun Nginx/Caddy.

#### Pipeline CI/CD `0 / 4`

Aucun `.github/workflows/`, `.gitlab-ci.yml`, `Makefile` ou `deploy.sh`.

---

### 7. Qualité du code & documentation — 15 / 25

#### Lisibilité & organisation `7 / 13`

| | |
|---|---|
| **Niveau** | Intermédiaire |
| **Correct** | Séparation Controller / Repository / Entity / Form / Enum / Twig / Security ✓. Aucun fichier > 1000 lignes (max : `Product.php` 351l) ✓ |
| **Violation** | Propriétés d'entité en **PascalCase** au lieu de camelCase PHP : `Product.php:14` → `$ProductName`, `$TechnicalName`, `$ReleaseDate`… Violation PSR-1 |

Règle : 1 critère manqué sur 3 → palier intermédiaire (7 XP)

#### README `3 / 7`

| Section | Présent |
|---|---|
| Description du projet | ✅ |
| Fonctionnalités | ✅ |
| Choix techniques justifiés | ✅ (Symfony uniquement) |
| Installation locale | ❌ |
| Variables d'environnement | ❌ |

Note : `README.md:106` — "Thanks Claude.IA for this nice readme" → l'enseignant peut poser des questions en soutenance sur la compréhension du projet.

#### Historique Git `5 / 5`

| | |
|---|---|
| **Niveau** | Maximum |
| **Preuve** | 44 commits. 10 feature branches (`feat/auth`, `feat/entities`, `feat/product-workflow`…). Messages précis et descriptifs. Merge commits propres |

---

### 8. Ambition technique — 7 / 10

#### Difficulté & richesse `3 / 6`

| | |
|---|---|
| **Niveau** | Intermédiaire |
| **Au-delà du CRUD** | Workflow validation 2 niveaux (6 états `SubmissionStatus`), calcul prix inflation, auth UUID-only, historique d'audit |
| **Manque** | Pas d'API externe (l'inflation est 3% fixe, pas une vraie API INSEE/BLS), pas de temps réel, pas de traitement asynchrone |

#### Originalité `4 / 4`

| | |
|---|---|
| **Niveau** | Maximum |
| **Preuve** | Concept d'archive communautaire Apple discontinués = niche précise. Recréation Apple Store 2017. Auth UUID-only inspirée de Mullvad |

---

## Récapitulatif

```
═══════════════════════════════════════════════════════════════
RÉCAPITULATIF — APPLE TIME MACHINE
═══════════════════════════════════════════════════════════════

  1. Cadrage & conception                   7  / 15
  2. Frontend                              15  / 25
  3. Backend                               25  / 30
  4. Base de données                       10  / 15
  5. Sécurité                              12  / 25
  6. Déploiement & infrastructure           0  / 15  ⚠ URL manquante
  7. Qualité du code & documentation       15  / 25
  8. Ambition technique du projet           7  / 10

PÉNALITÉS
  Retard                             À confirmer (date de rendu ?)
  App non déployée à la soutenance   À confirmer (URL ?)
  Plagiat                             0  (style homogène, code cohérent)

ESTIMATION actuelle                       91  / 160 XP
ESTIMATION si URL + HTTPS confirmés      102  / 160 XP
═══════════════════════════════════════════════════════════════
```

---

## Plan de remédiation

> Objectif : passer de **91 → 155+ / 160 XP**.  
> Classé par ratio **gain XP / effort** décroissant.

---

### R1 — Retirer les secrets committés `+5 XP` ⏱ 15 min

**Problème :** `APP_SECRET` en clair dans `.env` et `.env.dev` (tous deux trackés).

```bash
# 1. Supprimer du tracking (sans effacer les fichiers locaux)
git rm --cached .env .env.dev

# 2. Ajouter au .gitignore
echo ".env" >> .gitignore
echo ".env.dev" >> .gitignore

# 3. Régénérer un nouveau secret (l'actuel est compromis)
#    Dans .env.local (NON commité) :
APP_SECRET=<nouvelle_valeur_générée>

# 4. Documenter dans le README les variables à configurer (voir R3)

# 5. Commiter
git add .gitignore
git commit -m "Remove committed secrets: untrack .env and .env.dev"
```

> ⚠️ Le secret actuel (`<SECRET_REMOVED>`) reste dans l'historique git. Pour une vraie production, il faudrait purger l'historique (`git filter-repo`) ou invalider le secret côté serveur.

**Gain :** 0 → 5 XP (critère "Gestion des secrets")

---

### R2 — Ajouter un schéma d'architecture dans le README `+4 XP` ⏱ 20 min

**Problème :** le README décrit les fonctionnalités mais aucun schéma des composants.

Ajouter cette section dans `README.md` après "Tech Stack" :

````markdown
## 🏗️ Architecture

```mermaid
graph TD
    Browser["🌐 Navigateur"]

    subgraph Symfony["Symfony 8 (PHP 8.4)"]
        direction TB
        Controller["Controllers\n(ProductController, ReviewController…)"]
        Form["Form Types\n(ProductFormType)"]
        Security["Security\n(UuidAuthenticator)"]
        Twig["Templates Twig\n(Tailwind v4)"]
        Repository["Repositories\n(Doctrine ORM)"]
        Command["Console Commands\n(Seed OS / ProductType)"]
    end

    DB[("PostgreSQL 16")]
    Mail["Mailpit (dev)"]

    Browser -->|"HTTP / Sessions"| Security
    Security --> Controller
    Controller --> Form
    Controller --> Repository
    Controller --> Twig
    Repository -->|"QueryBuilder"| DB
    Controller -->|"Mailer"| Mail

    subgraph Rôles
        direction LR
        U["ROLE_USER"] --> R["ROLE_REVIEWER"]
        R --> B["ROLE_BARISTA"]
        B --> A["ROLE_ADMIN"]
    end

    subgraph "Workflow Soumission"
        direction LR
        P["Pending"] --> AR["ApprovedByReview"]
        AR --> AP["Approved ✓"]
        P --> RR["RejectedByReview"]
        P --> NC["NeedsChanges"]
    end
```

**Justifications techniques :**
- **Symfony 8** : framework maîtrisé, permet d'aller directement au cœur du projet (contenu, design) sans friction d'apprentissage.
- **PostgreSQL 16** : robustesse relationnelle, support JSON natif pour le champ `options` des produits.
- **Doctrine ORM** : mapping entité-table type-safe, QueryBuilder paramétré (protection SQLi native).
- **Tailwind v4** : utility-first CSS avec tokens de design Apple personnalisés, responsive sans JavaScript.
- **Symfony UX / Stimulus** : progressive enhancement, Turbo pour les transitions de page sans rechargement complet.
````

**Gain :** 3 → 7 XP (critère "Architecture proposée")

---

### R3 — Compléter le README avec installation + env vars `+4 XP` ⏱ 20 min

**Problème :** aucune section d'installation locale, aucune documentation des variables d'environnement.

Ajouter dans `README.md` :

````markdown
## 🚀 Installation locale

### Prérequis
- PHP 8.4+
- Composer
- Docker (pour PostgreSQL) ou PostgreSQL 16 installé localement
- Node.js (pour Tailwind, optionnel — le CSS compilé est inclus)

### Étapes

```bash
# 1. Cloner le repo
git clone <url-du-repo>
cd TimeMachine

# 2. Installer les dépendances PHP
composer install

# 3. Configurer l'environnement
cp .env .env.local
# Éditer .env.local avec vos valeurs (voir section Variables ci-dessous)

# 4. Démarrer la base de données (Docker)
docker compose up -d

# 5. Créer le schéma
php bin/console doctrine:migrations:migrate
# ou sur installation fraîche :
php bin/console doctrine:schema:create

# 6. Peupler les données de référence
php bin/console app:populate-os
php bin/console app:populate-product-type

# 7. Lancer le serveur
symfony serve
# → http://localhost:8000
```

## ⚙️ Variables d'environnement

| Variable | Exemple | Description |
|---|---|---|
| `APP_ENV` | `dev` | Environnement (`dev`, `prod`, `test`) |
| `APP_SECRET` | `<random_32_chars>` | Clé secrète Symfony (sessions, CSRF) — **ne jamais commiter** |
| `DATABASE_URL` | `postgresql://app:password@127.0.0.1:5432/app?serverVersion=16` | DSN PostgreSQL |
| `MESSENGER_TRANSPORT_DSN` | `doctrine://default?auto_setup=0` | Transport pour les messages asynchrones |
| `MAILER_DSN` | `null://null` (dev) | Configuration SMTP |

### Compte de démo (après seed)

Créer un compte via `/register` → noter le UUID affiché une seule fois → se connecter via `/login`.

Pour obtenir les droits admin, mettre à jour directement en base :
```sql
UPDATE "user" SET roles = '["ROLE_ADMIN"]' WHERE id = 1;
```
````

**Gain :** 3 → 7 XP (critère "README qualité")

---

### R4 — Générer les migrations Doctrine `+4 XP` ⏱ 15 min

**Problème :** dossier `migrations/` vide — le schéma n'est pas versionné.

```bash
# Générer la migration depuis l'état actuel des entités
php bin/console doctrine:migrations:diff

# Vérifier le fichier généré dans migrations/
# Commiter
git add migrations/
git commit -m "Add initial Doctrine migration from entity schema"
```

Optionnel mais recommandé : ajouter des index sur les colonnes filtrées fréquemment.  
Dans `Product.php` :

```php
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Index(fields: ['status'], name: 'idx_product_status')]
class Product
```

**Gain :** modélisation BDD 3 → 7 XP (critère "Modélisation")

---

### R5 — Déployer l'application `+11 à +15 XP` ⏱ 1-2h

**Problème :** 0/15 sur le bloc Déploiement. C'est le bloc le plus pénalisant.

**Option recommandée : Railway** (gratuit, supporte PHP/Symfony nativement)

```bash
# Créer un Dockerfile à la racine
```

```dockerfile
FROM php:8.4-fpm-alpine

RUN apk add --no-cache nginx postgresql-dev \
    && docker-php-ext-install pdo_pgsql opcache

COPY . /var/www/html
WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader

COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
```

```bash
# Puis déployer sur Railway
railway login
railway init
railway up
railway domain  # → obtenir l'URL publique en HTTPS
```

Ajouter l'URL dans le README :
```markdown
## 🌐 Application en ligne

https://apple-time-machine.railway.app
```

**Gain :**
- Accessible en ligne : 0 → 5 XP
- Domaine + TLS (Railway fournit HTTPS) : 0 → 6 XP
- Total déploiement : 0 → 11 XP

---

### R6 — Ajouter les tokens CSRF sur les formulaires review `+5 XP` ⏱ 30 min

**Problème :** les formulaires approve/reject/request-modification n'ont pas de protection CSRF.

**Dans `templates/review/show.html.twig`**, ajouter dans chaque `<form>` :

```twig
{# Formulaire approve (ligne ~38) #}
<form method="post" action="{{ path('app_review_approve', {id: product.id}) }}">
    <input type="hidden" name="_token" value="{{ csrf_token('review_action_' ~ product.id) }}">
    <button type="submit">…</button>
</form>

{# Pareil pour reject et request-modification #}
```

**Dans `ReviewController.php`**, valider le token dans chaque action :

```php
public function approve(int $id, Request $request, …): Response
{
    // Ajouter en début de méthode :
    if (!$this->isCsrfTokenValid('review_action_' . $id, $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Invalid CSRF token.');
    }
    // … suite du code
}
```

**Ajouter les headers de sécurité** via une réponse d'événement dans `src/EventSubscriber/SecurityHeadersSubscriber.php` :

```php
<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityHeadersSubscriber implements EventSubscriberInterface
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => 'onKernelResponse'];
    }
}
```

**Gain :** OWASP 4 → 9 XP (passage de 2/4 à 4/4 protections)

---

### R7 — Ajouter les spécifications fonctionnelles au README `+4 XP` ⏱ 30 min

**Problème :** pas de parcours utilisateur ni de MVP défini.

Ajouter dans `README.md` :

```markdown
## 📋 Spécifications fonctionnelles

### MVP (Minimum Viable Product)

| Fonctionnalité | Statut |
|---|---|
| Inscription sans email (UUID anonyme) | ✅ |
| Connexion par UUID | ✅ |
| Soumission d'un produit Apple discontinué | ✅ |
| Validation manuelle par un reviewer | ✅ |
| Approbation finale par un barista | ✅ |
| Affichage public des produits approuvés | ✅ |
| Browse par catégorie (iPhone, Mac, iPad…) | ✅ |
| Calcul du prix ajusté à l'inflation | ✅ |

### Parcours utilisateurs

**Contributeur (ROLE_USER)**
1. Créer un compte → recevoir son UUID
2. Se connecter avec son UUID
3. Soumettre un produit (nom, date, prix, OS, options…)
4. Le produit passe en statut "Pending"
5. Attendre l'approbation d'un reviewer

**Reviewer (ROLE_REVIEWER)**
1. Voir la cloche de notification avec le nombre de soumissions en attente
2. Consulter la fiche de chaque produit en attente
3. Approuver (→ envoi en validation barista), rejeter, ou demander des modifications

**Barista / Admin (ROLE_BARISTA)**
1. Voir les produits approuvés par les reviewers
2. Donner l'approbation finale → le produit devient public
3. Éditer / supprimer tout produit existant
4. Consulter l'historique des modifications
```

**Gain :** spécifications 4 → 8 XP

---

### R8 — Corriger la convention de nommage PHP `+6 XP` ⏱ 1h

**Problème :** propriétés PascalCase dans `Product.php` (violation PSR-1).

```bash
# Renommer toutes les propriétés + getters/setters dans Product.php
# $ProductName → $productName
# $TechnicalName → $technicalName
# etc.
```

Utiliser un refactor automatique dans l'IDE (PhpStorm : Shift+F6 sur chaque propriété).  
Mettre à jour les templates Twig si nécessaire (généralement pas d'impact car Twig appelle les getters).

**Gain :** lisibilité 7 → 13 XP (les 3 critères sont tous remplis)

---

### R9 — Ajouter un seed de produits `+1 XP` ⏱ 45 min

**Problème :** archive vide sur installation fraîche, impossible de tester sans ajouter manuellement des produits.

Créer `src/Command/PopulateProductCommand.php` avec 5 à 10 produits Apple discontinués emblématiques (iPhone 1, PowerBook, iPod Classic…) en statut `Approved`.

**Gain :** seed 1 → 2 XP

---

### R10 — Implémenter la recherche `+6 XP` ⏱ 2h

**Problème :** fonctionnalité décrite dans le README mais absente du code.

Ajouter dans `ProductRepository.php` :

```php
public function search(string $query): array
{
    return $this->createQueryBuilder('p')
        ->join('p.ProductType', 't')
        ->where('p.status = :status')
        ->andWhere('LOWER(p.ProductName) LIKE :q OR LOWER(p.TechnicalName) LIKE :q OR LOWER(t.Type) LIKE :q')
        ->setParameter('status', SubmissionStatus::Approved)
        ->setParameter('q', '%' . strtolower($query) . '%')
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult();
}
```

Créer `src/Controller/SearchController.php` + `templates/search/index.html.twig` + ajouter le champ de recherche dans `base.html.twig`.

**Gain :** fonctionnalités frontend 6 → 12 XP

---

## Score projeté après remédiation complète

| Bloc | Avant | Après | Delta |
|---|---|---|---|
| 1. Cadrage & conception | 7 | 15 | +8 |
| 2. Frontend | 15 | 25 | +10 |
| 3. Backend | 25 | 30 | +5 |
| 4. Base de données | 10 | 15 | +5 |
| 5. Sécurité | 12 | 25 | +13 |
| 6. Déploiement | 0 | 15 | +15 |
| 7. Qualité | 15 | 25 | +10 |
| 8. Ambition | 7 | 10 | +3 |
| **TOTAL** | **91** | **160** | **+69** |

---

### Ordre d'exécution recommandé (par ratio gain/effort)

| Priorité | Action | Gain | Effort |
|---|---|---|---|
| 🔴 1 | R1 — Retirer les secrets | +5 XP | 15 min |
| 🔴 2 | R5 — Déployer | +11 XP | 1-2h |
| 🟠 3 | R2 — Schéma archi README | +4 XP | 20 min |
| 🟠 4 | R3 — Install + env vars README | +4 XP | 20 min |
| 🟠 5 | R7 — Spécifications fonctionnelles | +4 XP | 30 min |
| 🟠 6 | R6 — CSRF review + security headers | +5 XP | 30 min |
| 🟡 7 | R4 — Migrations Doctrine | +4 XP | 15 min |
| 🟡 8 | R8 — Convention nommage PHP | +6 XP | 1h |
| 🟡 9 | R10 — Implémenter la recherche | +6 XP | 2h |
| 🟢 10 | R9 — Seed de produits | +1 XP | 45 min |

---

> **Rappel final :** ce rapport est produit par un **simulateur**, pas par l'enseignant qui notera réellement le projet. La note réelle peut diverger à la hausse (démo convaincante, Q&R maîtrisées) ou à la baisse (fonctionnalité qui ne marche pas en live, incompréhension du code en soutenance). L'objectif de ce document est d'identifier les chantiers prioritaires avant le rendu, pas de prédire la note exacte.