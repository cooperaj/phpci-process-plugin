<?php

namespace Cooperaj\PHPCI;

class ProcessManager
{
    static $instance;

    protected $processes;

    private function __construct()
    {
        $this->processes = array();
    }

    static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new ProcessManager();
        }

        return self::$instance;
    }

    public function createProcess($cmd)
    {
        $process = new Process($cmd);
        $this->processes[] = $process;

        return $process;
    }

    public function stopAllProcesses()
    {
        foreach ($this->processes as $process) {
            $process->stop();
        }
    }

    static function stop()
    {
        if (self::$instance) unset($instance);
    }
}

if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGTERM, ['Cooperaj\PHPCI\ProcessManager', 'stop']);
}
register_shutdown_function(['Cooperaj\PHPCI\ProcessManager', 'stop']);
