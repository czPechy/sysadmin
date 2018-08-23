<?php
$silent = ( !isset( $argv[1] ) || $argv[1] !== '-l' );

$homeDirectory = '/home/';
$mailDirectory = '/mail/';
$airmailDirectory = '.[Airmail]';
$spamFolders = ['.Spam', '.Junk', '.Unwanted', '.Trash.Spam', '.Junk E-mail'];
$trashFolders = ['.Trash', '.Deleted Messages', '.Ko&AWE-', '.Deleted Items'];
$ignoreFolders = [
    '.Sent', '.Sent Messages', '.Sent Items', '.Drafts', '.Odeslan&AOk-', '.Odeslane&AwE-',
    '.Rozepsan&AOk-', '.K odesl&AOE-n&AO0-'
];
$dirItems = ['..', '.'];
$users = array_diff( scandir( $homeDirectory, SCANDIR_SORT_NONE ), $dirItems);

foreach($users as $user) {
	$userPath = $homeDirectory . $user . $mailDirectory;
    if(!is_dir($userPath)) {
		continue;
	}
	$domains = array_diff( scandir( $userPath, SCANDIR_SORT_NONE ), $dirItems);
	foreach($domains as $domain) {
		$accounts = array_diff( scandir( $userPath . $domain . '/', SCANDIR_SORT_NONE ), $dirItems);
		foreach($accounts as $account) {
		    $accountPath = $userPath . $domain . '/' . $account . '/';
			$existingFolders =  array_diff( scandir( $accountPath, SCANDIR_SORT_NONE ), $dirItems);
			foreach($existingFolders as $existingFolder) {
			    if(strpos($existingFolder, '.') !== 0 || \in_array($existingFolder, $ignoreFolders, true) || \in_array($existingFolder, $trashFolders, true)) {
			        continue;
                }
                if(strpos($existingFolder, $airmailDirectory) === 0) {
                    continue;
                }
                if(\in_array($existingFolder, $spamFolders, true)) {
                    if($silent) {
                        exec('/usr/bin/sa-learn --use-ignores --spam "' . $accountPath . str_replace('&', '\&', $existingFolder) . '"');
                    } else {
                        echo 'SPAM: ' . $accountPath . $existingFolder . PHP_EOL;
                        echo exec('/usr/bin/sa-learn --use-ignores --spam "' . $accountPath . str_replace('&', '\&', $existingFolder) . '"') . PHP_EOL;
                    }
                    continue;
                }
                if($silent) {
                    exec('/usr/bin/sa-learn --use-ignores --ham "' . $accountPath . str_replace('&', '\&', $existingFolder) . '"');
                } else {
                    echo 'HAM: ' . $accountPath . $existingFolder . PHP_EOL;
                    echo exec('/usr/bin/sa-learn --use-ignores --ham "' . $accountPath . str_replace('&', '\&', $existingFolder) . '"') . PHP_EOL;
                }
			}
			if(!$silent) {
				echo PHP_EOL;
			} 
		}
	}
}

exec('/usr/bin/sa-learn --sync');
