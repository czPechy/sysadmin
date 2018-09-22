<?php
namespace ProfiCloS\Monitor;

class PluginsLoader
{

    /** @var IPlugin[] */
    protected $plugins = [];

    public function __construct()
    {
        $files = array_filter(array_diff(scandir(__DIR__ . '/Plugin', SCANDIR_SORT_NONE), [
            '.', '..'
        ]) , function($item) {
            return !is_dir(__DIR__ . '/Plugin/' . $item);
        });

        foreach ($files as $file) {
            if(strpos($file, '.php') !== false) {
                try {
                    $reflection = new \ReflectionClass( 'ProfiCloS\Monitor\Plugin\\' . str_replace( '.php', '', $file ) );
                } catch ( \ReflectionException $e ) {
                    continue;
                }
                if(!$reflection->isInterface() && !$reflection->isAbstract()) {
                    /** @var IPlugin $plugin */
                    $plugin = new $reflection->name();
                    $this->plugins[] = $plugin;
                }
            }
        }
    }

    public function getPlugins() {
        return $this->plugins;
    }

}