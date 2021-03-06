<?php

declare(strict_types=1);

namespace Timesplinter\Oyster;

use Timesplinter\Oyster\Output\OutputInterface;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
final class Executor implements ExecutorInterface
{

    public const MODE_TTY = 'tty';

    public const MODE_PIPE = 'pipe';

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $mode;

    /**
     * Executor constructor.
     * @param OutputInterface $output
     * @param string          $mode
     */
    public function __construct(OutputInterface $output, string $mode)
    {
        $this->output = $output;
        $this->mode = $mode;
    }

    public function execute(string $command, array $arguments, string $cwd, array $vars): int
    {
        $stream = null;
        $streamContent = null;
        $exitCode = 0;

        if (self::MODE_TTY === $this->mode) {
            $descriptorSpec = [
                0 => ['file', '/dev/tty', 'r'],
                1 => ['file', '/dev/tty', 'w'],
                2 => ['file', '/dev/tty', 'w'],
            ];
        } elseif (self::MODE_PIPE === $this->mode) {
            $descriptorSpec = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                //2 => ['file', "./error-output.txt", "a"]
            ];
        } else {
            throw new \RuntimeException(sprintf('Unknown mode "%s"', $this->mode));
        }

        $process = proc_open(
            $command . (count($arguments) > 0 ? ' ' . implode(' ', $arguments) : ''),
            $descriptorSpec,
            $pipes,
            $cwd,
            $vars
        );

        if (is_resource($process)) {
            while (true === ($info = proc_get_status($process))['running']);

            $exitCode = $info['exitcode'];

            if (self::MODE_PIPE === $this->mode) {
                $this->output->write(stream_get_contents($pipes[1]));
                fclose($pipes[1]);
            }

            proc_close($process);
        }

        return $exitCode;
    }
}
