<?php

declare(strict_types=1);
declare(ticks=1);

namespace Timesplinter\Oyster;

use Timesplinter\Oyster\Command\CommandInterface;
use Timesplinter\Oyster\Command\CommandExecutionException;
use Timesplinter\Oyster\Event\CommandStartEvent;
use Timesplinter\Oyster\Event\CommandStopEvent;
use Timesplinter\Oyster\Event\EventDispatcherInterface;
use Timesplinter\Oyster\Event\ExecutableStartEvent;
use Timesplinter\Oyster\Event\ExecutableStopEvent;
use Timesplinter\Oyster\Event\SignalEvent;
use Timesplinter\Oyster\History\FileHistoryInterface;
use Timesplinter\Oyster\History\ReadlineHistory;
use Timesplinter\Oyster\Helper\OutputColorizer;
use Timesplinter\Oyster\History\HistoryInterface;
use Timesplinter\Oyster\Input\InputInterface;
use Timesplinter\Oyster\OperatingSystemAdapter\OperatingSystemAdapter;
use Timesplinter\Oyster\Output\OutputInterface;
use Timesplinter\Oyster\Plugin\PluginInterface;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
final class Console
{

    /**
     * @var bool
     */
    private $running = false;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var CommandInterface[]|array
     */
    private $commands;

    /**
     * @var ExecutorInterface
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
     * @var ReadlineHistory
     */
    private $history;

    /**
     * @var Runtime
     */
    private $runtime;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array<PluginInterface>
     */
    private $plugins = [];

    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        OperatingSystemAdapter $osAdapter,
        array $commands,
        ExecutorInterface $executor,
        HistoryInterface $history,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->commands = $commands;
        $this->executor = $executor;
        $this->osAdapter = $osAdapter;
        $this->history = $history;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function run(): void
    {
        $this->handleSignals($this->output);

        $this->initializePlugins();
        
        $homeDirectory = $this->osAdapter->getHomeDirectory($this->osAdapter->getCurrentUser());

        $this->setTitle('Oyster 🐚');

        if ($this->history instanceof FileHistoryInterface) {
            $this->history->setFilename($homeDirectory . DIRECTORY_SEPARATOR . '.oyster_history');
        }

        $this->history->loadHistory();

        $this->config = $this->loadConfiguration($homeDirectory);

        $this->runtime = new Runtime($this->config['env']['vars']);
        $this->running = true;

        while (true === $this->running) {
            $temp = $this->input->read($this->preparePs1());

            if ('' === $line = trim($temp)) {
                continue;
            }

            $this->history->addToHistory($line);

            foreach (preg_split('/\s+&&\s+/', $line, -1, PREG_SPLIT_NO_EMPTY) as $command) {
                $commandParts = preg_split('/\s+/', $command, -1, PREG_SPLIT_NO_EMPTY);
                $commandStr = array_shift($commandParts);
                $args = $commandParts;

                if ('exit' === $commandStr) {
                    // Exit the console
                    $this->halt();
                } elseif (null !== $builtinCommand = $this->findBuiltinCommand($commandStr)) {
                    // Console command
                    $this->eventDispatcher->dispatch(new CommandStartEvent());

                    try {
                        $returnCode = $builtinCommand->execute($args, $this->runtime);
                    } catch (CommandExecutionException $e) {
                        $this->output->write(sprintf("%s: %s\n", $commandStr, $e->getMessage()));

                        $returnCode = $e->getCode();
                    }

                    $this->runtime->setEnvVar('?', (string) $returnCode);

                    $this->eventDispatcher->dispatch(new CommandStopEvent());
                } elseif (null !== $executablePath = $this->findExecutable($commandStr)) {
                    // Script or binary to execute
                    $this->eventDispatcher->dispatch(new ExecutableStartEvent());

                    $returnCode =  $this->executor->execute(
                        $executablePath,
                        $args,
                        getcwd(),
                        $this->runtime->getEnvVars()
                    );
                    $this->runtime->setEnvVar('?', (string) $returnCode);

                    $this->eventDispatcher->dispatch(new ExecutableStopEvent());
                } else {
                    $this->output->write(sprintf("oyster: Command \"%s\" not found\n" , trim($commandStr)));
                }
            }
        }

        $this->history->storeHistory();
    }

    public function halt(): void
    {
        $this->running = false;
    }

    public function registerPlugin(PluginInterface $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    private function handleSignals(OutputInterface $output): void
    {
        $signalHandler = function(int $signal) use ($output): void {
            $this->eventDispatcher->dispatch(new SignalEvent($signal));

            switch($signal) {
                case SIGINT:
                case SIGHUP:
                case SIGTERM:
                    // do nothing
                    break;
                default:
                    $output->write(sprintf('Unhandled signal "%d".', $signal));
                    break;
            }
        };

        pcntl_signal(SIGINT,  $signalHandler);
        pcntl_signal(SIGTERM, $signalHandler);
        pcntl_signal(SIGHUP,  $signalHandler);
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

        $paths = explode(':', $this->runtime->getEnvVars()['PATH']);

        foreach ($paths as $path) {
            if (false !== $executablePath = realpath($path . '/' . $executable)) {
                return $executablePath;
            }
        }

        return null;
    }

    /**
     * @param string $identifier
     * @return null|CommandInterface
     */
    private function findBuiltinCommand(string $identifier): ?CommandInterface
    {
        foreach ($this->commands as $command) {
            if ($identifier === $command->getIdentifier()) {
                return $command;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    private function preparePs1(): string
    {
        $ps1 = OutputColorizer::colorize($this->config['ps1']);

        return strtr($ps1, [
            '{%CURRENT_DIRECTORY%}' => $this->getCwd(),
            '{%USER%}' => $this->osAdapter->getCurrentUser(),
            '{%HOST%}' => $this->osAdapter->getHostname(OperatingSystemAdapter::HOSTNAME_SHORT),
            '{%HOST_FULL%}' => $this->osAdapter->getHostname(OperatingSystemAdapter::HOSTNAME_FULL)
        ]);
    }

    private function getCwd(): string
    {
        $cwd = getcwd();
        $homeDirectory = $this->runtime->getEnvVars()['HOME'];

        if (0 === strpos($cwd, $homeDirectory)) {
            $cwd = '~' . substr($cwd, strlen($homeDirectory));
        }

        return $cwd;
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
                throw new \RuntimeException("You have a syntax error in your .oysterrc file\n");
            }
        }

        $config = $this->mergeConfigRecursive([
            'ps1' => '$ ',
            'env' => [
                'vars' => [
                    'HOME' => $configDirectory,
                    'PATH' => '/sbin:/usr/bin:/bin:/usr/sbin'
                ]
            ]
        ], $config);

        return $config;
    }

    /**
     * @param string $title
     */
    private function setTitle(string $title): void
    {
        $this->output->write("\033]0;".$title."\007");
    }

    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private function mergeConfigRecursive(array $array1, array $array2): array
    {
        if (empty($array1)) return $array2; //optimize the base case

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($array1[$key]) && is_array($array1[$key])) {
                $value = $this->mergeConfigRecursive($array1[$key], $value);
            }
            $array1[$key] = $value;
        }
        return $array1;
    }

    private function initializePlugins(): void
    {
        foreach ($this->plugins as $plugin) {
            /** @var PluginInterface $plugin */
            foreach ($plugin->register() as $event => $eventHandler) {
                $this->eventDispatcher->subscribe($event, $eventHandler);
            }
        }
    }
}
