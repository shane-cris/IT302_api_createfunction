<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/helpers.php';

require_staff();

if (!isset($_POST['exportexcel'])) {
    redirect('roombook.php');
}

$records = fetch_all('SELECT * FROM roombook ORDER BY id DESC');

$filename = 'bluebird_roombook_data_' . date('Ymd') . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$showHeader = true;
foreach ($records as $record) {
    if ($showHeader) {
        echo implode("\t", array_keys($record)) . "\n";
        $showHeader = false;
    }
    echo implode("\t", array_values($record)) . "\n";
}

exit;