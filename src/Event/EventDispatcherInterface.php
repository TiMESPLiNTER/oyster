<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Event;

interface EventDispatcherInterface
{
    public function dispatch(EventInterface $event): void;

    public function subscribe(string $event, callable $handler): void;
}
