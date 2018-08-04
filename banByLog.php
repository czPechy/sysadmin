<?php
$oldIPban = @file_get_contents(__DIR__ . '/.logIPban.json');
if($oldIPban) {
	$oldIPban = json_decode($oldIPban, true);
} else {
	$oldIPban = [];
}
$oldIPwarn = @file_get_contents(__DIR__ . '/.logIPwarn.json');
if($oldIPwarn) {
	$oldIPwarn = json_decode($oldIPwarn, true);
} else {
	$oldIPwarn = [];	
}

$file="/var/log/exim/main.log";
$linecount = 0;
$ips = [];
$handle = fopen($file, "r");
while(!feof($handle)){
	$line = fgets($handle, 4096);
	if(preg_match('~^([\d\-]+\s[\d\:]+)\sdovecot.+\[([0-9\.]+)\]\:\s535\sIncorrect\sauthentication~', $line, $matches)){
		$date = $matches[1];
		$ip = $matches[2];
		if(!isset($ips[$ip])) {
			$ips[$ip] = [ 'cnt' => 0 ];
		}
		$ips[$ip]['cnt']++;
	}
	$linecount = $linecount + substr_count($line, PHP_EOL);
}
fclose($handle);

$warnIPs = $oldIPwarn;
$banIPs = $oldIPban;
$processBan = [];

foreach($ips as $ip => $stats) {
	if(isset($banIPs[$ip])) {
		continue;
	}
	if(isset($warnIPs[$ip])) {
		continue;
	}
	$ipInfo = isset($warnIPs[$ip]) ? null : @file_get_contents('http://ip-api.com/json/' . $ip);
	if($ipInfo) {
		$ipInfo = @json_decode($ipInfo);
		$stats['countryCode'] = $ipInfo->countryCode;
	} else {
		if($ipInfo === null) {
			$stats['countryCode'] = $warnIPs[$ip]['countryCode'];
		}
	}
	if( $stats['cnt'] >= 3 ) {
		if(isset($ips[$ip]['countryCode']) && $ips[$ip]['countryCode'] === 'CZ' && $stats['cnt'] < 30) {
			$warnIPs[$ip] = $stats;
			continue;
		} 		
		$banIPs[$ip] = $stats;
		$processBan[] = $ip;
		continue;
	} else {
		$warnIPs[$ip] = $stats;
	}
}

if(count($processBan)) {
	foreach($processBan as $banIP) {
		exec('iptables -A INPUT -s ' . $banIP . ' -j DROP');
	}
	echo PHP_EOL;
	echo exec('service iptables save');
	echo PHP_EOL;
}

echo PHP_EOL;
echo 'TOTAL LINES: ' . $linecount . PHP_EOL;
echo 'TOTAL WARN IPs: ' . count($warnIPs) . PHP_EOL;
echo 'TOTAL BAN IPs: ' . count($banIPs) . PHP_EOL;
echo PHP_EOL;
foreach($banIPs as $ip => $stats) {
	echo 'BAN: ' . $ip . ' [' . (isset($stats['countryCode']) ? $stats['countryCode'] : '--') . ']: ' . $stats['cnt'] . 'x' . PHP_EOL;
}
echo PHP_EOL;
foreach($warnIPs as $ip => $stats) {
	echo 'WARN: ' . $ip . ' [' . (isset($stats['countryCode']) ? $stats['countryCode'] : '--') . ']: ' . $stats['cnt'] . 'x' . PHP_EOL;
}
echo PHP_EOL;
echo PHP_EOL;

file_put_contents(__DIR__ . '/.logIPban.json', json_encode($banIPs));
file_put_contents(__DIR__ . '/.logIPwarn.json', json_encode($warnIPs));
