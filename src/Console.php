<?php

declare(strict_types=1);

namespace Timesplinter\Oyster;

use Timesplinter\Oyster\Command\CommandInterface;
use Timesplinter\Oyster\Command\CommandExecutionException;
use Timesplinter\Oyster\OperatingSystemAdapter\OperatingSystemAdapter;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
final class Console
{

    /**
     * @var CommandInterface[]|array
     */
    private $commands;

    /**
     * @var Executor
     */
    private $executor;

    /**
     * @var array
     */
    private $config;

    /**
     * @var OperatingSystemAdapter
     */
    private $osAdapter;

    /**
     * Console constructor.
     * @param OperatingSystemAdapter $osAdapter
     * @param array|CommandInterface[] $commands
     * @param Executor $executor
     */
    public function __construct(OperatingSystemAdapter $osAdapter, array $commands, Executor $executor)
    {
        $this->commands = $commands;
        $this->executor = $executor;
        $this->osAdapter = $osAdapter;
    }

    public function run(): void
    {
        $homeDirectory = $this->osAdapter->getHomeDirectory($this->osAdapter->getCurrentUser());

        $this->config = $this->loadConfiguration($homeDirectory);

        while (true) {
            echo $this->preparePs1();

            $temp = fopen('php://stdin', 'r');

            if ('' === $line = trim(fgets($temp))) {
                continue;
            }

            $lineParts = preg_split('/\s+/', $line);
            $commandStr = array_shift($lineParts);
            $args = $lineParts;

            if (null !== $command = $this->findCommand($commandStr)) {
                // Console command
                try {
                    echo $command->execute($args);
                } catch (CommandExecutionException $e) {
                    echo sprintf("%s: %s\n", $commandStr, $e->getMessage());
                }
            } elseif (null !== $executablePath = $this->findExecutable($commandStr)) {
                //echo "Command found: {$executablePath}. Execute...\n";
                $output = $this->executor->execute($executablePath, $args, getcwd(), $this->config['env']['vars']);
                echo $output;
                // Script or binary to execute
            } else {
                printf("Command \"%s\" not found\n" , trim($commandStr));
            }
        }
    }

    /**
     * Find the absolute path to an
     * @param string $executable
     * @return null|string
     */
    private function findExecutable(string $executable): ?string
    {
        if (false !== $executablePath = realpath(getcwd() . '/' . $executable)) {
            return $executablePath;
        }

        $paths = explode(':', $this->config['env']['vars']['PATH']);

        foreach ($paths as $path) {
            if (false !== $executablePath = realpath($path . '/' . $executable)) {
                return $executablePath;
            }
        }

        return null;
    }

    private function findCommand(string $identifier): ?CommandInterface
    {
        foreach ($this->commands as $command) {
            if ($identifier === $command->getIdentifier()) {
                return $command;
            }
        }

        return null;
    }

    private function preparePs1(): string
    {
        return strtr($this->config['ps1'], [
            '{%CURRENT_DIRECTORY%}' => getcwd()
        ]);
    }

    /**
     * @param $configDirectory
     * @return array
     */
    private function loadConfiguration($configDirectory): array
    {
        $config = [];
        $configFilePath = $configDirectory . DIRECTORY_SEPARATOR . '.oysterrc';

        if (true === file_exists($configFilePath)) {
            if (null === $config = json_decode(file_get_contents($configFilePath), true)) {
                throw new \RuntimeException("You have a syntax error in your .oysterrc file");
            }
        }

        $config += [
            'ps1' => '$ ',
            'env' => [
                'vars' => [
                    'HOME' => $configDirectory,
                    'PATH' => ''
                ]
            ]
        ];

        return $config;
    }
}
