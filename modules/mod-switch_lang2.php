<?php
// tiki-setup has already set the $language variable
$languages = array();
$languages = $tikilib->list_languages();

$langs = array();
foreach($languages as $lingua) {
    switch($lingua["value"]) {
        case 'cs': $flag = "img/flags/Czech_Republic.gif"; break;
        case 'da': $flag = "img/flags/Denmark.gif"; break;
        //case 'de': $flag = "img/flags/Germany.gif"; break;
        case 'de': $flag = "img/flags/German.gif"; break; // special flag
        //case 'en': $flag = "img/flags/United_Kingdom.gif"; break;
        case 'en': $flag = "img/flags/English.gif"; break; // special flag
        case 'es': $flag = "img/flags/Spain.gif"; break;
        //case 'el': $flag = "img/flags/Greek.gif"; break;
        case 'fr': $flag = "img/flags/France.gif"; break;
        case 'he': $flag = "img/flags/Israel.gif"; break;
        case 'it': $flag = "img/flags/Italy.gif"; break;
        case 'ja': $flag = "img/flags/Japan.gif"; break;
        case 'nl': $flag = "img/flags/Netherlands.gif"; break;
        case 'no': $flag = "img/flags/Norway.gif"; break;
        case 'pl': $flag = "img/flags/Poland.gif"; break;
        case 'ru': $flag = "img/flags/Russia.gif"; break;
        case 'sv': $flag = "img/flags/Sweden.gif"; break;
        case 'tw': $flag = "img/flags/Taiwan.gif"; break;
        case 'cn': $flag = "img/flags/China.gif"; break;
        default: $flag = "img/flags/Other.gif";
    }
    $tmp = array();
    $tmp["value"] = $lingua["value"];
    $tmp["name"] = $lingua["name"];
    $tmp["flag"] = $flag;
    $langs[] = $tmp;
}
$smarty->assign_by_ref('languages', $langs);
?>
