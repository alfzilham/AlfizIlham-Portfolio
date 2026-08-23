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
     * POST /api/admin/login  {password}
     */
    public function login()
    {
        $data = Request::json() ?: Request::all();
        $password = isset($data['password']) ? (string) $data['password'] : '';
        $hash = config('admin_password_hash');

        if ($hash && $password !== '' && password_verify($password, $hash)) {
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
        json_response(['success' => true, 'card' => ShowcaseProject::find($id)], 201);
    }

    /**
     * POST /api/admin/cards/{id} — update (multipart; image optional)
     */
    public function updateCard()
    {
        self::requireAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        $existing = ShowcaseProject::find($id);
        if (!$existing) {
            json_response(['success' => false, 'error' => 'Card not found'], 404);
        }

        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $link = self::normalizeLink($_POST['link'] ?? '');

        $errors = self::validateFields($title, $description, $link);
        if ($errors) {
            json_response(['success' => false, 'errors' => $errors], 422);
        }

        $imagePath = $existing['image'];
        if (!empty($_FILES['image']['name'])) {
            $upload = self::handleUpload();
            if (!$upload['ok']) {
                json_response(['success' => false, 'error' => $upload['error']], 422);
            }
            self::deleteImageFile($existing['image']);
            $imagePath = $upload['path'];
        }

        ShowcaseProject::update($id, $title, $description, $imagePath, $link);
        json_response(['success' => true, 'card' => ShowcaseProject::find($id)]);
    }

    /**
     * DELETE /api/admin/cards/{id}
     */
    public function deleteCard()
    {
        self::requireAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        $existing = ShowcaseProject::find($id);
        if (!$existing) {
            json_response(['success' => false, 'error' => 'Card not found'], 404);
        }

        self::deleteImageFile($existing['image']);
        ShowcaseProject::delete($id);
        json_response(['success' => true]);
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
