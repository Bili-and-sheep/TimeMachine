# 🕰️ Apple Time Machine

> *Rediscovering the products that defined an era — one discontinuation at a time.*

**[https://timemachine.eliottandre.com](https://timemachine.eliottandre.com)**

---

## What is this?

Apple Time Machine is a **community-driven archive** dedicated to Apple products that are no longer available — from iconic iPhones to forgotten accessories.

Born from a school project and a genuine love for Apple's history, this platform fills a gap that shouldn't exist: a beautiful, centralized place to explore, remember, and discover discontinued Apple products, with full specs, pricing (inflation-adjusted!), and community contributions.

---

## ✨ Features

### 🏪 The Store Experience
- Faithfully recreates the **2017 Apple Store aesthetic**
- Browse products by category: iPhone, MacBook, Apple Watch, Accessories, and more
- A home page that **spotlights featured and recently added products**

### 🔍 Search & Discovery
- Full-text search across product names, types, release dates, and tags
- Filter and sort to find exactly what you're looking for

### 📄 Product Pages
Each product page includes:

| Field                       | Details |
|-----------------------------|---|
| **Product Type**            | iPhone / MacBook / Apple Watch / Accessory / … |
| **Product Name**            | Official Apple name |
| **Technical Name**          | Official Apple name |
| **Release Date**            | When it launched |
| **Year Discontinued**       | When Apple pulled it |
| **Launch OS**               | iOS / macOS / watchOS version at release |
| **Last Supported OS**       | Final compatible software version |
| **Description**             | Context, legacy, and story |
| **Images**                  | Product photography |
| **Options**                 | Colors, storage, sizes |
| **Original Price**          | As entered by contributors |
| **Inflation-Adjusted Price** | Calculated from release year to today |
| **Tags**                    | Community-applied categories |
| **Sources**                 | Masked/attributed references |

### 👥 Community
- **Request** products to be added to the archive
- **Contribute** by creating product pages yourself
- All submissions go through **manual review** before publishing — quality over quantity
- **User accounts** with randomized IDs for maximum privacy

### 🛠️ Admin Panel
- Review and approve community submissions
- Manage contributions and maintain content quality

---

## 🏗️ Tech Stack & Architecture

**Framework: [Symfony](https://symfony.com/)**

Why Symfony and not something trendier? Because this project is about *joy*.

I'm no longer a full-time web developer — I do this for fun, in the margins of a busy life. Symfony is a framework I know, and it lets me skip the setup friction and go straight to the part I actually love: the content, the design, the idea. Using something familiar means more time building, less time debugging a new ecosystem.

**Justifications techniques :**
- **Symfony 8 / PHP 8.4** : framework maîtrisé, permet d'aller directement au cœur du projet sans friction d'apprentissage.
- **PostgreSQL 16** : robustesse relationnelle, support JSON natif pour le champ `options` des produits (couleurs, stockages…).
- **Doctrine ORM** : mapping entité-table type-safe, QueryBuilder paramétré (protection SQLi native).
- **Tailwind v4** : utility-first CSS avec tokens de design Apple personnalisés, responsive sans JavaScript.
- **Symfony UX / Stimulus / Turbo** : progressive enhancement, transitions de page sans rechargement complet.

```mermaid
graph TD
    Browser["🌐 Navigateur"]

    subgraph Symfony["Symfony 8 (PHP 8.4)"]
        direction TB
        Security["Security — UuidAuthenticator"]
        Controller["Controllers\nProduct · Review · Browse · User…"]
        Repository["Repositories — Doctrine ORM"]
        Twig["Templates Twig + Tailwind v4"]
    end

    DB[("PostgreSQL 16")]

    Browser -->|"HTTPS"| Security
    Security --> Controller
    Controller --> Repository
    Controller --> Twig
    Repository -->|"QueryBuilder"| DB

    subgraph Roles["Hiérarchie des rôles"]
        direction LR
        U["ROLE_USER"] --> R["ROLE_REVIEWER"]
        R --> B["ROLE_BARISTA"]
        B --> A["ROLE_ADMIN"]
    end

    subgraph Workflow["Workflow soumission"]
        direction LR
        P["Pending"] --> AR["ApprovedByReview"]
        AR --> AP["Approved ✓"]
        P --> NC["NeedsChanges"]
        P --> RR["RejectedByReview"]
    end
```

---

## 📋 Functional Specifications

### MVP

| Feature | Status |
|---|---|
| Anonymous registration (UUID, no email) | ✅ |
| Login by UUID | ✅ |
| Submit a discontinued Apple product | ✅ |
| First-pass review by a reviewer | ✅ |
| Final approval by a barista | ✅ |
| Public display of approved products | ✅ |
| Browse by category (iPhone, Mac, iPad…) | ✅ |
| Inflation-adjusted price calculation | ✅ |
| Modification request workflow | ✅ |
| Audit history per product | ✅ |

### User journeys

**Contributor (`ROLE_USER`)**
1. Register → receive a one-time UUID
2. Log in with the UUID
3. Submit a product (name, release date, price, OS, options…)
4. Product enters `Pending` status
5. Wait for reviewer approval — track status on the account page

**Reviewer (`ROLE_REVIEWER`)**
1. See the notification badge with the number of pending submissions
2. Open each pending product's review page
3. Approve (→ forwarded to barista), reject, or request modifications with a comment

**Barista / Admin (`ROLE_BARISTA`)**
1. See products approved by reviewers
2. Give final approval → product becomes publicly visible
3. Edit or delete any existing product
4. Consult the full modification history

---

## 💡 Why Apple? Why discontinued products?

I'm a huge fan of Apple — especially the **Jony Ive era**, when hardware design was its own art form.

The problem? Finding comprehensive, well-organized information about discontinued Apple products is *surprisingly hard*. You can find pieces — a spec sheet here, a forum post there — but nothing centralized, nothing beautiful, nothing community-maintained.

I wanted:
- One place with **all the specs** (OS at launch, last supported OS, price, colors…)
- **Inflation-adjusted pricing** so you can feel just how much that 2007 iPhone cost in today's money
- A **living archive** that grows as the community contributes
- An experience that *feels like* browsing the Apple Store — not a dry wiki


---

## 🗺️ Pages

| Page | Description |
|---|---|
| **Home** | Featured and latest products |
| **Browse** | iPhone / MacBook / Apple Watch / Accessories — Apple.com style |
| **Search** | Find products by name, type, date, and more |
| **Product Page** | Full details for a single product |
| **User Account** | Create an account, contribute, track your submissions |
| **Admin Panel** | Review submissions, manage the archive |
| **About** | The story, the goals, the team |
| **Contact** | Reach out for support or questions |

---

## 🌐 Live

[https://timemachine.eliottandre.com](https://timemachine.eliottandre.com)

---

## 🚀 Installation locale

### Prérequis
- PHP 8.4+
- Composer
- Docker (pour PostgreSQL) ou PostgreSQL 16 installé localement

### Étapes

```bash
# 1. Cloner le repo
git clone git@github.com:Bili-and-sheep/TimeMachine.git
cd TimeMachine

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env .env.local
# Éditer .env.local avec vos valeurs (voir Variables ci-dessous)

# 4. Démarrer la base de données
docker compose up -d

# 5. Créer le schéma et peupler les données de référence
php bin/console doctrine:schema:create
php bin/console app:populate-os
php bin/console app:populate-product-type

# 6. Lancer le serveur
symfony serve
# → http://localhost:8000
```

## ⚙️ Variables d'environnement

| Variable | Exemple | Description |
|---|---|---|
| `APP_ENV` | `dev` | Environnement (`dev`, `prod`, `test`) |
| `APP_SECRET` | `<random_32_chars>` | Clé secrète Symfony — **ne jamais commiter** |
| `DATABASE_URL` | `postgresql://app:password@127.0.0.1:5432/app?serverVersion=16` | DSN PostgreSQL |
| `MESSENGER_TRANSPORT_DSN` | `doctrine://default?auto_setup=0` | Transport messages asynchrones |
| `MAILER_DSN` | `null://null` | Configuration SMTP |

### Compte de démo

Créer un compte via `/register` → noter le UUID affiché une seule fois → se connecter via `/login`.

Pour obtenir les droits admin :
```sql
UPDATE "user" SET roles = '["ROLE_ADMIN"]' WHERE id = 1;
```

---

## 🚧 Status

This project is actively in development. Contributions, feedback, and product requests are welcome.

---

*Made with nostalgia, obsession, and a deep appreciation for hardware that had a soul.*

*Thanks Claude.IA for this nice readme °>° orignal scrap [here](orignialREAMDE.md)*
