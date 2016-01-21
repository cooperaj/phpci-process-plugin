<?php

namespace Cooperaj\PHPCI\Plugin;

use PHPCI\Builder;
use PHPCI\Model\Build;
use PHPCI\Plugin;
use PHPCI\ZeroConfigPlugin;
use Symfony\Component\Process\Process as SymfonyProcess;

/**
 * Process Plugin - Allows PHPCI to setup and manage long running processes throughout the build.
 * @author       Adam Cooper <adam@networkpie.co.uk>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class Process implements Plugin, ZeroConfigPlugin
{
    static $instances;

    protected $phpci;
    protected $build;
    protected $processCmds;
    protected $runningProcesses;

    /**
     * Constructor
     *
     * @param Builder $phpci
     * @param Build   $build
     * @param array   $options
     */
    public function __construct(Builder $phpci, Build $build, array $options = array())
    {
        $this->build = $build;
        $this->phpci = $phpci;

        $this->processCmds = $options;

        $this->runningProcesses = array();
        self::$instances[] = $this;
    }

    /**
     * Check if this plugin can be executed.
     *
     * @param $stage
     * @param Builder $builder
     * @param Build $build
     *
     * @return bool
     */
    public static function canExecute($stage, Builder $builder, Build $build)
    {
        if ($stage == 'setup') {
            return true;
        }
        return false;
    }

    /**
     * Executes processes
     */
    public function execute()
    {
        try {
            foreach ($this->processCmds as $cmd) {
                // build the command
                $cmd = 'cd %s && ' . $cmd;
                if (IS_WIN) {
                    $cmd = 'cd /d %s && ' . $cmd;
                }

                $process = new SymfonyProcess($cmd);
                $process->start();

                $runningProcesses[] = $process;
            }
        } catch (\Exception $ex) {
            $this->phpci->logFailure($ex->getMessage());
            return false;
        }
        return true;
    }

    static function stopRunningJobs()
    {
        foreach (self::$instances as $instance) {
            if ($instance) unset($instance);
        }
    }
}

if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGTERM, ['Cooperaj\PHPCI\Plugin\Process', 'stopRunningJobs']);
}
register_shutdown_function(['Cooperaj\PHPCI\Plugin\Process', 'stopRunningJobs']);
