<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Plugin;

interface PluginInterface
{
    public function register(): array;
}
