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
        $this->builder->setPrefix('exec');

        $this->builder->setArguments(explode(' ', $cmd));

        $this->symfonyProcess = $this->builder->getProcess();
    }

    public function start()
    {
        $this->symfonyProcess->start();

        // give the new process a bit of breathing room
        sleep(2);
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
