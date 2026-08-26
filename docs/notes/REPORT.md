# Security Audit Report

**Project:** Alfiz Ilham Portfolio
**Location:** `C:\xampp\htdocs\alfizilham`
**Audit type:** Read-only static/source-code audit
**Date:** 2026-08-25

## 1. Executive Summary

The application is a vanilla PHP MVC portfolio application backed by SQLite. Its public surface includes a contact form, visitor counter, content APIs, and an editor/admin API that can create, modify, delete content and upload images.

Two confirmed findings were identified: a publicly reachable editor login is configured with the documented default password, and the `lang` query parameter is reflected unsafely in two public legal pages. The default editor credential permits unauthorized content modification and image upload. No confirmed SQL injection, command injection, SSRF from a user-controlled URL, path traversal, unsafe deserialization, or unrestricted file upload was found in the reviewed source.

Several defense-in-depth weaknesses and deployment-dependent risks also need attention, principally CSRF coverage, session rotation, local filesystem ACLs, image-decoding resource limits, and browser-side supply-chain/header hardening.

## 2. Audit Scope

Reviewed the full project tree relevant to runtime behavior: PHP entry points, routes, controllers, models, services, templates, JavaScript, configuration, Apache `.htaccess` rules, example environment configuration, SQLite access code, and observable Windows ACLs on `data` and upload storage. The audit covered authentication, authorization, input/output handling, database use, uploads, session management, CSRF, APIs, dangerous PHP functions, configuration exposure, HTTP headers, dependencies, and external integrations.

No source, configuration, database, or project asset was changed. No active scanner, exploit payload, database query, or HTTP request was sent to the application.

## 3. Methodology

The review used the installed `performing-web-application-penetration-test` skill as an OWASP WSTG-oriented application-mapping and control-review guide, and `exploiting-api-injection-vulnerabilities` as a guide for mapping injection sinks and API data flow. Both were applied in a strictly static, read-only manner, supplemented by source-code, configuration, route, and ACL analysis.

Findings are classified as **Confirmed Vulnerability**, **Potential Vulnerability**, **Security Weakness**, **Informational**, or **Needs Verification**. Potential/deployment-dependent issues are not asserted as exploitable without the specified additional evidence.

## 4. Project Security Overview

`public/index.php` starts a PHP session and routes requests through a simple custom router. Public API routes expose contact submission, visitor statistics, project/tool data, and public cards. Admin routes use `$_SESSION['is_admin']` and permit CRUD operations for cards/certificates and image uploads. SQLite is accessed through PDO prepared statements; administrative object IDs are cast to integers before use.

The upload implementation validates MIME type with `finfo`, decodes through GD, re-encodes to server-generated WebP names, and the uploads directory has Apache rules disabling PHP execution. The root Apache rules deny direct access to source/configuration/data directories and common sensitive extensions. The application loads several third-party browser resources from CDNs.

## 5. Risk Summary

| Severity      | Count |
| ------------- | ----- |
| Critical      |     0 |
| High          |     1 |
| Medium        |     3 |
| Low           |     2 |
| Informational |     1 |

Counts include confirmed and clearly labelled potential/needs-verification findings. Confirmed vulnerabilities: 2. Potential or deployment-dependent findings: 4.

## 6. Findings

### [HIGH] Documented Default Password Enables Unauthorized Editor Access

**ID:** SEC-001
**Status:** Confirmed Vulnerability
**Severity:** High
**Confidence:** High
**CWE:** CWE-798 (Use of Hard-coded Credentials)
**OWASP:** A07:2021 Identification and Authentication Failures; API2:2023 Broken Authentication

**Location:**

- File: `config/config.php`
- Class: `AdminController`
- Function/Method: `login()`, `requireAdmin()`, and all admin CRUD handlers
- Line/Code reference: `config/config.php:37-39`; `app/Controllers/AdminController.php:20-27`; `public/index.php:39-52`

**Evidence:**

`config/config.php:37` explicitly documents the configured bcrypt value as the default password `admin123`. `AdminController::login()` retrieves this value and accepts it with `password_verify()`, then sets `$_SESSION['is_admin'] = true`. The public route `POST /api/admin/login` is registered in `public/index.php:39`. Admin authorization then protects content creation, updates, deletes, and uploads only by this session flag.

**Root Cause:**

A known default administrative credential was retained in a deployed application configuration. The credential is a bcrypt hash, but hashing does not mitigate use of a publicly documented and trivially guessable password.

**Attack Scenario:**

An unauthenticated remote visitor submits the documented default password to the public editor login endpoint and obtains an admin session. The visitor can then alter or remove showcase/certificate content and upload image content through the authorized admin APIs. No destructive test was performed.

**Impact:**

- Confidentiality: Limited; admin session state and non-public certificate listing may be exposed.
- Integrity: High; portfolio content and associated uploaded files can be created, changed, or deleted.
- Availability: Moderate; content can be removed or uploaded files can consume storage.
- Authentication/Authorization: Full compromise of the single editor role.
- Business data: Public portfolio reputation and published content can be manipulated.

**Recommended Remediation:**

Replace the default immediately with a unique high-entropy secret supplied outside version-controlled source (for example, a protected environment variable or secret store). Remove the default-password comment/value from deployment configuration, add rate limiting/lockout telemetry to login, and introduce a proper per-user administrative identity if editor access is shared.

**Regression Risk:**

The existing editor will be unable to log in until its operator has the replacement secret. Deployment configuration must be present before release.

**Verification:**

In a non-production environment, confirm that `admin123` is rejected, the replacement secret succeeds, the hash is not in tracked source, and unauthenticated admin write routes return 401.

---

### [MEDIUM] Reflected XSS in Legal-Page Language Attribute

**ID:** SEC-002
**Status:** Confirmed Vulnerability
**Severity:** Medium
**Confidence:** High
**CWE:** CWE-79 (Improper Neutralization of Input During Web Page Generation)
**OWASP:** A03:2021 Injection

**Location:**

- File: `public/privacy.php`, `public/terms.php`
- Class: N/A
- Function/Method: top-level page rendering
- Line/Code reference: `public/privacy.php:3`; `public/terms.php:3`

**Evidence:**

Both pages directly emit `$_GET['lang']` into the quoted HTML `lang` attribute: `<html lang="<?= ($lang = ($_GET['lang'] ?? $_SESSION['lang'] ?? 'en')) ?>">`. Unlike the main front controller, these pages do not apply the `en`/`id` allow-list and do not HTML-encode the value. An attacker-controlled quote can therefore break out of the attribute and inject another HTML attribute/event handler.

**Root Cause:**

Request data is output in an HTML attribute without context-appropriate encoding or allow-list validation.

**Attack Scenario:**

An attacker sends a victim a crafted link to `/privacy?lang=...` or `/terms?lang=...` containing an attribute-breaking payload. When the page renders, browser-side script can execute in the application origin. No payload was sent during this audit.

**Impact:**

- Confidentiality: Browser-accessible content and same-origin responses may be read.
- Integrity: A script may modify the displayed page or submit requests as the visitor.
- Availability: A script can disrupt the rendered page.
- Authentication/Authorization: If an administrator visits the link while logged in, the impact can extend to actions permitted by that session.
- Business data: Phishing/defacement in the trusted origin is possible.

**Recommended Remediation:**

Use the same strict language allow-list as `public/index.php` and encode every attribute output with `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`. Prefer a centralized page bootstrap/controller so direct public pages cannot bypass security controls.

**Regression Risk:**

Only unsupported language values should change, falling back to the configured default. Validate legitimate `en` and `id` rendering after release.

**Verification:**

Request each legal page with normal and malformed `lang` values. Confirm the response contains only an allowed language token and that special characters are never interpreted as markup.

---

