#!/usr/bin/env php
<?php
// Simple wrapper to run PHPUnit similar to phpunit.sh
chdir(__DIR__);
$cmd = __DIR__ . '/vendor/bin/phpunit';
$testsDir = __DIR__ . '/tests';
passthru(escapeshellcmd($cmd) . ' ' . escapeshellarg($testsDir));
