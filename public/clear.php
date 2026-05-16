<?php
// Emergency Cache Clear Script
// Visit this via: https://buyniger.com/clear.php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h1>Clearing All Caches...</h1>";

try {
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "Routes cleared...<br>";
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "Config cleared...<br>";
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "General cache cleared...<br>";
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "Views cleared...<br>";
    
    if (function_exists('opcache_reset')) {
        opcache_reset();
        echo "OPcache reset successful!<br>";
    } else {
        echo "OPcache reset function not found.<br>";
    }
    
    echo "<h2>DONE! Now visit the site.</h2>";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