### [MEDIUM] Missing CSRF Token Validation on Authenticated Content Writes

**ID:** SEC-003
**Status:** Potential Vulnerability / Needs Verification
**Severity:** Medium
**Confidence:** Medium
**CWE:** CWE-352 (Cross-Site Request Forgery)
**OWASP:** A01:2021 Broken Access Control

**Location:**

- File: `public/index.php`, `app/Controllers/AdminController.php`
- Class: `AdminController`
- Function/Method: `createCard()`, `updateCard()`, `createCertificate()`, `updateCertificate()`, `logout()`
- Line/Code reference: `public/index.php:40,44-45,50-51`; `app/Controllers/AdminController.php:36-40,61-82,86-118,153-175,179-212`

**Evidence:**

Authenticated state-changing routes rely on `$_SESSION['is_admin']` via `requireAdmin()`, but the request handlers do not validate a CSRF token, Origin, or Referer. The source also does not set PHP session cookie `SameSite` attributes; this may be set by `php.ini` or Apache outside the repository.

**Root Cause:**

No application-layer request-origin binding is implemented for cookie-authenticated state changes.

**Attack Scenario:**

If the production session cookie is sent on a cross-site POST (for example `SameSite=None`, an older browser setting, or another deployment override), a malicious site can submit a multipart form to an admin content-create/update endpoint while an editor is authenticated. Browser CORS does not protect simple HTML form submissions. This has not been dynamically verified because cookie configuration and deployed HTTPS behavior were outside the source audit.

**Impact:**

Integrity of public content and uploads may be affected under the stated cookie conditions. Confidentiality impact is not expected from a blind CSRF request.

**Recommended Remediation:**

Require a cryptographically random per-session CSRF token on every state-changing endpoint and validate it server-side. Set `Secure`, `HttpOnly`, and `SameSite=Strict` or an explicitly justified `Lax` session-cookie policy; additionally validate Origin for unsafe methods.

**Regression Risk:**

Existing JavaScript forms and any external integration must be updated to supply the token. Strict SameSite can affect intentionally cross-site flows.

**Verification:**

Inspect actual `Set-Cookie` headers over HTTPS and attempt a controlled cross-origin form submission in a non-production environment. It must fail without a valid CSRF token.

---

### [MEDIUM] Broad Local Write Permissions on Database and Upload Directories

**ID:** SEC-004
**Status:** Needs Verification
**Severity:** Medium
**Confidence:** Medium
**CWE:** CWE-732 (Incorrect Permission Assignment for Critical Resource)
**OWASP:** A05:2021 Security Misconfiguration

**Location:**

- File/Path: `data/`, `public/assets/uploads/`
- Class: N/A
- Function/Method: N/A
- Line/Code reference: Windows ACL observation during audit; upload storage is used by `app/Controllers/AdminController.php:319-334`

**Evidence:**

The observable NTFS ACL grants `NT AUTHORITY\Authenticated Users` Modify access to both directories. `data/` contains the SQLite database and `public/assets/uploads/` contains publicly served user-generated files. This is broader than a dedicated web-server service identity.

**Root Cause:**

Directory access is granted to the broad local Authenticated Users principal instead of a minimally privileged service account/group.

**Attack Scenario:**

On a shared or multi-user Windows host, any local authenticated account that maps to this ACL can replace the SQLite database or upload assets, bypassing application authorization. Whether untrusted local accounts exist and whether the deployed host uses these ACLs is not knowable from the repository.

**Impact:**

Potential full integrity compromise of application data/public assets and possible availability impact. Direct remote exploitability is not established.

**Recommended Remediation:**

Grant Modify only to the Apache/PHP service identity (and administrators as operationally required), with read-only access for other local users. Keep the SQLite database outside the web root and back up before ACL changes.

**Regression Risk:**

Overly restrictive ACLs can break visitor tracking, contact persistence, migrations, and uploads. Identify the actual Apache service account first.

**Verification:**

On the deployment host, inspect effective ACLs and test application write operations under the service identity and a non-privileged local account. The latter must be denied.

---

### [LOW] Session Identifier Is Not Regenerated After Administrative Login

**ID:** SEC-005
**Status:** Security Weakness
**Severity:** Low
**Confidence:** High
**CWE:** CWE-384 (Session Fixation)
**OWASP:** A07:2021 Identification and Authentication Failures

**Location:**

- File: `public/index.php`, `app/Controllers/AdminController.php`
- Class: `AdminController`
- Function/Method: `login()`
- Line/Code reference: `public/index.php:7`; `app/Controllers/AdminController.php:20-27`

**Evidence:**

The application calls `session_start()` and, after successful login, only sets `$_SESSION['is_admin'] = true`. There is no `session_regenerate_id(true)` before privilege elevation.

**Root Cause:**

The application preserves the pre-authentication session ID when granting administrative privileges.

**Attack Scenario:**

An attacker able to cause an editor to use a known pre-authentication PHP session ID could reuse it after the editor logs in. Modern PHP defaults and browsers may reduce practical fixation paths, but ID rotation is the standard control.

**Impact:**

Potential administrative session hijacking under a separate session-ID fixation precondition.

**Recommended Remediation:**

Regenerate the session ID immediately after successful password verification and again on privilege changes. Configure session cookie `Secure`, `HttpOnly`, and SameSite attributes in deployment.

**Regression Risk:**

AJAX clients holding cached CSRF/session-related state need retesting after login.

**Verification:**

Record the session cookie before and after a successful test login; the identifier must change while the authenticated state remains valid.

---

### [LOW] Image Decode Occurs Before Pixel-Dimension Resource Limit

**ID:** SEC-006
**Status:** Potential Vulnerability
**Severity:** Low
**Confidence:** Medium
**CWE:** CWE-400 (Uncontrolled Resource Consumption)
**OWASP:** A04:2021 Insecure Design

**Location:**

- File: `app/Controllers/AdminController.php`
- Class: `AdminController`
- Function/Method: `handleUpload()`
- Line/Code reference: `app/Controllers/AdminController.php:284-301,304-316`

**Evidence:**

The code enforces a 5 MB compressed-file limit and MIME allow-list, then calls `imagecreatefromstring(file_get_contents(...))`. Dimensions are capped only after GD has decoded the image. Highly compressed, high-pixel images can require disproportionate memory during decode.

**Root Cause:**

Resource limits are applied after parsing/decoding attacker-supplied image content.

**Attack Scenario:**

An authenticated editor submits a small compressed image with extremely large dimensions, causing PHP/GD memory or CPU exhaustion during decoding. Authentication is currently weakened by SEC-001. No sample image was generated or uploaded.

**Impact:**

Possible temporary denial of service for PHP workers; no code execution was identified.

**Recommended Remediation:**

Read dimensions with `getimagesize()` before decoding, reject images exceeding a maximum pixel count and width/height, set appropriate PHP memory/time limits, and retain the existing re-encode workflow.

**Regression Risk:**

Legitimate high-resolution portfolio images may be rejected; select limits based on accepted image quality requirements.

**Verification:**

In a controlled environment, test boundary images just below/above the configured pixel limits and confirm oversized images are rejected before GD decode.

---

### [INFORMATIONAL] Browser Security Headers and CDN Integrity Need Hardening

**ID:** SEC-007
**Status:** Informational / Security Hardening
**Severity:** Informational
**Confidence:** High
**CWE:** CWE-693 (Protection Mechanism Failure)
**OWASP:** A05:2021 Security Misconfiguration

**Location:**

- File: `.htaccess`, `views/layouts/main.php`
- Class: N/A
- Function/Method: response/template configuration
- Line/Code reference: `.htaccess:27-31`; `views/layouts/main.php:21,24,265-269`

**Evidence:**

