<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Command;

use Timesplinter\Oyster\Output\OutputInterface;
use Timesplinter\Oyster\Runtime;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class PwdCommand implements CommandInterface
{

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * PwdCommand constructor.
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'pwd';
    }

    /**
     * @param array $arguments
     * @param Runtime $runtime
     * @return int
     */
    public function execute(array $arguments, Runtime $runtime): int
    {
        $this->output->writeLn(getcwd());

        return 0;
    }
}
