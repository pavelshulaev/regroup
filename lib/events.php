<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 29.02.2016
 * Time: 21:56
 *
 * @author Shulaev (pavel.shulaev@gmail.com)
 */

namespace Rover\Regroup;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\UserTable;

class Events
{
	protected static $groups = [];
	/**
	 * @throws ArgumentNullException
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public static function update()
	{
		$usersIds = self::getAllUsersIds();

		foreach ($usersIds as $userId)
			Group::apply($userId, self::getUserGroupsIds($userId));
	}

	/**
	 * Применяем правила по всем пришедшим группам
	 * @param $params
	 * @author Shulaev (pavel.shulaev@gmail.com)
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
	 * @author Shulaev (pavel.shulaev@gmail.com)
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
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	protected static function getUpdatedGroupsIds($params)
	{
		if (!isset($params['GROUP_ID']))
			return null;

		$updatedSysGroupsIds = [];

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
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public static function getUserGroupsIds($userId)
	{
		if (!$userId)
			throw new ArgumentNullException('userId');

		if (!isset(self::$groups[$userId]))
			self::$groups[$userId] = array_diff(\CUser::GetUserGroup($userId), [2]);

		return self::$groups[$userId];
	}

	/**
	 * @return array
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public static function getAllUsersIds()
	{
		$query  = [
			'filter' => ['ACTIVE' => 'Y'],
			'select' => ['ID']
		];

		$users  = UserTable::getList($query);
		$result = [];

		while ($user = $users->fetch())
			$result[] = $user['ID'];

		return $result;
	}
}