<?php

namespace Cooperaj\PHPCI\Plugin;

use PHPCI\Builder;
use PHPCI\Model\Build;
use Symfony\Component\Process\Process as SymfonyProcess;

/**
 * Process Plugin - Allows PHPCI to setup and manage long running processes throughout the build.
 * @author       Adam Cooper <adam@networkpie.co.uk>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class Process implements \PHPCI\Plugin
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
    pcntl_signal(SIGTERM, ['Cooperaj\PHPCI\Process', 'stopRunningJobs']);
}
register_shutdown_function(['Cooperaj\PHPCI\Process', 'stopRunningJobs']);
