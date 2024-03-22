<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'LiipTestFixtures'.\DIRECTORY_SEPARATOR.'CacheDbConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class LiipTestFixturesConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $cacheDb;
    private $keepDatabaseAndSchema;
    private $cacheMetadata;
    private $_usedProperties = [];

    /**
     * @default {"sqlite":null}
    */
    public function cacheDb(array $value = []): \Symfony\Config\LiipTestFixtures\CacheDbConfig
    {
        if (null === $this->cacheDb) {
            $this->_usedProperties['cacheDb'] = true;
            $this->cacheDb = new \Symfony\Config\LiipTestFixtures\CacheDbConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "cacheDb()" has already been initialized. You cannot pass values the second time you call cacheDb().');
        }

        return $this->cacheDb;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function keepDatabaseAndSchema($value): static
    {
        $this->_usedProperties['keepDatabaseAndSchema'] = true;
        $this->keepDatabaseAndSchema = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function cacheMetadata($value): static
    {
        $this->_usedProperties['cacheMetadata'] = true;
        $this->cacheMetadata = $value;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'liip_test_fixtures';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('cache_db', $value)) {
            $this->_usedProperties['cacheDb'] = true;
            $this->cacheDb = new \Symfony\Config\LiipTestFixtures\CacheDbConfig($value['cache_db']);
            unset($value['cache_db']);
        }

        if (array_key_exists('keep_database_and_schema', $value)) {
            $this->_usedProperties['keepDatabaseAndSchema'] = true;
            $this->keepDatabaseAndSchema = $value['keep_database_and_schema'];
            unset($value['keep_database_and_schema']);
        }

        if (array_key_exists('cache_metadata', $value)) {
            $this->_usedProperties['cacheMetadata'] = true;
            $this->cacheMetadata = $value['cache_metadata'];
            unset($value['cache_metadata']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['cacheDb'])) {
            $output['cache_db'] = $this->cacheDb->toArray();
        }
        if (isset($this->_usedProperties['keepDatabaseAndSchema'])) {
            $output['keep_database_and_schema'] = $this->keepDatabaseAndSchema;
        }
        if (isset($this->_usedProperties['cacheMetadata'])) {
            $output['cache_metadata'] = $this->cacheMetadata;
        }

        return $output;
    }

}
