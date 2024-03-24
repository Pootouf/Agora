<?php

namespace Symfony\Config\Mercure;

require_once __DIR__.\DIRECTORY_SEPARATOR.'HubConfig'.\DIRECTORY_SEPARATOR.'JwtConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class HubConfig 
{
    private $url;
    private $publicUrl;
    private $jwt;
    private $jwtProvider;
    private $bus;
    private $_usedProperties = [];

    /**
     * URL of the hub's publish endpoint
     * @example https://demo.mercure.rocks/.well-known/mercure
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function url($value): static
    {
        $this->_usedProperties['url'] = true;
        $this->url = $value;

        return $this;
    }

    /**
     * URL of the hub's public endpoint
     * @example https://demo.mercure.rocks/.well-known/mercure
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function publicUrl($value): static
    {
        $this->_usedProperties['publicUrl'] = true;
        $this->publicUrl = $value;

        return $this;
    }

    /**
     * @template TValue
     * @param TValue $value
     * JSON Web Token configuration.
     * @return \Symfony\Config\Mercure\HubConfig\JwtConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\Mercure\HubConfig\JwtConfig : static)
     */
    public function jwt(string|array $value = []): \Symfony\Config\Mercure\HubConfig\JwtConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['jwt'] = true;
            $this->jwt = $value;

            return $this;
        }

        if (!$this->jwt instanceof \Symfony\Config\Mercure\HubConfig\JwtConfig) {
            $this->_usedProperties['jwt'] = true;
            $this->jwt = new \Symfony\Config\Mercure\HubConfig\JwtConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "jwt()" has already been initialized. You cannot pass values the second time you call jwt().');
        }

        return $this->jwt;
    }

    /**
     * The ID of a service to call to generate the JSON Web Token.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @deprecated The child node "jwt_provider" at path "" is deprecated, use "jwt.provider" instead.
     * @return $this
     */
    public function jwtProvider($value): static
    {
        $this->_usedProperties['jwtProvider'] = true;
        $this->jwtProvider = $value;

        return $this;
    }

    /**
     * Name of the Messenger bus where the handler for this hub must be registered. Default to the default bus if Messenger is enabled.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function bus($value): static
    {
        $this->_usedProperties['bus'] = true;
        $this->bus = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('url', $value)) {
            $this->_usedProperties['url'] = true;
            $this->url = $value['url'];
            unset($value['url']);
        }

        if (array_key_exists('public_url', $value)) {
            $this->_usedProperties['publicUrl'] = true;
            $this->publicUrl = $value['public_url'];
            unset($value['public_url']);
        }

        if (array_key_exists('jwt', $value)) {
            $this->_usedProperties['jwt'] = true;
            $this->jwt = \is_array($value['jwt']) ? new \Symfony\Config\Mercure\HubConfig\JwtConfig($value['jwt']) : $value['jwt'];
            unset($value['jwt']);
        }

        if (array_key_exists('jwt_provider', $value)) {
            $this->_usedProperties['jwtProvider'] = true;
            $this->jwtProvider = $value['jwt_provider'];
            unset($value['jwt_provider']);
        }

        if (array_key_exists('bus', $value)) {
            $this->_usedProperties['bus'] = true;
            $this->bus = $value['bus'];
            unset($value['bus']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['url'])) {
            $output['url'] = $this->url;
        }
        if (isset($this->_usedProperties['publicUrl'])) {
            $output['public_url'] = $this->publicUrl;
        }
        if (isset($this->_usedProperties['jwt'])) {
            $output['jwt'] = $this->jwt instanceof \Symfony\Config\Mercure\HubConfig\JwtConfig ? $this->jwt->toArray() : $this->jwt;
        }
        if (isset($this->_usedProperties['jwtProvider'])) {
            $output['jwt_provider'] = $this->jwtProvider;
        }
        if (isset($this->_usedProperties['bus'])) {
            $output['bus'] = $this->bus;
        }

        return $output;
    }

}
