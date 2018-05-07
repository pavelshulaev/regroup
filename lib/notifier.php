<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.03.2016
 * Time: 12:09
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Regroup;

use Bitrix\Main\Localization\Loc;
use Rover\Regroup\Config\Options;
use \Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

/**
 * Class Notifier
 *
 * @package Rover\Regroup
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Notifier
{
	const NOTIFY_ADDED      = 'notify_added';  // уведомить о добавлении в группу
	const NOTIFY_DELETED    = 'notify_deleted';  // уведомить о удалении из группы

    /**
     * Высылает оповещение о добавлении/удалении из раб. группы
     *
     * @param $userId
     * @param $socNetGroupId
     * @param $status
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function notify($userId, $socNetGroupId, $status)
	{
		// Если не подключен подуль мгновенных сообщений, то выходи
		if (!Loader::includeModule('im'))
			return false;

		// Если не передан статус или поль-ль или группа, то тоже выходим
		if (is_null($status) || ((int)$userId == 0)
			|| ((int)$socNetGroupId == 0))
			return false;

		$groupLink = self::getGroupBBLink($socNetGroupId);

		if (empty($groupLink))
			return false;

		$fields = array();

		switch($status){
			case self::NOTIFY_ADDED:
				//$fields['TITLE']        = Loc::getMessage('rover_regroup__notify_added_title');
				$fields['MESSAGE']      = Loc::getMessage('rover_regroup__notify_added_message', array('#GROUP_LINK#' => $groupLink));
				$fields['NOTIFY_EVENT'] = 'add_to_system_group';
				break;

			case self::NOTIFY_DELETED:
				//$fields['TITLE']        = Loc::getMessage('rover_regroup__notify_deleted_title');
				$fields['MESSAGE']      = Loc::getMessage('rover_regroup__notify_deleted_message', array('#GROUP_LINK#' => $groupLink));
				$fields['NOTIFY_EVENT'] = 'delete_from_system_group';
				break;

			default:
				return false;
		}

		global $USER;

		$fields['TITLE']            = $USER->GetFullName();
		$fields['TO_USER_ID']       = (int)$userId;
		$fields['NOTIFY_MODULE']    = Options::MODULE_ID;
		$fields['NOTIFY_TYPE']      = IM_NOTIFY_SYSTEM; // системное сообщение без отправителя
		$fields['MESSAGE_TYPE']     = IM_MESSAGE_SYSTEM; // системное сообщение без отправителя
		$fields['FROM_USER_ID']     = $USER->GetID(); // автор - админ
		$fields['AUTHOR_ID']        = $USER->GetID(); // автор - админ

		return \CIMMessenger::Add($fields);
	}

	/**
	 * Возвращает ссылку на группу в BB кодах
	 * @param $socNetGroupId
	 * @return string
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	private static function getGroupBBLink($socNetGroupId)
	{
		$groupFields = Options::getInstance()->getSocNetGroupById($socNetGroupId);
		if (empty($groupFields))
			return '';

		return "[URL=/workgroups/group/{$socNetGroupId}/]{$groupFields['NAME']}[/URL]";
	}
}