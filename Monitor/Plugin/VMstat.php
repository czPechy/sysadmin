<?php
namespace ProfiCloS\Monitor\Plugin;

use ProfiCloS\Monitor\IPlugin;

class VMstat implements IPlugin
{

    const COMMAND = 'vmstat -s';
    const PARSER = '~((?<memory_total>\d+)\sK\stotal\smemory)|((?<memory_used>\d+)\sK\sused\smemory)|((?<swap_total>\d+)\sK\stotal\sswap)|((?<swap_used>\d+)\sK\sused\sswap)|((?<boot_time>\d+)\sboot\stime)~';

    public function execute()
    {
        $result = shell_exec(self::COMMAND);
        preg_match_all(self::PARSER, $result, $matches);
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

}