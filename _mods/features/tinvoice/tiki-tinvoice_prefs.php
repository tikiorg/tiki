<?php
// Initialization
$section = 'tinvoice';
require_once ('tiki-setup.php');

include_once ('lib/tinvoice/tinvoicelib.php');

if ($feature_categories == 'y') {
    include_once ('lib/categories/categlib.php');
}
if ($feature_tinvoice != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_tinvoice");

	$smarty->display("error.tpl");
	die;
}
if ($tiki_p_tinvoice != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}
class tiki_invoice_prefs {
    var $tinvoicelib;

    /*public*/ function tiki_invoice_prefs() {
	global $dbTiki, $smarty;
	
	$this->tinvoicelib=new tinvoiceLib($dbTiki);

	if (isset($_REQUEST['invoice_saveprefs']))
	    $this->save_prefs();

	$this->open_prefs();

	// Display the template
	$smarty->assign('mid', 'tiki-tinvoice_prefs.tpl');
	$smarty->display("tiki.tpl");
    }

    /*private*/ function open_prefs() {
	global $smarty;
	
	$keys=array('emitter_address', 'image', 'emitter_rib', 'emitter_tvanumber', 'footer', 'locale', 
		    'numberingformat', 'daycountlimit', 'custom0', 'custom1', 'custom2', 'custom3');
	$show='';
	foreach($keys as $key) {
	    $value=$this->tinvoicelib->get_pref($key);
	    if ($value !== NULL) {
		$show.="show_hide_line('invoice_$key', 1);\n";
		switch($key) {
		case 'custom0':
		case 'custom1':
		case 'custom2':
		case 'custom3':
		case 'emitter_image':
		case 'emitter_rib':
		    $smarty->assign('invoice_'.$key, explode("\x01", $value));
		    break;
		default:
		    $smarty->assign('invoice_'.$key, $value);
		    break;
		}
	    }
	}
	$smarty->assign('invoice_showcmd', $show);
    }

    /*private*/ function save_prefs() {
	$keys=array('emitter_address', 'image', 'emitter_rib', 'emitter_tvanumber', 'footer', 'locale', 
		    'numberingformat', 'daycountlimit', 'custom0', 'custom1', 'custom2', 'custom3');
	foreach($keys as $key) {
	    if (!isset($_REQUEST['active_invoice_'.$key])
		|| $_REQUEST['active_invoice_'.$key]=='0') {
		$this->tinvoicelib->set_pref($key, NULL);
		continue;
	    }
	    switch($key) {
	    case 'custom0':
	    case 'custom1':
	    case 'custom2':
	    case 'custom3':
		$custom=$_REQUEST['invoice_'.$key];
		if (strstr($custom[0], "\x01") !== FALSE) die("invalid char");
		if (strstr($custom[1], "\x01") !== FALSE) die("invalid char");
		$this->tinvoicelib->set_pref($key, $custom[0]."\x01".$custom[1]);
		break;
	    case 'image':
		$image=$_REQUEST['invoice_image'];
		for ($i=0; $i<5; $i++)
		    if (strstr($image[$i], "\x01") !== FALSE) die("invalid char");
		//$this->tinvoicelib->set_pref_image
		break;
	    case 'emitter_rib':
		$rib=$_REQUEST['invoice_emitter_rib'];
		for ($i=0; $i<7; $i++)
		    if (strstr($rib[$i], "\x01") !== FALSE) die("invalid char");
		$this->tinvoicelib->set_pref_emitter_rib($rib[0], $rib[1], $rib[2],
							 $rib[3], $rib[4], $rib[5], $rib[6]);
		break;
	    default:
		$value=$_REQUEST['invoice_'.$key];
		$this->tinvoicelib->set_pref($key, $value);
		break;
	    }
	}
    }

}

new tiki_invoice_prefs();


?>