Apache config sets `X-Content-Type-Options`, `X-Frame-Options`, and `Referrer-Policy`, but no Content-Security-Policy, `Permissions-Policy`, or HSTS policy is defined in source. Multiple scripts/styles load from external CDNs; `lucide@latest` is mutable and none uses an `integrity` attribute. HSTS may be set at a proxy/load balancer, so its deployment status is Not Verified.

**Root Cause:**

The browser trust boundary depends on third-party CDN content without pinning and lacks a CSP mitigation layer.

**Attack Scenario:**

A compromised CDN/version resolution can execute script in visitors' browsers. CSP would also reduce impact from future XSS, though it does not replace output encoding.

**Impact:**

Defense-in-depth gap affecting client-side confidentiality/integrity if an external dependency or injected script is compromised.

**Recommended Remediation:**

Pin exact CDN versions, add SRI where feasible, preferably self-host critical assets, and deploy a tested CSP using nonces/hashes for required inline bootstrap scripts. Set HSTS only after HTTPS is universally available.

**Regression Risk:**

CSP can block inline scripts, dynamic imports, EmailJS, map tiles, and CDN resources until allow-lists/nonces are correctly configured.

**Verification:**

Use browser developer tools and CSP report-only mode to confirm all required scripts/styles/connections are permitted; verify each pinned asset hash/version.

## 7. Security Strengths

- Database queries in reviewed models use PDO prepared statements and bound parameters; no confirmed SQL injection sink was found. `Tool::filtered()` and search bind user input rather than concatenate it into SQL.
- Admin CRUD routes consistently call `requireAdmin()` and cast route IDs to integers before queries.
- Uploads require an actual uploaded file, limit compressed size, verify MIME with `finfo`, decode/re-encode to server-generated random `.webp` names, and block script execution in `public/assets/uploads/.htaccess`.
- `deleteImageFile()` resolves paths and confirms they remain under `assets/uploads` before deletion, reducing traversal risk.
- No source use of `eval`, `unserialize`, shell execution, process-spawning, or user-controlled URL fetch was identified. The only external fetch is a fixed `ip-api.com` URL built from `REMOTE_ADDR`; SSRF is not confirmed.
- Root `.htaccess` denies direct access to application, configuration, data, logs, documentation, VCS metadata, dotfiles, and database-like extensions; it disables directory indexing and blocks TRACE/TRACK.
- The application removes `X-Powered-By` and sets nosniff, frame, and referrer-policy headers when Apache `mod_headers` is enabled.
- The admin password uses `password_verify()` with a bcrypt hash rather than plaintext comparison. This does not mitigate SEC-001's default credential.

## 8. Recommended Security Improvements

### Priority 1 — Critical / High

1. Replace and remove the default editor password from tracked configuration (SEC-001).
2. Fix the unsafe `lang` output on `privacy.php` and `terms.php`; centrally apply allow-listing/encoding (SEC-002).
3. Add login rate limiting and audit logging after the credential replacement.

### Priority 2 — Medium

1. Add CSRF tokens and origin validation to all authenticated unsafe methods; set explicit secure session-cookie flags (SEC-003).
2. Restrict NTFS write ACLs to the web-server service identity after confirming deployment account requirements (SEC-004).
3. Move lazy schema migration/DDL out of normal page rendering into a controlled deployment/migration process. This is an integrity/operability improvement observed in `Service`, `ShowcaseProject`, and `Certificate`; it was not separately rated as an externally exploitable vulnerability.

### Priority 3 — Low / Hardening

1. Rotate the PHP session ID after login (SEC-005).
2. Enforce image dimension/pixel limits before GD decode (SEC-006).
3. Pin browser dependencies, add SRI/CSP, and verify HTTPS/HSTS at the deployment edge (SEC-007).
4. Add a dependency manifest/lock process or software bill of materials. No package lock file was present, so library CVE versions could not be determined statically.

## 9. Verification Plan

After remediation, perform an authorized non-production retest:

1. Test authentication with the old default, valid replacement, invalid passwords, rate-limit thresholds, and session-ID rotation.
2. Test both legal pages with malformed `lang` input and confirm only encoded/allow-listed output is produced.
3. Submit cross-origin form requests with/without CSRF tokens, inspect HTTPS cookie attributes, and confirm unsafe admin routes reject invalid origin/token combinations.
4. Exercise admin CRUD and uploads under the Apache service identity, then confirm a non-privileged local account cannot modify database/upload directories.
5. Test valid, oversized, and decompression-bomb-style image boundaries in an isolated environment.
6. Inspect response headers, CSP report-only telemetry, SRI hashes, and production TLS/HSTS configuration.
7. Re-run static analysis and conduct authorized dynamic WSTG/API injection testing using test accounts. SQLi, SSRF, and command-injection payload tests were intentionally not performed in this audit.

## 10. Audit Limitations

This was a source/configuration review, not a live penetration test. The following remain Not Verified without a controlled runtime environment and authorized test accounts:

- Effective Apache virtual-host configuration, `mod_headers`, HTTPS/TLS, proxy rules, PHP `session.*` settings, cookie flags, error display/logging, and CORS headers.
- Database contents, schema state, backups, encryption at rest, and production database/service-account identity. The database was not opened or modified.
- Whether the broad NTFS ACL is present on the production host and whether untrusted local users exist.
- Runtime behavior of EmailJS, PHP `mail()`, ip-api.com, third-party CDNs, rate limits/WAF, and actual error responses.
- Authorization testing across multiple user roles: the application appears to have one admin role only; no test accounts or live requests were used.
- Dependency CVEs: no Composer/npm lockfile was present, and dependency versions fetched from `latest`/CDN cannot be resolved safely from source alone.
- Full active testing for SQLi, NoSQLi, SSRF, command injection, XXE, race conditions, and IDOR. Static inspection found no relevant confirmed sink/bypass, but dynamic testing is required for a definitive negative conclusion.

## 11. Final Security Assessment

The application has useful baseline controls—prepared SQL statements, session-gated admin handlers, re-encoded image uploads, path-safe deletion, and Apache protections—but it is not safe to expose the editor while the documented default password remains configured. The confirmed reflected XSS on legal pages further raises risk because it can execute in the same origin as an authenticated editor session.

Prioritize SEC-001 and SEC-002 before public deployment, then complete CSRF/session/ACL hardening and production configuration verification. No remediation was applied during this audit.

---

# Accessibility, Performance & Duplicate Code Audit

**Audit Date:** 2026-08-26

**Project:** `C:\xampp\htdocs\alfizilham`

**Audit Mode:** Read-Only

## 1. Executive Summary

The project is a server-rendered vanilla PHP portfolio application with a custom MVC structure, SQLite/PDO, vanilla CSS/JavaScript, and a single-page UI with custom interactive controls. This audit found confirmed accessibility issues in keyboard operation and programmatic form feedback, a likely first-visit performance cost from synchronous external geolocation, and structural duplication in the admin CRUD and legal-page templates.

The highest usability concern is that custom dropdown triggers are deliberately removed from the tab order. The highest performance concern is a synchronous outbound HTTP lookup during request processing for a new visitor. The most material maintainability duplication is the parallel card/certificate CRUD implementation, which has already grown separate but substantially similar controller/model paths.

No runtime benchmark, browser assistive-technology session, Lighthouse run, database mutation, dependency installation, or source modification was performed. Findings whose practical impact depends on runtime/deployment conditions are labelled accordingly.

## 2. Audit Scope

The read-only review covered the application architecture and all source areas relevant to rendered UI and performance: `public/index.php`, custom routing, controllers, models, services, PHP views/partials/layouts, JavaScript UI code, CSS/asset loading declarations, `.htaccess`, configuration, database-access code, and third-party resource declarations.

### Project reconnaissance

