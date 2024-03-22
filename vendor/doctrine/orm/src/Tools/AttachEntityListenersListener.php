<?php

declare(strict_types=1);

namespace Doctrine\ORM\Tools;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
<<<<<<< HEAD
use Doctrine\ORM\Mapping\Builder\EntityListenerBuilder;

=======
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\EntityListenerBuilder;

use function assert;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
use function ltrim;

/**
 * Mechanism to programmatically attach entity listeners.
 */
class AttachEntityListenersListener
{
<<<<<<< HEAD
    /** @var mixed[][] */
=======
    /**
     * @var array<class-string, list<array{
     *     event: Events::*|null,
     *     class: class-string,
     *     method: string|null,
     * }>>
     */
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    private array $entityListeners = [];

    /**
     * Adds an entity listener for a specific entity.
     *
<<<<<<< HEAD
     * @param string      $entityClass      The entity to attach the listener.
     * @param string      $listenerClass    The listener class.
     * @param string|null $eventName        The entity lifecycle event.
     * @param string|null $listenerCallback The listener callback method or NULL to use $eventName.
=======
     * @param class-string          $entityClass      The entity to attach the listener.
     * @param class-string          $listenerClass    The listener class.
     * @param Events::*|null        $eventName        The entity lifecycle event.
     * @param non-falsy-string|null $listenerCallback The listener callback method or NULL to use $eventName.
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
     */
    public function addEntityListener(
        string $entityClass,
        string $listenerClass,
        string|null $eventName = null,
        string|null $listenerCallback = null,
    ): void {
        $this->entityListeners[ltrim($entityClass, '\\')][] = [
            'event'  => $eventName,
            'class'  => $listenerClass,
<<<<<<< HEAD
            'method' => $listenerCallback ?: $eventName,
=======
            'method' => $listenerCallback ?? $eventName,
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
        ];
    }

    /**
     * Processes event and attach the entity listener.
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $metadata = $event->getClassMetadata();

        if (! isset($this->entityListeners[$metadata->name])) {
            return;
        }

        foreach ($this->entityListeners[$metadata->name] as $listener) {
            if ($listener['event'] === null) {
                EntityListenerBuilder::bindEntityListener($metadata, $listener['class']);
            } else {
<<<<<<< HEAD
=======
                assert($listener['method'] !== null);
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
                $metadata->addEntityListener($listener['event'], $listener['class'], $listener['method']);
            }
        }
    }
}
