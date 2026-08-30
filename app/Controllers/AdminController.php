<?php
/**
 * AdminController — Editor mode authentication + showcase cards CRUD
 */
class AdminController
{
    /**
     * Require admin session, otherwise 401
     */
    private static function requireAdmin()
    {
        if (empty($_SESSION['is_admin'])) {
            json_response(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Validate the X-CSRF-Token header against the per-session token.
     * Must be called on every state-changing (POST/DELETE) endpoint.
     */
    private static function verifyCsrf()
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (
            empty($_SESSION['csrf_token'])
            || !is_string($token)
            || !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            json_response(['error' => 'Invalid CSRF token'], 403);
        }
    }

    /**
     * Resolve the route id + existing record via $finder, or respond 404.
     *
     * @param callable $finder          e.g. 'ShowcaseProject::find'
     * @param string   $notFoundMessage Error message for the 404 response
     * @return array{0: int, 1: array}  [id, existing row]
     */
    private static function requireExisting($finder, $notFoundMessage)
    {
        $id = (int) ($_GET['id'] ?? 0);
        $existing = $finder($id);
        if (!$existing) {
            json_response(['success' => false, 'error' => $notFoundMessage], 404);
        }
        return [$id, $existing];
    }

    /**
     * Shared image-replacement flow for update endpoints: when a new image was
     * uploaded, store it and delete the previous file; otherwise keep the old path.
     *
     * @param string $existingImage Current image path from the DB row
     * @return array{0: bool, 1: string} [ok, newPathOrError]
     */
    private static function replaceImageIfUploaded($existingImage)
    {
        if (empty($_FILES['image']['name'])) {
            return [true, $existingImage];
        }
        $upload = self::handleUpload();
        if (!$upload['ok']) {
            return [false, $upload['error']];
        }
        self::deleteImageFile($existingImage);
        return [true, $upload['path']];
    }

    /**
     * POST /api/admin/login  {password}
     */
    public function login()
    {
        self::verifyCsrf();
        $data = Request::json() ?: Request::all();
        $password = isset($data['password']) ? (string) $data['password'] : '';
        $hash = config('admin_password_hash');

        if ($hash && $password !== '' && password_verify($password, $hash)) {
            // Rotate the session ID on privilege elevation (session fixation defense)
            session_regenerate_id(true);
            $_SESSION['is_admin'] = true;
            json_response(['success' => true]);
        }

        usleep(400000);
        json_response(['success' => false, 'error' => i18n::t('editor_error_wrong_password')], 401);
    }

    /**
     * POST /api/admin/logout
     */
    public function logout()
    {
        self::verifyCsrf();
        unset($_SESSION['is_admin']);
        json_response(['success' => true]);
    }

    /**
     * GET /api/admin/session
     */
    public function session()
    {
        json_response(['authenticated' => !empty($_SESSION['is_admin'])]);
    }

    /**
     * GET /api/cards — public list
     */
    public function listCards()
    {
        json_response(['cards' => ShowcaseProject::all()]);
    }

    /**
     * POST /api/admin/cards — create (multipart: title, description, image)
     */
    public function createCard()
    {
        self::requireAdmin();
        self::verifyCsrf();

        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $link = self::normalizeLink($_POST['link'] ?? '');

        $errors = self::validateFields($title, $description, $link);
        if ($errors) {
            json_response(['success' => false, 'errors' => $errors], 422);
        }

        $upload = self::handleUpload();
        if (!$upload['ok']) {
            json_response(['success' => false, 'error' => $upload['error']], 422);
        }

        $id = ShowcaseProject::create($title, $description, $upload['path'], $link);
        $card = ShowcaseProject::find($id);
        $sync = KnowledgeIndexService::syncShowcase($card);
        json_response(['success' => true, 'card' => $card, 'knowledgeSync' => $sync], 201);
    }

    /**
     * POST /api/admin/cards/{id} — update (multipart; image optional)
     */
    public function updateCard()
    {
        self::requireAdmin();
        self::verifyCsrf();

        [$id, $existing] = self::requireExisting('ShowcaseProject::find', 'Card not found');

        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $link = self::normalizeLink($_POST['link'] ?? '');

        $errors = self::validateFields($title, $description, $link);
        if ($errors) {
            json_response(['success' => false, 'errors' => $errors], 422);
        }

        [$ok, $imagePath] = self::replaceImageIfUploaded($existing['image']);
        if (!$ok) {
            json_response(['success' => false, 'error' => $imagePath], 422);
        }

        ShowcaseProject::update($id, $title, $description, $imagePath, $link);
        $card = ShowcaseProject::find($id);
        $sync = KnowledgeIndexService::syncShowcase($card);
        json_response(['success' => true, 'card' => $card, 'knowledgeSync' => $sync]);
    }

    /**
     * DELETE /api/admin/cards/{id}
     */
    public function deleteCard()
    {
        self::requireAdmin();
        self::verifyCsrf();

        [$id, $existing] = self::requireExisting('ShowcaseProject::find', 'Card not found');

        self::deleteImageFile($existing['image']);
        ShowcaseProject::delete($id);
        json_response(['success' => true, 'knowledgeSync' => KnowledgeIndexService::delete('project', $id)]);
    }

    // ──────────────────────────────────────────────
    //  CERTIFICATES CRUD
    // ──────────────────────────────────────────────

    /**
     * GET /api/admin/certificates
     */
    public function listCertificates()
    {
        self::requireAdmin();
        json_response(['certificates' => Certificate::all()]);
    }

    /**
     * POST /api/admin/certificates
     */
    public function createCertificate()
    {
        self::requireAdmin();
        self::verifyCsrf();

        $title = sanitize($_POST['title'] ?? '');
        $company = sanitize($_POST['company'] ?? '');
        $credentialId = sanitize($_POST['credential_id'] ?? '');
        $credentialLink = self::normalizeLink($_POST['credential_link'] ?? '');

        $errors = self::validateCertificateFields($title);
        if ($errors) {
            json_response(['success' => false, 'errors' => $errors], 422);
        }

        $upload = self::handleUpload();
        if (!$upload['ok']) {
            json_response(['success' => false, 'error' => $upload['error']], 422);
        }

        $id = Certificate::create($title, $company, $credentialId, $credentialLink, $upload['path']);
        $certificate = Certificate::find($id);
        $sync = KnowledgeIndexService::syncCertificate($certificate);
        json_response(['success' => true, 'certificate' => $certificate, 'knowledgeSync' => $sync], 201);
    }

    /**
     * POST /api/admin/certificates/{id}
     */
    public function updateCertificate()
    {
        self::requireAdmin();
        self::verifyCsrf();

        [$id, $existing] = self::requireExisting('Certificate::find', 'Certificate not found');

        $title = sanitize($_POST['title'] ?? '');
        $company = sanitize($_POST['company'] ?? '');
        $credentialId = sanitize($_POST['credential_id'] ?? '');
        $credentialLink = self::normalizeLink($_POST['credential_link'] ?? '');

        $errors = self::validateCertificateFields($title);
        if ($errors) {
            json_response(['success' => false, 'errors' => $errors], 422);
        }

        [$ok, $imagePath] = self::replaceImageIfUploaded($existing['image']);
        if (!$ok) {
            json_response(['success' => false, 'error' => $imagePath], 422);
        }

        Certificate::update($id, $title, $company, $credentialId, $credentialLink, $imagePath);
        $certificate = Certificate::find($id);
        $sync = KnowledgeIndexService::syncCertificate($certificate);
        json_response(['success' => true, 'certificate' => $certificate, 'knowledgeSync' => $sync]);
    }

    /**
     * DELETE /api/admin/certificates/{id}
     */
    public function deleteCertificate()
    {
        self::requireAdmin();
        self::verifyCsrf();

        [$id, $existing] = self::requireExisting('Certificate::find', 'Certificate not found');

        self::deleteImageFile($existing['image']);
        Certificate::delete($id);
        json_response(['success' => true, 'knowledgeSync' => KnowledgeIndexService::delete('certificate', $id)]);
    }

    /** POST /api/admin/certificates/{id}/pin */
    public function toggleCertificatePin()
    {
        self::requireAdmin();
        self::verifyCsrf();
        [$id] = self::requireExisting('Certificate::find', 'Certificate not found');
        $certificate = Certificate::togglePinned($id);
        json_response(['success' => true, 'certificate' => $certificate]);
    }

    /** POST /api/admin/certificates/bulk-import */
    public function bulkImportCertificates()
    {
        self::requireAdmin(); self::verifyCsrf();
        $rows = Request::json();
        if (!is_array($rows) || count($rows) > 50) json_response(['success'=>false, 'error'=>'JSON must contain at most 50 entries.'], 422);
        $results = ['imported'=>0, 'failed'=>[]];
        foreach ($rows as $index => $row) {
            try {
                if (!is_array($row)) throw new InvalidArgumentException('Entry must be an object.');
                $title = sanitize($row['title'] ?? ''); $image = self::validateRemoteImageUrl($row['image'] ?? '');
                if (mb_strlen($title) < 2) throw new InvalidArgumentException('Missing or invalid title.');
                if (Certificate::findByTitle($title)) throw new InvalidArgumentException('Duplicate title skipped.');
                $localImage = self::downloadRemoteImage($image);
                Certificate::create($title, sanitize($row['company'] ?? ''), sanitize($row['credential_id'] ?? ''), self::normalizeLink($row['credential_link'] ?? ''), $localImage);
                $results['imported']++;
            } catch (Throwable $e) { $results['failed'][] = ['row'=>(int)$index + 1, 'error'=>$e->getMessage()]; }
        }
        json_response(['success'=>true] + $results);
    }

    /** POST /api/admin/projects/bulk-import */
    public function bulkImportProjects()
    {
        self::requireAdmin(); self::verifyCsrf();
        $rows = Request::json();
        if (!is_array($rows) || count($rows) > 50) json_response(['success'=>false, 'error'=>'JSON must contain at most 50 entries.'], 422);
        $results = ['imported'=>0, 'failed'=>[]];
        foreach ($rows as $index => $row) {
            try {
                if (!is_array($row)) throw new InvalidArgumentException('Entry must be an object.');
                $title = sanitize($row['title'] ?? ''); $description = sanitize($row['description'] ?? ''); $image = self::validateRemoteImageUrl($row['image'] ?? '');
                if (mb_strlen($title) < 2) throw new InvalidArgumentException('Missing or invalid title.');
                if (mb_strlen($description) < 10) throw new InvalidArgumentException('Missing or invalid description.');
                if (ShowcaseProject::findByTitle($title)) throw new InvalidArgumentException('Duplicate title skipped.');
                $localImage = self::downloadRemoteImage($image);
                ShowcaseProject::create($title, $description, $localImage, self::normalizeLink($row['link'] ?? ''));
                $results['imported']++;
            } catch (Throwable $e) { $results['failed'][] = ['row'=>(int)$index + 1, 'error'=>$e->getMessage()]; }
        }
        json_response(['success'=>true] + $results);
    }

    private static function validateRemoteImageUrl($raw)
    {
        $url = trim((string)$raw); $parts = parse_url($url);
        if (!$parts || !in_array(strtolower($parts['scheme'] ?? ''), ['http','https'], true) || empty($parts['host']) || !empty($parts['user']) || !empty($parts['pass'])) throw new InvalidArgumentException('Image must be a public HTTP(S) URL.');
        $host = strtolower($parts['host']); $ip = gethostbyname($host);
        if ($ip === $host || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) throw new InvalidArgumentException('Image host is not publicly routable.');
        return $url;
    }

    private static function downloadRemoteImage($url)
    {
        $ssl = ['verify_peer'=>true, 'verify_peer_name'=>true];
        $ca = env('HTTP_CA_BUNDLE');
        if (!$ca) foreach (['C:/xampp/php/extras/ssl/cacert.pem', 'C:/xampp/phpMyAdmin/vendor/composer/ca-bundle/res/cacert.pem'] as $candidate) if (is_readable($candidate)) { $ca = $candidate; break; }
        if ($ca && is_readable($ca)) $ssl['cafile'] = $ca;
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 15,
                // GitHub raw URLs commonly redirect to a CDN; follow only a small bounded chain.
                'follow_location' => 1,
                'max_redirects' => 3,
                'ignore_errors' => true,
                'header' => "User-Agent: AlfizIlham-Portfolio-Importer\r\nAccept: image/*\r\n",
            ],
            'ssl' => $ssl,
        ]);
        $binary = @file_get_contents($url, false, $context);
        if ($binary === false || strlen($binary) > 5 * 1024 * 1024) throw new RuntimeException('Image download failed or exceeds 5 MB.');
        $tmp = tempnam(sys_get_temp_dir(), 'portfolio-import-'); file_put_contents($tmp, $binary);
        try {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
            if (!in_array($mime, ['image/jpeg','image/png','image/webp','image/gif'], true)) throw new InvalidArgumentException('Unsupported image type.');
            $info = @getimagesize($tmp); if (!$info || $info[0] * $info[1] > 24000000 || max($info[0], $info[1]) > 8000) throw new InvalidArgumentException('Image dimensions are too large.');
            if (!extension_loaded('gd')) throw new RuntimeException('GD extension not available.');
            $source = @imagecreatefromstring(file_get_contents($tmp)); if (!$source) throw new InvalidArgumentException('Image could not be decoded.');
            $w=imagesx($source); $h=imagesy($source); if (max($w,$h)>1600) { $ratio=1600/max($w,$h); $scaled=imagescale($source,(int)round($w*$ratio),(int)round($h*$ratio)); if ($scaled) { imagedestroy($source); $source=$scaled; } }
            $dir = PUBLIC_PATH . '/assets/uploads/showcase'; if (!is_dir($dir)) mkdir($dir, 0775, true);
            $filename = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.webp'; $target=$dir.'/'.$filename; $ok=imagewebp($source,$target,85); imagedestroy($source);
            if (!$ok) throw new RuntimeException('WebP conversion failed.'); return 'assets/uploads/showcase/'.$filename;
        } finally { @unlink($tmp); }
    }

    /**
     * Validate certificate fields
     */
    private static function validateCertificateFields($title)
    {
        $errors = [];
        if (mb_strlen(trim($title)) < 2) {
            $errors['title'] = i18n::t('form_error_title');
        }
        return $errors;
    }

    /**
     * Validate title/description/link fields
     */
    private static function validateFields($title, $description, $link = null)
    {
        $errors = [];
        if (mb_strlen(trim($title)) < 2) {
            $errors['title'] = i18n::t('form_error_title');
        }
        if (mb_strlen(trim($description)) < 10) {
            $errors['description'] = i18n::t('form_error_description');
        }
        if ($link !== null && !filter_var($link, FILTER_VALIDATE_URL)) {
            $errors['link'] = i18n::t('form_error_link');
        }
        return $errors;
    }

    /**
     * Normalize optional link: empty → null, auto-prepend https://
     */
    private static function normalizeLink($raw)
    {
        $link = trim((string) $raw);
        if ($link === '') return null;
        if (!preg_match('#^https?://#i', $link)) {
            $link = 'https://' . $link;
        }
        return sanitize($link);
    }

    /**
     * Handle image upload → convert to WebP via GD
     */
    private static function handleUpload()
    {
        if (empty($_FILES['image']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
            return ['ok' => false, 'error' => i18n::t('upload_error_required')];
        }

        $file = $_FILES['image'];

        if ($file['size'] > 5 * 1024 * 1024) {
            return ['ok' => false, 'error' => i18n::t('upload_error_size')];
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!in_array($mime, $allowed, true)) {
            return ['ok' => false, 'error' => i18n::t('upload_error_type')];
        }

        // Reject oversized pixel dimensions BEFORE decoding (decompression-bomb guard)
        $info = @getimagesize($file['tmp_name']);
        if ($info === false) {
            return ['ok' => false, 'error' => i18n::t('upload_error_type')];
        }
        [$infoW, $infoH] = $info;
        if ($infoW * $infoH > 24000000 || max($infoW, $infoH) > 8000) {
            return ['ok' => false, 'error' => i18n::t('upload_error_dimensions')];
        }

        if (!extension_loaded('gd')) {
            return ['ok' => false, 'error' => 'GD extension not available'];
        }

        $source = @imagecreatefromstring(file_get_contents($file['tmp_name']));
        if (!$source) {
            return ['ok' => false, 'error' => i18n::t('upload_error_type')];
        }

        // Cap max dimension at 1600px
        $w = imagesx($source);
        $h = imagesy($source);
        $maxDim = 1600;
        if (max($w, $h) > $maxDim) {
            $ratio = $maxDim / max($w, $h);
            $scaled = imagescale($source, (int) round($w * $ratio), (int) round($h * $ratio));
            if ($scaled) {
                imagedestroy($source);
                $source = $scaled;
            }
        }

        $dir = PUBLIC_PATH . '/assets/uploads/showcase';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $filename = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.webp';
        $target = $dir . '/' . $filename;

        $ok = imagewebp($source, $target, 85);
        imagedestroy($source);

        if (!$ok) {
            return ['ok' => false, 'error' => 'WebP conversion failed'];
        }

        return ['ok' => true, 'path' => 'assets/uploads/showcase/' . $filename];
    }

    /**
     * Delete an image file inside the uploads dir (path-safe)
     */
    private static function deleteImageFile($relativePath)
    {
        $base = realpath(PUBLIC_PATH);
        $full = realpath(PUBLIC_PATH . '/' . ltrim($relativePath, '/'));
        if ($full && strpos($full, $base . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads') === 0) {
            @unlink($full);
        }
    }
}
