<?php
namespace ProfiCloS\Monitor;

class Monitor
{

    /** @var IPlugin[] */
    protected $plugins;

    public function __construct()
    {
        $pluginsLoader = new PluginsLoader();
        $this->plugins = $pluginsLoader->getPlugins();
    }

    public function getArray() {
        $data = [];
        foreach($this->plugins as $plugin) {
            $data[] = [
                'plugin' => get_class($plugin),
                'data' => $plugin->execute()
            ];
        }
        return $data;
    }

    public function getJSON() {
        return json_encode($this->getArray());
    }

}