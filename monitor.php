<?php
ini_set('display_errors', 1);
require_once 'Monitor/autoload.php';

$monitor = new \ProfiCloS\Monitor\Monitor();
var_dump($monitor->getJSON());


//$basic = host();
//$memory = memory();
//$disks = disk();
//$sensors = sensors();
//$iostat = iostat();
//$cpu = cpu();
//
//$isTesting = \in_array('-t', $argv, true);
//
//if($isTesting) {
//    echo 'TESTING MODE:' . PHP_EOL;
//    echo PHP_EOL;
//
//    echo 'SYSTEM:' . PHP_EOL;
//    echo ' - host:   ' . $basic['Static hostname'] . PHP_EOL;
//    echo ' - os:     ' . $basic['Operating System'] . PHP_EOL;
//    echo ' - kernel: ' . $basic['Kernel'] . PHP_EOL;
//    echo ' - id:     ' . $basic['Machine ID'] . PHP_EOL;
//    echo ' - boot:   ' . (new DateTime())->setTimestamp($memory['boot'])->format('Y-m-d H:i:s') . PHP_EOL;
//    echo ' - uptime: ' . bootTime(time() - $memory['boot']) . PHP_EOL;
//
//    echo PHP_EOL;
//
//    echo 'CPU:' . PHP_EOL;
//    echo ' - model:  ' . $cpu['desc']['Patic'] . 'x ' . $cpu['desc']['Model name'] . PHP_EOL;
//    echo ' - cores:  ' . $cpu['cores'] . ' (' . ($cpu['desc']['Patic'] . 'x ' . $cpu['desc']['Jader na patici']) . 'c/' . $cpu['cores']/$cpu['desc']['Patic'] . 't)' . PHP_EOL;
//    echo ' - temp:   ~' . number_format(avgTemp($sensors),0) . '°C' . PHP_EOL;
//    echo ' - used:   ' . number_format($cpu['usage'], 1) . '%' . PHP_EOL;
//    echo ' - avg:    ' . PHP_EOL;
//    echo '   - system: ' . number_format($iostat['cpu']['system'], 2) . '%' . PHP_EOL;
//    echo '   - user:   ' . number_format($iostat['cpu']['user'], 2) . '%' . PHP_EOL;
//    echo '   - idle:   ' . number_format($iostat['cpu']['idle'], 2) . '%' . PHP_EOL;
//    echo PHP_EOL;
//
//    echo 'MEMORY:' . PHP_EOL;
//    echo ' - total: ' . formatBytes($memory['memory']['total']) . PHP_EOL;
//    echo ' - used:  ' . number_format(($memory['memory']['used']/$memory['memory']['total']) * 100, 1) . '% (' . formatBytes($memory['memory']['used']) . ')' . PHP_EOL;
//    echo PHP_EOL;
//
//    echo 'SWAP:' . PHP_EOL;
//    echo ' - total: ' . formatBytes($memory['swap']['total']) . PHP_EOL;
//    echo ' - used:  ' . number_format(($memory['swap']['used']/$memory['swap']['total']) * 100, 1) . '% (' . formatBytes($memory['swap']['used']) . ')' . PHP_EOL;
//    echo PHP_EOL;
//
//    echo 'DISK:' . PHP_EOL;
//    foreach($iostat['disk'] as $disk) {
//        echo ' - ' . $disk['disk'] . PHP_EOL;
//        echo '   - stats:' . PHP_EOL;
//        echo '     - tps: ' . $disk['tps'] . PHP_EOL;
//        echo '     - r/w: ' . $disk['read_s'] . '/' . $disk['write_s'] . PHP_EOL;
//    }
//    foreach($disks as $disk) {
//        echo ' - ' . $disk['disk'] . ':' . PHP_EOL;
//        echo '   - total: ' . formatBytes($disk['total']) . PHP_EOL;
//        echo '   - used:  ' . number_format($disk['used_percentage'], 0) . '% (' . formatBytes($disk['used']) . ')' . PHP_EOL;
//    }
//    echo PHP_EOL;
//
//    echo 'TEMP:' . PHP_EOL;
//    foreach ( $sensors as $key => $cpu ) {
//        echo ' - CPU' . $key . ':' . PHP_EOL;
//        foreach($cpu as $core) {
//            echo '   - core' . $core['core'] . ': ' . $core['temp'] . '°C' . PHP_EOL;
//        }
//    }
//    echo PHP_EOL;
//} else {
//    $data = [
//        'basic' => $basic,
//        'memory' => $memory,
//        'disks' => $disks,
//        'sensors' => $sensors,
//        'iostat' => $iostat,
//        'cpu' => $cpu,
//    ];
//    echo json_encode($data);
//}
//
//function bootTime($input) {
//    $unit = 's';
//    $sec = null;
//    $mins = null;
//    $hrs = null;
//
//    if($input > 60) {
//        $sec = str_pad($input % 60, 2, '0', STR_PAD_LEFT);
//        $input = round($input/60);
//        $unit = 'm';
//    }
//    if($input > 60) {
//        $mins = str_pad($input % 60, 2, '0', STR_PAD_LEFT);
//        $input = round($input/60);
//        $unit = 'h';
//    }
//    if($input > 24) {
//        $hrs = str_pad($input % 24, 2, '0', STR_PAD_LEFT);
//        $input = round($input/24);
//        $unit = 'd';
//    }
//
//    return $input . $unit . ($hrs !== null ? ', ' . $hrs . 'h' : '') . ($mins !== null ? ', ' . $mins . 'm' : '') . ($sec !== null ? ', ' . $sec . 's' : '');
//}
//
//function formatBytes($input) {
//    $unit = 'KB';
//    if($input >= 1024) {
//        $input /= 1024;
//        $unit = 'MB';
//    }
//    if($input >= 1024) {
//        $input /= 1024;
//        $unit = 'GB';
//    }
//    if($input >= 1024) {
//        $input /= 1024;
//        $unit = 'TB';
//    }
//    return number_format($input, 2) . ' ' . $unit;
//}
//
//function avgTemp($sensors) {
//    $tmpTemp = 0;
//    $tmpItems = 0;
//    foreach($sensors as $group) {
//        foreach($group as $core) {
//            $tmpTemp += $core['temp'];
//            $tmpItems++;
//        }
//    }
//    return $tmpTemp/$tmpItems;
//}
//
//function toFloat($input) {
//    return (float) str_replace(',', '.', $input);
//}