# Rapport d'évaluation simulée — Apple Time Machine

> **Simulateur basé sur la grille officielle du cours « Infrastructure et Programmation Web » — Bachelor 3 Oteria.**
> Ce rapport n'est pas la note officielle. La note réelle peut diverger selon la démo en direct et les Q&R.
>
> Date de simulation : 2026-05-14

---

## Récapitulatif

```
═══════════════════════════════════════════════════
  1. Cadrage & conception                   15 / 15
  2. Frontend                               25 / 25
  3. Backend                                27 / 30
  4. Base de données                        15 / 15
  5. Sécurité                               17 / 25
  6. Déploiement & infrastructure           15 / 15
  7. Qualité du code & documentation        25 / 25
  8. Ambition technique du projet           10 / 10

  PÉNALITÉS                                  - 0 XP

  ESTIMATION                               149 / 160 XP
═══════════════════════════════════════════════════
```

**Points laissés sur la table : 11 XP**
- Backend 3.3 — Validation : −3 XP
- Sécurité 5.1 — Authentification : −3 XP
- Sécurité 5.3 — OWASP : −5 XP

---

## Détail par bloc

### Bloc 1 — Cadrage & conception — 15 / 15

| Critère | XP | Niveau |
|---|---|---|
| Spécifications fonctionnelles | 8 / 8 | Max |
| Architecture proposée | 7 / 7 | Max |

**1.1 Spécifications fonctionnelles — 8/8**
- Preuve : `README.md:113–148` — MVP status table (10 features ✅), user journeys pour 4 rôles (User, Reviewer, Barista, Admin)

**1.2 Architecture — 7/7**
- Preuve : `README.md:75–109` — Mermaid diagram (Browser → Security → Controller → Repository → DB), sous-diagrammes rôles + workflow, paragraphes de justification par techno

---

### Bloc 2 — Frontend — 25 / 25

| Critère | XP | Niveau |
|---|---|---|
| Structure HTML/CSS/JS | 8 / 8 | Max |
| Fonctionnalités clés | 12 / 12 | Max |
| UX & cohérence visuelle | 5 / 5 | Max |

**2.1 Structure — 8/8**
- Héritage Twig (`base.html.twig` + templates enfants), Tailwind v4, Stimulus controllers, Turbo pour transitions sans rechargement

**2.2 Fonctionnalités — 12/12**
- Browse par catégorie, recherche full-text, soumission produit, inscription/login UUID, workflow review, prix inflation-adjusted, badge cloche (pending count)

**2.3 UX & responsive — 5/5**
- Preuve responsive : `templates/product/show.html.twig:29,66,88,238,285` (`md:`, `lg:`, `sm:` breakpoints Tailwind)
- Redirect HTTP→HTTPS 301 confirmé, design Apple 2017 cohérent

---

### Bloc 3 — Backend — 27 / 30

| Critère | XP | Niveau |
|---|---|---|
| API / endpoints | 12 / 12 | Max |
| Logique métier | 8 / 8 | Max |
| Validation des entrées | **3 / 6** | **Intermédiaire** |
| Gestion des erreurs | 4 / 4 | Max |

**3.1 Endpoints — 12/12**
- 9 controllers : Home, Browse (7 catégories), Search, Product (CRUD), Review (5 actions), Security, Registration, User (CRUD), History

**3.2 Logique métier — 8/8**
- Machine à états 6 statuts, calcul inflation (compound interest), hiérarchie 4 rôles, audit trail automatique, authenticateur UUID custom

**3.3 Validation — 3/6** ⚠️ −3 XP
- `$form->isSubmitted() && $form->isValid()` présent partout ✅
- Validation implicite via types de champs (IntegerType, EntityType…) ✅
- **Manquant : zéro contrainte `#[Assert\...]` explicite sur les entités**
- `grep -rn "Assert\\" src/` → aucun résultat
- Un produit peut être soumis avec `originalPrice = -999` sans erreur Symfony

**3.4 Erreurs — 4/4**
- `createNotFoundException()`, `createAccessDeniedException()`, flash messages, CSRF invalide → 403 explicite

#### Remédiation 3.3 — +3 XP

Ajouter les contraintes Assert sur `src/Entity/Product.php` :

```php
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\NotBlank(message: 'Product name is required.')]
#[Assert\Length(max: 255)]
private ?string $productName = null;

#[Assert\NotBlank(message: 'Technical name is required.')]
#[Assert\Length(max: 255)]
private ?string $technicalName = null;

#[Assert\NotNull(message: 'Price is required.')]
#[Assert\Positive(message: 'Price must be a positive integer.')]
private ?int $originalPrice = null;
```

Et sur `src/Entity/User.php` (déjà géré par UniqueEntity, mais ajouter) :

