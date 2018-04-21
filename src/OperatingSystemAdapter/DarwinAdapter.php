<?php

declare(strict_types=1);


namespace Timesplinter\Oyster\OperatingSystemAdapter;

use Timesplinter\Oyster\Executor;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class DarwinAdapter implements OperatingSystemAdapter
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
        $output = $this->executor->execute('dscl', ['.', '-read /Users/'.$user, 'NFSHomeDirectory'], __DIR__, []);

        if (0 === preg_match('/^NFSHomeDirectory:\s*(.+)$/', trim($output), $matches)) {
            throw new \RuntimeException('Could not find home directory for user: ' . $user);
        }

        return $matches[1];
    }

    /**
     * Returns name of current user
     * @return string
     */
    public function getCurrentUser(): string
    {
        return trim($this->executor->execute('users', [], __DIR__, []));
    }
}