<?php declare(strict_types = 1);
/*
 * This file is part of PharIo\Manifest.
 *
<<<<<<< HEAD
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PharIo\Manifest;

=======
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Manifest;

use function sprintf;

>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
class ManifestLoader {
    public static function fromFile(string $filename): Manifest {
        try {
            return (new ManifestDocumentMapper())->map(
                ManifestDocument::fromFile($filename)
            );
        } catch (Exception $e) {
            throw new ManifestLoaderException(
<<<<<<< HEAD
                \sprintf('Loading %s failed.', $filename),
=======
                sprintf('Loading %s failed.', $filename),
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
                (int)$e->getCode(),
                $e
            );
        }
    }

    public static function fromPhar(string $filename): Manifest {
        return self::fromFile('phar://' . $filename . '/manifest.xml');
    }

    public static function fromString(string $manifest): Manifest {
        try {
            return (new ManifestDocumentMapper())->map(
                ManifestDocument::fromString($manifest)
            );
        } catch (Exception $e) {
            throw new ManifestLoaderException(
                'Processing string failed',
                (int)$e->getCode(),
                $e
            );
        }
    }
}
