<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);

class rover_regroup extends CModule
{
    var $MODULE_ID	= "rover.regroup";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";

    /**
     * @var array
     */
    protected $errors = array();

    function __construct()
    {
		$arModuleVersion = array();

        require(__DIR__ . "/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
			$this->MODULE_VERSION		= $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE	= $arModuleVersion["VERSION_DATE"];
        } else
	        $this->errors[] = Loc::getMessage('rover_regroup__version_info_error');

        $this->MODULE_NAME			= Loc::getMessage('rover_regroup__name');
        $this->MODULE_DESCRIPTION	= Loc::getMessage('rover_regroup__descr');
        $this->PARTNER_NAME         = GetMessage('rover_regroup__partner_name');
        $this->PARTNER_URI          = GetMessage('rover_regroup__partner_uri');
	}

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    function DoInstall()
    {
        global $APPLICATION;
        $rights = $APPLICATION->GetGroupRight($this->MODULE_ID);

        if ($rights == "W")
            $this->ProcessInstall();
	}

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    function DoUninstall()
    {
        global $APPLICATION;
        $rights = $APPLICATION->GetGroupRight($this->MODULE_ID);

        if ($rights == "W")
            $this->ProcessUninstall();
    }

    /**
     * @return array
     * @author Pavel Shulaev (http://rover-it.me)
     */
    function GetModuleRightsList()
    {
        return array(
            "reference_id" => array("D", "R", "W"),
            "reference" => array(
                Loc::getMessage('rover_regroup__reference_deny'),
                Loc::getMessage('rover_regroup__reference_read'),
                Loc::getMessage('rover_regroup__reference_write')
            )
        );
    }

	/**
	 * Инсталляция файлов и зависимотей, регистрация модуля
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	private function ProcessInstall()
    {
        if (PHP_VERSION_ID < 50400)
            $this->errors[] = Loc::getMessage('rover_regroup__php_version_error');

	    if(!ModuleManager::isModuleInstalled('rover.fadmin'))
		    $this->errors[] = Loc::getMessage('rover_regroup__rover-fadmin_not_found');

        if(!ModuleManager::isModuleInstalled('socialnetwork'))
		    $this->errors[] = Loc::getMessage('rover_regroup__socialnetwork_not_found');

	    if (empty($this->errors)){
            ModuleManager::registerModule($this->MODULE_ID);
	        $this->registerEvents();
	    }

        global $APPLICATION, $errors;
        $errors = $this->errors;

        $APPLICATION->IncludeAdminFile(Loc::getMessage("rover_regroup__install_title"), $_SERVER['DOCUMENT_ROOT'] . getLocalPath("modules/". $this->MODULE_ID ."/install/message.php"));
    }

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    protected function registerEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler('main', 'OnAfterUserAdd', $this->MODULE_ID, '\Rover\Regroup\Events', 'onAfterUserAdd');
        $eventManager->registerEventHandler('main', 'OnBeforeUserUpdate', $this->MODULE_ID, '\Rover\Regroup\Events', 'onBeforeUserUpdate');
    }

	/**
	 * Удаление файлов и зависимостей. Снятие модуля с регистрации
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	private function ProcessUninstall()
	{
        $this->unRegisterEvents();
        ModuleManager::unRegisterModule($this->MODULE_ID);

        global $APPLICATION, $errors;
        $errors = $this->errors;

        $APPLICATION->IncludeAdminFile(Loc::getMessage("rover_regroup__uninstall_title"), $_SERVER['DOCUMENT_ROOT'] . getLocalPath("modules/". $this->MODULE_ID ."/install/unMessage.php"));
	}

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    protected function unRegisterEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler('main', 'OnAfterUserAdd', $this->MODULE_ID, '\Rover\Regroup\Events', 'onAfterUserAdd');
        $eventManager->unRegisterEventHandler('main', 'OnBeforeUserUpdate', $this->MODULE_ID, '\Rover\Regroup\Events', 'onBeforeUserUpdate');
    }
}