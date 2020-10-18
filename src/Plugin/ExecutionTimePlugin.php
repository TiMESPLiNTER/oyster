<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Plugin;

use Timesplinter\Oyster\Event\CommandStartEvent;
use Timesplinter\Oyster\Event\CommandStopEvent;
use Timesplinter\Oyster\Event\ExecutableStartEvent;
use Timesplinter\Oyster\Event\ExecutableStopEvent;
use Timesplinter\Oyster\Event\SignalEvent;
use Timesplinter\Oyster\Output\OutputInterface;

final class ExecutionTimePlugin implements PluginInterface
{
    /**
     * @var float
     */
    private $startTime = 0.0;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function register(): array
    {
        return [
            ExecutableStartEvent::class => [$this, 'startStopwatch'],
            CommandStartEvent::class => [$this, 'startStopwatch'],

            ExecutableStopEvent::class => [$this, 'stopStopwatch'],
            CommandStopEvent::class => [$this, 'stopStopwatch'],
        ];
    }

    public function startStopwatch(): void
    {
        $this->startTime = microtime(true);
    }

    public function stopStopwatch(): void
    {
        $executionTime = microtime(true) - $this->startTime;

        $this->output->writeLn(sprintf('Execution time: %dms', $executionTime));
    }
}
