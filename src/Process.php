<?php

namespace Cooperaj\PHPCI;

use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\ProcessBuilder;

class Process
{
    /** @var ProcessBuilder $builder  */
    private $builder;

    /** @var PhpProcess $symfonyProcess */
    private $symfonyProcess;

    public function __construct($cmd)
    {
        $this->builder = new ProcessBuilder();
        
        $this->builder->inheritEnvironmentVariables();

        $arguments=explode(' ', $cmd);
        
        foreach ($arguments as $key => $argument) {
            if (preg_match('/([A-Z][A-Z0-9_-]+)=(.*)/', $argument, $matches)) {
                $this->builder->setEnv($matches[1], $matches[2]);
                unset($arguments[$key]);
            }
        }

        # $this->builder->setPrefix('exec');

        $this->builder->setArguments($arguments);

        $this->symfonyProcess = $this->builder->getProcess();
#        var_dump($this->symfonyProcess);

    }

    public function start($callback = null)
    {
        $proccess=$this->symfonyProcess->start($callback);

        // give the new process a bit of breathing room
        sleep(2);
        return $proccess;
    }

    public function stop()
    {
        $this->symfonyProcess->stop();

        // give the new process a bit of breathing room
        sleep(2);
    }

    public function getOutput()
    {
        return $this->symfonyProcess->getOutput();
    }

    public function getPid()
    {
        return $this->symfonyProcess->getPid();
    }
}
