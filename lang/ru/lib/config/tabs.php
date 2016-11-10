<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.03.2016
 * Time: 13:02
 *
 * @author Shulaev (pavel.shulaev@gmail.com)
 */
use Rover\Regroup\Config\Tabs;

$MESS[Tabs::TAB__MAIN . '_label']       = 'Настройки';
$MESS[Tabs::TAB__MAIN . '_description'] = 'Общие настройки';

$MESS[Tabs::INPUT__LEAVE_MODERATORS . '_label'] = 'Оставлять пользователя в рабочей группе, если он модератор';
$MESS[Tabs::INPUT__CONNECT_DISC . '_label']     = 'Подключать диск группы к диску пользователя';
$MESS[Tabs::INPUT__REGROUP_ALL . '_label']      = 'Обновить для всех';
$MESS[Tabs::INPUT__NEW_PRESET . '_label']       = 'Создать новое правило';
$MESS[Tabs::INPUT__NEW_PRESET . '_popup']       = 'Введите имя правила';
$MESS[Tabs::INPUT__NEW_PRESET . '_default']     = 'Новое правило';

$MESS[Tabs::TAB__PRESET . '_label']         = 'Правило';
$MESS[Tabs::TAB__PRESET . '_description']   = 'Настройки правила';

$MESS['preset__header_common_label']            = 'Общие';
$MESS[Tabs::INPUT__PRESET_ENABLED . '_label']   = 'Включено';
$MESS[Tabs::INPUT__PRESET_NAME . '_label']      = 'Название правила';
/*$MESS[Tabs::INPUT__PRESET_SORT . '_label']   = 'Включен';*/
$MESS[Tabs::INPUT__PRESET_GROUP . '_label']     = 'Применяется к группе';

$MESS['preset__header_join_sys_label']                      = 'При присоединении к системной группе и обновлении';
$MESS[Tabs::INPUT__PRESET_JOIN_SYS_JOIN_WORK . '_label']    = 'Рабочие группы, к которым присоединяется пользователь';
$MESS[Tabs::INPUT__PRESET_JOIN_SYS_LEAVE_WORK . '_label']   = 'Рабочие группы, из которых выходит пользователь';

$MESS['preset__header_leave_sys_label']                     = 'При выходе из системной группы';
$MESS[Tabs::INPUT__PRESET_LEAVE_SYS_JOIN_WORK . '_label']   = 'Рабочие группы, к которым присоединяется пользователь';
$MESS[Tabs::INPUT__PRESET_LEAVE_SYS_LEAVE_WORK . '_label']  = 'Рабочие группы, из которых выходит пользователь';

$MESS['preset__header_remove_label']            = 'Удалить правило';
$MESS[Tabs::INPUT__PRESET_REMOVE . '_label']    = 'Удалить';
$MESS[Tabs::INPUT__PRESET_REMOVE . '_popup']    = 'Вы уверены, что хотите удалить это правило?';
