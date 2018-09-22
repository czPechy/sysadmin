<?php
namespace ProfiCloS\Monitor\Plugin;

use ProfiCloS\Monitor\IPlugin;
use ProfiCloS\Monitor\Tools\Numbers;

class IOstat implements IPlugin
{

    const COMMAND = 'iostat';
    const PARSER_CPU = '~\s+(?<user>[\d\,]+)\s+(?<nice>[\d\,]+)\s+(?<system>[\d\,]+)\s+(?<iowait>[\d\,]+)\s+(?<steal>[\d\,]+)\s+(?<idle>[\d\,]+)~';
    const PARSER_DISK = '~^(?<disk>[\da-zA-Z\,]+)\s+(?<tps>[\d\,]+)\s+(?<read_s>[\d\,]+)\s+(?<write_s>[\d\,]+)\s+(?<read>[\d\,]+)\s+(?<write>[\d\,]+)$~m';

    public function execute()
    {
        $result = shell_exec(self::COMMAND);
        preg_match(self::PARSER_CPU, $result, $matches);
        $cpu = [
            'user' => Numbers::float($matches['user']),
            'nice' => Numbers::float($matches['nice']),
            'system' => Numbers::float($matches['system']),
            'iowait' => Numbers::float($matches['iowait']),
            'steal' => Numbers::float($matches['steal']),
            'idle' => Numbers::float($matches['idle'])
        ];
        preg_match_all(self::PARSER_DISK, $result, $matches);
        $disks = [];
        foreach($matches['disk'] as $key => $disk) {
            $disks[] = [
                'disk' => $disk,
                'tps' => Numbers::float($matches['tps'][$key]),
                'read_s' => Numbers::float($matches['read_s'][$key]),
                'read' => Numbers::float($matches['read'][$key]),
                'write_s' => Numbers::float($matches['write_s'][$key]),
                'write' => Numbers::float($matches['write'][$key]),
            ];
        }
        return [
            'cpu' => $cpu,
            'disk' => $disks
        ];
    }

}