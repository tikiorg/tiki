<?php
require_once ('tiki-setup.php');
require_once ('lib/cc/cclib.php');

if (!isset($_REQUEST['page'])) { $_REQUEST['page'] = ''; }
$page = $_REQUEST['page'];

$mid = "cc/index.tpl";

if ($page == 'help') {
  $mid = "cc/help.tpl";
}

if ($user) {
	
	$ccuser = $cclib->user_infos($user);
	$smarty->assign_by_ref("ccuser", $ccuser);

	if ($page == 'my') {
		$thelist = $cclib->get_ledgers(0,-1,'last_tr_date_desc',$user);
		$smarty->assign("thelist",$thelist['data']);
		$smarty->assign("userid",$user);
		$mid = "cc/app.tpl";

	} elseif ($page == 'app') {
		$thelist = $cclib->get_ledgers();
		$smarty->assign("thelist",$thelist['data']);
		$mid = "cc/app.tpl";

	} elseif ($page == 'tr_my') {
		$thelist = $cclib->get_transactions(0,-1,'tr_date_desc','',$user);
		$smarty->assign('thelist',$thelist['data']);
		$mid = "cc/history.tpl";
	
	} elseif ($page == 'history') {
		$thelist = $cclib->get_transactions();
		$smarty->assign('thelist',$thelist['data']);
		$mid = "cc/history.tpl";
	
	} elseif ($page == 'registercc') {
		if (isset($_REQUEST['register'])) {
			if ($cclib->is_currency($_REQUEST['register'])) {
				$cclib->register_cc($_REQUEST['register'],$user);
				$ccuser = $cclib->user_infos($user);
			}
		} elseif (isset($_REQUEST['unregister'])) {
			if ($cclib->is_currency($_REQUEST['unregister'])) {
				$cclib->unregister_cc($_REQUEST['unregister'],$user);
				$ccuser = $cclib->user_infos($user);
			}
		}
		$thelist = $cclib->get_currencies();
		$smarty->assign('thelist', $thelist['data']);
		$mid = "cc/registercc.tpl";

	} elseif ($page == 'newcc') {
		if (isset($_REQUEST['id']) and $tiki_p_cc_create == 'y') {
			if (isset($_REQUEST['id']) and isset($_REQUEST['cc_name'])) {
				if (!isset($_REQUEST['cc_description'])) $_REQUEST['cc_description'] = '';
				if (isset($_REQUEST['owner']) and $tiki_p_cc_admin == 'y') {
					$owner = $_REQUEST['owner'];
				} else {
					$owner = $user;
				}
				if (!$cclib->replace_currency($owner,$_REQUEST['id'],$_REQUEST['cc_name'],$_REQUEST['cc_description'],$_REQUEST['requires_approval'],$_REQUEST['listed'])) {
					$this->feedback[] = array('num'=>1,'mes'=>$cclib->msg);
				} else {
					$this->feedback[] = array('num'=>0,'mes'=>"Currency ". $_REQUEST['id'] ." created.");
				}
			}
		}
		$mid = "cc/ccform.tpl";

	} elseif ($page == 'admincc' and $tiki_p_cc_admin == 'y') {
		if (isset($_REQUEST['cc_id'])) {
			$info = $cclib->get_currency($_REQUEST['cc_id']);
			if ($tiki_p_cc_admin == 'y' or $info['owner_id'] == $user) {
				
				$smarty->assign('info', $info);
				$mid = "cc/ccform.tpl";
			} else {
				$smarty->assign('msg',"no perm");
				$mid = "error_simple.tpl";
			}
		} else {
			$thelist = $cclib->get_currencies();
			$smarty->assign('thelist', $thelist['data']);
			$info = $cclib->get_currencies();
			$mid = "cc/admincc.tpl";
		}

	} elseif ($page == 'tr_record') {
		if (isset($_REQUEST['tr_amount'])) {
			if (isset($_REQUEST['from_id']) and isset($_REQUEST['to_id']) and isset($_REQUEST['cc_id'])) {
				$from_user = $_REQUEST['from_id'];
				$to_user = $_REQUEST['to_id'];
				$cc_id = $_REQUEST['cc_id'];
				$from = $to = false;
				if ($cclib->user_exists($from_user)) {
					if ($cclib->is_registered($from_user,$cc_id)) {
						$from = true;
					} else {
						$smarty->assign('msg',"User $from_user not registered in $cc_id");
						$mid = "error_simple.tpl";
					}
				} else {
					$smarty->assign('msg',"User $from_user not found");
				}
				if ($from and $cclib->user_exists($to_user)) {
					if ($cclib->is_registered($to_user,$cc_id)) {
						$to = true;
					} else {
						$smarty->assign('msg',"User $to_user not registered in $cc_id");
					}
				} else {
					$smarty->assign('msg',"User $to_user not found");
				}
				if ($from and $to) {
					$cclib->record_transaction($cc_id,$from_user,$to_user,$_REQUEST['tr_amount'],$_REQUEST['tr_item']);
					$_GET = array();
				}
			}
		}
		$currencies = $cclib->get_currencies(0,1000,'cc_name_asc','',$user);
		$smarty->assign('currencies',$currencies['data']);
		$mid = "cc/tr_record.tpl";
	}
}

$smarty->assign('mid', $mid);
$smarty->display('tiki.tpl');
?>
