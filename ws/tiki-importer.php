<?php

require_once('tiki-setup.php');
require_once('lib/importer/TikiImporter.php');
require_once('lib/importer/TikiImporter_Wiki.php');

// TODO: define a permission for the tiki importer
if ($tiki_p_admin != 'y') {
    $smarty->assign('msg', tra("Permission denied you cannot view this section"));
    $smarty->display("error.tpl");
    die;
}

if (!empty($_POST['importerClassName']))
    $importerClassName = $_POST['importerClassName'];

if (!empty($_FILES['importFile'])) {
    require_once('lib/importer/' . $importerClassName . '.php');
    $importer = new $importerClassName(); 
} else if (!empty($_POST['importerClassName'])) {
    if (!file_exists('lib/importer/' . $importerClassName . '.php')) {
        $smarty->assign('msg', tra("Invalid software name"));
        $smarty->display("error.tpl");
        die;
    }
    
    require_once('lib/importer/' . $importerClassName . '.php');
    // TODO: get software specific options from class
    
    $smarty->assign('softwareSpecificOptions', true);
    $smarty->assign('importerClassName', $importerClassName);
} else {
    // $availableSoftwares is an array thtat control the list of available software importers.
    // The array key is the name of the importer class and the value is the name of the software
    $availableSoftwares = array('TikiImporter_Wiki_Mediawiki' => 'Mediawiki');
    $smarty->assign('availableSoftwares', $availableSoftwares);
    $smarty->assign('chooseSoftware', true);
}

$smarty->assign('mid', 'tiki-importer.tpl');
$smarty->display('tiki.tpl');

?>