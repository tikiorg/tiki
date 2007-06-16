<?php
// Initialization
$section = 'tinvoice';
require_once ('tiki-setup.php');
require_once ('lib/tinvoice/tinvoicelib.php');
require_once ('lib/webmail/contactlib.php');
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


if ($feature_categories == 'y') {
    include_once ('lib/categories/categlib.php');
}

//$owner=$tinvoicelib->get_owner_contact($contactId);

$tinvoicelib=new TinvoiceLib($dbTiki);

if (isset($_REQUEST['delete'])) {
    $delete_invoice_id=(int)$_REQUEST['delete'];
    $invoice=$tinvoicelib->get_invoice($delete_invoice_id);
    if ($invoice === NULL) {
	die("facture introuvable");
    } else {
	$invoice->delete();
    }
    unset($invoice);
}

$id_emitter=isset($_REQUEST['id_emitter']) ? (int)$_REQUEST['id_emitter'] : NULL;
$id_receiver=isset($_REQUEST['id_receiver']) ? (int)$_REQUEST['id_receiver'] : NULL;
$invoices=$tinvoicelib->list_invoices($id_emitter, 'tiki', $id_receiver, 'tiki');
$smarty->assign('invoices', $invoices);
$smarty->assign('me_tikiid', $userlib->get_user_id($user));

$contacts=$contactlib->list_contacts($user);
$smarty->assign('contacts', $contacts);

// Display the template
$smarty->assign('mid', 'tiki-tinvoice_list.tpl');
$smarty->display("tiki.tpl");
?>
