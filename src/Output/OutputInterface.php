<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Output;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
interface OutputInterface
{
    public function write(string $buffer);

    public function writeLn(string $buffer);
}
