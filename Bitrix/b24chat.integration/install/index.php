<?php

use Bitrix\Main\Localization\Loc;
use B24Chat\Integration\Handlers\ScriptHandler;

Loc::loadMessages(__FILE__);

class b24chat_integration extends CModule
{
    var $MODULE_ID = "b24chat.integration";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $PARTNER_NAME = 'B24';
    var $PARTNER_URI = 'https://b24chat.com';

    function __construct()
    {
        $arModuleVersion = array();

        include("version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = Loc::getMessage("MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("MODULE_DESCRIPTION");
    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/b24chat.integration/install/components",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/components/b24chat");
        return true;
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;

        $this->InstallFiles();

        RegisterModule($this->MODULE_ID);
        $this->IncludeModule($this->MODULE_ID);
        ScriptHandler::register($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile("Установка модуля b24chat.integration", $DOCUMENT_ROOT . "/bitrix/modules/b24chat.integration/install/step.php");
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;

        $this->UnInstallFiles();

        $this->IncludeModule($this->MODULE_ID);
        ScriptHandler::unRegister($this->MODULE_ID);
        UnRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile("Деинсталляция модуля b24chat.integration", $DOCUMENT_ROOT . "/bitrix/modules/b24chat.integration/install/unstep.php");
    }
}

?>