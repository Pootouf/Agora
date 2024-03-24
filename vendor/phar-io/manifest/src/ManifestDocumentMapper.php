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

use PharIo\Version\Exception as VersionException;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;
<<<<<<< HEAD
=======
use Throwable;
use function sprintf;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939

class ManifestDocumentMapper {
    public function map(ManifestDocument $document): Manifest {
        try {
            $contains          = $document->getContainsElement();
            $type              = $this->mapType($contains);
            $copyright         = $this->mapCopyright($document->getCopyrightElement());
            $requirements      = $this->mapRequirements($document->getRequiresElement());
            $bundledComponents = $this->mapBundledComponents($document);

            return new Manifest(
                new ApplicationName($contains->getName()),
                new Version($contains->getVersion()),
                $type,
                $copyright,
                $requirements,
                $bundledComponents
            );
<<<<<<< HEAD
        } catch (VersionException $e) {
            throw new ManifestDocumentMapperException($e->getMessage(), (int)$e->getCode(), $e);
        } catch (Exception $e) {
=======
        } catch (Throwable $e) {
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
            throw new ManifestDocumentMapperException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    private function mapType(ContainsElement $contains): Type {
        switch ($contains->getType()) {
            case 'application':
                return Type::application();
            case 'library':
                return Type::library();
            case 'extension':
                return $this->mapExtension($contains->getExtensionElement());
        }

        throw new ManifestDocumentMapperException(
<<<<<<< HEAD
            \sprintf('Unsupported type %s', $contains->getType())
=======
            sprintf('Unsupported type %s', $contains->getType())
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
        );
    }

    private function mapCopyright(CopyrightElement $copyright): CopyrightInformation {
        $authors = new AuthorCollection();

        foreach ($copyright->getAuthorElements() as $authorElement) {
            $authors->add(
                new Author(
                    $authorElement->getName(),
<<<<<<< HEAD
                    new Email($authorElement->getEmail())
=======
                    $authorElement->hasEMail() ? new Email($authorElement->getEmail()) : null
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
                )
            );
        }

        $licenseElement = $copyright->getLicenseElement();
        $license        = new License(
            $licenseElement->getType(),
            new Url($licenseElement->getUrl())
        );

        return new CopyrightInformation(
            $authors,
            $license
        );
    }

    private function mapRequirements(RequiresElement $requires): RequirementCollection {
        $collection = new RequirementCollection();
        $phpElement = $requires->getPHPElement();
        $parser     = new VersionConstraintParser;

        try {
            $versionConstraint = $parser->parse($phpElement->getVersion());
        } catch (VersionException $e) {
            throw new ManifestDocumentMapperException(
<<<<<<< HEAD
                \sprintf('Unsupported version constraint - %s', $e->getMessage()),
=======
                sprintf('Unsupported version constraint - %s', $e->getMessage()),
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
                (int)$e->getCode(),
                $e
            );
        }

        $collection->add(
            new PhpVersionRequirement(
                $versionConstraint
            )
        );

        if (!$phpElement->hasExtElements()) {
            return $collection;
        }

        foreach ($phpElement->getExtElements() as $extElement) {
            $collection->add(
                new PhpExtensionRequirement($extElement->getName())
            );
        }

        return $collection;
    }

    private function mapBundledComponents(ManifestDocument $document): BundledComponentCollection {
        $collection = new BundledComponentCollection();

        if (!$document->hasBundlesElement()) {
            return $collection;
        }

        foreach ($document->getBundlesElement()->getComponentElements() as $componentElement) {
            $collection->add(
                new BundledComponent(
                    $componentElement->getName(),
                    new Version(
                        $componentElement->getVersion()
                    )
                )
            );
        }

        return $collection;
    }

    private function mapExtension(ExtensionElement $extension): Extension {
        try {
            $versionConstraint = (new VersionConstraintParser)->parse($extension->getCompatible());

            return Type::extension(
                new ApplicationName($extension->getFor()),
                $versionConstraint
            );
        } catch (VersionException $e) {
            throw new ManifestDocumentMapperException(
<<<<<<< HEAD
                \sprintf('Unsupported version constraint - %s', $e->getMessage()),
=======
                sprintf('Unsupported version constraint - %s', $e->getMessage()),
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
                (int)$e->getCode(),
                $e
            );
        }
    }
}