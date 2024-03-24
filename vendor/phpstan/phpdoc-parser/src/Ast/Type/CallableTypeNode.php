<?php declare(strict_types = 1);

namespace PHPStan\PhpDocParser\Ast\Type;

use PHPStan\PhpDocParser\Ast\NodeAttributes;
<<<<<<< HEAD
=======
use PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
use function implode;

class CallableTypeNode implements TypeNode
{

	use NodeAttributes;

	/** @var IdentifierTypeNode */
	public $identifier;

<<<<<<< HEAD
=======
	/** @var TemplateTagValueNode[] */
	public $templateTypes;

>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
	/** @var CallableTypeParameterNode[] */
	public $parameters;

	/** @var TypeNode */
	public $returnType;

	/**
	 * @param CallableTypeParameterNode[] $parameters
<<<<<<< HEAD
	 */
	public function __construct(IdentifierTypeNode $identifier, array $parameters, TypeNode $returnType)
=======
	 * @param TemplateTagValueNode[]  $templateTypes
	 */
	public function __construct(IdentifierTypeNode $identifier, array $parameters, TypeNode $returnType, array $templateTypes = [])
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
	{
		$this->identifier = $identifier;
		$this->parameters = $parameters;
		$this->returnType = $returnType;
<<<<<<< HEAD
=======
		$this->templateTypes = $templateTypes;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
	}


	public function __toString(): string
	{
		$returnType = $this->returnType;
		if ($returnType instanceof self) {
			$returnType = "({$returnType})";
		}
<<<<<<< HEAD
		$parameters = implode(', ', $this->parameters);
		return "{$this->identifier}({$parameters}): {$returnType}";
=======
		$template = $this->templateTypes !== []
			? '<' . implode(', ', $this->templateTypes) . '>'
			: '';
		$parameters = implode(', ', $this->parameters);
		return "{$this->identifier}{$template}({$parameters}): {$returnType}";
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
	}

}
