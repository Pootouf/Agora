<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\WebServerBundle\WebServerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new AppBundle\AppBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new AGORA\PlatformBundle\AGORAPlatformBundle(),
            new AGORA\UserBundle\AGORAUserBundle(),
            new AGORA\AdminPlatformBundle\AGORAAdminPlatformBundle(),
            new AGORA\Game\SQPBundle\AGORAGameSQPBundle(),
            new AGORA\Game\AveCesarBundle\AGORAGameAveCesarBundle(),
            new AGORA\Game\GameBundle\AGORAGameGameBundle(),
            new AGORA\Game\AugustusBundle\AugustusBundle(),
            new AGORA\Game\SplendorBundle\AGORAGameSplendorBundle(),
            new AGORA\Game\MorpionBundle\AGORAGameMorpionBundle(),
            new AGORA\Game\AzulBundle\AGORAGameAzulBundle(),
            new AGORA\Game\RRBundle\AGORAGameRRBundle(),
            new AGORA\Game\Puissance4Bundle\AGORAGamePuissance4Bundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();

            if ('dev' === $this->getEnvironment()) {
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            }
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
