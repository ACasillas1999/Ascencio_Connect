<?php
$cacheDir = __DIR__ . '/bootstrap/cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}
chmod($cacheDir, 0777);

$servicesFile = $cacheDir . '/services.php';
$packagesFile = $cacheDir . '/packages.php';

if (file_exists($servicesFile)) unlink($servicesFile);
if (file_exists($packagesFile)) unlink($packagesFile);

echo "Directorio bootstrap/cache asegurado.\n";