* **Architecture/framework:** Vanilla PHP MVC; no application framework or Composer manifest was found.
* **PHP/database:** PHP 8.x is documented in `AGENT.md`; SQLite is accessed via PDO in `app/Core/Database.php`.
* **Frontend:** Server-rendered PHP templates, vanilla CSS, and vanilla JavaScript in `public/assets/js/main.js`; no CSS framework was identified.
* **Routing/controllers:** `public/index.php` registers a custom `Router`, `PageController`, `ApiController`, and `AdminController`.
* **Views:** `views/layouts/main.php` composes section and partial templates through `View`.
* **Endpoints:** Public contact/visitor/tools/projects/cards routes plus session-protected editor CRUD routes for showcase cards and certificates.
* **Assets:** CSS is loaded in the document head; JavaScript and third-party scripts are loaded near the end of `main.php`. Fonts, icon libraries, maps, animation libraries, and Chart.js are CDN resources.
* **Image handling:** Admin uploads are re-encoded to WebP by GD; most content images declare `loading="lazy"`.

## 3. Methodology

The audit used static source-code and configuration inspection. Accessibility review considered applicable WCAG 2.1/2.2 concepts—semantic controls, keyboard access, labels, validation feedback, accessible names, dialogs, landmarks, headings, and dynamic content. Performance review classified observations as static code findings, likely runtime issues, or needs-benchmark items. Duplicate-code review compared exact and structural similarities across controllers, models, templates, and JavaScript.

The installed UI-guideline skill could not be read because the environment repeatedly failed to create a Windows subprocess for that path; no substitute external scanner was used. This limitation does not affect the direct source evidence cited below, but visual contrast and runtime assistive-technology behavior remain unverified.

## 4. Accessibility Findings

## [HIGH] Custom Dropdown Triggers Are Removed From Keyboard Tab Navigation

**ID:** AUDIT-001

**Category:** Accessibility

**Status:** Confirmed Issue

**Confidence:** High

**Location:**

* File: `views/sections/contact.php`
* Class: N/A
* Function: Contact form custom dropdown markup
* Line/reference: country-code and service custom dropdown trigger buttons, including `tabindex="-1"`; JavaScript dropdown initialization is in `public/assets/js/main.js`

**Evidence:**

The country-code and service dropdown trigger elements are native `<button>` elements but explicitly include `tabindex="-1"`. They open custom dropdown popups, while the actual associated values are stored in hidden inputs. No native `<select>` alternative is present in the rendered template.

**Problem:**

Removing the primary trigger from sequential keyboard navigation prevents keyboard-only users from reaching and operating those custom choices through normal Tab navigation. Because the visible control holds the interaction and the form value is hidden, this affects completion of the contact form rather than merely a cosmetic control.

**Impact:**

Keyboard-only users, users of switch devices, and some screen-reader workflows may be unable to choose country/service values or understand how to activate them. This can block contact-form submission.

**Root Cause:**

A custom dropdown pattern was implemented while intentionally suppressing focusability of its visible buttons.

**Recommendation:**

Prefer a semantic `<select>` when the design permits. If a custom combobox is necessary, keep its trigger focusable, expose an accessible name, implement Arrow/Enter/Escape keyboard behavior, maintain focus/state, and synchronize the hidden value.

**Implementation Guidance:**

Do not add ARIA alone to a non-working control. Either use a labelled native select, or follow the WAI-ARIA combobox/listbox interaction model with one tab stop and predictable focus handling.

**Regression Risk:**

Changing focus order can expose assumptions in existing JavaScript popup handling and mobile styling.

**Verification:**

Using keyboard only, Tab to every contact field, open each choice, select an option, close it with Escape, and submit the form. Test with at least one screen reader and browser combination.

---

## [MEDIUM] Custom Form Fields Lack Reliably Associated Programmatic Labels

**ID:** AUDIT-002

**Category:** Accessibility

**Status:** Confirmed Issue

**Confidence:** High

**Location:**

* File: `views/sections/contact.php`
* Class: N/A
* Function: Contact form field markup
* Line/reference: phone, service, and budget label/group markup; visible `<label>` elements preceding wrapper `<div>` elements instead of targeting the nested input/control

**Evidence:**

Several labels are standalone elements without a `for` attribute and do not wrap the relevant nested control. For example, the phone label is followed by a wrapper containing `#contactPhone`; similarly, the service and budget labels precede custom-control wrappers. The input elements do not declare `aria-label` or `aria-labelledby` linking them to those labels.

**Problem:**

A visible caption is not necessarily an accessible name. Assistive technologies can announce an unlabeled telephone input or a generic button, leaving users without the field purpose.

**Impact:**

Screen-reader users can have difficulty identifying and completing important contact fields; voice-control users may also lack a stable visible-label target.

**Root Cause:**

Visual grouping was used in place of explicit HTML label/control association.

**Recommendation:**

Associate each label with exactly one form control via `for`/`id`, or use `aria-labelledby` for composite widgets. Use `fieldset`/`legend` for genuinely grouped controls such as phone country code plus local number.

**Implementation Guidance:**

Give the phone label `for="contactPhone"`; for a custom service combobox, reference the visible service label from the focusable trigger. Keep the semantic relationship independent of styling wrappers.

**Regression Risk:**

Low. CSS selectors or JavaScript that target a particular DOM nesting may need a small compatibility check.

**Verification:**

Inspect the accessibility tree and confirm each interactive form field has a single, meaningful accessible name matching its visible label.

---

## [MEDIUM] Client-Side Validation Feedback Is Not Programmatically Announced

**ID:** AUDIT-003

**Category:** Accessibility

**Status:** Confirmed Issue

**Confidence:** High

**Location:**

* File: `views/sections/contact.php`, `views/layouts/main.php`
* Class: N/A
* Function: contact/editor form error and status regions
* Line/reference: `form` elements use `novalidate`; errors are plain `<span>` elements such as `#nameError`; status is `<div class="form-status" id="formStatus" hidden>`

**Evidence:**

The contact and editor forms opt out of browser constraint validation via `novalidate`. Their error/status elements have no `role="alert"`, `aria-live`, `aria-describedby`, or `aria-invalid` relationship in the rendered markup. The same pattern appears in the login/card/certificate modal forms.

**Problem:**

When JavaScript reveals or changes a visually displayed error, assistive technologies are not given a reliable announcement or a programmatic relation between the input and its error.

**Impact:**

Users of screen readers may submit an invalid form without hearing why it failed, especially where focus remains on the submit button or another field.

**Root Cause:**

Custom validation UI replaces native validation without corresponding accessible-error semantics.

**Recommendation:**

On validation failure, mark affected controls with `aria-invalid="true"`, reference concise error text with `aria-describedby`, announce a summary/status through a suitable live region, and move focus predictably to the first invalid control or an error summary.

**Implementation Guidance:**

Use a single persistent `aria-live="polite"` status region for non-critical updates and an alert/summary for submission failures. Do not make every field a live region, because that produces noisy announcements.

**Regression Risk:**

Incorrectly broad live regions can cause duplicate announcements; test asynchronous submit success and failure paths.

**Verification:**

Submit empty/invalid fields using a screen reader. Confirm the first invalid field is reached or clearly identified and that its associated error is announced once.

---

## [LOW] Focus Management for Custom Modals Requires Runtime Verification

**ID:** AUDIT-004

**Category:** Accessibility

**Status:** Needs Verification

**Confidence:** Medium

**Location:**

* File: `views/layouts/main.php`, `public/assets/js/main.js`
* Class: N/A
* Function: editor login, card, certificate, delete, timeline, and lightbox dialog handling
* Line/reference: dialog containers include `role="dialog"` and `aria-modal="true"`; custom open/close behavior is implemented in `main.js`

**Evidence:**

