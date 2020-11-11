<?php declare(strict_types=1);

use Qlimix\CodeStandard\CodeStandardConfig;

function writeConfig(CodeStandardConfig $config): void
{
    if (copy($config->getResourcePath(), $config->getDestinationPath()) === false) {
        throw new RuntimeException('Failed to copy config');
    }
}

$path = getcwd().'/../qlimix/code-standard';

$configs = [
    new CodeStandardConfig(
        'GrumPHP',
        $path.'/resources/grumphp.yml.dist',
        getcwd().'/grumphp.yml'
    ),
    new CodeStandardConfig(
        'PHPCS',
        $path.'/resources/phpcs.xml.dist',
        getcwd().'/phpunit.xml'
    ),
    new CodeStandardConfig(
        'PHPUnit',
        $path.'/resources/phpcs.xml.dist',
        getcwd().'/phpcs.xml'
    ),
    new CodeStandardConfig(
        'Psalm',
        $path.'/resources/psalm.xml.dist',
        getcwd().'/psalm.xml'
    ),
];

foreach ($this->configs as $config) {
    writeConfig($config);
}
