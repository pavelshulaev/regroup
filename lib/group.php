<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.03.2016
 * Time: 15:11
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Regroup;

use Bitrix\Main\ArgumentNullException;
use Rover\Regroup\Config\Options;

/**
 * Class Group
 *
 * @package Rover\Regroup
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Group
{
	const EVENT__SYS_JOIN   = 'JoinSys';
	const EVENT__SYS_LEAVE  = 'LeaveSys';

	const QUERY__WORK_JOIN  = 'JoinWork';
	const QUERY__WORK_LEAVE = 'LeaveWork';

    /**
     * @param       $userId
     * @param array $joinedSysGroups
     * @param array $leavedSysGroups
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function apply($userId, $joinedSysGroups = array(), $leavedSysGroups = array())
	{
		if (!$userId)
			throw new ArgumentNullException('userId');

		$leavedWorkGroups = self::getWorkGroups($joinedSysGroups,
			$leavedSysGroups, self::QUERY__WORK_LEAVE);
		$joinedWorkGroups = self::getWorkGroups($joinedSysGroups,
			$leavedSysGroups, self::QUERY__WORK_JOIN);

		//������, � ������� ������������ ������ ��������
		$stayWorkGroups = self::getWorkGroups(
			array_diff(Events::getUserGroupsIds($userId), $leavedSysGroups),
			array(),
			self::QUERY__WORK_JOIN);

		self::removeFromWorkGroups($userId,
			array_diff($leavedWorkGroups,
				array_merge($joinedWorkGroups, $stayWorkGroups)));
		self::addToWorkGroups($userId,
			array_diff($joinedWorkGroups, $leavedWorkGroups));
	}

    /**
     * @param array  $joinedSysGroups
     * @param array  $leavedSysGroups
     * @param string $query
     * @return array
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function getWorkGroups($joinedSysGroups = array(), $leavedSysGroups = array(), $query = self::QUERY__WORK_JOIN)
	{
		$result = array_merge(
			self::getWorkGroupsBySysGroups($joinedSysGroups, self::EVENT__SYS_JOIN, $query),
			self::getWorkGroupsBySysGroups($leavedSysGroups, self::EVENT__SYS_LEAVE, $query)
		);

		return $result ? $result : array();
	}

    /**
     * @param        $sysGroups
     * @param string $event
     * @param string $query
     * @return array
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function getWorkGroupsBySysGroups($sysGroups, $event = self::EVENT__SYS_JOIN, $query = self::QUERY__WORK_JOIN)
	{
		$presetsIds = Presets::getBySysGroupsIds($sysGroups);
		$result     = array();

		foreach ($presetsIds as $presetId)
			$result = array_merge($result,
				Presets::getWorkGroups($presetId, $event, $query));

		return $result;
	}


    /**
     * ��������� ������������ � ������� ���������� ����
     *
     * @param $userId
     * @param $workGroupsIds
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	private static function addToWorkGroups($userId, $workGroupsIds)
	{
		foreach($workGroupsIds as $workGroupId)
		{
			$userInWorkGroup = self::getUserInWorkGroup($userId, $workGroupId);

			if (!$userInWorkGroup)
			{
			    global $USER;

				$query = array(
					"USER_ID"   => $userId,
					"GROUP_ID"  => $workGroupId,
					"ROLE"      => SONET_ROLES_USER, // ��� ������� ����
					"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"=DATE_UPDATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"INITIATED_BY_TYPE" => SONET_INITIATED_BY_USER,
					"INITIATED_BY_USER_ID" => $USER->GetID(),
					"MESSAGE" => false,
                );

				$sonetUserToGroup = new \CSocNetUserToGroup();

				if ($sonetUserToGroup->Add($query)) {
					Notifier::notify($userId, $workGroupId, Notifier::NOTIFY_ADDED);

					// ���� ���������, ���������� ����
					if (Options::getInstance()->getConnectDisc())
						Disc::connect($userId, $workGroupId);
				}
			}
		}
	}

    /**
     * ������� ������������� �� ����� ���������� ����
     *
     * @param $userId
     * @param $workGroupsIds
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	private static function removeFromWorkGroups($userId, $workGroupsIds)
	{
		foreach($workGroupsIds as $workGroupId)
		{
			// ��������� ���� ������������ � ������
			$role = \CSocNetUserToGroup::GetUserRole($userId, $workGroupId);

			if (strlen($role) && ($role !== SONET_ROLES_OWNER)){

				// ������� ��� ��� ����������?
				if (($role == SONET_ROLES_MODERATOR)
					&& (Options::getInstance()->getLeaveModerators()))
					continue;

				$userInWorkGroup = self::getUserInWorkGroup($userId, $workGroupId);

				if ($userInWorkGroup)
				{
					Disc::disconnect($userId, $workGroupId);
					$result = \CSocNetUserToGroup::Delete($userInWorkGroup['ID']);

					if ($result->result)
						Notifier::notify($userId, $workGroupId, Notifier::NOTIFY_DELETED);
				}
			}
		}
	}

	/**
	 * ��������� �� ������������ � ���������� ������.
	 * � ���� ��, �� ���������� ������ ���������� ���� �����
	 *
	 * @param $userId
	 * @param $workGroupId
	 * @return array
	 * @throws ArgumentNullException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	private static function getUserInWorkGroup($userId, $workGroupId)
	{
		if (!$userId)
			throw new ArgumentNullException('userId');

		if (is_null($workGroupId))
			throw new ArgumentNullException('workGroupId');

		return \CSocNetUserToGroup::GetList(
			array("ID" => "ASC"),
			array('USER_ID' => $userId, 'GROUP_ID' => $workGroupId),
			false,
			false,
			array('ID')
		)->Fetch();
	}
}