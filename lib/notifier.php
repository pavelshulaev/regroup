<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.03.2016
 * Time: 12:09
 *
 * @author Shulaev (pavel.shulaev@gmail.com)
 */

namespace Rover\Regroup;

use Bitrix\Main\Localization\Loc;
use Rover\Regroup\Config\Options;

Loc::loadMessages(__FILE__);

class Notifier
{
	const NOTIFY_ADDED      = 'notify_added';  // ��������� � ���������� � ������
	const NOTIFY_DELETED    = 'notify_deleted';  // ��������� � �������� �� ������

	/**
	 * �������� ���������� � ����������/�������� �� ���. ������
	 * @param      $userId
	 * @param      $socNetGroupId
	 * @param      $status
	 * @return bool|int
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public static function notify($userId, $socNetGroupId, $status)
	{
		// ���� �� ��������� ������ ���������� ���������, �� ������
		if (!\Bitrix\Main\Loader::includeModule('im'))
			return false;

		// ���� �� ������� ������ ��� ����-�� ��� ������, �� ���� �������
		if (is_null($status) || ((int)$userId == 0)
			|| ((int)$socNetGroupId == 0))
			return false;

		$groupLink = self::getGroupBBLink($socNetGroupId);

		if (empty($groupLink))
			return false;

		$fields = [];

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
		$fields['NOTIFY_TYPE']      = IM_NOTIFY_SYSTEM; // ��������� ��������� ��� �����������
		$fields['MESSAGE_TYPE']     = IM_MESSAGE_SYSTEM; // ��������� ��������� ��� �����������
		$fields['FROM_USER_ID']     = $USER->GetID(); // ����� - �����
		$fields['AUTHOR_ID']        = $USER->GetID(); // ����� - �����

		return \CIMMessenger::Add($fields);
	}

	/**
	 * ���������� ������ �� ������ � BB �����
	 * @param $socNetGroupId
	 * @return string
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	private static function getGroupBBLink($socNetGroupId)
	{
		$groupFields = Options::getInstance()->getSocNetGroupById($socNetGroupId);
		if (empty($groupFields))
			return '';

		return "[URL=/workgroups/group/{$socNetGroupId}/]{$groupFields['NAME']}[/URL]";
	}
}