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
class Author {
    /** @var string */
    private $name;

<<<<<<< HEAD
    /** @var Email */
    private $email;

    public function __construct(string $name, Email $email) {
=======
    /** @var null|Email */
    private $email;

    public function __construct(string $name, ?Email $email = null) {
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
        $this->name  = $name;
        $this->email = $email;
    }

    public function asString(): string {
<<<<<<< HEAD
        return \sprintf(
=======
        if (!$this->hasEmail()) {
            return $this->name;
        }

        return sprintf(
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
            '%s <%s>',
            $this->name,
            $this->email->asString()
        );
    }

    public function getName(): string {
        return $this->name;
    }

<<<<<<< HEAD
    public function getEmail(): Email {
=======
    /**
     * @psalm-assert-if-true Email $this->email
     */
    public function hasEmail(): bool {
        return $this->email !== null;
    }

    public function getEmail(): Email {
        if (!$this->hasEmail()) {
            throw new NoEmailAddressException();
        }

>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
        return $this->email;
    }
}