The templates define multiple custom dialogs and overlays. They correctly expose dialog semantics in several places, but static template evidence alone does not establish that focus moves into a dialog on open, remains contained while open, returns to its invoking control on close, or that Escape behavior works across all paths.

**Problem:**

Dialogs without complete focus management can allow keyboard focus to move behind the overlay or leave users stranded after close.

**Impact:**

Potential keyboard-navigation failure in editor, timeline, and lightbox workflows.

**Root Cause:**

Custom dialogs require JavaScript lifecycle behavior beyond static ARIA attributes.

**Recommendation:**

Perform a runtime keyboard review before changing code. If absent, implement initial focus, focus containment, Escape close where appropriate, focus restoration, and accessible dialog labels consistently.

**Implementation Guidance:**

Use a tested focus-trap utility or one shared dialog helper; avoid implementing divergent focus logic independently for each overlay.

**Regression Risk:**

Focus trapping can interfere with nested dialogs and the custom timeline popup if stack state is not managed.

**Verification:**

Open each dialog with keyboard, cycle Tab/Shift+Tab, close it with keyboard, and verify focus returns to the trigger. Repeat with a screen reader.

## 5. Performance Findings

## [MEDIUM] New-Visitor Requests Perform a Synchronous External Geolocation Call

**ID:** AUDIT-005

**Category:** Performance

**Status:** Likely Runtime Issue

**Confidence:** High

**Location:**

* File: `public/index.php`, `app/Services/VisitorService.php`
* Class: `VisitorService`
* Function: `track()`, `getCountry()`
* Line/reference: `public/index.php:20`; `VisitorService::track()` calls `getCountry()`; `getCountry()` calls `file_get_contents("http://ip-api.com/json/{$ip}..." )` with a two-second timeout

**Evidence:**

Visitor tracking runs before every routed page/API response, except once the session flag is set. For a new non-private/non-local visitor, `getCountry()` executes a blocking HTTP request to ip-api.com. The stream context timeout is two seconds and no asynchronous queue/cache is used.

**Problem:**

Availability and latency of an external service are placed on the request critical path. A slow/unreachable lookup can delay the initial page response by up to the configured timeout.

**Impact:**

Likely elevated first-visit TTFB and degraded perceived performance for new visitors, especially when the third-party service is slow. Subsequent requests in the same session are skipped.

**Root Cause:**

Synchronous enrichment is coupled to request-time analytics recording.

**Recommendation:**

Decouple geolocation from the page response: use a short-lived cache, background/queue processing, deferred analytics, or accept a default/unknown country if the lookup is not immediately available.

**Implementation Guidance:**

Keep visitor counting local and make country enrichment best-effort. If a synchronous fallback is retained, lower the timeout and monitor lookup failure/latency.

**Regression Risk:**

Country statistics may arrive later or be less complete; define whether analytics accuracy or page responsiveness has priority.

**Verification:**

Measure server response timings for a fresh session with normal, delayed, and blocked ip-api connectivity. Confirm the revised path does not block page rendering.

---

## [LOW] Homepage Performs Numerous Independent Reads and Schema Checks Per Request

**ID:** AUDIT-006

**Category:** Performance

**Status:** Potential Issue / Needs Benchmark

**Confidence:** High

**Location:**

* File: `app/Controllers/PageController.php`, `app/Models/Service.php`, `app/Models/ShowcaseProject.php`, `app/Models/Certificate.php`
* Class: `PageController`, `Service`, `ShowcaseProject`, `Certificate`
* Function: `PageController::index()`, `all()`, `ensureImageColumn()`, `ensureTable()`
* Line/reference: `PageController::index()` calls multiple `::all()` methods plus visitor count; the model ensure methods use `PRAGMA table_info`, `CREATE TABLE IF NOT EXISTS`, and/or `ALTER TABLE` checks

**Evidence:**

One homepage render loads projects, showcase cards, tools, FAQs, FAQ categories, testimonials, services, gallery, certificates, and a visitor count. `Service::all()`, `ShowcaseProject::all()`, and `Certificate::all()` each include migration/schema-check logic on their first invocation in a PHP request. The visitor API similarly performs multiple aggregate queries.

**Problem:**

The application makes several separate SQLite reads and repeats schema-introspection/DDL eligibility checks during ordinary web requests. This is structurally avoidable work, although current seeded data is small and no measured slowdown was obtained.

**Impact:**

Potentially higher database/filesystem overhead as traffic or SQLite data grows. The current impact is not quantifiable without a benchmark.

**Root Cause:**

All page data is assembled eagerly and schema migration responsibilities reside in runtime model reads.

**Recommendation:**

Move migrations to deployment/maintenance tasks, keep read models read-only, and benchmark request/query counts before introducing caching or combining queries. Consider modest cache lifetimes only for public, infrequently changing portfolio data.

**Implementation Guidance:**

Instrument query count and request timing first. A cache should be invalidated by editor writes; otherwise, caching can make updated portfolio content appear stale.

**Regression Risk:**

Moving migrations can cause deployment failures if the migration is not run. Caching can create stale public content after admin updates.

**Verification:**

Benchmark a cold and warm homepage/API response, capture SQLite query counts, and confirm schema checks no longer occur in normal requests.

---

## [LOW] Full Third-Party UI Library Set Is Loaded for the Single Page

**ID:** AUDIT-007

**Category:** Performance

**Status:** Potential Issue / Needs Benchmark

**Confidence:** Medium

**Location:**

* File: `views/layouts/main.php`, `public/assets/js/main.js`
* Class: N/A
* Function: document asset loading and UI initialization
* Line/reference: `views/layouts/main.php:21,24,265-269`; consolidated UI initialization in `main.js`

**Evidence:**

The main layout loads Google Fonts, Bootstrap Icons, Leaflet, Lucide, Lenis, GSAP, and Chart.js for the single page. Third-party scripts are loaded on all visits, and `main.js` initializes numerous interactive sections. Several assets are functionally relevant, but source review alone cannot quantify their transfer/execution cost or prove that every library is used on every rendered route.

**Problem:**

The critical page path includes multiple remote resources and a broad JS feature set, which may add network, parse, and execution cost on constrained devices.

**Impact:**

Potential slower interaction readiness and increased transfer size, particularly on mobile/high-latency connections.

**Root Cause:**

Feature libraries are globally included rather than conditionally loaded by need.

**Recommendation:**

Use a performance budget and real-user/Lighthouse measurements before removing or deferring anything. Load route/feature-specific libraries conditionally where measurements show benefit; retain only resources essential for initial content.

**Implementation Guidance:**

Audit actual runtime use of Chart.js and other libraries, pin versions, and consider `defer`/feature-level dynamic imports when compatible with required initialization order.

**Regression Risk:**

Deferring animation/map/icon code can cause visual regressions or race conditions if initialization expects globals synchronously.

**Verification:**

Record a performance trace on mobile and desktop, including transfer sizes, long tasks, LCP, INP, and unused JavaScript. Compare before/after only after a measured change.

## 6. Duplicate Code Findings

## [MEDIUM] Parallel Showcase-Card and Certificate CRUD Flows Duplicate Controller Logic

**ID:** AUDIT-008

**Category:** Duplicate Code

**Status:** Confirmed Issue

**Confidence:** High

**Location:**

* File A: `app/Controllers/AdminController.php`
* Location A: `createCard()`, `updateCard()`, `deleteCard()` (approximately lines 61-137)
* File B: `app/Controllers/AdminController.php`
* Location B: `createCertificate()`, `updateCertificate()`, `deleteCertificate()` (approximately lines 153-231)
* Similarity/type: Structural duplication

**Evidence:**

Both CRUD families repeat the same sequence: `requireAdmin()`, read/cast route ID where applicable, load existing record, validate fields, optionally call `handleUpload()`, delete the previous image on replacement/deletion, persist through a model, then emit a JSON response. They differ mainly in field names and model calls.

