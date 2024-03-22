<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\MakerBundle;

/**
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
final class GeneratorTwigHelper
{
    public function __construct(
        private FileManager $fileManager,
    ) {
    }

    public function getEntityFieldPrintCode($entity, $field): string
    {
<<<<<<< HEAD
        $twigField = preg_replace_callback('/(?!^)_([a-z0-9])/', static function ($s) {
            return strtoupper($s[1]);
        }, $field['fieldName']);
        $printCode = $entity.'.'.str_replace('_', '', $twigField);

        switch ($field['type']) {
            case 'datetimetz_immutable':
            case 'datetimetz':
                $printCode .= ' ? '.$printCode.'|date(\'Y-m-d H:i:s T\') : \'\'';
                break;
            case 'datetime_immutable':
            case 'datetime':
                $printCode .= ' ? '.$printCode.'|date(\'Y-m-d H:i:s\') : \'\'';
                break;
            case 'dateinterval':
                $printCode .= ' ? '.$printCode.'.format(\'%y year(s), %m month(s), %d day(s)\') : \'\'';
                break;
            case 'date_immutable':
            case 'date':
                $printCode .= ' ? '.$printCode.'|date(\'Y-m-d\') : \'\'';
                break;
            case 'time_immutable':
            case 'time':
                $printCode .= ' ? '.$printCode.'|date(\'H:i:s\') : \'\'';
                break;
            case 'json':
                $printCode .= ' ? '.$printCode.'|json_encode : \'\'';
                break;
            case 'array':
                $printCode .= ' ? '.$printCode.'|join(\', \') : \'\'';
                break;
            case 'boolean':
                $printCode .= ' ? \'Yes\' : \'No\'';
                break;
        }
=======
        $twigField = preg_replace_callback('/(?!^)_([a-z0-9])/', static fn ($s) => strtoupper($s[1]), $field['fieldName']);
        $printCode = $entity.'.'.str_replace('_', '', $twigField);

        match ($field['type']) {
            'datetimetz_immutable', 'datetimetz' => $printCode .= ' ? '.$printCode.'|date(\'Y-m-d H:i:s T\') : \'\'',
            'datetime_immutable', 'datetime' => $printCode .= ' ? '.$printCode.'|date(\'Y-m-d H:i:s\') : \'\'',
            'dateinterval' => $printCode .= ' ? '.$printCode.'.format(\'%y year(s), %m month(s), %d day(s)\') : \'\'',
            'date_immutable', 'date' => $printCode .= ' ? '.$printCode.'|date(\'Y-m-d\') : \'\'',
            'time_immutable', 'time' => $printCode .= ' ? '.$printCode.'|date(\'H:i:s\') : \'\'',
            'json' => $printCode .= ' ? '.$printCode.'|json_encode : \'\'',
            'array' => $printCode .= ' ? '.$printCode.'|join(\', \') : \'\'',
            'boolean' => $printCode .= ' ? \'Yes\' : \'No\'',
            default => $printCode,
        };
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939

        return $printCode;
    }

    public function getHeadPrintCode($title): string
    {
        if ($this->fileManager->fileExists($this->fileManager->getPathForTemplate('base.html.twig'))) {
            return <<<TWIG
                {% extends 'base.html.twig' %}

                {% block title %}$title{% endblock %}

                TWIG;
        }

        return <<<HTML
            <!DOCTYPE html>

            <title>$title</title>

            HTML;
    }

    public function getFileLink($path, $text = null, $line = 0): string
    {
        trigger_deprecation('symfony/maker-bundle', 'v1.53.0', 'getFileLink() is deprecated and will be removed in the future.');

        $text = $text ?: $path;

        return "<a href=\"{{ '$path'|file_link($line) }}\">$text</a>";
    }
}
