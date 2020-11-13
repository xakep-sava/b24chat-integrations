<?php
namespace B24Chat\Integration;

use Bitrix\Main\Config\Option;

class Configuration
{
    public static function getModuleId()
    {
        return 'b24chat.integration';
    }

    public static function isEnabled($config)
    {
        return
            !defined('WIZARD_DEFAULT_SITE_ID') &&
            $config['enabled'] === 'Y' &&
            $config['token'];
    }

    public static function getConfig($siteId = SITE_ID)
    {
        return array(
            'enabled' => Option::get(self::getModuleId(), "enabled", '', $siteId),
            'token' => Option::get(self::getModuleId(), "token", '', $siteId)
        );
    }
}