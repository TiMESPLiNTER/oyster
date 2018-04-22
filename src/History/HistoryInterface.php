<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\History;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
interface HistoryInterface
{
    public function loadHistory(): void;

    public function storeHistory(): void;

    public function addToHistory(string $line): bool;

    public function getHistory(): array;
}
