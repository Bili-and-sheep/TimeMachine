# Roadmap — Apple Time Machine

> Community archive of discontinued Apple products.
> This file traces the real project history (git commits) and plans future evolutions.

---

## History — What has been built

### Foundations — April 2026

#### `816c578` · `742d8ba` · `c4ed7e5` — 2026-04-09 — Init
- Symfony 8 / PHP 8.4 project initialization
- Added webapp packages (Twig, Doctrine, Security, Tailwind…)

#### `314e6a6` · `f76e955` — 2026-04-23 — Initial README
- Created README.md: project description, goals, planned stack

---

### Sprint 1 — Technical foundation — 2026-05-01

#### `e96569e` · `f29326b` — Setup
- Composer dependencies configured (`symfony/uid`, `symfony/security-bundle`, `doctrine/orm`…)
- `.gitignore`: IDE files, `.env.local`, temporary directories

#### `8e63e63` · `0edcb55` · `a7ebe4d` — UUID Auth
- Custom `UuidAuthenticator` (AbstractAuthenticator)
- Role hierarchy: `ROLE_USER → ROLE_REVIEWER → ROLE_BARISTA → ROLE_ADMIN`
- Registration (UUID v4 generation + SHA-256 hash), login, logout, profile page `/mydrilla`

#### `ccb885d` · `c6ccf49` · `7524942` · `8351b10` · `7baf652` · `23845f5` — Entities & Enums
- Enums: `OsFamily`, `Role`, `SubmissionStatus`
- Entities: `User`, `ProductType`, `ModificationHistory`, `OperatingSystem`, `Product` (with inflation calculation), `Tag`, `ProductImage`

#### `ffead37` · `edcea59` · `42dff52` — Initial seeding
- `app:populate-os`: iOS, macOS, watchOS versions
- `app:populate-product-type`: iPhone, MacBook, iPad, Apple Watch, Accessory…

#### `e31d7bb` · `18086ae` · `4ad7948` · `04fe962` — Submission & review workflow
- Base layout: sticky nav, bell badge (pending count)
- Product submission form (`ROLE_USER`)
- Product detail page
- Review workflow: approve / reject with required comment (`ROLE_REVIEWER`)
- Real-time notification badge (pending count)

#### `e9119ec` · `d40f35e` — User management
- Admin panel: user list, role selector (`ROLE_ADMIN`)

#### `1499cd4` · `844d659` · `7f9b4db` · `4eaa75c` — Modification history
- `ModificationAction` enum (Created, StatusChanged, Edited)
- Automatic history recording on every action (create/approve/reject)
- History page per product (`/product/{id}/history`, `ROLE_BARISTA`)

#### `cfe0d80` · `71e3dbf` · `d30d5c0` · `0ea3fcf` — Edit & modification request
- `NeedsChanges` status added
- Product editing for `ROLE_BARISTA` with history recording
- Modification request with comment for `ROLE_REVIEWER`

#### `db0a715` · `93d42ff` · `949afa3` · `0967b48` — Apple 2017 design
- Browse controller with category filter (iPhone, Mac, iPad, Watch, TV, Accessories, Other)
- Apple 2017 layout: dark sticky nav, category store bar, hero home, browse grid

---

### Sprint 2 — Design & Deployment — 2026-05-01 to 2026-05-04

#### `33b73aa` · `3910879` · `64b80ba` · `77dee72` — Tailwind v4 migration
- Installed `symfonycasts/tailwind-bundle` v4
- Custom Apple design tokens (`apple-dark`, `apple-gray`…)
- Full rewrite of all templates to Tailwind utility classes

#### `538acd0` · `f94a51b` — Apple 2025 product page
- Hero section, OS timeline (launch → last supported), specs, sticky footer
- Faithful Apple.com 2025 layout

#### `8712fa8` — 2026-05-04 — Product page refactor
- Apple-style hero, options display (colors, storage), improved navigation

---

### Sprint 3 — Quality, Security & CI/CD — 2026-05-13 to 2026-05-14

#### `f909223` — Secret cleanup
- Removed `APP_SECRET` from tracked `.env.dev` → moved to `.env.local`

