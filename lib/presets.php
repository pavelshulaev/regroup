<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.03.2016
 * Time: 15:15
 *
 * @author Shulaev (pavel.shulaev@gmail.com)
 */

namespace Rover\Regroup;

use Rover\Regroup\Config\Options;
use Bitrix\Main\ArgumentOutOfRangeException;

class Presets
{
	/**
	 * ¬озвращает ид фильтров, которые действуют на пользовател€ напр€мую
	 * —читаем, что список всех пресетов уже создан
	 * @param $sysGroupsIds - группы, в которые входит пользователь
	 * @return array - массив пресетов, действующих на пользовател€
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public static function getBySysGroupsIds(array $sysGroupsIds = [])
	{
		$resultPresets  = [];
		$options        = Options::getInstance();
		$presetsIds     = $options->getPresetsIds();

		foreach ($presetsIds as $presetId)
		{
			/* ѕропускаем выключенные пресеты */
			if (!$options->getPresetEnabled($presetId))
				continue;

			/* получаем группу, на которую распростран€етс€ пресет */
			$presetGroup = $options->getPresetGroup($presetId);

			/* ≈сли есть пересечение с группами пользовател€, заносим ид пресета в список */
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
	 * @author Shulaev (pavel.shulaev@gmail.com)
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