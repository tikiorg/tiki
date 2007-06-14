<?php
// Initialization
$section = 'tinvoice';
require_once ('tiki-setup.php');

require_once ('lib/webmail/contactlib.php');
require_once ('lib/tinvoice/tinvoicelib.php');

if ($feature_tinvoice != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_tinvoice");

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

/*static*/
class tiki_edit_invoice {
    /*static public*/ function init() {
	global $smarty;
	global $dbTiki;
	global $user, $userlib;
	global $contactlib;

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
	$smarty->assign('mid', 'tiki-tinvoice_edit.tpl');
	$smarty->display("tiki.tpl");
    }

    /*static public*/ function create_or_edit_invoice($tinvoice) {

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
	$tinvoice->set_date($_REQUEST["invoice_date"]);
	$tinvoice->set_libelle($_REQUEST['invoice_libelle']);
	if ($_REQUEST["invoice_datelimit"] != "")
	    $tinvoice->set_datelimit($_REQUEST["invoice_datelimit"]);
	$tinvoice->set_refdevis($_REQUEST['invoice_refdevis']);
	$tinvoice->set_refbondecommande($_REQUEST['invoice_refbondecommande']);
	$tinvoice->set_receiver_tvanumber($_REQUEST['invoice_receiver_tvanumber']);
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
	if ($tinvoice->get_datelimit() !== NULL)
	    $smarty->assign("invoice_datelimit", date("Y-m-d", strtotime($tinvoice->get_datelimit())));
	$smarty->assign("invoice_refdevis", $tinvoice->get_refdevis());
	$smarty->assign("invoice_refbondecommande", $tinvoice->get_refbondecommande());
	$smarty->assign("invoice_receiver_tvanumber", $tinvoice->get_receiver_tvanumber());
    }
    
    /*static public*/ function open_new_invoice($tinvoice) {
	global $smarty;
	$smarty->assign("initinvoicelines",
			"add_invoice_line(--last_invoice_id, '', '', 19.60, 1, 0.00);\n");
	$smarty->assign("invoice_date", date("Y-m-d"));
	$smarty->assign("invoice_datelimit", date("Y-m-d", strtotime("now + 1 month")));
    }

    /*static public*/ function show_invoice($tinvoice) {
	global $dbTiki;
	require_once('lib/tinvoice/tinvoicepdf.php');

	$tinvoicepdf=new Tinvoicepdf($tinvoice);
	$tinvoicepdf->make_pdf();
	exit(0);
    }
    
}

tiki_edit_invoice::init();

?>