#### `0c60f7f` · `7617d9e` · `ef2751b` · `ba11e80` — Deployment
- GitHub Actions pipeline: push `main` → SSH → `git pull` + `composer install --no-dev` + `cache:clear` + reload Apache/PHP-FPM
- Live URL: [timemachine.eliottandre.com](https://timemachine.eliottandre.com)
- Credentials stored in GitHub Secrets

#### `5c4c68d` — Enhanced review panel
- Display of rejected products in the review panel, status checks

#### `0b61e4b` — Security
- Explicit CSRF protection on all review actions (`isCsrfTokenValid`)
- `SecurityHeadersSubscriber`: X-Content-Type-Options, X-Frame-Options, Referrer-Policy

#### `2021f4f` — Architecture documentation
- Mermaid diagram in README
- Per-technology technical justifications
- Local install guide + environment variables table

#### `7e818d8` — Functional specifications
- MVP status table + user journeys for all 4 roles in README

#### `c63da05` — Doctrine migration
- Initial migration: full schema, FK, `IDX_PRODUCT_STATUS` index

#### `61e24d7` — PSR-1
- Renamed PascalCase properties to camelCase, PSR-1 compliance

#### `4338064` — Product seed
- `PopulateProductCommand`: 21 iconic discontinued Apple products

#### `a76a645` — Full-text search
- `SearchController` + `ProductRepository::search()`: normalized LIKE (LOWER + mb_strtolower)
- Search results template

---

## Current state — 2026-05-15

**Estimated score: 149 / 160 XP**

| Block | Score | Status |
|---|---|---|
| Framing & conception | 15/15 | ✅ Complete |
| Frontend | 25/25 | ✅ Complete |
| Backend | 27/30 | ⚠️ Partial validation |
| Database | 15/15 | ✅ Complete |
| Security | 17/25 | ⚠️ Incomplete OWASP |
| Deployment | 15/15 | ✅ Complete |
| Code quality | 25/25 | ✅ Complete |
| Technical ambition | 10/10 | ✅ Complete |

**11 XP recoverable** before the presentation (see next section).

---

## Roadmap — Upcoming features

### P0 — Fixes before the presentation (11 XP, ~1h30)

#### `feat(security)` — CSP + HSTS · +5 XP
**File**: `src/EventSubscriber/SecurityHeadersSubscriber.php`

Add in `onKernelResponse()`:
```php
$response->headers->set(
    'Strict-Transport-Security',
    'max-age=31536000; includeSubDomains'
);
$response->headers->set(
    'Content-Security-Policy',
    "default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; frame-ancestors 'none'"
);
```

#### `feat(validation)` — Assert constraints on entities · +3 XP
**File**: `src/Entity/Product.php`

```php
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\NotBlank]
#[Assert\Length(max: 255)]
private ?string $productName = null;

#[Assert\NotBlank]
private ?string $technicalName = null;

#[Assert\NotNull]
#[Assert\Positive]
private ?int $originalPrice = null;
```

#### `refactor(auth)` — Replace SHA-256 with bcrypt · +3 XP
**Files**: `src/Entity/User.php`, `src/Security/UuidAuthenticator.php`, `src/Controller/RegistrationController.php`

Implement `PasswordAuthenticatedUserInterface` on `User`, use `UserPasswordHasherInterface` to hash the UUID at registration, and `password_verify()` in the authenticator.

#### `chore` — Remove GentlePuppyController
Delete `src/Controller/GentlePuppyController.php` and `templates/gentle_puppy/`. Leftover scaffold visible at `/puppy`.

---

### P1 — Product features (post-presentation)

#### `feat(upload)` — Product image upload
The `ProductImage` entity and its repository exist but there is no upload form.
- Add `FileType` to `ProductFormType` (or a dedicated form)
- Store files in `public/uploads/products/`
- Configure VichUploaderBundle or manual handling with `symfony/filesystem`
- Display images on the product page (carousel or gallery)

#### `feat(tags)` — Tag management interface
Tags exist in the database and are associable via the product form, but there is no page to create or manage them.
- `TagController`: CRUD for `ROLE_BARISTA`
- Browse page filterable by tag
- Auto-completion in the product form

#### `feat(pagination)` — Pagination on Browse and Search
- Integrate `KnpPaginatorBundle` or native Doctrine pagination
- Browse and Search can return many products with no limit

#### `feat(profile)` — Enhanced profile page
The `/mydrilla` page is minimal.
- Show products submitted by the user + their status
- Show rejection comments received
- Allow UUID copy from the interface (store it server-side, expose a copy button)

#### `feat(request)` — Product request (lightweight submission)
Allow a `ROLE_USER` to flag a missing product without filling out the full form.
- Lightweight form: product name + optional source
- `Requested` status in `SubmissionStatus`
- Barista panel to review requests

---

### P2 — Technical improvements

#### `feat(search)` — Native PostgreSQL full-text search
Replace LIKE with a `tsvector` index for better performance and relevance.
```sql
ALTER TABLE product ADD COLUMN search_vector tsvector;
CREATE INDEX idx_product_fts ON product USING GIN(search_vector);
```
Use `to_tsquery()` in the QueryBuilder with a native Doctrine query.

#### `feat(api)` — Public JSON endpoints
Expose a lightweight REST API for approved products:
- `GET /api/products` — paginated list
- `GET /api/products/{id}` — detail
- `GET /api/products/search?q=` — search
Useful for third-party integrations or a future mobile app.

#### `feat(rss)` — RSS feed of new products
Generate an RSS/Atom feed of the latest approved products.
- `GET /rss.xml`
- Symfony `SyndicationComponent` or manual XML generation

#### `feat(notifications)` — Email notifications
`MAILER_DSN` is set to `null://null` — no emails are sent.
- Notify the contributor when their product is approved / rejected / needs changes
- Use `symfony/mailer` + Twig email templates

#### `feat(stats)` — Admin dashboard
`/admin/stats` page for `ROLE_ADMIN`:
- Products by status (chart)
- Most active contributors
- Products added per month

#### `feat(compare)` — Product comparison
Select 2 products and display their specs side by side.
- Store selection in session
- Comparison template inspired by Apple Compare

#### `feat(favorites)` — User favorites
Allow logged-in users to bookmark products.
- `user_favorite` table (ManyToMany User ↔ Product)
- `/mydrilla/favorites` page

---

### P3 — Infrastructure

#### `ci` — Automated tests in the pipeline
- Add PHPUnit to the GitHub Actions workflow
- Functional tests on critical routes (submission, review, auth)
- `phpunit.dist.xml` is already configured

#### `ci` — Staging environment
- `staging` branch → deployment on `staging.timemachine.eliottandre.com`
- Regression tests before merging into `main`

#### `infra` — Full Docker Compose stack
`compose.yaml` and `compose.override.yaml` exist but the local stack relies on `symfony serve`.
- Add a PHP-FPM + Nginx service in `compose.yaml` to exactly mirror production

---

*Last updated: 2026-05-15*
