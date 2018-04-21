<?php

declare(strict_types=1);

namespace Timesplinter\Oyster;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class Executor
{
    /**
     * @todo make async!
     * @param string $command
     * @param array $arguments
     * @param array $vars
     * @return null|string
     */
    public function execute(string $command, array $arguments, string $cwd, array $vars): ?string
    {
        $stream = null;
        $streamContent = null;

        $descriptorSpec = [
            0 => ['pipe', "r"],
            1 => ['pipe', "w"],
            //2 => ['file', "./error-output.txt", "a"]
        ];

        $env = $vars; //array('some_option' => 'aeiou');

        //var_dump($command . ' ' . implode(' ', $arguments));

        $process = proc_open(
            $command . (count($arguments) > 0 ? ' ' . implode(' ', $arguments) : ''),
            $descriptorSpec,
            $pipes,
            $cwd,
            $env
        );

        if (is_resource($process)) {
            /*fwrite($pipes[0], '<?php echo "HELLO FROM TEST"; ?>'); // here directly the code of child.php
            fclose($pipes[0]);*/

            $streamContent = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            proc_close($process);
        }

        return $streamContent;
    }
}