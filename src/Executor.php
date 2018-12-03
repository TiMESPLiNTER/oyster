<?php

declare(strict_types=1);

namespace Timesplinter\Oyster;

use Timesplinter\Oyster\Output\OutputInterface;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class Executor
{

    const MODE_TTY = 'tty';

    const MODE_PIPE = 'pipe';

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

    /**
     * @todo make async! and needs TTY support for vim etc [e.x. system("command > `tty`")]
     * @param string $command
     * @param array $arguments
     * @param string $cwd
     * @param array $vars
     * @return int
     */
    public function execute(string $command, array $arguments, string $cwd, array $vars): int
    {
        $stream = null;
        $streamContent = null;

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

        //var_dump($command . ' ' . implode(' ', $arguments));

        $process = proc_open(
            $command . (count($arguments) > 0 ? ' ' . implode(' ', $arguments) : ''),
            $descriptorSpec,
            $pipes,
            $cwd,
            $vars
        );

        if (is_resource($process)) {
            /*fwrite($pipes[0], '<?php echo "HELLO FROM TEST"; ?>'); // here directly the code of child.php
            fclose($pipes[0]);*/

            while (true === ($info = proc_get_status($process))['running']) {

            }

            file_put_contents('returnCodes.txt', $command . ' -> ' .var_export($info, true) . PHP_EOL . PHP_EOL, FILE_APPEND);

            if (self::MODE_PIPE === $this->mode) {
                $this->output->write(stream_get_contents($pipes[1]));
                fclose($pipes[1]);
            }

            proc_close($process);

            /*if (self::MODE_TTY === $this->mode) {
                $this->output->write("\033[1J");
            }*/
        }

        return 0;
    }
}
