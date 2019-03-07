<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.03.2016
 * Time: 15:15
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Regroup;

use Rover\Regroup\Config\Options;
use Bitrix\Main\ArgumentOutOfRangeException;

/**
 * Class Presets
 *
 * @package Rover\Regroup
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Presets
{
    /**
     * Возвращает ид фильтров, которые действуют на пользователя напрямую
     * Считаем, что список всех пресетов уже создан
     * @param array $sysGroupsIds
     * @return array
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getBySysGroupsIds($sysGroupsIds = array())
	{
		$resultPresets = array();
		if (empty($sysGroupsIds))
		    return $resultPresets;

		if (!is_array($sysGroupsIds))
		    $sysGroupsIds = [$sysGroupsIds];

		$options        = Options::getInstance();
		$presetsIds     = $options->preset->getIds();

		foreach ($presetsIds as $presetId)
		{
			/* Пропускаем выключенные пресеты */
			if (!$options->getPresetEnabled($presetId))
				continue;

			/* получаем группу, на которую распространяется пресет */
			$presetGroup = $options->getPresetGroup($presetId);

			/* Если есть пересечение с группами пользователя, заносим ид пресета в список */
			if (in_array($presetGroup, $sysGroupsIds))
				$resultPresets[] = $presetId;
		}

		return $resultPresets;
	}

	/**
	 * @param        $presetId
	 * @param string $event
	 * @param string $query
	 * @return mixed
	 * @throws ArgumentOutOfRangeException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getWorkGroups($presetId, $event = Group::EVENT__SYS_JOIN, $query = Group::QUERY__WORK_JOIN)
	{
		if (($event != Group::EVENT__SYS_JOIN)
			&& ($event != Group::EVENT__SYS_LEAVE))
			throw new ArgumentOutOfRangeException('event');

		if (($query != Group::QUERY__WORK_JOIN)
			&& ($query != Group::QUERY__WORK_LEAVE))
			throw new ArgumentOutOfRangeException('query');

		$methodName = 'getPreset' . $event . $query;

		return Options::getInstance()->$methodName($presetId);
	}
}