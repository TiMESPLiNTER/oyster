<?php

declare(strict_types=1);


namespace Timesplinter\Oyster\Command;


/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class PwdCommand implements CommandInterface
{

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'pwd';
    }

    /**
     * @param array $arguments
     * @return string
     */
    public function execute(array $arguments): string
    {
        return getcwd() . "\n";
    }
}
