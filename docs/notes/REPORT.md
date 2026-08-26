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
