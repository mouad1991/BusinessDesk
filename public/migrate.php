<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
Artisan::call('migrate', ['--force' => true]);
echo '<pre>' . Artisan::output() . '</pre>';
echo 'DONE';
