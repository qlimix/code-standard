<?php declare(strict_types=1);

namespace Qlimix\CodeStandard\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Composer\Semver\Constraint\MatchAllConstraint;
use RuntimeException;
use Throwable;
use function basename;
use function file_exists;
use function getcwd;

final class CodeStandardPluginInstaller implements PluginInterface, EventSubscriberInterface
{
    /** @var CodeStandardConfig[] */
    private array $configs;
    private ?string $previousVersion = null;
    private IOInterface $io;
    private Composer $composer;

    /**
     * @inheritDoc
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->io = $io;
        $this->composer = $composer;
        $installer = new CodeStandardInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);


        $path = getcwd().'/vendor/qlimix/code-standard';

        $this->configs = [
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
                getcwd().'/phpcs.yml'
            ),
            new CodeStandardConfig(
                'Psalm',
                $path.'/resources/psalm.xml.dist',
                getcwd().'/psalm.yml'
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => ['postInstall', 10],
            ScriptEvents::PRE_UPDATE_CMD => ['preUpdate', 10],
            ScriptEvents::POST_UPDATE_CMD => ['postUpdate', 10],
        ];
    }

    public function postInstall(): void
    {
        $this->installConfigs();
    }

    public function preUpdate(Event $event): void
    {
        $package = $this->composer->getRepositoryManager()
            ->findPackage('qlimix/code-standard',  new MatchAllConstraint());

        if ($package !== null) {
            $this->previousVersion = $package->getVersion();
        }
    }

    public function postUpdate(Event $event): void
    {
        if ($this->previousVersion === null) {
            $this->installConfigs();
            return;
        }

        if ($this->hasMajorVersionChanged()) {
            $this->io->info('Major version of code standard changed');
            if ($this->confirmOverwrite($this->configs)) {
                foreach ($this->configs as $config) {
                    $this->overwriteConfig($config);
                }
                return;
            }

            $this->io->warning('Double check possible deprecated configurations!');
            return;
        }

        try {
            /** @var $nonExistingConfigs CodeStandardConfig[] */
            [, $nonExistingConfigs] = $this->configsExist();

            foreach ($nonExistingConfigs as $nonExistingConfig) {
                $this->installConfig($nonExistingConfig);
            }
        } catch (Throwable $exception) {
            $this->io->writeError('<fg=red>'.$exception->getMessage().'</fg=red>');
            return;
        }
    }

    private function hasMajorVersionChanged(): bool
    {
        if ($this->previousVersion === null) {
            return true;
        }

        $package = $this->composer->getRepositoryManager()
            ->findPackage('qlimix/code-standard',  new MatchAllConstraint());

        if ($package === null) {
            throw new RuntimeException('Can\'t call this method without installing');
        }

        $previousMajor = (int) explode('.', $this->previousVersion)[0];
        $currentMajor = (int) explode('.', $package->getVersion())[0];

        return $previousMajor !== $currentMajor;

    }

    /**
     * @return array
     */
    private function configsExist(): array
    {
        $exist = [];
        $exist[0] = [];
        $exist[1] = [];

        foreach ($this->configs as $config) {
            if (file_exists($config->getDestinationPath())) {
                $exists[0][] = $config;
            } else {
                $exists[1][] = $config;
            }
        }

        return $exist;
    }

    /**
     * @param CodeStandardConfig[] $configs
     */
    private function confirmOverwrite(array $configs): bool
    {
        if (count($configs) === 0) {
            return false;
        }

        $this->io->write('The following configs already exist:');

        foreach ($configs as $config) {
            $this->io->write(' - '.basename($config->getDestinationPath()));
        }

        return $this->io->askConfirmation('Overwrite them?');
    }

    private function overwriteConfig(CodeStandardConfig $config) : void {
        if (!file_exists($config->getDestinationPath())) {
            return;
        }

        $this->io->write('<fg=green>Overwriting '.$config->getName().' config to project root</fg=green>');
        $this->writeConfig($config->getResourcePath(), $config->getDestinationPath());
    }

    private function installConfigs(): void
    {
        try {
            foreach ($this->configs as $config) {
                $this->installConfig($config);
            }
        } catch (Throwable $exception) {
            $this->io->writeError('<fg=red>'.$exception->getMessage().'</fg=red>');
            return;
        }
    }

    private function installConfig(CodeStandardConfig $config) : void {
        if (file_exists($config->getDestinationPath())) {
            return;
        }

        $this->io->write('<fg=green>Coping '.$config->getName().' config to project root</fg=green>');
        $this->writeConfig($config->getResourcePath(), $config->getDestinationPath());
    }

    /**
     * @throws RuntimeException
     */
    private function writeConfig(string $pathBaseConfig, string $configDestinationPath): void
    {
        if (copy($pathBaseConfig, $configDestinationPath) === false) {
            throw new RuntimeException('Failed to copy config');
        }
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        if($io->askConfirmation('Remove GrumPHP, PHPUnit, PHPCS and Psalm?')) {
            if (file_exists(getcwd().'/grumphp.yml')) {
                unlink(getcwd().'/grumphp.yml');
            }

            if (file_exists(getcwd().'/phpunit.xml')) {
                unlink(getcwd().'/phpunit.xml');
            }

            if (file_exists(getcwd().'/phpcs.xml')) {
                unlink(getcwd().'/phpcs.xml');
            }

            if (file_exists(getcwd().'/psalm.xml')) {
                unlink(getcwd().'/psalm.xml');
            }
        }
    }
}
