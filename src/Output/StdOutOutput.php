<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Output;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class StdOutOutput implements OutputInterface
{

    /**
     * @var resource
     */
    private $stdOut;

    public function __construct()
    {
        $this->stdOut = fopen('php://stdout', 'w');
    }

    public function write(string $buffer)
    {
        fwrite($this->stdOut, $buffer);
    }

    public function writeLn(string $buffer)
    {
        $this->write($buffer . PHP_EOL);
    }
}
