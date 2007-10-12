<?php
// tiki-setup has already set the $language variable

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$languages = array();
$languages2 = array();
$languages = $tikilib->list_languages();
foreach($languages as $lingua) {
    // if a language name in the language's native tongue is provided,
    // use that for display; if none is provided,
    // use the two-letter code for display

    // LocalName (NativeName, code)
    if(preg_match("/\((.*)\,/", $lingua["name"], $tmp) === 1) {
        $disp = $tmp[1];
    } else { // no native language name provided
        // LocalName (code) or Unknown (code)
        $disp = $lingua["value"]; // get two-letter code

        // currently displayed language's entry has it's native/local name
        // in front of the brackets, not inside; get it from there
        // NativeName (code)
        if($disp === $prefs['language']) {
            $tmp = explode (" ", $lingua["name"]);
            $disp = $tmp[0];
        }
    }

    $tmp = array();
    $tmp["display"] = $disp;
    $tmp["value"] = $lingua["value"];
    $tmp["name"] = $lingua["name"];
    $languages2[] = $tmp;
}
$smarty->assign_by_ref('languages', $languages2);
?>
