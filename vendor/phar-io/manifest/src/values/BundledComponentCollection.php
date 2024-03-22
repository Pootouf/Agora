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

class BundledComponentCollection implements \Countable, \IteratorAggregate {
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

/** @template-implements IteratorAggregate<int,BundledComponent> */
class BundledComponentCollection implements Countable, IteratorAggregate {
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    /** @var BundledComponent[] */
    private $bundledComponents = [];

    public function add(BundledComponent $bundledComponent): void {
        $this->bundledComponents[] = $bundledComponent;
    }

    /**
     * @return BundledComponent[]
     */
    public function getBundledComponents(): array {
        return $this->bundledComponents;
    }

    public function count(): int {
<<<<<<< HEAD
        return \count($this->bundledComponents);
=======
        return count($this->bundledComponents);
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    }

    public function getIterator(): BundledComponentCollectionIterator {
        return new BundledComponentCollectionIterator($this);
    }
}
