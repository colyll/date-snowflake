<?php
/**
 * Copyright (c) 2021.
 * Project: DateSnowflake
 * File:       test.php
 * Date:     2021/03/13 17:25:40
 * Author:  ColyLL
 * QQ :      857859975
 */

require('../src/DateSnowflake.php');
use Colyll\DateSnowflake;
$dateSnowflake = new DateSnowflake(1);
$timestamp = microtime(true);
for ($i = 0; $i < 100000; $i++) {
    // 2021031336140492049070
    $id = $dateSnowflake->id() . "\n";
}

echo "100,000 times usd " . (microtime(true) - $timestamp) . 'seconds';