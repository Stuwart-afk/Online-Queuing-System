<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
var_dump(\Illuminate\Support\Facades\Schema::getColumnListing('queue_tickets'));
