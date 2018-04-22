<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\History;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class ReadlineHistory implements HistoryInterface, FileHistoryInterface
{

    /**
     * @var string
     */
    private $filename;

    public function loadHistory(): void
    {
        readline_read_history($this->filename);
    }

    public function storeHistory(): void
    {
        readline_write_history($this->filename);
    }

    public function addToHistory(string $line): bool
    {
        return readline_add_history($line);
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function getHistory(): array
    {
        return readline_list_history();
    }
}
