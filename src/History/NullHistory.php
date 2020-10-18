<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Helper;

use Timesplinter\Oyster\History\HistoryInterface;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
final class NullHistory implements HistoryInterface
{

    public function loadHistory(): void
    {
    }

    public function storeHistory(): void
    {
    }

    public function addToHistory(string $line): bool
    {
        return true;
    }

    public function getHistory(): array
    {
        return [];
    }
}
