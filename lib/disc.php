<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.03.2016
 * Time: 1:44
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Regroup;

use Rover\Regroup\Config\Options;
use \Bitrix\Main\Loader;
/**
 * Class Disc
 *
 * @package Rover\Regroup
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Disc
{
	const DISC_CONNECTED      = 'connected';    // ���� ����������� �����
	const DISC_DISCONNECTED   = 'disconnected'; // ���� ���������� �����

    /**
     * ���������� ����
     * @param $userId
     * @param $socNetGroupId
     * @return bool|int
     * @throws \Bitrix\Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function connect($userId, $socNetGroupId)
	{
		$params = self::getParams($userId, $socNetGroupId);
		if (empty($params))
			return false;

		$attachSectionData = self::getAttachSectionData($params);
		$targetSectionData = self::getTargetSectionData($params);

		if (empty($attachSectionData)
			|| empty($targetSectionData))
			return false;

		$linkData = array(
			'ID'            => $attachSectionData['SECTION_ID'],
			'IBLOCK_ID'     => $attachSectionData['IBLOCK_ID'],
			'NAME'          => \CWebDavIblock::correctName(
				\CWebDavSymlinkHelper::generateNameForGroupLink(
					$params['attachObject']['id'], $targetSectionData)
			),
			'CREATED_BY'    => $params['inviteFromUserId'],
			'INVITE_USER_ID' => $params['attachToUserId'],
			//'CAN_EDIT' => $params['canEdit'],
			'CAN_FORWARD'   => 0,
        );

		return \CWebDavSymlinkHelper::createSymLinkSection(
			$targetSectionData, $linkData, $params['attachObject']['type']);
	}

    /**
     * ��������� ����
     * @param $userId
     * @param $socNetGroupId
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function disconnect($userId, $socNetGroupId)
	{
		$params = self::getParams($userId, $socNetGroupId);
		if (empty($params)) return false;

		$attachSectionData = self::getAttachSectionData($params);
		$targetSectionData = self::getTargetSectionData($params);

		if (empty($attachSectionData)
			|| empty($targetSectionData))
			return false;

		$linkData = array(
			'ID'                => $attachSectionData['SECTION_ID'],
			'IBLOCK_ID'         => $attachSectionData['IBLOCK_ID'],
			'INVITE_USER_ID'    => $params['attachToUserId'],
		);

		return \CWebDavSymlinkHelper::deleteSymLinkSection(
			$targetSectionData, $linkData, $params['attachObject']['type']);
	}

	/**
	 * ���������, ��� ��������� ���� � ������
	 * @param $userId
	 * @param $socNetGroupId
	 * @return bool
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	private static function checkFields($userId, $socNetGroupId)
	{
		if ((!(int)$userId)	|| (!(int)$socNetGroupId == 0))
			return false;

		return true;
	}

    /**
     * ���������� ������ ����������, ����� ��� ���� �������� ����������� �������� ��������
     *
     * @param $userId
     * @param $socNetGroupId
     * @return array|bool
     * @throws \Bitrix\Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	private static function getParams($userId, $socNetGroupId)
	{
		if(!Loader::includeModule('webdav')
			|| !Loader::includeModule('iblock'))
			return false;

		if (!self::checkFields($userId, $socNetGroupId))
			return false;

		return array(
			//'ajax'          => true,
			//'action'        => $sAction,
			'attachObject'  => array(
				'id'    => (int)$socNetGroupId,
				'type'  => \CWebDavSymlinkHelper::ENTITY_TYPE_GROUP,
			),
			'attachToUserId'    => $userId,
			'inviteFromUserId'  => $userId,
			'canEdit'           => 1
        );
	}

    /**
     * �������� ���������� � ���������� ��� ������
     * @param $params
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
	private static function getAttachSectionData($params)
	{
		$group      = Options::getInstance()->getSocNetGroupById($params['attachObject']['id']);
		$groupLib   = \CWebDavIblock::LibOptions(
			'group_files',
			false,
			$group['SITE_ID']
		);

		if ($groupLib && isset($groupLib['id'])
			&& ($iblockId = intval($groupLib['id'])))
		{
			$groupSectionId = \CIBlockWebdavSocnet::getSectionId($iblockId, 'group', $params['attachObject']['id']);
			if ($groupSectionId) {
				return array(
					'IBLOCK_ID'         => $iblockId,
					'SECTION_ID'        => $groupSectionId,
					'SOCNET_GROUP_ID'   => $params['attachObject']['id']
                );
			}
		}

		return array();
	}

	/**
	 * �������� ������������ � ���������� �����
	 * @param $params
	 * @return array
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	private static function getTargetSectionData($params)
	{
		$group      = Options::getInstance()->getSocNetGroupById($params['attachObject']['id']);
		$userLib    = \CWebDavIblock::LibOptions(
			'user_files',
			false,
			$group['SITE_ID']
		);

		if ($userLib && isset($userLib['id'])
			&& ($iblockId = intval($userLib['id'])))
		{
			$userSectionId = \CWebDavIblock::getRootSectionIdForUser($iblockId, $params['attachToUserId']);
			if ($userSectionId)
				return array(
					'IBLOCK_ID'         => $iblockId,
					'SECTION_ID'        => $userSectionId,
					'IBLOCK_SECTION_ID' => $userSectionId,
                );

		}

		return array();
	}
}