<?php

declare(strict_types=1);


namespace Timesplinter\Oyster\OperatingSystemAdapter;

use Timesplinter\Oyster\Executor;
use Timesplinter\Oyster\Output\BufferedOutput;

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
     * @var BufferedOutput
     */
    private $output;

    /**
     * DarwinAdapter constructor.
     * @param Executor $executor
     * @param BufferedOutput $output
     */
    public function __construct(Executor $executor, BufferedOutput $output)
    {
        $this->executor = $executor;
        $this->output = $output;
    }

    /**
     * Returns the home directory for a user
     * @param string $user
     * @return string
     */
    public function getHomeDirectory(string $user): string
    {
        $this->executor->execute('dscl', ['.', '-read /Users/'.$user, 'NFSHomeDirectory'], __DIR__, []);

        if (0 === preg_match('/^NFSHomeDirectory:\s*(.+)$/', trim($this->output->getBuffer()), $matches)) {
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
        $this->executor->execute('users', [], __DIR__, []);

        return trim($this->output->getBuffer());
    }

    /**
     * Returns the name of the device
     * @param string $type
     * @return string
     */
    public function getHostname(string $type): string
    {
        $flag = self::HOSTNAME_FULL === $type ? '-f' : '-s';

        $this->executor->execute('hostname', [$flag], __DIR__, []);

        return trim($this->output->getBuffer());
    }
}