<?php
use Bitrix\Main\Localization\Loc;
use \Rover\Fadmin\Layout\Admin\Form;
use \Rover\Regroup\Config\Options;
use \Bitrix\Main\Loader;
use \Bitrix\Main\SystemException;

if (!Loader::includeModule($mid)
	|| !Loader::includeModule('rover.fadmin'))
	throw new SystemException('module "' . $mid . '" or module rover.fadmin not found!');

Loc::LoadMessages(__FILE__);
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/options.php");

$form = new Form(Options::getInstance());
$form->show();