<?php

namespace B24Chat\Integration\Handlers;

use Bitrix\Main\EventManager;
use B24Chat\Integration\Configuration;

class ScriptHandler
{
    public static function register($moduleId)
    {
        EventManager::getInstance()->registerEventHandler('main', 'OnPageStart', $moduleId,
            __CLASS__, 'onPageStart');
    }

    public static function unRegister($moduleId)
    {
        EventManager::getInstance()->registerEventHandler('main', 'OnPageStart', $moduleId,
            __CLASS__, 'onPageStart');
    }

    public static function onPageStart()
    {
        $config = Configuration::getConfig();

        // TODO: url в кофиг
        if (Configuration::isEnabled($config)) {
            EventManager::getInstance()->addEventHandler(
                'main',
                'OnEndBufferContent',
                function (&$content) use ($config) {
                    $content = str_replace("</body>",
                        '<script src="https://b24chat.com/init.js" api="{&quot;url&quot;:&quot;https://b24chat.com/&quot;,&quot;widgets&quot;:{&quot;token&quot;:&quot;' . $config['token'] . '&quot;}}"></script></body>',
                        $content);
                }
            );
        }
    }
}