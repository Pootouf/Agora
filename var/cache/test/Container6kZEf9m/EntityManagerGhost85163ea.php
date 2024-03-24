<?php

namespace Container6kZEf9m;
include_once \dirname(__DIR__, 4).'/vendor/doctrine/persistence/src/Persistence/ObjectManager.php';
include_once \dirname(__DIR__, 4).'/vendor/doctrine/orm/src/EntityManagerInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/doctrine/orm/src/EntityManager.php';

class EntityManagerGhost85163ea extends \Doctrine\ORM\EntityManager implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".parent::class."\0".'cache' => [parent::class, 'cache', null],
        "\0".parent::class."\0".'closed' => [parent::class, 'closed', null],
        "\0".parent::class."\0".'config' => [parent::class, 'config', parent::class],
        "\0".parent::class."\0".'conn' => [parent::class, 'conn', parent::class],
        "\0".parent::class."\0".'eventManager' => [parent::class, 'eventManager', parent::class],
        "\0".parent::class."\0".'expressionBuilder' => [parent::class, 'expressionBuilder', null],
        "\0".parent::class."\0".'filterCollection' => [parent::class, 'filterCollection', null],
        "\0".parent::class."\0".'metadataFactory' => [parent::class, 'metadataFactory', parent::class],
        "\0".parent::class."\0".'proxyFactory' => [parent::class, 'proxyFactory', parent::class],
        "\0".parent::class."\0".'repositoryFactory' => [parent::class, 'repositoryFactory', parent::class],
        "\0".parent::class."\0".'unitOfWork' => [parent::class, 'unitOfWork', parent::class],
        'cache' => [parent::class, 'cache', null],
        'closed' => [parent::class, 'closed', null],
        'config' => [parent::class, 'config', parent::class],
        'conn' => [parent::class, 'conn', parent::class],
        'eventManager' => [parent::class, 'eventManager', parent::class],
        'expressionBuilder' => [parent::class, 'expressionBuilder', null],
        'filterCollection' => [parent::class, 'filterCollection', null],
        'metadataFactory' => [parent::class, 'metadataFactory', parent::class],
        'proxyFactory' => [parent::class, 'proxyFactory', parent::class],
        'repositoryFactory' => [parent::class, 'repositoryFactory', parent::class],
        'unitOfWork' => [parent::class, 'unitOfWork', parent::class],
    ];
}

// Help opcache.preload discover always-needed symbols
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('EntityManagerGhost85163ea', false)) {
    \class_alias(__NAMESPACE__.'\\EntityManagerGhost85163ea', 'EntityManagerGhost85163ea', false);
}
