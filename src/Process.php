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

    /**
     * Constructs the proccess
     *
     * @param string $cmd command to be executed
     */
    public function __construct($cmd)
    {
        $this->builder = new ProcessBuilder();

        // Inherit environment variables from Host operating system
        $this->builder->inheritEnvironmentVariables();

        $arguments = explode(' ', $cmd);

        // Environment variables could be passed as per *nix command line (FLAG)=(VALUE) pairs
        foreach ($arguments as $key => $argument) {
            if (preg_match('/([A-Z][A-Z0-9_-]+)=(.*)/', $argument, $matches)) {
                $this->builder->setEnv($matches[1], $matches[2]);
                unset($arguments[$key]); // Unset it from arguments list since we do not want it in proccess (otherwise command not found is given)
            } else {
                // Break if first non-environment argument is found, since after that everything is either command or option
                break;
            }
        }

        $this->builder->setArguments($arguments);

        $this->symfonyProcess = $this->builder->getProcess();
    }

    /**
     * Starts background proccess
     *
     * @param \Closure $callback Function to execute if executed application provides output(either STDERR or STDOUT) ($type, $buffer)
     *
     * @return int The exit status code
     */
    public function start($callback = null)
    {
        $statusCode = $this->symfonyProcess->start($callback);

        // give the new process a bit of breathing room
        sleep(2);

        return $statusCode;
    }

    /**
     * Stops the backgroud proccess by sending it SIGKILL
     *
     * @return int The exit status code
     */
    public function stop()
    {
        $statusCode = $this->symfonyProcess->stop();

        // give the new process a bit of breathing room
        sleep(2);

        return $statusCode;
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
