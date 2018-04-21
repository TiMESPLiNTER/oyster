<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\OperatingSystemAdapter;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
interface OperatingSystemAdapter
{
    /**
     * Returns the home directory for a user
     * @param string $user
     * @return string
     */
    public function getHomeDirectory(string $user): string;

    /**
     * Returns name of current user
     * @return string
     */
    public function getCurrentUser(): string;
}