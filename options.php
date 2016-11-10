<?php
use Bitrix\Main\Localization\Loc;
use \Rover\Fadmin\Admin\Panel;
use \Rover\Regroup\Config\Options;
use \Bitrix\Main\Loader;
use \Bitrix\Main\SystemException;

if (!Loader::includeModule($mid)
	|| !Loader::includeModule('rover.fadmin'))
	throw new SystemException('module "' . $mid . '" or module rover.fadmin not found!');

Loc::LoadMessages(__FILE__);
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/options.php");

(new Panel(Options::getInstance()))->show();