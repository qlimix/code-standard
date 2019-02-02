<?php declare(strict_types=1);

namespace Qlimix\CodeStandard\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Throwable;

final class CodeStandardPluginInstaller implements PluginInterface, EventSubscriberInterface
{
    /**
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new CodeStandardInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => ['runScheduledTasks', 10],
            ScriptEvents::POST_UPDATE_CMD => ['runScheduledTasks', 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function runScheduledTasks(Event $event)
    {
        $path = getcwd().'/vendor/qlimix/code-standard';

        try {
            $event->getIO()->write('<fg=green>Copy grumphp config to project root</fg=green>');
            copy($path.'/resources/grumphp.yml.dist', getcwd().'/grumphp.yml');

            $event->getIO()->write('<fg=green>Copy phpcs config to project root</fg=green>');
            copy($path.'/resources/phpcs.xml.dist', getcwd().'/phpcs.xml.dist');
        } catch (Throwable $exception) {
            $event->getIO()->writeError('<fg=red>'.$exception->getMessage().'</fg=red>');
            return;
        }
    }
}
