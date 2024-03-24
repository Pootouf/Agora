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

use const FILTER_VALIDATE_EMAIL;
use function filter_var;

>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
class Email {
    /** @var string */
    private $email;

    public function __construct(string $email) {
        $this->ensureEmailIsValid($email);

        $this->email = $email;
    }

    public function asString(): string {
        return $this->email;
    }

    private function ensureEmailIsValid(string $url): void {
<<<<<<< HEAD
        if (\filter_var($url, \FILTER_VALIDATE_EMAIL) === false) {
=======
        if (filter_var($url, FILTER_VALIDATE_EMAIL) === false) {
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
            throw new InvalidEmailException;
        }
    }
}
