<?php
require_once ('tiki-setup.php');
require_once ('lib/cc/cclib.php');

if (!isset($_REQUEST['page'])) { $_REQUEST['page'] = ''; }
$page = $_REQUEST['page'];

if ($page == '') {
  $mainpage = $smarty->fetch("cc/index.tpl");
  $smarty->assign("content", $mainpage);
} elseif ($page == 'help') {
  $mainpage = $smarty->fetch("cc/help.tpl");
  $smarty->assign("content", $mainpage);
}

if ($user) {
	include "lib/cc/auth.php";
	$auth = new auth($user);
	$smarty->assign("ccusername", $user);
	$smarty->assign("ccuseraccount", $user); 
	$smarty->assign("ccuserid", $user);
	
	$ccuser = $cclib->user_infos($user);
	$smarty->assign("ccuser", $ccuser);

	if ($page == 'app') {
		require_once "lib/cc/db_tr_summary.php";
		$summary = new db_tr_summary();
		$summary->setAcct($auth->id);
		$smarty->assign("tr_summaryinfo",$summary->get());
		$app = $smarty->fetch("cc/app.tpl");
		$smarty->assign("content", $app);

	} elseif ($page == 'registercc') {
		require_once "lib/cc/list_cc.php";
		$cclist =  new list_cc();
		$cclistdata = $cclist->get();
		$smarty->assign("list", $cclistdata);
		$registercc = $smarty->fetch("cc/registercc.tpl");
		$smarty->assign("content", $registercc);

	} elseif ($page == 'registeruserforcc') {  
		require_once "lib/cc/db_ledger.php";
		$checkled = new db_ledger();
		$checkled ->loadOne($auth->id,$_REQUEST[cc_id]);
		if ($checkled->balance == '') {
			$ledger = new db_ledger();
			$ledger->create($auth->id,$_REQUEST[cc_id]);
			$ledger->send();
			require_once "lib/cc/db_cc.php";
			$cc = new db_cc();
			$cc->load($_REQUEST[cc_id]);
			$smarty->assign("id", $cc->id);
			$smarty->assign("cc_name", $cc->cc_name);
			$registered = $smarty->fetch("cc/registered.tpl");
			$smarty->assign("content", $registered);
		}

	} elseif ($page == 'history') {
		require_once "lib/cc/db_history.php";
		$db_history = new db_history();
		$db_history->setUsercc($auth->id, $_REQUEST['id']);
		$smarty->assign("data", $db_history->get());
		require_once "lib/cc/db_cc.php";
		$cc = new db_cc();
		$cc->load($_REQUEST['id']);
		$smarty->assign("username", $auth->username);
		$smarty->assign("cc", $cc->id);
		$smarty->assign("content", $smarty->fetch("cc/history.tpl"));

	} elseif ($page == 'newcc') {
		$newcc = $smarty->fetch("cc/newcc.tpl");
		$smarty->assign("content", $newcc);

	} elseif ($page == 'createcc') {
		require_once "lib/cc/db_cc.php";
		 $cc = new db_cc();
			$cc->establish(
					$auth->id,             //set owner_id to current user
				$_REQUEST['id'], 
					$_REQUEST['cc_name'], 
				$_REQUEST['cc_description'],
				$_REQUEST['requires_approval']);

			if ($cc->errors != '') {
				// handle errors on this page.
			} else {
				$cc->send();
				$smarty->assign("id", $cc->getid());
				$smarty->assign("cc_name", $cc->getcc_name());
				$smarty->assign("cc_description", $cc->getcc_description());
				$smarty->assign("requires_approval", $cc->getRequires_approval());
				$createcc = $smarty->fetch("cc/createcc.tpl");
				$smarty->assign("content", $createcc);
			}

	} elseif ($page == 'admincc') {
		require_once "lib/cc/list_admincc.php";
		$admincclist =  new list_admincc();
		$admincclist->setAuthid($auth->id);
		$cclistdata = $admincclist->get();
		$smarty->assign("list", $cclistdata);
		$admincc = $smarty->fetch("cc/admincc.tpl");
		$smarty->assign("content", $admincc);

	} elseif ($page == 'editcc') {
		$editcc = $smarty->fetch("cc/editcc.tpl");
		$smarty->assign("content", $editcc);

	} elseif ($page == 'tr_record') {
		require_once "lib/cc/list_regcc.php";
		$reg = new list_regcc();
		$reg->setRegister($auth->id);
		$smarty->assign("regcc", $reg->getHtmlSelect());
		$transaction = $smarty->fetch("cc/tr_record.tpl");
		$smarty->assign("content", $transaction);
		$this->trcount = 0;		    

	} elseif ($page == 'tr_recorded') {
		require_once "lib/cc/db_transaction.php";
		$transaction = new db_transaction();

		if ($this->trcount == 0) {
			$transaction->create($auth->id, 
			$_REQUEST['other_id'],
			$_REQUEST['tr_item'],
			$_REQUEST['tr_amount'],
			$_REQUEST['cc_id']);
		} else {
		}

		if ($transaction->errors == '') {
			$smarty->assign("fromaccount", $auth->id);
			$smarty->assign("toaccount", $_REQUEST['other_id']);
			$smarty->assign("description", $transaction->tr_item);
			$smarty->assign("amount", $transaction->tr_amount);
			$smarty->assign("cc", $_REQUEST['cc_id']);
			require_once "lib/cc/db_tr_summary.php";
			$summary = new db_tr_summary();
			$summary->setAcct($auth->id);
			$smarty->assign("tr_summaryinfo",$summary->get());
			$tr_processed = $smarty->fetch("cc/tr_recorded.tpl");
			$smarty->assign("content", $tr_processed);
			$this->trcount = 1;		 
		} else {
			$smarty->assign("reason", $transaction->errors);
			require_once "lib/cc/list_regcc.php";
			$reg = new list_regcc();
			$reg->setRegister($auth->id);
			$smarty->assign("regcc", $reg->getHtmlSelect());
			$smarty->assign("form", $smarty->fetch("tr_record.tpl"));
			$tr_failed = $smarty->fetch("cc/tr_failed.tpl");
			$smarty->assign("content", $tr_failed);
		}
	}
}

$menubar = $smarty->fetch("cc/menu.tpl");
$smarty->assign("menubar", $menubar);
$smarty->assign('mid', 'cc/internal.tpl');
$smarty->display('tiki.tpl');
?>
