<?php

function logSystemAlert($message) {
    $file = __DIR__ . '/system_alerts.log';
    $time = date('Y-m-d H:i:s');
    $entry = "[$time] $message" . PHP_EOL;
    file_put_contents($file, $entry, FILE_APPEND);
}


function getRecentAlerts($limit = 10) {
    $file = __DIR__ . '/system_alerts.log';
    if (!file_exists($file)) return [];
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_slice(array_reverse($lines), 0, $limit);
}

?>
