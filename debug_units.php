<?php

use App\Models\Unit;
use App\Models\Building;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Checking Units in Database:\n";
echo "---------------------------\n";

$buildings = Building::with('units')->get();

foreach ($buildings as $building) {
    echo "Building: {$building->name} (ID: {$building->id})\n";
    foreach ($building->units as $unit) {
        echo "  - Unit ID: {$unit->id}, Number: '{$unit->unit_number}' (Status: {$unit->status})\n";
    }
    if ($building->units->isEmpty()) {
        echo "  - No units found.\n";
    }
    echo "\n";
}
