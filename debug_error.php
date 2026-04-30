<?php
// Visit: buyniger.com/debug_error.php
// DELETE AFTER USE!
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Reading last 50KB of Laravel log</h2><hr>";

$basePath = __DIR__;
$corePath = file_exists($basePath . '/v-core') ? $basePath . '/v-core' : $basePath;
$logFile = $corePath . '/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "<p style='color:red;'>Log file not found</p>";
    exit;
}

$logSize = filesize($logFile);

// Read last 50KB
$readSize = min(50000, $logSize);
$fp = fopen($logFile, 'r');
fseek($fp, max(0, $logSize - $readSize));
$content = fread($fp, $readSize);
fclose($fp);

// Find all lines with actual error messages (not just stack traces)
$pattern = '/\[\d{4}-\d{2}-\d{2}[^\]]*\]\s+\w+\.\w+:\s+(.+)/';
preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

if (!empty($matches)) {
    $lastErrors = array_slice($matches, -5);
    echo "<h3>Last " . count($lastErrors) . " log entries:</h3>";
    foreach (array_reverse($lastErrors) as $i => $m) {
        $line = $m[0];
        // Extract just the message part (first 500 chars)
        $msg = substr($line, 0, 500);
        echo "<div style='margin-bottom:15px;'>";
        echo "<pre style='background:#fef2f2;padding:12px;border-radius:8px;font-size:12px;word-wrap:break-word;white-space:pre-wrap;'>";
        echo htmlspecialchars($msg);
        echo "</pre></div>";
    }
} else {
    echo "<p>No timestamped entries found. Showing last 5000 chars:</p>";
    echo "<pre style='background:#f8fafc;padding:15px;border-radius:10px;max-height:500px;overflow:auto;font-size:11px;white-space:pre-wrap;'>";
    echo htmlspecialchars(substr($content, -5000));
    echo "</pre>";
}

// Also check compiled view cache for errors
echo "<hr><h3>Checking compiled views...</h3>";
$viewCachePath = $corePath . '/storage/framework/views/';
if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '*.php');
    echo "<p>" . count($files) . " compiled views found</p>";
    echo "<p>Try clearing them? <a href='?clear_views=1'>Click here to clear compiled views</a></p>";
    
    if (isset($_GET['clear_views'])) {
        $deleted = 0;
        foreach ($files as $f) {
            if (unlink($f)) $deleted++;
        }
        echo "<p style='color:green;'>Deleted {$deleted} compiled view files. Try the dashboard now!</p>";
    }
}
