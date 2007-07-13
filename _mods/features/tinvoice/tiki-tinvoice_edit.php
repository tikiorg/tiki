<?php
// Initialization
$section = 'tinvoice';
require_once ('tiki-setup.php');
require_once ("lib/ajax/ajaxlib.php");
require_once ('lib/webmail/contactlib.php');
require_once ('lib/tinvoice/tinvoicelib.php');

if ($feature_tinvoice != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_tinvoice");

	$smarty->display("error.tpl");
	die;
}
if ($feature_ajax != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_ajax");

	$smarty->display("error.tpl");
	die;
}
if ($tiki_p_tinvoice_edit != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}
if ($feature_categories == 'y') {
    include_once ('lib/categories/categlib.php');
}


function getContactExt($contact, $exts, $fieldname) {
    foreach($exts as $ext) {
	if ($ext['fieldname'] == $fieldname) {
	    if (isset($contact['ext'][$ext['fieldId']])) {
		return $contact['ext'][$ext['fieldId']];
	    } else return NULL;
	}
    }
    return NULL;
}

function getthedate($str) {
    if (preg_match('/^[0-9]{4}-[01][0-9]-[0123][0-9]$/', $str)) return $str;
    if (preg_match('/^[0-9]{8,20}$/', $str)) return strftime('%Y-%m-%d', $str);
    return $str;
}

/*static*/
class tiki_edit_invoice {
    /*static public*/ function init() {
	global $smarty;
	global $dbTiki;
	global $user, $userlib;
	global $contactlib;
	global $ajaxlib;

	$tinvoicelib = new TinvoiceLib($dbTiki);
	$id_invoice=isset($_REQUEST['invoiceId']) ? (int)$_REQUEST['invoiceId'] : 0;

	if ($id_invoice > 0) {
	    $tinvoice=$tinvoicelib->get_invoice($id_invoice);
	    if ($tinvoice === NULL) {
		$smarty->assign('msg', tra("Invoice not found"));
		$smarty->display("error.tpl");
		die;
	    }
	} else {
	    $tinvoice=$tinvoicelib->new_invoice();
	}

	if (isset($_REQUEST['create_invoice'])) {
	    $id_invoice=tiki_edit_invoice::create_or_edit_invoice($tinvoice);
	}

	if (isset($_REQUEST['pdf']) && ($id_invoice > 0))
	    tiki_edit_invoice::show_invoice($tinvoice);
	
	if ($id_invoice > 0) {
	    tiki_edit_invoice::open_invoice($tinvoice);
	} else {
	    tiki_edit_invoice::open_new_invoice($tinvoice);
	}
	
	$smarty->assign('invoiceId', $id_invoice);
	//$smarty->assign('me_tikiid', $userlib->get_user_id($user));

	// Display the template

	$smarty->assign('contacts', $contactlib->list_contacts($user));
	//$c=$contactlib->list_contacts($user);
	//var_dump($c[0][ext][7]);

	$smarty->assign('mid', 'tiki-tinvoice_edit.tpl');
	domyajax();
	$smarty->display("tiki.tpl");
    }

    /*static public*/ function create_or_edit_invoice($tinvoice) {
	global $user, $userlib;
	global $contactlib;

	$id_invoice=$tinvoice->get_id();
	$isnew=($id_invoice == 0);
	
	/* remove lines */
	if (!$isnew) {
	    $lines_removed=explode(',', $_REQUEST['invoice_lines_removed']);
	    foreach($lines_removed as $line_removed) {
		if ($line_removed > 0) {
		    $tinvoice->remove_line($line_removed);
		}
	    }
	}

	/* add or update lines */
	$idlist=$_REQUEST['invoice_idlist'];
	$idlist=explode(",", $idlist);
	
	foreach($idlist as $id) {
	    $ref=$_REQUEST["invoice_ref_$id"];
	    $designation=$_REQUEST["invoice_designation_$id"];
	    $vat=$_REQUEST["invoice_tva_$id"];
	    $qty=$_REQUEST["invoice_qty_$id"];
	    $unitprice=$_REQUEST["invoice_unitprice_$id"];
	    
	    if ($id > 0)
		$tinvoice->update_line($id, $ref, $designation, $vat, $qty, $unitprice);
	    else
		$tinvoice->add_line($ref, $designation, $vat, $qty, $unitprice);
	}

	$tinvoice->set_receiver($_REQUEST["invoice_id_receiver"], 'contact');
	$tinvoice->set_date(getthedate($_REQUEST["invoice_date"]));
	$tinvoice->set_libelle($_REQUEST['invoice_libelle']);
	if ($_REQUEST["invoice_datelimit"] != "")
	    $tinvoice->set_datelimit(getthedate($_REQUEST["invoice_datelimit"]));
	$tinvoice->set_refdevis($_REQUEST['invoice_refdevis']);
	$tinvoice->set_refbondecommande($_REQUEST['invoice_refbondecommande']);
	$tinvoice->set_receiver_tvanumber($_REQUEST['invoice_receiver_tvanumber']);
	$tinvoice->set_receiver_address($_REQUEST['receiveraddress']);

	$receiver=$contactlib->get_contact((int)$_REQUEST["invoice_id_receiver"], $user);
        $exts=$contactlib->get_ext_list($user);

	$tinvoice->commit();
	return $tinvoice->get_id();
    }
    
