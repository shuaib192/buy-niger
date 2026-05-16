<?php
/**
 * BuyNiger - Emergency 500 Fix
 * Clears ALL caches including OPcache and route cache
 */

$appPath = __DIR__ . '/..';
$output = [];

// 1. Reset OPcache completely
if (function_exists('opcache_reset')) {
    opcache_reset();
    $output[] = "✅ OPcache reset";
} else {
    $output[] = "⚠️ OPcache not available";
}

// 2. Delete cached route file directly (the main suspect)
$routeCache = $appPath . '/bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    unlink($routeCache);
    $output[] = "✅ Deleted route cache: routes-v7.php";
} else {
    $output[] = "ℹ️ No route cache file found";
}

// Also try other route cache names
foreach (glob($appPath . '/bootstrap/cache/routes*.php') as $f) {
    unlink($f);
    $output[] = "✅ Deleted: " . basename($f);
}

// 3. Delete config cache
$configCache = $appPath . '/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    unlink($configCache);
    $output[] = "✅ Deleted config cache";
}

// 4. Delete compiled services
$servicesCache = $appPath . '/bootstrap/cache/services.php';
if (file_exists($servicesCache)) {
    unlink($servicesCache);
    $output[] = "✅ Deleted services cache";
}

// 5. Clear compiled views
$viewsPath = $appPath . '/storage/framework/views';
if (is_dir($viewsPath)) {
    $deleted = 0;
    foreach (glob($viewsPath . '/*.php') as $f) {
        unlink($f);
        $deleted++;
    }
    $output[] = "✅ Deleted $deleted compiled views";
}

// 6. Show what's in bootstrap/cache now
$output[] = "";
$output[] = "=== bootstrap/cache contents ===";
foreach (glob($appPath . '/bootstrap/cache/*') as $f) {
    $output[] = "  " . basename($f);
}

// 7. Show ShopController file modification time on server
$shopController = $appPath . '/app/Http/Controllers/ShopController.php';
if (file_exists($shopController)) {
    $mtime = filemtime($shopController);
    $size = filesize($shopController);
    $output[] = "";
    $output[] = "=== ShopController on SERVER ===";
    $output[] = "Modified: " . date('Y-m-d H:i:s', $mtime);
    $output[] = "Size: $size bytes";
    
    // Check if it has the broken Cache::flush() code
    $content = file_get_contents($shopController);
    if (strpos($content, 'Cache::flush()') !== false) {
        $output[] = "❌ FOUND BROKEN: Cache::flush() IS in ShopController - OLD VERSION IS LIVE";
    } else {
        $output[] = "✅ Cache::flush() NOT found - new version is live";
    }
    
    // Also check if it has emergency cache clear
    if (strpos($content, 'EMERGENCY CACHE CLEAR') !== false) {
        $output[] = "❌ EMERGENCY CACHE CLEAR block is still there!";
    } else {
        $output[] = "✅ EMERGENCY CACHE CLEAR block removed";
    }
}

echo "<h2>BuyNiger Emergency Fix</h2>";
echo "<pre>" . implode("\n", $output) . "</pre>";
echo "<hr><p style='color:red'><b>DELETE this file after fixing!</b></p>";
