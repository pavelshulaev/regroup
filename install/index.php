<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);

class rover_regroup extends CModule
{
    public $MODULE_ID	= "rover.regroup";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "Y";

    protected $errors = [];

    /**
     *
     */
    function __construct()
    {
		$moduleVersion = [];

        require(__DIR__ . "/version.php");

		if (is_array($moduleVersion) && array_key_exists("VERSION", $moduleVersion))
        {
			$this->MODULE_VERSION		= $moduleVersion["VERSION"];
			$this->MODULE_VERSION_DATE	= $moduleVersion["VERSION_DATE"];
        } else
	        $this->errors[] = Loc::getMessage('rover_regroup__version_info_error');

        $this->MODULE_NAME			= Loc::getMessage('rover_regroup__name');
        $this->MODULE_DESCRIPTION	= Loc::getMessage('rover_regroup__descr');
        $this->PARTNER_NAME         = GetMessage('rover_regroup__partner_name');
        $this->PARTNER_URI          = GetMessage('rover_regroup__partner_uri');
	}

    /**
     * @author Shulaev (pavel.shulaev@gmail.com)
     */
    function DoInstall()
    {
        global $APPLICATION;
        $rights = $APPLICATION->GetGroupRight($this->MODULE_ID);

        if ($rights == "W")
            $this->ProcessInstall();
	}

    /**
     * @author Shulaev (pavel.shulaev@gmail.com)
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
     * @author Shulaev (pavel.shulaev@gmail.com)
     */
    function GetModuleRightsList()
    {
        return array(
            "reference_id" => ["D", "R", "W"],
            "reference" => [
                Loc::getMessage('rover_regroup__reference_deny'),
                Loc::getMessage('rover_regroup__reference_read'),
                Loc::getMessage('rover_regroup__reference_write')
            ]
        );
    }

	/**
	 * Инсталляция файлов и зависимотей, регистрация модуля
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	private function ProcessInstall()
    {
	    if(false == \Bitrix\Main\ModuleManager::isModuleInstalled('rover.fadmin'))
		    $this->errors[] = 'Module "rover.fadmin" is not installed';

	    if (empty($this->errors)){
            ModuleManager::registerModule($this->MODULE_ID);
	        $this->registerEvents();
	    }

        global $APPLICATION, $errors;
        $errors = $this->errors;

        $APPLICATION->IncludeAdminFile(Loc::getMessage("rover_regroup__install_title"), $_SERVER['DOCUMENT_ROOT'] . getLocalPath("modules/". $this->MODULE_ID ."/install/message.php"));
    }

    /**
     * @author Shulaev (pavel.shulaev@gmail.com)
     */
    protected function registerEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler('main', 'OnAfterUserAdd', $this->MODULE_ID, '\Rover\Regroup\Events', 'onAfterUserAdd');
        $eventManager->registerEventHandler('main', 'OnBeforeUserUpdate', $this->MODULE_ID, '\Rover\Regroup\Events', 'onBeforeUserUpdate');
    }

	/**
	 * Удаление файлов и зависимостей. Снятие модуля с регистрации
	 * @author Shulaev (pavel.shulaev@gmail.com)
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
     * @author Shulaev (pavel.shulaev@gmail.com)
     */
    protected function unRegisterEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler('main', 'OnAfterUserAdd', $this->MODULE_ID, '\Rover\Regroup\Events', 'onAfterUserAdd');
        $eventManager->unRegisterEventHandler('main', 'OnBeforeUserUpdate', $this->MODULE_ID, '\Rover\Regroup\Events', 'onBeforeUserUpdate');
    }
}