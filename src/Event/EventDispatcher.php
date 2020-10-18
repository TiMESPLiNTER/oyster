<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Event;

final class EventDispatcher implements EventDispatcherInterface
{

    private $subscriptions = [];

    public function dispatch(EventInterface $event): void
    {
        $eventName = get_class($event);

        if (!array_key_exists($eventName, $this->subscriptions)) {
            return;
        }

        foreach ($this->subscriptions[$eventName] as $eventHandler) {
            call_user_func($eventHandler, $event);
        }
    }

    public function subscribe(string $event, callable $handler): void
    {
        $this->subscriptions[$event][] = $handler;
    }
}
