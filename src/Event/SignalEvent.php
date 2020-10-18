<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Event;

final class SignalEvent implements EventInterface
{
    /**
     * @var int
     */
    private $signal;

    public function __construct(int $signal)
    {
        $this->signal = $signal;
    }

    public function getSignal(): int
    {
        return $this->signal;
    }
}
