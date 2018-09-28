<?php
ini_set('display_errors', 1);
require_once 'Monitor/autoload.php';

$isTesting = \in_array('-t', $argv, true);
if($isTesting) {
    $monitor = new \ProfiCloS\Monitor\Monitor();
    echo $monitor->getJSON();
    return;
}

for ($i=0; $i <= 30; $i++) {
    postData();
    sleep(10);
}

function postData() {
    $monitor = new \ProfiCloS\Monitor\Monitor();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,'https://api.profihost.cloud/3bc18294-1c31-438a-9c41-d46b4496a7ac/');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $monitor->getJSON());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close ($ch);
}