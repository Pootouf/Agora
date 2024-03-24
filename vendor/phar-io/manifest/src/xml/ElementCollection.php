<?php declare(strict_types = 1);
/*
 * This file is part of PharIo\Manifest.
 *
<<<<<<< HEAD
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
=======
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
 */
namespace PharIo\Manifest;

use DOMElement;
use DOMNodeList;
<<<<<<< HEAD

abstract class ElementCollection implements \Iterator {
=======
use Iterator;
use ReturnTypeWillChange;
use function count;
use function get_class;
use function sprintf;

/** @template-implements Iterator<int,DOMElement> */
abstract class ElementCollection implements Iterator {
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    /** @var DOMElement[] */
    private $nodes = [];

    /** @var int */
    private $position;

    public function __construct(DOMNodeList $nodeList) {
        $this->position = 0;
        $this->importNodes($nodeList);
    }

<<<<<<< HEAD
    #[\ReturnTypeWillChange]
=======
    #[ReturnTypeWillChange]
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    abstract public function current();

    public function next(): void {
        $this->position++;
    }

    public function key(): int {
        return $this->position;
    }

    public function valid(): bool {
<<<<<<< HEAD
        return $this->position < \count($this->nodes);
=======
        return $this->position < count($this->nodes);
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    }

    public function rewind(): void {
        $this->position = 0;
    }

    protected function getCurrentElement(): DOMElement {
        return $this->nodes[$this->position];
    }

    private function importNodes(DOMNodeList $nodeList): void {
        foreach ($nodeList as $node) {
            if (!$node instanceof DOMElement) {
                throw new ElementCollectionException(
<<<<<<< HEAD
                    \sprintf('\DOMElement expected, got \%s', \get_class($node))
=======
                    sprintf('\DOMElement expected, got \%s', get_class($node))
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
                );
            }

            $this->nodes[] = $node;
        }
    }
}
