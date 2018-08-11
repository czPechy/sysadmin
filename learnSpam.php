<?php
$silent = (!isset($argv[1]) || $argv[1] !== '-l' ? true : false);

$homeDirectory = '/home/';
$mailDirectory = '/mail/';
$users = array_diff(scandir($homeDirectory), array('..', '.'));

foreach($users as $user) {
	if(!is_dir($homeDirectory . $user . $mailDirectory)) {
		continue;
	}
	$domains = array_diff(scandir($homeDirectory . $user . $mailDirectory), array('..', '.'));
	foreach($domains as $domain) {
		$accounts = array_diff(scandir($homeDirectory . $user . $mailDirectory . $domain . '/'), array('..', '.'));
		foreach($accounts as $account) {
			foreach(['Spam', 'Junk'] as $folder) {	
				$spamFolder = $homeDirectory . $user . $mailDirectory . $domain . '/' . $account . '/.' . $folder . '/';
				if(!is_dir($spamFolder)) {
					continue;
				}
				if($silent) {
					exec('/usr/bin/sa-learn --spam ' . $spamFolder);
				} else {
					echo $spamFolder . PHP_EOL;
					echo exec('/usr/bin/sa-learn --spam ' . $spamFolder) . PHP_EOL;
				}
			}
			if(!$silent) {
				echo PHP_EOL;
			} 
		}
	}
}

exec('/usr/bin/sa-learn --sync');