```php
#[Assert\NotBlank]
#[Assert\Uuid]
private ?string $uuid = null;
```

**Effort estimé : 20 minutes.**

---

### Bloc 4 — Base de données — 15 / 15

| Critère | XP | Niveau |
|---|---|---|
| Modélisation | 7 / 7 | Max |
| Accès aux données | 6 / 6 | Max |
| Seed / données de démo | 2 / 2 | Max |

**4.1 Modélisation — 7/7**
- 8 tables, FK avec CASCADE, index métier `IDX_PRODUCT_STATUS`, JSON pour options

**4.2 Accès — 6/6**
- Doctrine QueryBuilder + `setParameter()` partout, aucune concaténation SQL

**4.3 Seed — 2/2**
- `PopulateProductCommand` (21 produits), `PopulateOperatingSystemCommand`, `PopulateProductTypeCommand`

---

### Bloc 5 — Sécurité — 17 / 25

| Critère | XP | Niveau |
|---|---|---|
| Authentification | **3 / 6** | **Intermédiaire** |
| Autorisation | 5 / 5 | Max |
| Protection OWASP | **4 / 9** | **Intermédiaire** |
| Gestion des secrets | 5 / 5 | Max |

**5.1 Authentification — 3/6** ⚠️ −3 XP
- Système passwordless UUID ✅, session Symfony ✅, CsrfTokenBadge sur login ✅
- UUID stocké en SHA-256 : `User::setUuid():36` → `hash('sha256', $uuid)`
- **Problème : SHA-256 est un hash rapide, pas un KDF (bcrypt/argon2)**
- `password_hashers: 'auto'` déclaré dans `security.yaml` mais jamais invoqué
- Bien que l'entropie d'un UUID v4 rende le brute-force impraticable, le critère attend un algorithme de dérivation de clé

**5.2 Autorisation — 5/5**
- `#[IsGranted]` sur tous les endpoints sensibles, hiérarchie de rôles complète, aucun IDOR trouvé

**5.3 OWASP — 4/9** ⚠️ −5 XP
| Check | Statut | Preuve |
|---|---|---|
| XSS | ✅ | Twig auto-échappe, aucun `\|raw` trouvé |
| SQLi | ✅ | QueryBuilder + setParameter() |
| CSRF | ✅ | `csrf.yaml` + `isCsrfTokenValid()` partout |
| Headers | ⚠️ | 3/5 présents, CSP et HSTS manquants |

Confirmé en prod : `curl -sI https://timemachine.eliottandre.com` retourne X-Content-Type-Options, X-Frame-Options, Referrer-Policy — mais **pas de Content-Security-Policy, pas de Strict-Transport-Security**.

**5.4 Secrets — 5/5**
- `.env.local` gitignore + non-tracké ✅, GitHub Actions utilise `${{ secrets.* }}` ✅, aucun secret réel dans les fichiers trackés ✅

#### Remédiation 5.1 — +3 XP

Remplacer SHA-256 par `password_hash()` avec BCRYPT, ou utiliser le `PasswordHasher` Symfony.

Option A — rester "passwordless" mais utiliser le hasher Symfony dans `src/Entity/User.php` :

```php
// Implémenter PasswordAuthenticatedUserInterface
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private ?string $password = null; // stocke le hash du UUID

    public function getPassword(): ?string { return $this->password; }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid; // stocker le UUID brut pour identifier
        return $this;
    }
}
```

Dans `RegistrationController`, hasher le UUID avec le service :

```php
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

public function register(
    Request $request,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $hasher
): Response {
    // ...
    $rawUuid = Uuid::v4()->toRfc4122();
    $user->setUuid($rawUuid);
    $user->setPassword($hasher->hashPassword($user, $rawUuid)); // bcrypt/argon2 auto
    // ...
}
```

Option B (plus simple) — garder l'approche actuelle mais remplacer `hash('sha256', $uuid)` par `password_hash($uuid, PASSWORD_BCRYPT)` et comparer avec `password_verify()` dans l'authenticateur.

**Effort estimé : 45 minutes.**

#### Remédiation 5.3 — +5 XP

Compléter `src/EventSubscriber/SecurityHeadersSubscriber.php` :

```php
public function onKernelResponse(ResponseEvent $event): void
{
    $response = $event->getResponse();

    // Headers existants
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'DENY');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

    // À ajouter — HSTS (force HTTPS pendant 1 an)
    $response->headers->set(
        'Strict-Transport-Security',
        'max-age=31536000; includeSubDomains'
    );

    // À ajouter — CSP (adapter selon les CDN effectivement utilisés)
    $response->headers->set(
        'Content-Security-Policy',
        "default-src 'self'; " .
        "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
        "style-src 'self' 'unsafe-inline'; " .
        "img-src 'self' data: https:; " .
        "font-src 'self'; " .
        "frame-ancestors 'none'"
    );
}
```