    /*static public*/ function open_invoice($tinvoice) {
	global $dbTiki, $smarty;
	
	$lines=$tinvoice->get_lines();
	$str="";
	foreach($lines as $k => $line) {
	    $str.="add_invoice_line("
		.$line['id'].", '"
		.str_replace("'", "\\'", $line['ref'])."', '"
		.str_replace("'", "\\'", $line['designation'])."', "
		.$line['vat'].", "
		.$line['quantity'].", "
		.$line['unitprice'].");\n";
	}
	$smarty->assign("initinvoicelines", $str);
	$smarty->assign("invoice_date", date("Y-m-d", strtotime($tinvoice->get_date())));
	$smarty->assign("invoice_libelle", $tinvoice->get_libelle());
	$invoice_receiver=$tinvoice->get_receiver();
	$smarty->assign("invoice_id_receiver", $invoice_receiver['id_receiver']);
	if ($tinvoice->get_datelimit() !== NULL)
	    $smarty->assign("invoice_datelimit", date("Y-m-d", strtotime($tinvoice->get_datelimit())));
	$smarty->assign("invoice_refdevis", $tinvoice->get_refdevis());
	$smarty->assign("invoice_refbondecommande", $tinvoice->get_refbondecommande());
	$smarty->assign("invoice_receiver_tvanumber", $tinvoice->get_receiver_tvanumber());
	$smarty->assign("receiveraddress", $tinvoice->get_receiver_address());
    }
    
    /*static public*/ function open_new_invoice($tinvoice) {
	global $smarty;
	$smarty->assign("initinvoicelines",
			"add_invoice_line(--last_invoice_id, '', '', 19.60, 1, 0.00);\n");
	$smarty->assign("invoice_date", date("Y-m-d"));

	$daycountlimit=$tinvoice->tinvoicelib->get_pref("daycountlimit");
	if (!$daycountlimit) $daycountlimit=30;
	$smarty->assign("invoice_datelimit", date("Y-m-d", strtotime("now + ".$daycountlimit." day")));
    }

    /*static public*/ function show_invoice($tinvoice) {
	global $dbTiki;
	require_once('lib/tinvoice/tinvoicepdf.php');

	$tinvoicepdf=new Tinvoicepdf($tinvoice);
	$tinvoicepdf->make_pdf();
	exit(0);
    }
    
}


function myajax_getcontact($arg) {
    global $user, $userlib;
    global $contactlib;

    $exts=$contactlib->get_ext_list($user);
    $contact=$contactlib->get_contact((int)$arg, $user);

    $objResponse = new xajaxResponse();

    $address='';
    

    $tmp=getContactExt($contact, $exts, 'Company');
    if ($tmp !== NULL) {
    	$address.=$tmp."\n";
    } else {
	if (isset($contact['lastName'])) {
		if (isset($contact['firstName'])) {
	    		$address.=$contact['firstName'].' ';
		}
		$address.=$contact['lastName'];
		$address.="\n";
	}
    }
    $tmp=getContactExt($contact, $exts, 'Organization');
    if ($tmp !== NULL) $address.=$tmp."\n";

    $tmp=getContactExt($contact, $exts, 'Department');
    if ($tmp !== NULL) $address.=$tmp."\n";

    $tmp=getContactExt($contact, $exts, 'Division');
    if ($tmp !== NULL) $address.=$tmp."\n";

    $tmp=getContactExt($contact, $exts, 'Street Address');
    if ($tmp !== NULL) $address.=$tmp."\n";

    $tmp=getContactExt($contact, $exts, 'Zip Code');
    if ($tmp !== NULL) $address.=$tmp." ";

    $tmp=getContactExt($contact, $exts, 'City');
    if ($tmp !== NULL) $address.=$tmp."\n";

    $tmp=getContactExt($contact, $exts, 'State');
    if ($tmp !== NULL) $address.=$tmp."\n";

    $tmp=getContactExt($contact, $exts, 'Country');
    if ($tmp !== NULL) $address.=$tmp."\n";

    $objResponse->addAssign("receiveraddress", "value", $address);

    $tmp=getContactExt($contact, $exts, 'VAT Number');
    $objResponse->addAssign("invoice_receiver_tvanumber", "value", $tmp === NULL ? '' : $tmp);

    return $objResponse;
}

function domyajax() {
    global $ajaxlib;

    $ajaxlib->registerFunction("myajax_getcontact");
    $ajaxlib->processRequests();
}

if (isset($_REQUEST['xajax'])) {
    domyajax();
} else {
    tiki_edit_invoice::init();
}

?>