**Problem:**

Cross-cutting behavior such as authorization, upload replacement, not-found handling, error status conventions, or audit logging must be maintained in two parallel paths. A future fix can easily reach one content type but not the other.

**Impact:**

Medium maintainability and consistency risk in a privileged content-management surface; it also increases the test matrix.

**Root Cause:**

Two domain entities were implemented as separate end-to-end CRUD workflows without extracting their shared administrative operation pattern.

**Recommendation:**

When a future feature requires changes to both, extract only the genuinely common pieces: authorization middleware, upload-replacement helper, entity lookup/error helper, and a small interface/metadata map. Keep entity-specific validation and response payloads explicit.

**Implementation Guidance:**

Avoid a large generic CRUD abstraction prematurely. Start with a shared `replaceUploadedImage()`/`deleteOwnedUpload()` helper and consistent request/response helpers, then reassess whether the remaining structural duplication warrants a domain service.

**Regression Risk:**

Over-generalization can obscure the differences between card links/descriptions and certificate fields. Preserve existing response shapes for JavaScript clients.

**Verification:**

After any future refactor, regression-test create/update/delete/error cases for both resource types, including image replacement and missing IDs.

---

## [MEDIUM] Privacy and Terms Pages Duplicate Their Standalone Document Shell

**ID:** AUDIT-009

**Category:** Duplicate Code

**Status:** Confirmed Issue

**Confidence:** High

**Location:**

* File A: `public/privacy.php`
* Location A: document head, stylesheet links, page wrapper, and back link
* File B: `public/terms.php`
* Location B: equivalent document head, stylesheet links, page wrapper, and back link
* Similarity/type: Exact/structural template duplication

**Evidence:**

Both standalone pages require `bootstrap.php`, render their own HTML document shell, load the same Google Fonts and three local stylesheets, and render nearly identical page/container/back-link markup. They also duplicate the unsafe direct `lang` output previously documented in security finding SEC-002.

**Problem:**

Shared layout behavior must be corrected in two files. The duplicated unsafe language handling demonstrates the practical consistency cost rather than merely a stylistic preference.

**Impact:**

Medium maintenance risk and repeat defect risk for accessibility, security, and asset-loading changes across legal pages.

**Root Cause:**

Legal pages bypass the existing `View` layout/partial system and each own a complete document shell.

**Recommendation:**

At the next planned template change, use a shared legal-page layout/partial or route these pages through the existing view renderer. Centralize language normalization, document metadata, stylesheet inclusion, landmarks, and the return link.

**Implementation Guidance:**

Keep only the legal-page title/body as per-page content. Do not refactor solely for abstraction if the page architecture is scheduled to change; first address the confirmed security issue in both locations.

**Regression Risk:**

URL rewriting and relative asset paths may change when moving files through the main layout. Check `/privacy`, `/terms`, and direct `.php` redirects.

**Verification:**

Compare rendered legal pages before/after for title, styling, navigation target, language behavior, and semantic landmarks.

---

## [LOW] Repeated Lazy-Migration Pattern Exists Across Read Models

**ID:** AUDIT-010

**Category:** Duplicate Code

**Status:** Improvement Recommendation

**Confidence:** High

**Location:**

* File A: `app/Models/Service.php`
* Location A: `ensureImageColumn()`
* File B: `app/Models/ShowcaseProject.php`
* Location B: `ensureTable()`
* File C: `app/Models/Certificate.php`
* Location C: `ensureTable()`
* Similarity/type: Structural/logical duplication

**Evidence:**

Each model holds a request-static `$done` guard, inspects schema with `PRAGMA table_info`, then conditionally executes DDL before read operations. The table/column details differ, but the migration-on-read pattern is repeated.

**Problem:**

Schema-evolution responsibility is duplicated among domain read models. This duplicates error handling and makes normal request behavior depend on migration state.

**Impact:**

Low direct maintainability impact, with a related potential performance/operational cost described in AUDIT-006.

**Root Cause:**

No dedicated deployment migration mechanism is used.

**Recommendation:**

Adopt one controlled migration entry point when the project next changes schema. Do not create an abstraction simply to remove a few lines; the primary goal is to eliminate DDL from ordinary reads.

**Implementation Guidance:**

Use ordered/idempotent migration files or a single CLI migration command with recorded schema version, executed before web traffic is served.

**Regression Risk:**

Migration ordering and rollback need operational discipline; do not remove legacy checks until deployment procedures are proven.

**Verification:**

Run migration from an empty and an existing database copy, then confirm normal read paths perform no DDL/schema check.

## 7. Cross-Cutting Findings

* The custom dropdown issue (AUDIT-001) and label issue (AUDIT-002) stem from custom visual controls replacing native form semantics. A shared, accessible form-control pattern would reduce both accessibility defects and future duplicated JavaScript.
* The lazy-migration duplication (AUDIT-010) directly contributes to the potential per-request work in AUDIT-006. This is a relationship between findings, not an additional performance claim.
* Legal-page duplication (AUDIT-009) has already propagated a security defect recorded in SEC-002. Centralizing shared rendering reduces future security, accessibility, and asset-consistency drift.
* The global third-party loading observation (AUDIT-007) must be assessed with real measurements; removing dependencies merely because they are third-party would be an unsupported optimization.

## 8. Strengths / Good Practices

* The main page uses structural landmarks including `main`; templates also use native buttons and links extensively rather than clickable generic elements.
* Multiple dialog templates provide `role="dialog"`, `aria-modal="true"`, and visible labels. Runtime focus behavior still requires verification.
* Many images use descriptive `alt` text or intentional empty alternative text for decorative content, and many use native `loading="lazy"`.
* The main layout includes a viewport meta tag and uses responsive CSS files.
* Public data access uses a small number of simple PDO prepared-query model methods; no N+1 loop query was found in the inspected controller/model flows.
* Apache configuration enables gzip compression and sets cache lifetimes for common static asset types.
* Reusable layout/partial/section primitives already exist for the primary site, providing an appropriate foundation for future consolidation.

## 9. Recommended Improvements

### Priority 1 — High

1. Restore keyboard-operable, labelled contact selectors (AUDIT-001 and AUDIT-002).
2. Add programmatic form validation feedback and test all contact/editor flows with keyboard and screen reader (AUDIT-003).
3. Decouple visitor geolocation from the synchronous page request path (AUDIT-005).

### Priority 2 — Medium

1. Runtime-test and, if needed, standardize modal focus lifecycle (AUDIT-004).
2. Consolidate shared admin CRUD behaviors incrementally, retaining explicit entity-specific validation (AUDIT-008).
3. Centralize legal-page document/layout behavior while preserving public URLs (AUDIT-009).

### Priority 3 — Low / Evidence-Gated

1. Benchmark database/request work before caching or query consolidation (AUDIT-006).
2. Establish a performance budget and measure third-party transfer/execution before deferring assets (AUDIT-007).
3. Move migrations out of request-time read paths in a scheduled schema-management improvement (AUDIT-010).

## 10. Verification Plan

1. Complete keyboard-only and screen-reader acceptance tests for navigation, contact form, editor, dropdowns, dialogs, delete confirmation, timeline picker, and lightbox.
2. Use browser accessibility-tree inspection and automated checks (for example axe) as support, then manually validate dynamic states and focus order.
3. Measure cold/warm response times and database query counts with fresh sessions and controlled external-geolocation latency.
4. Capture mobile and desktop performance traces before any asset-loading change; compare LCP, INP, transfer, long tasks, and unused JavaScript.
5. Establish regression tests for both card and certificate CRUD flows before consolidating common code.
6. Compare rendered `/privacy` and `/terms` routes after any template consolidation, including the pre-existing security remediation.

