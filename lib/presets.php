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

class Presets
{
	/**
	 * ���������� �� ��������, ������� ��������� �� ������������ ��������
	 * �������, ��� ������ ���� �������� ��� ������
	 * @param $sysGroupsIds - ������, � ������� ������ ������������
	 * @return array - ������ ��������, ����������� �� ������������
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getBySysGroupsIds(array $sysGroupsIds = [])
	{
		$resultPresets  = [];
		$options        = Options::getInstance();
		$presetsIds     = $options->preset->getIds();

		foreach ($presetsIds as $presetId)
		{
			/* ���������� ����������� ������� */
			if (!$options->getPresetEnabled($presetId))
				continue;

			/* �������� ������, �� ������� ���������������� ������ */
			$presetGroup = $options->getPresetGroup($presetId);

			/* ���� ���� ����������� � �������� ������������, ������� �� ������� � ������ */
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