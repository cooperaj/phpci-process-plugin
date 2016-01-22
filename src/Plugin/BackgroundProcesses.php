<?php

namespace Cooperaj\PHPCI\Plugin;

use Cooperaj\PHPCI\ProcessManager;
use PHPCI\Builder;
use PHPCI\Model\Build;
use PHPCI\Plugin;
use PHPCI\ZeroConfigPlugin;

/**
 * BackgroundProcesses Plugin
 *
 * Allows PHPCI to setup long running processes throughout the build.
 *
 * @author       Adam Cooper <adam@networkpie.co.uk>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class BackgroundProcesses implements Plugin, ZeroConfigPlugin
{
    protected $processManager;

    protected $phpci;
    protected $build;
    protected $processCmds;

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

        $this->processManager = ProcessManager::instance();
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
                $process = $this->processManager->createProcess($cmd);

                $process->start();

                $this->phpci->log($process->getOutput() . 'PID: ' . $process->getPid());
            }
        } catch (\Exception $ex) {
            $this->phpci->logFailure($ex->getMessage());
            return false;
        }
        return true;
    }
}
