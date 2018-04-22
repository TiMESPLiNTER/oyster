<?php

declare(strict_types=1);

namespace Timesplinter\Oyster;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class Runtime
{

    /**
     * @var array
     */
    private $envVars = [];

    /**
     * Runtime constructor.
     * @param array $envVars
     */
    public function __construct(array $envVars)
    {
        $this->envVars = $envVars;
    }

    /**
     * @return array
     */
    public function getEnvVars(): array
    {
        return $this->envVars;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setEnvVar(string $name, string $value): void
    {
        $this->envVars[$name] = $value;
    }
}
