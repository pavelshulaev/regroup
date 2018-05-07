<?php
namespace Rover\Regroup\Config;
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 29.02.2016
 * Time: 21:56
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

use Bitrix\Main\SystemException;
use Rover\Fadmin\Options as FadminOptions;
use Bitrix\Main\Localization\Loc;
use Rover\Regroup\Events;
use Rover\Fadmin\Tab;
use Rover\Fadmin\Inputs\Input;

if (!\Bitrix\Main\Loader::includeModule('rover.fadmin'))
	throw new SystemException('rover.fadmin module not found');

Loc::loadMessages(__FILE__);

/**
 * Class Options
 *
 * @package Rover\Regroup\Config
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Options extends FadminOptions
{
	const MODULE_ID = 'rover.regroup';

	/**
	 * ��� ���������� �������� ������� �����
	 * @var array
	 */
	protected $socNetGroups = array();

    /**
     * @param string $moduleId
     * @return static|self|FadminOptions
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getInstance($moduleId = self::MODULE_ID)
	{
		return parent::getInstance($moduleId);
	}

    /**
     * @return array|mixed
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getConfig()
	{
		return array(
            'tabs' => Tabs::get()
        );
	}

	/**
	 * ���������� ��������� �������� ������� ������ �� �� ��
	 * @param $groupId
	 * @return mixed
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getSocNetGroupById($groupId)
	{
		if (!isset($this->socNetGroups[$groupId]))
			$this->socNetGroups[$groupId] = \CSocNetGroup::GetByID($groupId);

		return $this->socNetGroups[$groupId];
	}

	/**
	 * @return mixed|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getLeaveModerators()
	{
		return $this->getNormalValue(Tabs::INPUT__LEAVE_MODERATORS);
	}

	/**
	 * @return mixed|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getConnectDisc()
	{
		return $this->getNormalValue(Tabs::INPUT__CONNECT_DISC);
	}

    /**
     * @param $presetId
     * @return mixed
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getPresetEnabled($presetId)
	{
		return $this->getPresetValue(Tabs::INPUT__PRESET_ENABLED, $presetId);
	}

    /**
     * @param $presetId
     * @return mixed
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getPresetGroup($presetId)
	{
		return $this->getPresetValue(Tabs::INPUT__PRESET_GROUP, $presetId);
	}

    /**
     * @param $presetId
     * @return mixed
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getPresetJoinSysJoinWork($presetId)
	{
		return $this->getPresetValue(Tabs::INPUT__PRESET_JOIN_SYS_JOIN_WORK, $presetId);
	}

    /**
     * @param $presetId
     * @return mixed
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getPresetJoinSysLeaveWork($presetId)
	{
		return $this->getPresetValue(Tabs::INPUT__PRESET_JOIN_SYS_LEAVE_WORK, $presetId);
	}

    /**
     * @param $presetId
     * @return mixed
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getPresetLeaveSysJoinWork($presetId)
	{
		return $this->getPresetValue(Tabs::INPUT__PRESET_LEAVE_SYS_JOIN_WORK, $presetId);
	}

    /**
     * @param $presetId
     * @return mixed
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getPresetLeaveSysLeaveWork($presetId)
	{
		return $this->getPresetValue(Tabs::INPUT__PRESET_LEAVE_SYS_LEAVE_WORK, $presetId);
	}

    /**
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected function beforeGetRequest()
	{
		$request = \Bitrix\Main\Application::getInstance()
			->getContext()->getRequest();

		if (!$request->get(Tabs::INPUT__REGROUP_ALL))
			return;

		Events::update();
	}

    /**
     * @param array $params
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
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
			$this->preset->updateName($presetId, $presetName);
		else {
			$presetName = $this->preset->getNameById($presetId);
			$inputName->setValue($presetName);
		}

		$params['description']  = $params['description'] . ' "' . $presetName . '"';
		$params['label']        = Loc::getMessage('rover_regroup__rule') . $presetName;
	}
}