<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Command;

use Timesplinter\Oyster\Output\OutputInterface;
use Timesplinter\Oyster\Runtime;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class EchoCommand implements CommandInterface
{

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * PwdCommand constructor.
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'echo';
    }

    /**
     * @param array $arguments
     * @param Runtime $runtime
     * @return int
     * @throws CommandExecutionException
     */
    public function execute(array $arguments, Runtime $runtime): int
    {
        if (count($arguments) > 1) {
            throw new CommandExecutionException('Only 1 argument allowed', 1);
        }

        $str = preg_replace_callback('/(\\\)?\$(\w+)/', function($m) use ($runtime) {
            if ('\\' === $m[1]) {
                return '$' . $m[2];
            }

            return $runtime->getEnvVars()[$m[2]];
        }, $arguments[0]);

        $this->output->writeLn($str);

        return 0;
    }
}
