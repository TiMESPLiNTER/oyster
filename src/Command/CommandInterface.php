<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Command;
use Timesplinter\Oyster\Runtime;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
interface CommandInterface
{

    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @param array $arguments
     * @param Runtime $runtime
     * @return int
     */
    public function execute(array $arguments, Runtime $runtime): int;
}
