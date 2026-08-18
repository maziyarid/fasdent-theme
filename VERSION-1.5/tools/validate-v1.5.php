<?php
declare(strict_types=1);

/**
 * Static Version 1.5 validator. Run from the repository root:
 * php VERSION-1.5/tools/validate-v1.5.php
 *
 * This never claims production readiness. It checks source hygiene only.
 */

$root = dirname(__DIR__, 2);
$theme = $root . '/VERSION-1.5/Theme';
$plugin = $root . '/VERSION-1.5/Plugin';
$errors = [];

foreach ([$theme . '/style.css', $theme . '/functions.php', $theme . '/front-page.php', $theme . '/header.php', $plugin . '/alipasandi-service-content.php'] as $file) {
    if (!is_file($file)) {
        $errors[] = 'Missing required file: ' . $file;
    }
}

foreach ([$theme, $plugin, $root . '/VERSION-1.5/tools'] as $sourceRoot) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceRoot, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
            continue;
        }
        $path = $file->getPathname();
        $contents = file_get_contents($path);
        if ($contents === false) {
            $errors[] = 'Unreadable PHP file: ' . $path;
        } elseif (str_starts_with($contents, "\xEF\xBB\xBF")) {
            $errors[] = 'UTF-8 BOM: ' . $path;
        }
    }
}

$requiredDirs = [$theme . '/assets/css', $theme . '/assets/js', $theme . '/assets/images', $theme . '/assets/fonts'];
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        $errors[] = 'Missing asset directory: ' . $dir;
    }
}

if ($errors !== []) {
    fwrite(STDERR, implode(PHP_EOL, $errors) . PHP_EOL);
    exit(1);
}

echo "PASS: Version 1.5 source structure and BOM scan completed.\n";
echo "NOTE: PHP runtime, WordPress activation, browser, server, backup, and production checks remain environment-dependent.\n";
