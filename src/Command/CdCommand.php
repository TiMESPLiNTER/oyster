<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Command;

use Timesplinter\Oyster\OperatingSystemAdapter\OperatingSystemAdapter;
use Timesplinter\Oyster\Runtime;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class CdCommand implements CommandInterface
{

    /**
     * @var OperatingSystemAdapter
     */
    private $osAdapter;

    /**
     * @param OperatingSystemAdapter $osAdapter
     */
    public function __construct(OperatingSystemAdapter $osAdapter)
    {
        $this->osAdapter = $osAdapter;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'cd';
    }

    /**
     * @param array $arguments
     * @param Runtime $runtime
     * @return int
     * @throws CommandExecutionException
     */
    public function execute(array $arguments, Runtime $runtime): int
    {
        $argc = count($arguments);

        if (0 === $argc) {
            throw new CommandExecutionException('Missing argument 1', 1);
        } elseif (1 < $argc) {
            throw new CommandExecutionException('Only accepts one argument', 2);
        }

        $path = $arguments[0];

        if ('~' === $path) {
            $path = realpath($this->osAdapter->getHomeDirectory($this->osAdapter->getCurrentUser()));
        } elseif (0 !== strpos($path, '/')) {
            $path = realpath(getcwd() . DIRECTORY_SEPARATOR . $path);
        }

        chdir($path);

        return 0;
    }
}
