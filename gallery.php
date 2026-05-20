<?php
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/');

$IMAGE_EXTS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// ─── Aksi Hapus ───────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $target = basename($_GET['delete']);
    $path   = UPLOAD_DIR . $target;
    if (file_exists($path) && is_file($path)) {
        unlink($path);
    }
    header('Location: gallery.php');
    exit;
}

// ─── Baca Semua File ──────────────────────────────────────────
$allFiles = [];
foreach (glob(UPLOAD_DIR . '*') as $filePath) {
    if (is_file($filePath)) {
        $allFiles[] = [
            'name'  => basename($filePath),
            'size'  => filesize($filePath),
            'mtime' => filemtime($filePath),
            'ext'   => strtolower(pathinfo($filePath, PATHINFO_EXTENSION)),
        ];
    }
}

usort($allFiles, fn($a, $b) => $b['mtime'] - $a['mtime']);

function formatSize(int $bytes): string {
    if ($bytes < 1024)    return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 1) . ' MB';
}

function fileIcon(string $ext): string {
    $map = [
        'pdf'  => '📄',
        'zip'  => '🗜', 'rar' => '🗜', '7z' => '🗜',
        'doc'  => '📝', 'docx' => '📝',
        'xls'  => '📊', 'xlsx' => '📊',
        'ppt'  => '📋', 'pptx' => '📋',
        'txt'  => '📃',
        'mp4'  => '🎬', 'mov' => '🎬', 'avi' => '🎬',
        'mp3'  => '🎵', 'wav' => '🎵',
        'php'  => '⚙️',  'js'  => '⚙️', 'py' => '⚙️',
    ];
    return $map[$ext] ?? '📁';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>File Tersimpan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">

  <!-- Navbar -->
  <nav class="bg-white border-b border-gray-200">
    <div class="max-w-2xl mx-auto px-6 h-14 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
          </svg>
        </div>
        <span class="text-sm font-semibold text-gray-800 tracking-tight">FILE UPLOADER</span>
      </div>
      <a href="index.html"
         class="inline-flex items-center gap-1.5 text-sm text-blue-600 font-medium hover:text-blue-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32 5.75 5.75 0 011.045 11.095"/>
        </svg>
        Upload File
      </a>
    </div>
  </nav>

  <main class="max-w-2xl mx-auto px-6 py-10">

    <!-- Page header -->
    <div class="flex items-start justify-between mb-7">
      <div>
        <h1 class="text-xl font-semibold text-gray-900">File Tersimpan</h1>
        <p class="text-sm text-gray-500 mt-1">Semua file yang telah diupload ke server.</p>
      </div>
      <span class="text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1.5 rounded-full mt-1">
        <?= count($allFiles) ?> file
      </span>
    </div>

    <!-- Notifikasi -->
    <?php if (isset($_GET['success'])): ?>
      <div class="flex items-center gap-2.5 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-5">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        File berhasil diupload.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
      <div class="flex items-center gap-2.5 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
        </svg>
        Beberapa file gagal diupload.
      </div>
    <?php endif; ?>

    <!-- File List -->
    <?php if (empty($allFiles)): ?>
      <div class="bg-white border border-gray-200 rounded-2xl py-16 text-center">
        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776"/>
          </svg>
        </div>
        <p class="text-sm font-medium text-gray-600">Folder masih kosong</p>
        <p class="text-xs text-gray-400 mt-1">Belum ada file yang diupload</p>
        <a href="index.html"
           class="inline-flex items-center gap-1.5 mt-5 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
          Upload file sekarang
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
          </svg>
        </a>
      </div>
    <?php else: ?>
      <div class="space-y-2">
        <?php foreach ($allFiles as $file):
          $isImage = in_array($file['ext'], $IMAGE_EXTS);
          $fileUrl = UPLOAD_URL . rawurlencode($file['name']);
        ?>
          <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-3 py-2.5 hover:border-gray-300 transition-colors group">

            <!-- Thumbnail -->
            <div class="w-11 h-11 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100 flex items-center justify-center">
              <?php if ($isImage): ?>
                <img src="<?= htmlspecialchars($fileUrl) ?>"
                     alt="<?= htmlspecialchars($file['name']) ?>"
                     loading="lazy"
                     class="w-full h-full object-cover">
              <?php else: ?>
                <span class="text-xl leading-none"><?= fileIcon($file['ext']) ?></span>
              <?php endif; ?>
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <a href="<?= htmlspecialchars($fileUrl) ?>" target="_blank"
                 class="text-sm font-medium text-gray-800 hover:text-blue-600 transition-colors truncate block"
                 title="<?= htmlspecialchars($file['name']) ?>">
                <?= htmlspecialchars($file['name']) ?>
              </a>
              <p class="text-xs text-gray-400 mt-0.5">
                <?= formatSize($file['size']) ?>
                <span class="mx-1">&bull;</span>
                <?= date('d M Y, H:i', $file['mtime']) ?>
              </p>
            </div>

            <!-- Badge ekstensi -->
            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md flex-shrink-0 uppercase">
              <?= htmlspecialchars($file['ext'] ?: 'file') ?>
            </span>

            <!-- Unduh -->
            <a href="<?= htmlspecialchars($fileUrl) ?>"
               download="<?= htmlspecialchars($file['name']) ?>"
               class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:bg-blue-50 hover:text-blue-400 transition-colors"
               title="Unduh file">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
              </svg>
            </a>

            <!-- Hapus -->
            <a href="gallery.php?delete=<?= urlencode($file['name']) ?>"
               onclick="return confirm('Hapus file <?= htmlspecialchars(addslashes($file['name'])) ?>?')"
               class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:bg-red-50 hover:text-red-400 transition-colors"
               title="Hapus file">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
              </svg>
            </a>

          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </main>

</body>
</html>
