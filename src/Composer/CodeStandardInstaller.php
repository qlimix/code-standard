<?php declare(strict_types=1);

namespace Qlimix\CodeStandard\Composer;

use Composer\Composer;
use Composer\Installer\PluginInstaller;
use Composer\IO\IOInterface;

final class CodeStandardInstaller extends PluginInstaller
{
    /** @var Composer */
    protected $composer;

    /** @var IOInterface*/
    protected $io;
}
