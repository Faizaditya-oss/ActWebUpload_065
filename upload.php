<?php
define('UPLOAD_DIR', __DIR__ . '/uploads/');

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    $count = count($_FILES['files']['name']);

    for ($i = 0; $i < $count; $i++) {
        $origName = basename($_FILES['files']['name'][$i]);
        $tmpPath  = $_FILES['files']['tmp_name'][$i];
        $error    = $_FILES['files']['error'][$i];

        if ($error !== UPLOAD_ERR_OK || empty($origName)) continue;

        // Sanitasi nama file
        $safeName = preg_replace('/[^a-zA-Z0-9_.\-]/', '_', $origName);

        // Hindari nama duplikat
        if (file_exists(UPLOAD_DIR . $safeName)) {
            $info     = pathinfo($safeName);
            $safeName = $info['filename'] . '_' . time() . '.' . ($info['extension'] ?? '');
        }

        if (!move_uploaded_file($tmpPath, UPLOAD_DIR . $safeName)) {
            $errors[] = $origName;
        }
    }
}

// Redirect ke gallery.php setelah proses selesai
$query = !empty($errors) ? '?error=1' : '?success=1';
header('Location: gallery.php' . $query);
exit;
