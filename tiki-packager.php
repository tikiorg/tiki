<?
/*
  TikiPackager module, copyright by Gongo aka Dimitri Smits 
  
  This file is part of TikiWiki, and is LGPLed. See the licence at http://www.fsf.org
  or the one accompanying tikiwiki
  
  Version: 0.1
*/

require_once ('tiki-setup.php');
require_once ('tikiticketlib.php');
require_once ('packages/packagerlib.php');

if ($user != 'admin') 
{
	if (($tiki_p_admin != 'y') && ($tiki_p_admin_packager != 'y') && ($tiki_p_edit_package != 'y' )) 
	{
		$smarty->assign('msg', tra("You don't have permission to use this feature"));

		$smarty->display("error.tpl");
		die;
	}
}

//$wizardry = array(
//
//);


function twp_displayPackageList()
{
    global $smarty;
    
    $smarty->assign('initials', split(' ','0 1 2 3 4 5 6 7 8 9 a b c d e f g h i j k l m n o p q r s t u v w x y z'));
    
    $numrows = 10;
    if (isset($_REQUEST['numrows'])) 
    {
        if ( is_numeric( $_REQUEST['numrows'] )) 
        {
            $numrows = $_REQUEST['numrows'];
            if ($numrows <= 0) 
            {
                $numrows = 10;
            }
        }
    }
    $smarty->assign('numrows', $numrows);

    $sortmode = 1; // ascending
    if (isset($_REQUEST['sort_mode'])) 
    {
        $sortmode = $_REQUEST['sort_mode'];
        $sortmode = ( ( $sortmode == 1 ) || ( $sortmode == 0 )) ? $sortmode : 1;
    }
    
    $smarty->assign('sort_mode', $sortmode);
    
    $beginchar = '';
    if (isset($_REQUEST['initial'])) 
    {
        if ( strlen($_REQUEST['initial']) == 1) 
        {
            $beginchar = $_REQUEST['initial'];
        }
    }
    $smarty->assign('initial', $beginchar);
    
    $searchstring = '';
    if (isset($_REQUEST['find'])) 
    {
        $searchstring=$_REQUEST['find'];
    }
    $smarty->assign('find', $searchstring);
    
    $offset = 0;
    if (isset($_REQUEST['offset'])) 
    {
        if (is_numeric($_REQUEST['offset'])) 
        {
            $offset = $_REQUEST['offset'];
        }
    }
    $smarty->assign('offset', $offset);

    $mfDir = fetch_manifest_dir($numrows, $beginchar , $sortmode, $offset, $searchstring);
  
    $smarty->assign_by_ref('manifests', $mfDir->get_listing_values() );
    
    $offset = 0;
    if (isset($_REQUEST['offset'] )) 
    {
        $offset = $_REQUEST['offset'];
    }
    
    $totalNrRecords = $mfDir->get_listing_count();
    
    if ($totalNrRecords <= 0) 
    {
        $totalNrRecords = 1;
    }
    
    $smarty->assign('count_my_pages', ceil($totalNrRecords / $numrows));
    $smarty->assign('actual_page', 1 + ($offset / $numrows));

    if ($totalNrRecords > ($offset + $numrows)) 
    {
	   $smarty->assign('next_offset', $offset + $numrows);
    } 
    else 
    {
	   $smarty->assign('next_offset', -1);
    }
    
    if ($offset > 0) 
    {
	   $smarty->assign('prev_offset', $offset - $numrows);
    } 
    else 
    {
	   $smarty->assign('prev_offset', -1);
    }

    $smarty->assign('uses_tabs', 'y');
    $smarty->assign('mid', 'tiki-packager_admin_packages.tpl');
}

function twp_dispatch_actions($action) 
{
    global $smarty;
    if ($action == 'delete-package') 
    {
        check_ticket('tikiwiki-packager');
        if (isset($_REQUEST['package'])) 
        {
            $package = $_REQUEST['package'];
            remove_manifest($package);
        }
        return twp_displayPackageList();
    } 
    
    if ($action == tra('Remove selected packages')) 
    {
        check_ticket('tikiwiki-packager');
        if (isset($_REQUEST['packages'])) 
        {
            $packages = $_REQUEST['packages'];
            foreach ( $packages as $package ) 
            {
                remove_manifest($package);
            }
            remove_manifest($package);
        }
        return twp_displayPackageList();
    }
}

$smarty->assign('myURL', 'tiki-packager.php');

if (isset($_REQUEST["action"]) ) 
{
    twp_dispatch_actions($_REQUEST['action']);
    
    $smarty->assign('uses_tabs', 'y');
} else {
    twp_displayPackageList();

    ask_ticket('tikiwiki-packager');
}    
    
$smarty->display("tiki.tpl");

die();

//check_ticket('packager');

if ( !isset($_REQUEST["pkgSelectedTables"]) || ( count($_REQUEST["pkgSelectedTables"] ) < 1) ) 
{
  $smarty->assign('wizard', 'selectTables');
  $smarty->assign('metatables', $dbTiki->MetaTables());
} else {
  $smarty->assign('wizard', 'tableContent');
  $tablesInfo = array();
  //echo '<pre>';
  $dbdict = NewDataDictionary($dbTiki);


  for ( $selectedTablesIndex = count($_REQUEST["pkgSelectedTables"]) - 1; $selectedTablesIndex >= 0 ; $selectedTablesIndex-- ) {
    // a ADOFieldObject array is returned.
    // ADOFieldObjects contain the following fields
    //   name, max_length, scale, type, not_null, has_default, default_value, primary_key, auto_increment, binary
    
    $fieldObjects = $dbTiki->MetaColumns($_REQUEST['pkgSelectedTables'][$selectedTablesIndex]);
   
    $tableInfo = array();
    //print_r($fieldObjects);
    $fieldCount = 0;
    foreach ($fieldObjects as $field) {
        //print_r($field);
        
        $field->meta_type = $dbdict->MetaType($field, $field->max_length);
        
        $fieldArray = array(
                "fieldName" => $field->name,
                "fieldDefault" => $field->default_value,
                "fieldHasDefault" => $field->has_default,
                "fieldMaxLength" => $field->max_length,
                "fieldType" => $field->type,
                "fieldNotNull" => $field->not_null,
                "fieldScale" => $field->scale,
                "fieldAutoIncrement" => $field->auto_increment,
                "fieldPrimaryKey" => $field->primary_key,
                "fieldBinary" => $field->binary,
                "fieldMetaType" => $field->meta_type
        );
        
        
        //print_r($field);
        $tableInfo[$fieldCount] = $fieldArray;
        $fieldCount++;
    }    
    
    $tablesInfo[$selectedTablesIndex] =
        array( 
            "TableName" => $_REQUEST['pkgSelectedTables'][$selectedTablesIndex],
            "TableInfo" => $tableInfo
        );
  }
  $smarty->assign_by_ref('metatableInfo', $tablesInfo );
}

//print_r($tablesInfo);
//  echo '</pre>';


$smarty->assign('mid','tiki-packager_showtables.tpl');
$smarty->display("tiki.tpl");

?>