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

class RequirementCollection implements \Countable, \IteratorAggregate {
=======
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Manifest;

use Countable;
use IteratorAggregate;
use function count;

/** @template-implements IteratorAggregate<int,Requirement> */
class RequirementCollection implements Countable, IteratorAggregate {
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    /** @var Requirement[] */
    private $requirements = [];

    public function add(Requirement $requirement): void {
        $this->requirements[] = $requirement;
    }

    /**
     * @return Requirement[]
     */
    public function getRequirements(): array {
        return $this->requirements;
    }

    public function count(): int {
<<<<<<< HEAD
        return \count($this->requirements);
=======
        return count($this->requirements);
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    }

    public function getIterator(): RequirementCollectionIterator {
        return new RequirementCollectionIterator($this);
    }
}
