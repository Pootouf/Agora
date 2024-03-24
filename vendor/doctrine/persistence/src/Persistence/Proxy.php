<?php

declare(strict_types=1);

namespace Doctrine\Persistence;

/**
 * Interface for proxy classes.
 *
 * @template T of object
<<<<<<< HEAD
 * @method void __setInitialized(bool $initialized) Implementing this method will be mandatory in version 4.
=======
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
 */
interface Proxy
{
    /**
     * Marker for Proxy class names.
     */
    public const MARKER = '__CG__';

    /**
     * Length of the proxy marker.
     */
    public const MARKER_LENGTH = 6;

    /**
     * Initializes this proxy if its not yet initialized.
     *
     * Acts as a no-op if already initialized.
     *
     * @return void
     */
    public function __load();

    /**
     * Returns whether this proxy is initialized or not.
     *
     * @return bool
     */
    public function __isInitialized();
}