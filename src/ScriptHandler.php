<?php

    namespace Lacodda\BxModule;

    use Composer\Script\Event;

    class ScriptHandler
    {
        public static function copy (Event $event)
        {
            // wet get ALL installed packages
            $packages = $event->getComposer()->getRepositoryManager()->getLocalRepository()->getPackages();

            $installationManager = $event->getComposer()->getInstallationManager();

            foreach ($packages as $package) {
                $installPath = $installationManager->getInstallPath($package);
                //do my process here
            }

            $event->getIO ()->write ("Show me after INSTALL/UPDATE command");
            $event->getIO ()->write ($_SERVER['DOCUMENT_ROOT']);
        }
    }