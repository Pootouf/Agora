<?php

namespace Symfony\Config\LiipTestFixtures;

use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class CacheDbConfig 
{
    private $sqlite;
    private $_usedProperties = [];
    private $_extraKeys;

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function sqlite($value): static
    {
        $this->_usedProperties['sqlite'] = true;
        $this->sqlite = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('sqlite', $value)) {
            $this->_usedProperties['sqlite'] = true;
            $this->sqlite = $value['sqlite'];
            unset($value['sqlite']);
        }

        $this->_extraKeys = $value;

    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['sqlite'])) {
            $output['sqlite'] = $this->sqlite;
        }

        return $output + $this->_extraKeys;
    }

    /**
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function set(string $key, mixed $value): static
    {
        $this->_extraKeys[$key] = $value;

        return $this;
    }

}
