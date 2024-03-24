<?php

namespace Symfony\Config\Mercure\HubConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class JwtConfig 
{
    private $value;
    private $provider;
    private $factory;
    private $publish;
    private $subscribe;
    private $secret;
    private $passphrase;
    private $algorithm;
    private $_usedProperties = [];

    /**
     * JSON Web Token to use to publish to this hub.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function value($value): static
    {
        $this->_usedProperties['value'] = true;
        $this->value = $value;

        return $this;
    }

    /**
     * The ID of a service to call to provide the JSON Web Token.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function provider($value): static
    {
        $this->_usedProperties['provider'] = true;
        $this->provider = $value;

        return $this;
    }

    /**
     * The ID of a service to call to create the JSON Web Token.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function factory($value): static
    {
        $this->_usedProperties['factory'] = true;
        $this->factory = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed>|mixed $value
     *
     * @return $this
     */
    public function publish(mixed $value): static
    {
        $this->_usedProperties['publish'] = true;
        $this->publish = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed>|mixed $value
     *
     * @return $this
     */
    public function subscribe(mixed $value): static
    {
        $this->_usedProperties['subscribe'] = true;
        $this->subscribe = $value;

        return $this;
    }

    /**
     * The JWT Secret to use.
     * @example !ChangeMe!
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function secret($value): static
    {
        $this->_usedProperties['secret'] = true;
        $this->secret = $value;

        return $this;
    }

    /**
     * The JWT secret passphrase.
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function passphrase($value): static
    {
        $this->_usedProperties['passphrase'] = true;
        $this->passphrase = $value;

        return $this;
    }

    /**
     * The algorithm to use to sign the JWT
     * @default 'hmac.sha256'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function algorithm($value): static
    {
        $this->_usedProperties['algorithm'] = true;
        $this->algorithm = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('value', $value)) {
            $this->_usedProperties['value'] = true;
            $this->value = $value['value'];
            unset($value['value']);
        }

        if (array_key_exists('provider', $value)) {
            $this->_usedProperties['provider'] = true;
            $this->provider = $value['provider'];
            unset($value['provider']);
        }

        if (array_key_exists('factory', $value)) {
            $this->_usedProperties['factory'] = true;
            $this->factory = $value['factory'];
            unset($value['factory']);
        }

        if (array_key_exists('publish', $value)) {
            $this->_usedProperties['publish'] = true;
            $this->publish = $value['publish'];
            unset($value['publish']);
        }

        if (array_key_exists('subscribe', $value)) {
            $this->_usedProperties['subscribe'] = true;
            $this->subscribe = $value['subscribe'];
            unset($value['subscribe']);
        }

        if (array_key_exists('secret', $value)) {
            $this->_usedProperties['secret'] = true;
            $this->secret = $value['secret'];
            unset($value['secret']);
        }

        if (array_key_exists('passphrase', $value)) {
            $this->_usedProperties['passphrase'] = true;
            $this->passphrase = $value['passphrase'];
            unset($value['passphrase']);
        }

        if (array_key_exists('algorithm', $value)) {
            $this->_usedProperties['algorithm'] = true;
            $this->algorithm = $value['algorithm'];
            unset($value['algorithm']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['value'])) {
            $output['value'] = $this->value;
        }
        if (isset($this->_usedProperties['provider'])) {
            $output['provider'] = $this->provider;
        }
        if (isset($this->_usedProperties['factory'])) {
            $output['factory'] = $this->factory;
        }
        if (isset($this->_usedProperties['publish'])) {
            $output['publish'] = $this->publish;
        }
        if (isset($this->_usedProperties['subscribe'])) {
            $output['subscribe'] = $this->subscribe;
        }
        if (isset($this->_usedProperties['secret'])) {
            $output['secret'] = $this->secret;
        }
        if (isset($this->_usedProperties['passphrase'])) {
            $output['passphrase'] = $this->passphrase;
        }
        if (isset($this->_usedProperties['algorithm'])) {
            $output['algorithm'] = $this->algorithm;
        }

        return $output;
    }

}
