<?php

namespace Symfony\Config;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DamaDoctrineTestConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $enableStaticConnection;
    private $enableStaticMetaDataCache;
    private $enableStaticQueryCache;
    private $_usedProperties = [];

    /**
     * @default true
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function enableStaticConnection(mixed $value = true): static
    {
        $this->_usedProperties['enableStaticConnection'] = true;
        $this->enableStaticConnection = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enableStaticMetaDataCache($value): static
    {
        $this->_usedProperties['enableStaticMetaDataCache'] = true;
        $this->enableStaticMetaDataCache = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enableStaticQueryCache($value): static
    {
        $this->_usedProperties['enableStaticQueryCache'] = true;
        $this->enableStaticQueryCache = $value;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'dama_doctrine_test';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enable_static_connection', $value)) {
            $this->_usedProperties['enableStaticConnection'] = true;
            $this->enableStaticConnection = $value['enable_static_connection'];
            unset($value['enable_static_connection']);
        }

        if (array_key_exists('enable_static_meta_data_cache', $value)) {
            $this->_usedProperties['enableStaticMetaDataCache'] = true;
            $this->enableStaticMetaDataCache = $value['enable_static_meta_data_cache'];
            unset($value['enable_static_meta_data_cache']);
        }

        if (array_key_exists('enable_static_query_cache', $value)) {
            $this->_usedProperties['enableStaticQueryCache'] = true;
            $this->enableStaticQueryCache = $value['enable_static_query_cache'];
            unset($value['enable_static_query_cache']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['enableStaticConnection'])) {
            $output['enable_static_connection'] = $this->enableStaticConnection;
        }
        if (isset($this->_usedProperties['enableStaticMetaDataCache'])) {
            $output['enable_static_meta_data_cache'] = $this->enableStaticMetaDataCache;
        }
        if (isset($this->_usedProperties['enableStaticQueryCache'])) {
            $output['enable_static_query_cache'] = $this->enableStaticQueryCache;
        }

        return $output;
    }

}
