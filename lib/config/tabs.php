<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.03.2016
 * Time: 12:31
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Regroup\Config;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Rover\Fadmin\Inputs\Input;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class Tabs
{
	const TAB__MAIN     = 'tab__main';

	const INPUT__LEAVE_MODERATORS   = 'input__leave_moderators';
	const INPUT__CONNECT_DISC       = 'input__connect_disc';
	const INPUT__REGROUP_ALL        = 'input__regroup_all';
	const INPUT__NEW_PRESET         = 'input__new_preset';

	const TAB__PRESET   = 'tab__preset';

	const INPUT__PRESET_ENABLED     = 'input__preset_enabled';
	const INPUT__PRESET_NAME        = 'input__preset_name';
	const INPUT__PRESET_SORT        = 'input__preset_sort';
	const INPUT__PRESET_GROUP       = 'input__preset_group';

	const INPUT__PRESET_JOIN_SYS_JOIN_WORK      = 'input__preset_join_sys_join_work';
	const INPUT__PRESET_JOIN_SYS_LEAVE_WORK     = 'input__preset_join_sys_leave_work';
	const INPUT__PRESET_LEAVE_SYS_JOIN_WORK     = 'input__preset_leave_sys_join_work';
	const INPUT__PRESET_LEAVE_SYS_LEAVE_WORK    = 'input__preset_leave_sys_leave_work';
	const INPUT__PRESET_REMOVE                  = 'input__preset_remove';

	/**
	 * sys groups cache
	 * @var array
	 */
	protected static $sysGroups;

	/**
	 * work groups cache
	 * @var array
	 */
	protected static $workGroups;

    /**
     * @return array
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function get()
	{
		return array(
			self::getMain(),
			self::getPreset()
        );
	}

    /**
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function getMain()
	{
		return array(
			'name'          => self::TAB__MAIN,
			'label'         => Loc::getMessage(self::TAB__MAIN . '_label'),
			'description'   => Loc::getMessage(self::TAB__MAIN . '_description'),
			'inputs' => array(
				array(
					'type'      => Input::TYPE__CHECKBOX,
					'name'      => self::INPUT__LEAVE_MODERATORS,
					'label'     => Loc::getMessage(self::INPUT__LEAVE_MODERATORS . '_label')
                ),
				array(
					'type'      => Input::TYPE__CHECKBOX,
					'name'      => self::INPUT__CONNECT_DISC,
					'label'     => Loc::getMessage(self::INPUT__CONNECT_DISC . '_label')
                ),
				array(
					'type'      => Input::TYPE__SUBMIT,
					'name'      => self::INPUT__REGROUP_ALL,
					'label'     => Loc::getMessage(self::INPUT__REGROUP_ALL . '_label'),
					'default'   => self::INPUT__REGROUP_ALL
                ),
				array(
					'type'      => Input::TYPE__ADD_PRESET,
					'name'      => self::INPUT__NEW_PRESET,
					'label'     => Loc::getMessage(self::INPUT__NEW_PRESET . '_label'),
					'popup'     => Loc::getMessage(self::INPUT__NEW_PRESET . '_popup'),
					'default'   => Loc::getMessage(self::INPUT__NEW_PRESET . '_default'),
                ),
            )
        );
	}

    /**
     * @return array
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function getPreset()
	{
		return array(
			'name'          => self::TAB__PRESET,
			'label'         => Loc::getMessage(self::TAB__PRESET . '_label'),
			'description'   => Loc::getMessage(self::TAB__PRESET . '_description'),
			'preset'        => true,
			'inputs' => array(
				array(
					'type'      => Input::TYPE__HEADER,
					'name'      => 'preset__header_common',
					'label'     => Loc::getMessage('preset__header_common_label')
				),
				array(
					'type'      => Input::TYPE__TEXT,
					'name'      => self::INPUT__PRESET_NAME,
					'label'     => Loc::getMessage(self::INPUT__PRESET_NAME . '_label'),
					'size'      => 35
				),
				array(
					'type'      => Input::TYPE__CHECKBOX,
					'name'      => self::INPUT__PRESET_ENABLED,
					'label'     => Loc::getMessage(self::INPUT__PRESET_ENABLED . '_label'),
					'default'   => 'Y'
				),
				/*array(
					'type'      => Input::TYPE__TEXT,
					'name'      => self::INPUT__PRESET_SORT,
					'label'     => Loc::getMessage(self::INPUT__PRESET_SORT . '_label'),
					'default'   => 100
				),*/
				array(
					'type'      => Input::TYPE__SELECTBOX,
					'name'      => self::INPUT__PRESET_GROUP,
					'label'     => Loc::getMessage(self::INPUT__PRESET_GROUP . '_label'),
					'options'   => self::getSysGroups()
				),
				array(
					'type'      => Input::TYPE__HEADER,
					'name'      => 'preset__header_join_sys',
					'label'     => Loc::getMessage('preset__header_join_sys_label')
				),
				array(
					'type'      => Input::TYPE__SELECTBOX,
					'name'      => self::INPUT__PRESET_JOIN_SYS_JOIN_WORK,
					'label'     => Loc::getMessage(self::INPUT__PRESET_JOIN_SYS_JOIN_WORK . '_label'),
					'options'   => self::getWorkGroups(),
					'multiple'  => true,
					'size'      => 5,
				),
				array(
					'type'      => Input::TYPE__SELECTBOX,
					'name'      => self::INPUT__PRESET_JOIN_SYS_LEAVE_WORK,
					'label'     => Loc::getMessage(self::INPUT__PRESET_JOIN_SYS_LEAVE_WORK . '_label'),
					'options'   => self::getWorkGroups(),
					'multiple'  => true,
					'size'      => 5,
				),
				array(
					'type'      => Input::TYPE__HEADER,
					'name'      => 'preset__header_leave_sys',
					'label'     => Loc::getMessage('preset__header_leave_sys_label')
				),
				array(
					'type'      => Input::TYPE__SELECTBOX,
					'name'      => self::INPUT__PRESET_LEAVE_SYS_LEAVE_WORK,
					'label'     => Loc::getMessage(self::INPUT__PRESET_LEAVE_SYS_LEAVE_WORK . '_label'),
					'options'   => self::getWorkGroups(),
					'multiple'  => true,
					'size'      => 5,
				),
				array(
					'type'      => Input::TYPE__SELECTBOX,
					'name'      => self::INPUT__PRESET_LEAVE_SYS_JOIN_WORK,
					'label'     => Loc::getMessage(self::INPUT__PRESET_LEAVE_SYS_JOIN_WORK . '_label'),
					'options'   => self::getWorkGroups(),
					'multiple'  => true,
					'size'      => 5,
				),
				array(
					'type'      => Input::TYPE__HEADER,
					'name'      => 'preset__header_remove',
					'label'     => Loc::getMessage('preset__header_remove_label')
				),
				array(
					'type'      => Input::TYPE__REMOVE_PRESET,
					'name'      => self::INPUT__PRESET_REMOVE,
					'label'     => Loc::getMessage(self::INPUT__PRESET_REMOVE . '_label'),
					'popup'     => Loc::getMessage(self::INPUT__PRESET_REMOVE . '_popup'),
				)
			)
		);
	}

	/**
	 * @return array
	 * @throws \Bitrix\Main\ArgumentException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getSysGroups()
	{
		if (is_null(self::$sysGroups)) {
			$query = array(
				'order' => array('ID' => 'ASC'),
				'select' => array('ID', 'NAME')
			);

			$sysGroups  = \Bitrix\Main\GroupTable::getList($query);
			self::$sysGroups     = array();

			while($sysGroup = $sysGroups->fetch())
				self::$sysGroups[$sysGroup['ID']]
					= $sysGroup['NAME'] . ' [' . $sysGroup['ID'] . ']';
		}

		return self::$sysGroups;
	}

    /**
     * @return array
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getWorkGroups()
	{
		if (!Loader::includeModule('socialnetwork'))
			throw new SystemException('Socialnetworl module not found');

		if (is_null(self::$workGroups)) {

			$query = array(
				'order'     => array('ID' => 'ASC'),
				'select'    => array('ID', 'NAME')
			);

			$workGroups = \Bitrix\Socialnetwork\WorkgroupTable::getList($query);
			self::$workGroups = array(0 => Loc::getMessage('REGROUP__NOT_SET'));

			while($workGroup = $workGroups->fetch())
				self::$workGroups[$workGroup['ID']]
					= $workGroup['NAME'] . ' [' . $workGroup['ID'] . ']';
		}

		return self::$workGroups;
	}
}