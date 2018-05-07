<?php
    global $APPLICATION, $regroupErrors;

    if (!$regroupErrors)
    {
        echo \CAdminMessage::ShowNote(GetMessage("MOD_UNINST_OK"));
    }
    else
    {
        $details = implode("<br/>", $regroupErrors);
        echo \CAdminMessage::ShowMessage(
            Array(
                "TYPE"=>"ERROR",
                "MESSAGE" =>GetMessage("MOD_UNINST_ERR"),
                "DETAILS"=>$details,
                "HTML"=>true)
        );
    }

?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
        <input type="hidden" name="lang" value="<?echo LANG?>">
        <input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
<form>