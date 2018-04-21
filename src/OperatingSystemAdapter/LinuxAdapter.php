<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\OperatingSystemAdapter;

use Timesplinter\Oyster\Executor;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class LinuxAdapter implements OperatingSystemAdapter
{

    /**
     * @var Executor
     */
    private $executor;

    /**
     * DarwinAdapter constructor.
     * @param Executor $executor
     */
    public function __construct(Executor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * Returns the home directory for a user
     * @param string $user
     * @return string
     */
    public function getHomeDirectory(string $user): string
    {
        $output = trim($this->executor->execute('getent', ['passwd', 'tecmint'], __DIR__, []));

        $info = explode(':', $output);

        return $info[count($info)-2];
    }

    /**
     * Returns name of current user
     * @return string
     */
    public function getCurrentUser(): string
    {
        return trim($this->executor->execute('whoami', [], __DIR__, []));
    }
}