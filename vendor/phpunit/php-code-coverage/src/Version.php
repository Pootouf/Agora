<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage;

use function dirname;
use SebastianBergmann\Version as VersionId;

final class Version
{
    /**
     * @var string
     */
    private static $version;

    public static function id(): string
    {
        if (self::$version === null) {
<<<<<<< HEAD
            self::$version = (new VersionId('9.2.30', dirname(__DIR__)))->getVersion();
=======
            self::$version = (new VersionId('9.2.31', dirname(__DIR__)))->getVersion();
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
        }

        return self::$version;
    }
}
