<?php
namespace ProfiCloS\Monitor\Plugin;

use ProfiCloS\Monitor\IPlugin;

class Hostname implements IPlugin
{

    const COMMAND = 'hostnamectl';
    const PARSER = '~^\s*([a-zA-Z0-9\s]+):\s(.*)$~m';

    public function execute()
    {
        $result = shell_exec(self::COMMAND);
        preg_match_all(self::PARSER, $result, $matches);

        $data = [];
        foreach($matches[0] as $key => $item) {
            $data[$matches[1][$key]] = $matches[2][$key];
        }
        return $data;
    }

}