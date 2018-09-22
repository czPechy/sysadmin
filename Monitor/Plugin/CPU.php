<?php
namespace ProfiCloS\Monitor\Plugin;

use ProfiCloS\Monitor\IPlugin;
use ProfiCloS\Monitor\Tools\Numbers;

class CPU implements IPlugin
{

    const COMMAND_TOP = 'top -b -n 1 | grep "Cpu(s)\:" | awk \'{print $2}\'';
    const COMMAND_NPROC = 'nproc';
    const COMMAND_LSCPU = 'lscpu';
    const PARSER = '~^([a-zA-Z0-9\s]+):\s+(.*)$~mu';

    public function execute()
    {
        $cpuUsage = shell_exec(self::COMMAND_TOP);
        $cpuCores = shell_exec(self::COMMAND_NPROC);
        $result =   shell_exec(self::COMMAND_LSCPU);

        $result = str_replace('Název modelu', 'Model name', $result);
        preg_match_all(self::PARSER, $result, $matches);

        $data = [];
        foreach($matches[0] as $key => $item) {
            $data[$matches[1][$key]] = preg_replace('~(\s+\s+)~', ' ', $matches[2][$key]);
        }
        unset($data['Flags']);

        return [
            'usage' => Numbers::float($cpuUsage),
            'cores' => (int) $cpuCores,
            'desc' => $data
        ];
    }

}
