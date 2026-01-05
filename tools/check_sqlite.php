<?php

echo 'CWD: ' . getcwd() . PHP_EOL;
$path = 'writable/database/parking.db';
echo 'realpath: ' . (realpath($path) ?: 'NULL') . PHP_EOL;
try {
    $db = new SQLite3($path);
    $db->exec('PRAGMA journal_mode=WAL');
    echo "sqlite_open_ok\n";
} catch (Exception $e) {
    echo "sqlite_error: " . $e->getMessage() . PHP_EOL;
}
