<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Output;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class BufferedOutput implements OutputInterface
{

    /**
     * @var string
     */
    private $buffer = '';

    public function write(string $buffer)
    {
        $this->buffer .= $buffer;
    }

    public function writeLn(string $buffer)
    {
        $this->buffer .= $buffer . PHP_EOL;
    }

    public function getBuffer(): string
    {
        $content = $this->buffer;

        $this->buffer = '';

        return $content;
    }
}
