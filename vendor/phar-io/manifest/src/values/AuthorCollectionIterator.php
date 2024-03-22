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

class AuthorCollectionIterator implements \Iterator {
=======
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Manifest;

use Iterator;
use function count;

/** @template-implements Iterator<int,Author> */
class AuthorCollectionIterator implements Iterator {
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    /** @var Author[] */
    private $authors;

    /** @var int */
    private $position = 0;

    public function __construct(AuthorCollection $authors) {
        $this->authors = $authors->getAuthors();
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function valid(): bool {
<<<<<<< HEAD
        return $this->position < \count($this->authors);
=======
        return $this->position < count($this->authors);
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    }

    public function key(): int {
        return $this->position;
    }

    public function current(): Author {
        return $this->authors[$this->position];
    }

    public function next(): void {
        $this->position++;
    }
}