## 11. Audit Limitations

* No live browser, assistive technology, automated accessibility scanner, or visual contrast measurement was run. Color contrast, responsive focus visibility, dynamic ARIA state changes, and modal keyboard behavior need runtime verification.
* No performance benchmark or production telemetry was collected; no finding claims measured timing, CPU, memory, Core Web Vitals, or transfer sizes.
* Source evidence cannot prove effective PHP/Apache production configuration, CDN behavior, network latency, browser cache state, or database index definitions.
* The UI-guideline skill was unavailable to read because the local Windows process launcher repeatedly returned error 1920. Static source inspection continued without it.
* Exact duplication percentages were intentionally not provided because no similarity tool was run; all duplicate findings cite concrete matching structures instead.

## 12. Final Assessment

The application has a reasonable foundation—native HTML is common, responsive assets are separated, image lazy-loading is present, and the main site already uses reusable templates. However, custom form controls currently undermine keyboard and screen-reader access, and synchronous visitor geolocation can directly delay first-visit responses. Structural duplication in admin and legal-page code raises the probability that fixes will be applied inconsistently.

Address the keyboard/form-feedback issues and external request path first. Then use measured performance data and targeted shared helpers/layouts to improve maintainability without speculative refactoring. This audit applied no source, configuration, database, dependency, or formatting changes; it only appended this report section.

---

# Browser Console Warning & Runtime Intervention Audit

**Audit Date:** 2026-08-26  
**Project:** `C:\xampp\htdocs\alfizilham`  
**Audit Mode:** Read-Only static source review

## 1. Executive Summary

Two console-message classes were audited: the repeated browser Tracking Prevention storage notice and the native lazy-image intervention reported at `public/#service:610`. Neither message is evidence of a security vulnerability or of broken functionality by itself.

The project directly loads several third-party browser resources (Google Fonts, jsDelivr, unpkg, Leaflet/CARTO map resources, dynamically loaded EmailJS, and dynamic module resources), any of which could be the origin shown in a Tracking Prevention message. However, the exact blocked URL was not supplied and no project-owned frontend use of `localStorage`, `sessionStorage`, IndexedDB, `document.cookie`, or an iframe was identified in the reviewed source. The Tracking Prevention message therefore cannot be attributed to a particular domain or fixed responsibly without a DevTools capture of the complete message/initiator.

The image intervention is a browser informational behavior triggered by native `loading="lazy"`. The `#service` portion is a URL fragment, not a project file named `public/#service`; it identifies the rendered document/fragment context. Source contains lazy-loaded images and service data passed to JavaScript, but the supplied message does not identify which rendered image was involved. No source evidence shows that project JavaScript depends on that image's deferred `load` event. Lazy loading should not be removed merely to silence the browser message.

## 2. Console Messages Audited

| ID | Exact message | Initial classification | Can be eliminated from application? |
| --- | --- | --- | --- |
| CONSOLE-001 | `Tracking Prevention blocked access to storage for <URL>.` | Needs Verification; browser privacy control affecting an unknown third-party resource | Needs Verification |
| CONSOLE-002 | `public/#service:610 [Intervention] Images loaded lazily and replaced with placeholders. Load events are deferred. See https://go.microsoft.com/fwlink/?linkid=2048113` | Browser behavior / Informational | Partially |

## 3. Tracking Prevention Analysis

### Source reconnaissance relevant to tracking/storage

Project-owned code reviewed in the PHP templates and `public/assets/js/main.js` did not reveal direct calls to `localStorage`, `sessionStorage`, IndexedDB, `document.cookie`, or a project-owned iframe. PHP uses server-side sessions (`session_start()`), which are not equivalent to a third-party browser-storage API call. `VisitorService` records a visitor server-side and calls ip-api.com from PHP; that server-to-server request cannot itself produce a browser console Tracking Prevention warning.

The following externally loaded resources are project-controlled inclusions and are plausible candidates only until the actual blocked URL is captured:

| Resource | Domain | Type | Loaded from | Purpose | Storage API evidenced in project source | First/third party | Can application control inclusion? |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Plus Jakarta Sans | `fonts.googleapis.com`, `fonts.gstatic.com` | stylesheet/font | `views/layouts/main.php` head | typography | None | Third-party | Yes |
| Bootstrap Icons / Chart.js / dynamic EmailJS | `cdn.jsdelivr.net` | CSS/JavaScript | `views/layouts/main.php`; EmailJS loader in `public/assets/js/main.js` | icons, chart capability, contact email | None in project source; library internals not verified | Third-party | Yes |
| Leaflet, Lucide, Lenis, GSAP | `unpkg.com` | CSS/JavaScript | `views/layouts/main.php` | map, icons, scrolling, animation | None in project source; library internals not verified | Third-party | Yes |
| Map tiles/attribution | CARTO/OpenStreetMap domains | image/tile API | map initialization in `public/assets/js/main.js` | map display | Not verified | Third-party | Yes |
| OGL/module imports, if exercised | `esm.sh` | JavaScript module | dynamic loader in `public/assets/js/main.js` | circular gallery rendering | Not verified | Third-party | Yes |
| ip-api | `ip-api.com` | server-side HTTP API | `app/Services/VisitorService.php` | country lookup | Not browser storage; PHP request | Third-party server-side | Yes |

No advertising pixel, analytics SDK, project-owned iframe, or explicit browser-storage implementation was established from the reviewed source. External anchor links (for example social media) are navigation links, not automatically loaded embedded resources.

### Per-resource attribution result

The required `<URL>` is absent from the supplied message. Consequently, source cannot determine the resource type, exact domain, actual storage API, or whether the call is direct versus inside a dependency. The browser can block a third-party resource's cookie/localStorage/other storage access independently of whether the application itself reads storage. This is normal privacy-protection behavior and is not a vulnerability finding.

## 4. Lazy Loading Image Intervention Analysis

### Source evidence

* Many template and dynamically rendered image elements declare `loading="lazy"`, including gallery, testimonial, project/card, certificate, and dynamically generated UI images.
* `views/sections/services.php` does not itself contain a static service `<img>` element; it serializes service data to `window.__SERVICES` for JavaScript rendering.
* The `#service` token in `public/#service:610` is an in-page fragment pointing at the service section, not a filesystem path. There is no source file named `public/#service` to inspect.
* The main layout loads `main.js`, which renders/initializes interactive sections. The supplied browser message does not identify the individual image URL, DOM selector, or initiator stack needed to distinguish a service image from another image near that viewport position.
* No project-owned lazy-loading library was identified; the relevant implementation is the standard HTML `loading` attribute. No project-owned image `load` event dependency was established in the reviewed source.

### Strategy assessment

The service section is below the primary hero content in `views/layouts/main.php`, so lazy loading of its non-critical content images is generally an appropriate native strategy. The browser may replace offscreen lazy images with placeholders and defer their load events. This intervention preserves bandwidth/initial rendering work; it becomes a project problem only if an image is actually required before interaction/visibility, if layout shifts occur, or if application code relies on its early load event. None of those effects is proven by the console line alone.

## 5. Root Cause Analysis

### Tracking Prevention

**Project trigger:** The project includes third-party origins.  
**Probable direct actor:** A third-party resource/library attempting storage access, or browser privacy logic evaluating it.  
**Unverified detail:** Exact URL/domain, resource type, storage API, and initiator.  
**Browser reason:** Tracking Prevention commonly restricts storage for cross-site content to limit cross-site tracking.  
**Application control:** The project can remove, replace, self-host, or defer a resource only after identifying it; it cannot and should not disable a visitor's privacy protections.

### Lazy-image intervention

