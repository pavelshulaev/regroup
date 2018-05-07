<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 29.02.2016
 * Time: 21:56
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Regroup;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\UserTable;

/**
 * Class Events
 *
 * @package Rover\Regroup
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Events
{
    /**
     * @var array
     */
	protected static $groups = array();
	
    /**
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\ArgumentException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function update()
	{
		$usersIds = self::getAllUsersIds();

		foreach ($usersIds as $userId)
			Group::apply($userId, self::getUserGroupsIds($userId));
	}

    /**
     * Применяем правила по всем пришедшим группам
     *
     * @param $params
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function onAfterUserAdd($params)
	{
		if (!isset($params['ID']) || !intval($params['ID']))
			return;

		Group::apply($params['ID'], self::getUpdatedGroupsIds($params));
	}


	/**
	 * @param $params
	 * @throws ArgumentNullException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function onBeforeUserUpdate($params)
	{
		if (!isset($params['ID']))
			throw new ArgumentNullException('userId');

		$updatedSysGroupsIds = self::getUpdatedGroupsIds($params);

		// if groups not updated
		if (is_null($updatedSysGroupsIds))
			return;

		$oldSysGroupsIds = self::getUserGroupsIds($params['ID']);

		$joinedGroupsIds = array_diff($updatedSysGroupsIds, $oldSysGroupsIds);
		$leavedGroupsIds = array_diff($oldSysGroupsIds, $updatedSysGroupsIds);

		Group::apply($params['ID'], $joinedGroupsIds, $leavedGroupsIds);
	}

	/**
	 * Возвращает айдишники групп пользователя после обновления
	 * @param $params
	 * @return array
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected static function getUpdatedGroupsIds($params)
	{
		if (!isset($params['GROUP_ID']))
			return null;

		$updatedSysGroupsIds = array();

		foreach ($params['GROUP_ID'] as $group)
			$updatedSysGroupsIds[] = $group['GROUP_ID'];

		return $updatedSysGroupsIds;
	}

	/**
	 * Возвращает айдишники групп пользователя до обновления
	 *
	 * убираем группу "Все пользователи (в том числе неавторизованные)", потому что в обновлённых группах ее никогда
	 * нет, а пользователь в ней состоит всегда
	 *
	 * @param $userId
	 * @return array
	 * @throws ArgumentNullException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getUserGroupsIds($userId)
	{
		if (!$userId)
			throw new ArgumentNullException('userId');

		if (!isset(self::$groups[$userId]))
			self::$groups[$userId] = array_diff(\CUser::GetUserGroup($userId), array(2));

		return self::$groups[$userId];
	}

    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getAllUsersIds()
	{
		$query  = array(
			'filter' => array('ACTIVE' => 'Y'),
			'select' => array('ID')
        );

		$users  = UserTable::getList($query);
		$result = array();

		while ($user = $users->fetch())
			$result[] = $user['ID'];

		return $result;
	}
}