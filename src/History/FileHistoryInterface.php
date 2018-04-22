<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\History;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
interface FileHistoryInterface
{
    /**
     * @param string $filename
     */
    public function setFilename(string $filename): void;
}
