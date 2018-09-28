<?php
namespace ProfiCloS\Monitor\Plugin;

use ProfiCloS\Monitor\IPlugin;

class DiskFree implements IPlugin
{

    const COMMAND = 'df';
    const PARSER = '~(?<disk>\/[a-zA-Z0-9\/\-]+)\s+(?<total>\d+)\s+(?<used>\d+)\s+(?<free>\d+)\s+(?<used_percentage>\d+)%\s(?<mount>.+)~';

    public function execute()
    {
        $result = shell_exec(self::COMMAND);
        preg_match_all(self::PARSER, $result, $matches);
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

}
