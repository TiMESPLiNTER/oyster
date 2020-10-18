<?php

declare(strict_types=1);

namespace Timesplinter\Oyster;

interface ExecutorInterface
{
    public function execute(string $command, array $arguments, string $cwd, array $vars): int;
}