**Project trigger:** Native `loading="lazy"` attributes on non-critical images.  
**Direct actor:** Browser lazy-loading/intervention logic.  
**Browser reason:** Offscreen image loading is intentionally delayed and represented with placeholders.  
**Application control:** The project can change an individual image's loading strategy, but doing so solely to remove the message is not justified.  
**Relevant condition to verify:** Whether the actual affected image is critical/above-the-fold or has JavaScript depending on its early `load` event.

## 6. Findings

### [INFORMATIONAL] Tracking Prevention Warning Cannot Be Attributed Without the Blocked URL

**ID:** CONSOLE-001

**Type:** Needs Verification

**Message:**

`Tracking Prevention blocked access to storage for <URL>.`

**Status:** Needs Verification

**Source:** Unknown external resource; project includes third-party CDN/font/map/email resources.

**Location:** Exact DevTools URL and initiator were not supplied. Candidate inclusions are in `views/layouts/main.php`, with dynamic EmailJS/map/module loading in `public/assets/js/main.js`.

**Root Cause:**

The browser reports an attempted storage access for a resource but the audit input redacts/replaces the identifying URL. Project source has third-party inclusions but no direct project-owned browser-storage call establishing causation.

**Evidence:**

The layout loads Google Fonts, jsDelivr, and unpkg resources; main JavaScript loads EmailJS dynamically and initializes external map/module functionality. Project-owned frontend source reviewed for this audit contains no identified direct `localStorage`, `sessionStorage`, IndexedDB, `document.cookie`, or iframe usage.

**Impact:**

* Functionality: Unknown until the resource is identified; many such blocks are harmless.
* Performance: Repeated console output can complicate debugging but is not itself a measured performance issue.
* Accessibility: No direct impact established.
* Privacy: The browser is applying a privacy protection; this is normally protective.
* User experience: Usually no visible effect, unless the blocked dependency requires storage for a feature.

**Recommended Fix:**

Capture the full console entry in a clean browser profile, including the complete URL, `Initiator`/stack, Network request, resource type, and whether the feature fails. Only then decide whether the corresponding inclusion should be retained, self-hosted, replaced, or loaded after user consent/action.

**Alternative:**

If the captured URL is a nonessential third-party widget, remove or replace that widget. If it is an essential library feature, accept the browser message when the feature works without storage, rather than advising users to disable Tracking Prevention.

**Verification:**

Use DevTools Console and Network with cache disabled; preserve logs; reproduce from a fresh profile. Record the full URL, `Sec-Fetch-Site`, request initiator, and storage access. Test after any targeted resource change.

**Can Be Eliminated?:** Needs Verification

---

### [INFORMATIONAL] Native Lazy-Image Intervention at the Service Fragment

**ID:** CONSOLE-002

**Type:** Browser

**Message:**

`public/#service:610 [Intervention] Images loaded lazily and replaced with placeholders. Load events are deferred. See https://go.microsoft.com/fwlink/?linkid=2048113`

**Status:** Not an Application Issue

**Source:** Native browser lazy-loading behavior triggered by project image markup using `loading="lazy"`.

**Location:** Rendered `#service` fragment; relevant static source is `views/sections/services.php` (service data hand-off), `views/layouts/main.php` (section order), and image-rendering code in `public/assets/js/main.js`. Exact image/DOM line is Needs Verification because the console message does not include it.

**Root Cause:**

The browser detected native lazy-loaded offscreen image content and deferred its loading event while displaying a placeholder. `#service` is an anchor fragment, not an invalid file or a JavaScript error location.

**Evidence:**

Project templates and JS-generated cards use native `loading="lazy"`; the service section occurs after the hero and several other sections. There is no identified third-party lazy-load library or established project image-load handler whose execution is broken by the deferral.

**Impact:**

* Functionality: No broken functionality demonstrated.
* Performance: Usually beneficial for initial bandwidth and rendering work; eager loading can worsen initial load.
* Accessibility: No direct impact demonstrated.
* Privacy: None.
* User experience: Potential issue only if the specific image is visually critical, shifts layout, or is needed before user interaction.

**Recommended Fix:**

Keep native lazy loading for confirmed below-the-fold service images. Identify the exact rendered image before changing it. If measurement shows a genuinely critical image is deferred, make only that image eager and consider `fetchpriority="high"` only when it is a primary visual/LCP candidate. Provide dimensions/responsive sources where missing to control layout shift.

**Alternative:**

Accept the intervention message as normal browser behavior when the service image is below the fold and no load-event dependency/visual defect is observed. Do not remove lazy loading globally for console cleanliness.

**Verification:**

In DevTools, select the console message and inspect the DOM/network initiator for the exact image. Throttle network, scroll to `#service`, and check image visibility, layout stability, and any dependent scripts. Measure LCP/CLS before changing a loading attribute.

**Can Be Eliminated?:** Partially

## 7. Recommended Fixes

1. **Capture first, change second:** obtain the full URL and initiator for CONSOLE-001. A resource-specific remediation is impossible without it.
2. **Audit third-party resources by necessity:** after attribution, remove/replace only nonessential resources that require blocked third-party storage. Prefer direct user action/consent before loading optional widgets.
3. **Retain below-fold lazy loading:** treat CONSOLE-002 as informational unless runtime verification ties it to an image that is critical or functionally depended on.
4. **Target individual critical images only:** use eager/high priority only with measured evidence for the actual hero/LCP visual; preserve lazy loading for service/gallery/card content below the fold.
5. **Improve future diagnosability:** retain source maps/clear resource names in development and capture browser/version/privacy-mode context with console reports.

## 8. Warnings That Cannot Be Eliminated From Application Code

* **Browser Tracking Prevention itself:** application code cannot control a visitor's privacy settings or prevent a browser from reporting storage restrictions. Once the exact resource is known, the project may control only whether it loads that resource.
* **Native lazy-loading intervention behavior:** browsers may emit this informational intervention for valid `loading="lazy"` images. The project can suppress it only by changing the loading strategy, which may be a performance regression and is not a valid default remedy.

## 9. Verification Plan

1. Reproduce each message in a clean Edge/Chrome profile and preserve the full console text, timestamp, browser version, privacy-mode setting, and complete blocked URL.
2. Use the Network panel's Initiator column/stack to map Tracking Prevention to one project inclusion or third-party dependency.
3. Temporarily block only the identified third-party request in DevTools to establish whether any visible functionality actually depends on it; do not change project source during diagnosis.
4. For the lazy-image message, inspect the exact DOM node, computed `loading`, visibility, dimensions, and network priority. Test scroll-to-service under throttling.
5. If a change is later approved, retest functionality and measure LCP/CLS/transfer before and after; confirm no new console errors or broken map/contact/gallery behavior.

## 10. Audit Limitations

* The exact `<URL>` in the Tracking Prevention message was not provided. No browser console, Network log, initiator stack, HAR, or runtime DOM was available.
* The `:610` component of the lazy message is a runtime rendered-document location; it cannot be mapped reliably to a PHP source line without the browser's inspected DOM/source view.
* This audit did not execute third-party code, inspect minified dependency internals, send requests, alter browser privacy configuration, or benchmark rendering.
* The local command launcher repeatedly failed with Windows error 1920 during this audit, so no additional filesystem search or graph query could be run. Findings rely on the project source already inspected in the ongoing read-only audit context and explicitly mark unresolved attribution as Needs Verification.

## 11. Final Assessment

Neither requested console message should be treated as a vulnerability or automatically “fixed.” CONSOLE-001 is a browser privacy notice whose source cannot be assigned without the missing URL; project code may only control the inclusion of an identified external resource. CONSOLE-002 is expected browser behavior for a valid performance optimization and should remain unless the exact image is demonstrated to be critical or to break dependent code.

No source code, configuration, database, dependency, asset, or prior report content was changed. This section was appended to the bottom of the existing report only.
