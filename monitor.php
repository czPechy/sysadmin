<?php
$memory = memory();
$disks = disk();
$sensors = sensors();

echo 'TESTING MODE:' . PHP_EOL;
echo PHP_EOL;

echo 'BOOT:' . PHP_EOL;
echo ' - ' . (new DateTime())->setTimestamp($memory['boot'])->format('Y-m-d H:i:s') . PHP_EOL;
echo ' - ' . bootTime(time() - $memory['boot']) . PHP_EOL;
echo PHP_EOL;

echo 'CPU:' . PHP_EOL;
echo ' - used:  ' . number_format(cpu(), 1) . '%' . PHP_EOL;
echo PHP_EOL;

echo 'MEMORY:' . PHP_EOL;
echo ' - total: ' . formatBytes($memory['memory']['total']) . PHP_EOL;
echo ' - used:  ' . number_format(($memory['memory']['used']/$memory['memory']['total']) * 100, 1) . '% (' . formatBytes($memory['memory']['used']) . ')' . PHP_EOL;
echo PHP_EOL;

echo 'SWAP:' . PHP_EOL;
echo ' - total: ' . formatBytes($memory['swap']['total']) . PHP_EOL;
echo ' - used:  ' . number_format(($memory['swap']['used']/$memory['swap']['total']) * 100, 1) . '% (' . formatBytes($memory['swap']['used']) . ')' . PHP_EOL;
echo PHP_EOL;

echo 'DISK:' . PHP_EOL;
foreach($disks as $disk) {
    echo ' - ' . $disk['disk'] . ':' . PHP_EOL;
    echo '   - total: ' . formatBytes($disk['total']) . PHP_EOL;
    echo '   - used:  ' . number_format($disk['used_percentage'], 0) . '% (' . formatBytes($disk['used']) . ')' . PHP_EOL;
}
echo PHP_EOL;

echo 'TEMP:' . PHP_EOL;
foreach ( $sensors as $key => $cpu ) {
    echo ' - CPU' . $key . ':' . PHP_EOL;
    foreach($cpu as $core) {
        echo '   - core' . $core['core'] . ': ' . $core['temp'] . '°C' . PHP_EOL;
    }
}
echo PHP_EOL;

function bootTime($input) {
    $unit = 's';
    $sec = null;
    $mins = null;
    $hrs = null;

    if($input > 60) {
        $sec = str_pad($input % 60, 2, ' ', STR_PAD_LEFT);
        $input = round($input/60);
        $unit = 'm';
    }
    if($input > 60) {
        $mins = str_pad($input % 60, 2, ' ', STR_PAD_LEFT);
        $input = round($input/60);
        $unit = 'h';
    }
    if($input > 24) {
        $hrs = str_pad($input % 24, 2, ' ', STR_PAD_LEFT);
        $input = round($input/24);
        $unit = 'd';
    }

    return $input . $unit . ($hrs !== null ? ', ' . $hrs . 'h' : '') . ($mins !== null ? ', ' . $mins . 'm' : '') . ($sec !== null ? ', ' . $sec . 's' : '');
}

function formatBytes($input) {
    $unit = 'KB';
    if($input >= 1024) {
        $input /= 1024;
        $unit = 'MB';
    }
    if($input >= 1024) {
        $input /= 1024;
        $unit = 'GB';
    }
    if($input >= 1024) {
        $input /= 1024;
        $unit = 'TB';
    }
    return number_format($input, 2) . ' ' . $unit;
}

function memory() {
	$result = shell_exec('vmstat -s');
	preg_match_all('~((?<memory_total>\d+)\sK\stotal\smemory)|((?<memory_used>\d+)\sK\sused\smemory)|((?<swap_total>\d+)\sK\stotal\sswap)|((?<swap_used>\d+)\sK\sused\sswap)|((?<boot_time>\d+)\sboot\stime)~', $result, $matches);
	return [
	    'memory' => [
	        'total' => $matches['memory_total'][0],
	        'used' => $matches['memory_used'][1]
        ],
        'swap' => [
            'total' => $matches['swap_total'][2],
            'used' => $matches['swap_used'][3]
        ],
        'boot' => $matches['boot_time'][4]
    ];
}

function disk() {
	$result = shell_exec('df');
	preg_match_all('~(?<disk>\/[a-zA-Z0-9\/]+)\s+(?<total>\d+)\s+(?<used>\d+)\s+(?<free>\d+)\s+(?<used_percentage>\d+)%\s(?<mount>.+)~', $result, $matches);
	$disks = [];

	foreach($matches['disk'] as $key => $disk) {
	    $disks[$key] = [
	        'disk' => $disk,
            'total' => $matches['total'][$key],
            'free' => $matches['free'][$key],
            'used' => $matches['used'][$key],
            'used_percentage' => $matches['used_percentage'][$key],
            'mount' => $matches['mount'][$key],
        ];
    }

	return $disks;
}

function sensors() {
    $result = shell_exec('sensors');
    preg_match_all('~Core\s(?<core>\d+):\s+\+(?<temp>[\d\.]+)\°C\s+\(high\s\=\s\+(?<high>[\d\.]+)\°C\,\scrit\s\=\s\+(?<crit>[\d\.]+)\°C\)~', $result, $matches);
    $cpus = [];

    foreach($matches['core'] as $key => $core) {
        $cpus[$key] = [
            'core' => (int)$core,
            'temp' => (float)$matches['temp'][$key],
            'high' => (float)$matches['high'][$key],
            'crit' => (float)$matches['crit'][$key],
        ];
    }

    $lastCore = null;
    $group = 0;
    $coreNum = 0;
    $cpuGroup = [];

    foreach($cpus as $cpu) {
        if($lastCore !== null && $cpu['core'] < $lastCore) {
            $group++;
            $coreNum = 0;
        }
        $lastCore = $cpu['core'];
        if(!isset($cpuGroup[$group])) {
            $cpuGroup[$group] = [];
        }
        $cpu['core'] = $coreNum;
        $cpuGroup[$group][] = $cpu;

        $coreNum++;
    }

	return $cpuGroup;
}

function cpu() {
	$cpu = shell_exec('top -b -n 1 | grep "Cpu(s)\:" | awk \'{print $2}\'');
	$cpu = str_replace(',', '.', $cpu);
	return (float) $cpu;
}