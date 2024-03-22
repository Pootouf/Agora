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

class RequirementCollectionIterator implements \Iterator {
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

/** @template-implements Iterator<int,Requirement> */
class RequirementCollectionIterator implements Iterator {
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    /** @var Requirement[] */
    private $requirements;

    /** @var int */
    private $position = 0;

    public function __construct(RequirementCollection $requirements) {
        $this->requirements = $requirements->getRequirements();
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function valid(): bool {
<<<<<<< HEAD
        return $this->position < \count($this->requirements);
=======
        return $this->position < count($this->requirements);
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    }

    public function key(): int {
        return $this->position;
    }

    public function current(): Requirement {
        return $this->requirements[$this->position];
    }

    public function next(): void {
        $this->position++;
    }
}
