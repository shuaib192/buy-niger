<?php
/**
 * BuyNiger AI - Database Migration Runner
 * Visit this file in your browser to run migrations.
 */

use Illuminate\Support\Facades\Artisan;

// Define the path to the Laravel application
$appPath = __DIR__ . '/..';

// Load Laravel's autoload and bootstrap
require $appPath . '/vendor/autoload.php';
$app = require_once $appPath . '/bootstrap/app.php';

// Create a kernel to run the command
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h1>BuyNiger Migration Runner</h1>";
echo "<pre>";

try {
    // Run the migration command
    $status = Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    echo "\n\n<b>Success!</b> Database updated.";
} catch (\Exception $e) {
    echo "<b>Error:</b> " . $e->getMessage();
}

echo "</pre>";
echo "<hr><p style='color:red;'><b>SECURITY WARNING:</b> Please delete this file (public/run-migrate.php) after use.</p>";
