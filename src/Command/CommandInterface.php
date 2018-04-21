<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Command;

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
     * @return string
     * @throws CommandExecutionException
     */
    public function execute(array $arguments): string;
}
