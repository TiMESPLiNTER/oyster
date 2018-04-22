<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Input;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class ReadlineInput implements InputInterface
{

    /**
     * @param string $prompt
     * @return string
     */
    public function read(string $prompt): string
    {
        return readline($prompt);
    }
}
