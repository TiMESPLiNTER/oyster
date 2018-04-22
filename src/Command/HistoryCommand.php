<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Command;

use Timesplinter\Oyster\History\HistoryInterface;
use Timesplinter\Oyster\Output\OutputInterface;
use Timesplinter\Oyster\Runtime;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class HistoryCommand implements CommandInterface
{

    /**
     * @var HistoryInterface
     */
    private $history;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * HistoryCommand constructor.
     * @param OutputInterface $output
     * @param HistoryInterface $history
     */
    public function __construct(OutputInterface $output, HistoryInterface $history)
    {
        $this->history = $history;
        $this->output = $output;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'history';
    }

    /**
     * @param array $arguments
     * @param Runtime $runtime
     * @return int
     */
    public function execute(array $arguments, Runtime $runtime): int
    {
        $this->output->writeLn(implode(PHP_EOL, $this->history->getHistory()));

        return 0;
    }
}
