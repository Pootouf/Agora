<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\MakerBundle\Security;

<<<<<<< HEAD
=======
use Symfony\Bundle\MakerBundle\Security\Model\Authenticator;
use Symfony\Bundle\MakerBundle\Security\Model\AuthenticatorType;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @internal
 */
final class InteractiveSecurityHelper
{
<<<<<<< HEAD
    public function guessFirewallName(SymfonyStyle $io, array $securityData, string $questionText = null): string
    {
        $realFirewalls = array_filter(
            $securityData['security']['firewalls'] ?? [],
            static function ($item) {
                return !isset($item['security']) || true === $item['security'];
            }
=======
    public function guessFirewallName(SymfonyStyle $io, array $securityData, ?string $questionText = null): string
    {
        $realFirewalls = array_filter(
            $securityData['security']['firewalls'] ?? [],
            static fn ($item) => !isset($item['security']) || true === $item['security']
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
        );

        if (0 === \count($realFirewalls)) {
            return 'main';
        }

        if (1 === \count($realFirewalls)) {
            return key($realFirewalls);
        }

        return $io->choice(
            $questionText ?? 'Which firewall do you want to update?',
            array_keys($realFirewalls),
            key($realFirewalls)
        );
    }

<<<<<<< HEAD
    public function guessUserClass(SymfonyStyle $io, array $providers, string $questionText = null): string
=======
    public function guessUserClass(SymfonyStyle $io, array $providers, ?string $questionText = null): string
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    {
        if (1 === \count($providers) && isset(current($providers)['entity'])) {
            $entityProvider = current($providers);

            return $entityProvider['entity']['class'];
        }

        return $io->ask(
            $questionText ?? 'Enter the User class that you want to authenticate (e.g. <fg=yellow>App\\Entity\\User</>)',
            $this->guessUserClassDefault(),
<<<<<<< HEAD
            [Validator::class, 'classIsUserInterface']
=======
            Validator::classIsUserInterface(...)
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
        );
    }

    private function guessUserClassDefault(): string
    {
        if (class_exists('App\\Entity\\User') && isset(class_implements('App\\Entity\\User')[UserInterface::class])) {
            return 'App\\Entity\\User';
        }

        if (class_exists('App\\Security\\User') && isset(class_implements('App\\Security\\User')[UserInterface::class])) {
            return 'App\\Security\\User';
        }

        return '';
    }

    public function guessUserNameField(SymfonyStyle $io, string $userClass, array $providers): string
    {
        if (1 === \count($providers) && isset(current($providers)['entity']) && isset(current($providers)['entity']['property'])) {
            $entityProvider = current($providers);

            return $entityProvider['entity']['property'];
        }

        if (property_exists($userClass, 'email') && !property_exists($userClass, 'username')) {
            return 'email';
        }

        if (!property_exists($userClass, 'email') && property_exists($userClass, 'username')) {
            return 'username';
        }

        $classProperties = [];
        $reflectionClass = new \ReflectionClass($userClass);
        foreach ($reflectionClass->getProperties() as $property) {
            $classProperties[] = $property->name;
        }

        if (empty($classProperties)) {
            throw new \LogicException(sprintf('No properties were found in "%s" entity', $userClass));
        }

        return $io->choice(
            sprintf('Which field on your <fg=yellow>%s</> class will people enter when logging in?', $userClass),
            $classProperties,
            property_exists($userClass, 'username') ? 'username' : (property_exists($userClass, 'email') ? 'email' : null)
        );
    }

    public function guessEmailField(SymfonyStyle $io, string $userClass): string
    {
        if (property_exists($userClass, 'email')) {
            return 'email';
        }

        $classProperties = [];
        $reflectionClass = new \ReflectionClass($userClass);
        foreach ($reflectionClass->getProperties() as $property) {
            $classProperties[] = $property->name;
        }

        return $io->choice(
            sprintf('Which field on your <fg=yellow>%s</> class holds the email address?', $userClass),
            $classProperties
        );
    }

    public function guessPasswordField(SymfonyStyle $io, string $userClass): string
    {
        if (property_exists($userClass, 'password')) {
            return 'password';
        }

        $classProperties = [];
        $reflectionClass = new \ReflectionClass($userClass);
        foreach ($reflectionClass->getProperties() as $property) {
            $classProperties[] = $property->name;
        }

        return $io->choice(
            sprintf('Which field on your <fg=yellow>%s</> class holds the encoded password?', $userClass),
            $classProperties
        );
    }

<<<<<<< HEAD
    public function getAuthenticatorClasses(array $firewallData): array
    {
        if (isset($firewallData['guard'])) {
            return array_filter($firewallData['guard']['authenticators'] ?? [], static function ($authenticator) {
                return class_exists($authenticator);
            });
        }

        if (isset($firewallData['custom_authenticator'])) {
            $authenticators = $firewallData['custom_authenticator'];
            if (\is_string($authenticators)) {
                $authenticators = [$authenticators];
            }

            return array_filter($authenticators, static function ($authenticator) {
                return class_exists($authenticator);
            });
        }

        return [];
    }

=======
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    public function guessPasswordSetter(SymfonyStyle $io, string $userClass): string
    {
        if (null === ($methodChoices = $this->methodNameGuesser($userClass, 'setPassword'))) {
            return 'setPassword';
        }

        return $io->choice(
            sprintf('Which method on your <fg=yellow>%s</> class can be used to set the encoded password (e.g. setPassword())?', $userClass),
            $methodChoices
        );
    }

    public function guessEmailGetter(SymfonyStyle $io, string $userClass, string $emailPropertyName): string
    {
        $supposedEmailMethodName = sprintf('get%s', Str::asCamelCase($emailPropertyName));

        if (null === ($methodChoices = $this->methodNameGuesser($userClass, $supposedEmailMethodName))) {
            return $supposedEmailMethodName;
        }

        return $io->choice(
            sprintf('Which method on your <fg=yellow>%s</> class can be used to get the email address (e.g. getEmail())?', $userClass),
            $methodChoices
        );
    }

    public function guessIdGetter(SymfonyStyle $io, string $userClass): string
    {
        if (null === ($methodChoices = $this->methodNameGuesser($userClass, 'getId'))) {
            return 'getId';
        }

        return $io->choice(
            sprintf('Which method on your <fg=yellow>%s</> class can be used to get the unique user identifier (e.g. getId())?', $userClass),
            $methodChoices
        );
    }

<<<<<<< HEAD
=======
    /**
     * @param array<string, array<string, mixed>> $firewalls Config data from security.firewalls
     *
     * @return Authenticator[]
     */
    public function getAuthenticatorsFromConfig(array $firewalls): array
    {
        $authenticators = [];

        /* Iterate over each firewall that exists e.g. security.firewalls.main
         * $firewallName could be "main" or "dev", etc...
         * $firewallConfig should be an array of the firewalls params
         */
        foreach ($firewalls as $firewallName => $firewallConfig) {
            if (!\is_array($firewallConfig)) {
                continue;
            }

            $authenticators = [
                ...$authenticators,
                ...$this->getAuthenticatorsFromConfigData($firewallConfig, $firewallName),
            ];
        }

        return $authenticators;
    }

    /**
     * Pass in a firewalls config e.g. security.firewalls.main like:
     *      pattern: ^/path
     *      form_login:
     *          login_path: app_login
     *      custom_authenticator:
     *          - App\Security\MyAuthenticator
     *
     * @param array<string, mixed> $firewallConfig
     *
     * @return Authenticator[]
     */
    private function getAuthenticatorsFromConfigData(array $firewallConfig, string $firewallName): array
    {
        $authenticators = [];

        foreach ($firewallConfig as $potentialAuthenticator => $configData) {
            // Check if $potentialAuthenticator is a supported authenticator or if its some other key.
            if (null === ($authenticator = AuthenticatorType::tryFrom($potentialAuthenticator))) {
                // $potentialAuthenticator is probably something like "pattern" or "lazy", not an authenticator
                continue;
            }

            // $potentialAuthenticator is a supported authenticator. Check if it's a custom_authenticator.
            if (AuthenticatorType::CUSTOM !== $authenticator) {
                // We found a "built in" authenticator - "form_login", "json_login", etc...
                $authenticators[] = new Authenticator($authenticator, $firewallName);

                continue;
            }

            /*
             * $potentialAuthenticator = custom_authenticator.
             * $configData is either [App\MyAuthenticator] or (string) App\MyAuthenticator
             */
            $customAuthenticators = $this->getCustomAuthenticators($configData, $firewallName);

            $authenticators = [...$authenticators, ...$customAuthenticators];
        }

        return $authenticators;
    }

    /**
     * @param string|array<string> $customAuthenticators A single entry from custom_authenticators or an array of authenticators
     *
     * @return Authenticator[]
     */
    private function getCustomAuthenticators(string|array $customAuthenticators, string $firewallName): array
    {
        if (\is_string($customAuthenticators)) {
            $customAuthenticators = [$customAuthenticators];
        }

        $authenticators = [];

        foreach ($customAuthenticators as $customAuthenticatorClass) {
            $authenticators[] = new Authenticator(AuthenticatorType::CUSTOM, $firewallName, $customAuthenticatorClass);
        }

        return $authenticators;
    }

>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    private function methodNameGuesser(string $className, string $suspectedMethodName): ?array
    {
        $reflectionClass = new \ReflectionClass($className);

        if ($reflectionClass->hasMethod($suspectedMethodName)) {
            return null;
        }

        $classMethods = [];

        foreach ($reflectionClass->getMethods() as $method) {
            $classMethods[] = $method->name;
        }

        return $classMethods;
    }
}
