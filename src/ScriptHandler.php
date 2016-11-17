<?php

    namespace Lacodda\BxModule;

    use Composer\Script\CommandEvent;

    class ScriptHandler
    {
        public static function copy (Event $event)
        {
            $event->getIO ()->write ("Show me after INSTALL/UPDATE command");
            $event->getIO ()->write ($_SERVER['DOCUMENT_ROOT']);
        }
    }