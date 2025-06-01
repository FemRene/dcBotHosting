<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check if the bots table exists
if (Schema::hasTable('bots')) {
    echo "The 'bots' table exists.\n";
} else {
    echo "The 'bots' table does not exist.\n";
    exit(1);
}

// Try to query the bots table
try {
    $bots = DB::table('bots')->get();
    echo "Successfully queried the 'bots' table. Found " . count($bots) . " records.\n";
} catch (Exception $e) {
    echo "Error querying the 'bots' table: " . $e->getMessage() . "\n";
    exit(1);
}

echo "All tests passed. The 'bots' table exists and can be queried.\n";
