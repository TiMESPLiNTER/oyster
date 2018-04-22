<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Input;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class StdInInput implements InputInterface
{

    private $stdIn;

    public function __construct()
    {
        $this->stdIn = fopen('php://stdin', 'r');
    }

    /**
     * @param string $prompt
     * @return string
     */
    public function read(string $prompt): string
    {
        echo $prompt;

        return fgets($this->stdIn);
    }
}
