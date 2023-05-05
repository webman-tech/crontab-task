<?php

$defined = [
    // 'name' => TaskClass
];

$processes = [];
foreach ($defined as $name => $process) {
    $processes["cron_task_{$name}"] = [
        'handler' => $process,
    ];
}

return $processes;
