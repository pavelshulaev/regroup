<?php
namespace Rover\Regroup\Config;
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 29.02.2016
 * Time: 21:56
 *
 * @author Shulaev (pavel.shulaev@gmail.com)
 */

use Bitrix\Main\SystemException;
use Rover\Fadmin\Options as FadminOptions;
use Bitrix\Main\Localization\Loc;
use Rover\Fadmin\Presets;
use Rover\Regroup\Events;
use Rover\Fadmin\Tab;
use Rover\Fadmin\Inputs\Input;
use Rover\Regroup\Config\Tabs;

if (!\Bitrix\Main\Loader::includeModule('rover.fadmin'))
	throw new SystemException('rover.fadmin module not found');

Loc::loadMessages(__FILE__);

class Options extends FadminOptions
{
	const MODULE_ID = 'rover.regroup';

	protected $cache = [];
	/**
	 * Кеш детального описания рабочих групп
	 * @var array
	 */
	protected $socNetGroups = [];

	/**
	 * Кеш айдишников всех активных юзеров
	 * @var array
	 */
	protected $allUsersIds;

	/**
	 * @return static
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public static function getInstance()
	{
		return parent::getInstance(self::MODULE_ID);
	}

	/**
	 * @return array
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public function getConfig()
	{
		$tabs = Tabs::get();

		return [
			'tabs' => $tabs
		];
	}

	/**
	 * Возвращает детальное описание рабочей группы по ее ид
	 * @param $groupId
	 * @return mixed
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public function getSocNetGroupById($groupId)
	{
		if (!isset($this->socNetGroups[$groupId]))
			$this->socNetGroups[$groupId] = \CSocNetGroup::GetByID($groupId);

		return $this->socNetGroups[$groupId];
	}

	/**
	 * @return mixed|null
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public function getLeaveModerators()
	{
		return $this->getNormalValue(Tabs::INPUT__LEAVE_MODERATORS);
	}

	/**
	 * @return mixed|null
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public function getConnectDisc()
	{
		return $this->getNormalValue(Tabs::INPUT__CONNECT_DISC);
	}

	/**
	 * @param $presetId
	 * @return mixed|null
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public function getPresetEnabled($presetId)
	{
		return $this->getPresetValue(Tabs::INPUT__PRESET_ENABLED, $presetId);
	}

	/**
	 * @param $presetId
	 * @return mixed|null
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public function getPresetGroup($presetId)
	{
		return $this->getPresetValue(Tabs::INPUT__PRESET_GROUP, $presetId);
	}

	/**
	 * @param $presetId
	 * @return mixed|null
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public function getPresetJoinSysJoinWork($presetId)
	{
		return $this->getPresetValue(Tabs::INPUT__PRESET_JOIN_SYS_JOIN_WORK, $presetId);
	}

	/**
	 * @param $presetId
	 * @return mixed|null
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public function getPresetJoinSysLeaveWork($presetId)
	{
		return $this->getPresetValue(Tabs::INPUT__PRESET_JOIN_SYS_LEAVE_WORK, $presetId);
	}

	/**
	 * @param $presetId
	 * @return mixed|null
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public function getPresetLeaveSysJoinWork($presetId)
	{
		return $this->getPresetValue(Tabs::INPUT__PRESET_LEAVE_SYS_JOIN_WORK, $presetId);
	}

	/**
	 * @param $presetId
	 * @return mixed|null
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public function getPresetLeaveSysLeaveWork($presetId)
	{
		return $this->getPresetValue(Tabs::INPUT__PRESET_LEAVE_SYS_LEAVE_WORK, $presetId);
	}

	/**
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	protected function beforeRequest()
	{
		$request = \Bitrix\Main\Application::getInstance()
			->getContext()->getRequest();

		if (!$request->get(Tabs::INPUT__REGROUP_ALL))
			return;

		Events::update();
	}

	/**
	 * @param $params
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	protected function beforeGetTabInfo(array &$params)
	{
		/**
		 * @var Tab $tab
		 */
		$tab = $params['tab'];

		if (!$tab->isPreset())
			return;

		$presetId   = $tab->getPresetId();
		/**
		 * @var Input $inputName
		 */
		$inputName  = $tab->searchByName(Tabs::INPUT__PRESET_NAME);
		$presetName = $inputName->getValue(true);

		if (strlen($presetName))
			$this->setPresetName($presetId, $presetName);
		else {
			$presetName = $this->getPresetNameById($presetId);
			$inputName->setValue($presetName);
		}

		$params['description']  = $params['description'] . ' "' . $presetName . '"';
		$params['label']        = Loc::getMessage('rover_regroup__rule') . $presetName;
	}
}