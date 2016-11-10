<?php
if(!$USER->IsAuthorized())
	return false;

/*IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("dmdrl.ufilter") != "D")
{
	return array(
		"parent_menu"	=> "global_menu_services",
		"sort"			=> 105,
		"icon"			=> "sale_menu_icon",
		"page_icon"		=> "sale_page_icon",
		"url"			=> "dmdrl.ufilter.php",
		"text"			=> GetMessage('dmdrl_ufilter_menu_text'),
		"title"			=> GetMessage('dmdrl_ufilter_menu_title'),
		"module"		=> "dmdrl.ufilter"
	);
}*/

return false;