> Note : `'unsafe-inline'` est nécessaire si des styles/scripts inline existent dans les templates Twig. Pour un score optimal, les remplacer par des classes Tailwind et supprimer `'unsafe-inline'`.

**Effort estimé : 30 minutes. Gain : +5 XP (passage de 4 à 9 XP sur le critère OWASP).**

---

### Bloc 6 — Déploiement & infrastructure — 15 / 15

| Critère | XP | Niveau |
|---|---|---|
| Application accessible en ligne | 5 / 5 | Max |
| Domaine + TLS + reverse proxy | 6 / 6 | Max |
| Pipeline CI/CD | 4 / 4 | Max |

**6.1 Accessible — 5/5**
- `curl -sI https://timemachine.eliottandre.com` → 200 OK ✅

**6.2 Domaine + TLS — 6/6**
- Custom domain + TLS valide + redirect HTTP→HTTPS 301 + Apache/2.4.58 ✅

**6.3 CI/CD — 4/4**
- `.github/workflows/deploy.yml` : push main → SSH → git pull + composer + cache + reload services ✅

---

### Bloc 7 — Qualité du code & documentation — 25 / 25

| Critère | XP | Niveau |
|---|---|---|
| Lisibilité & organisation | 13 / 13 | Max |
| README | 7 / 7 | Max |
| Historique Git | 5 / 5 | Max |

**7.1 Lisibilité — 13/13**
- Max 370 lignes (`PopulateProductCommand.php`), aucun fichier > 1000 lignes
- Couches bien séparées : Controllers / Repositories / Entities / Forms / Security / EventSubscribers / Enums / Commands
- PSR-1 respecté (refactorisé dans commit `61e24d7`)

**7.2 README — 7/7**
- Description, install locale step-by-step, table des variables d'env, justifications techniques par paragraphe, diagramme Mermaid

**7.3 Git — 5/5**
- 59 commits, Conventional Commits (`feat(scope):`, `fix:`, `docs:`, `refactor:`, `ci:`), branches feature utilisées

---

### Bloc 8 — Ambition technique — 10 / 10

| Critère | XP | Niveau |
|---|---|---|
| Difficulté & richesse fonctionnelle | 6 / 6 | Max |
| Originalité | 4 / 4 | Max |

**8.1 Richesse — 6/6**
- Workflow 6 états multi-acteurs, auth custom, inflation-adjusted pricing, audit trail, recherche full-text — bien au-delà d'un CRUD

**8.2 Originalité — 4/4**
- Auth passwordless style Mullvad, archive Apple discontinués, reconstitution Apple Store 2017, prix historique + inflation en parallèle

---

## Plan de remédiation — 11 XP récupérables

| Priorité | Action | Fichier(s) | XP gagné | Effort |
|---|---|---|---|---|
| 1 | Ajouter CSP + HSTS dans SecurityHeadersSubscriber | `src/EventSubscriber/SecurityHeadersSubscriber.php` | **+5 XP** | ~30 min |
| 2 | Ajouter contraintes Assert sur les entités | `src/Entity/Product.php`, `src/Entity/User.php` | **+3 XP** | ~20 min |
| 3 | Remplacer SHA-256 par bcrypt/argon2 pour le hash UUID | `src/Entity/User.php`, `src/Security/UuidAuthenticator.php`, `src/Controller/RegistrationController.php` | **+3 XP** | ~45 min |

**Total récupérable : 11 XP → score cible 160/160**

### Ordre d'exécution recommandé

1. **SecurityHeadersSubscriber** (30 min, +5 XP) — modification d'une seule méthode dans un seul fichier, aucun risque de régression.
2. **Contraintes Assert** (20 min, +3 XP) — ajout d'annotations sur les entités, tester le formulaire de soumission après.
3. **Hash bcrypt** (45 min, +3 XP) — le plus risqué fonctionnellement (l'authenticateur doit être mis à jour en parallèle). Tester le cycle complet register → login avant de pousser.

---

## Points à surveiller pour la soutenance

- **GentlePuppyController** (`/puppy`) : vestige de scaffold Symfony, sans impact sur la note ici mais visible lors d'une lecture humaine. Supprimer avant la démo.
- **Compte démo admin** : la procédure actuelle (`UPDATE "user" SET roles = ...`) suppose un accès direct à la base. Prévoir une démonstration en live avec un compte admin déjà provisionné.
- **CSP `unsafe-inline`** : si le correcteur teste avec DevTools, une CSP trop permissive (`unsafe-inline`) sera visible. Idéalement, externaliser les styles inline restants avant d'activer la CSP.

---

*Rapport généré par simulateur — pas l'enseignant officiel. Note estimée : **149 / 160 XP**.*
