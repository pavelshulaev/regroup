<?php
    global $APPLICATION, $errors; 

    if (empty($errors))
	{
        echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
	}
    else
	{
        $details = implode("<br/>", $errors);
        echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage("MOD_INST_ERR"), "DETAILS"=>$details, "HTML"=>true));
    }
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=GetMessage("MOD_BACK")?>">
<form>