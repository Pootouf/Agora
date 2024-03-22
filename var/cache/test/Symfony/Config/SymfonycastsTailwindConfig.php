<?php

namespace Symfony\Config;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class SymfonycastsTailwindConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $inputCss;
    private $configFile;
    private $binary;
    private $binaryVersion;
    private $_usedProperties = [];

    /**
     * Path to CSS file to process through Tailwind
     * @default '%kernel.project_dir%/assets/styles/app.css'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function inputCss($value): static
    {
        $this->_usedProperties['inputCss'] = true;
        $this->inputCss = $value;

        return $this;
    }

    /**
     * Path to the tailwind.config.js file
     * @default '%kernel.project_dir%/tailwind.config.js'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function configFile($value): static
    {
        $this->_usedProperties['configFile'] = true;
        $this->configFile = $value;

        return $this;
    }

    /**
     * The tailwind binary to use instead of downloading a new one
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function binary($value): static
    {
        $this->_usedProperties['binary'] = true;
        $this->binary = $value;

        return $this;
    }

    /**
     * Tailwind CLI version to download - null means the latest version
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function binaryVersion($value): static
    {
        $this->_usedProperties['binaryVersion'] = true;
        $this->binaryVersion = $value;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'symfonycasts_tailwind';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('input_css', $value)) {
            $this->_usedProperties['inputCss'] = true;
            $this->inputCss = $value['input_css'];
            unset($value['input_css']);
        }

        if (array_key_exists('config_file', $value)) {
            $this->_usedProperties['configFile'] = true;
            $this->configFile = $value['config_file'];
            unset($value['config_file']);
        }

        if (array_key_exists('binary', $value)) {
            $this->_usedProperties['binary'] = true;
            $this->binary = $value['binary'];
            unset($value['binary']);
        }

        if (array_key_exists('binary_version', $value)) {
            $this->_usedProperties['binaryVersion'] = true;
            $this->binaryVersion = $value['binary_version'];
            unset($value['binary_version']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['inputCss'])) {
            $output['input_css'] = $this->inputCss;
        }
        if (isset($this->_usedProperties['configFile'])) {
            $output['config_file'] = $this->configFile;
        }
        if (isset($this->_usedProperties['binary'])) {
            $output['binary'] = $this->binary;
        }
        if (isset($this->_usedProperties['binaryVersion'])) {
            $output['binary_version'] = $this->binaryVersion;
        }

        return $output;
    }

}
