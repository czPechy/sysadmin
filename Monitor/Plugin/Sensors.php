<?php
namespace ProfiCloS\Monitor\Plugin;

use ProfiCloS\Monitor\IPlugin;

class Sensors implements IPlugin
{

    const COMMAND = 'sensors';
    const PARSER = '~Core\s(?<core>\d+):\s+\+(?<temp>[\d\.]+)\°C\s+\(high\s\=\s\+(?<high>[\d\.]+)\°C\,\scrit\s\=\s\+(?<crit>[\d\.]+)\°C\)~u';

    public function execute()
    {
        $result = shell_exec(self::COMMAND);
        preg_match_all(self::PARSER, $result, $matches);
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

}
