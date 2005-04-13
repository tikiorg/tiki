<?php
require_once ('tiki-setup.php');
require_once ('lib/cc/cclib.php');

if (!isset($_REQUEST['page'])) { $_REQUEST['page'] = ''; }
$page = $_REQUEST['page'];

if (isset($_REQUEST['all'])) { 
	$_SESSION['viewrange'] = 'all'; 
} elseif (isset($_REQUEST['local'])) {
	$_SESSION['viewrange'] = 'local'; 
} elseif (isset($_REQUEST['my'])) {
	$_SESSION['viewrange'] = 'my'; 
} elseif (isset($_REQUEST['reg'])) { 
	$_SESSION['viewrange'] = 'reg'; 
}

$mid = "cc/index.tpl";
$view = '';

if ($user) {

	$ccuser = $cclib->user_infos($user);
	$smarty->assign_by_ref("ccuser", $ccuser);

	// ----------------- LEDGERS ----------------------------------------------
	if ($page == 'ledgers') {
		if (!isset($_REQUEST["sort_mode"])) {
			$sort_mode = 'last_tr_date_desc';
			$_REQUEST["sort_mode"] = $sort_mode;
		} else {
			$sort_mode = $_REQUEST["sort_mode"];
		}
		if ($_SESSION['viewrange'] = 'all' and $tiki_p_cc_admin == 'y') {
			if (isset($_REQUEST['cc_id'])) {
				$thelist = $cclib->get_ledgers(0,-1,$sort_mode,false,$_REQUEST['cc_id']);
				$smarty->assign("ccid",$_REQUEST['cc_id']);
			} else {
				$thelist = $cclib->get_ledgers(0,-1,$sort_mode);
				$smarty->assign("ccid",false);
			}
		} else {
			$thelist = $cclib->get_ledgers(0,-1,$sort_mode,$user);
			$smarty->assign("userid",$user);
		}
		$smarty->assign("thelist",$thelist['data']);
		$mid = "cc/ledgers.tpl";

	// ---------------- TRANSACTIONS -------------------------------------------
	} elseif ($page == 'transactions' or $page == 'my_tr') {
		if (!isset($_REQUEST["sort_mode"])) {
			$sort_mode = 'tr_date_desc';
			$_REQUEST["sort_mode"] = $sort_mode;
		} else {
			$sort_mode = $_REQUEST["sort_mode"];
		}	
		if (isset($_REQUEST['tr_amount'])) {
			if ($tiki_p_cc_admin != 'y') {
				$_REQUEST['from_id'] = $user;
			}
			if (isset($_REQUEST['from_id']) and isset($_REQUEST['to_id']) and isset($_REQUEST['cc_id'])) {
				$type = $_REQUEST['tr_type'];
				$from_user = $_REQUEST['from_id'];
				$to_user = $_REQUEST['to_id'];
				$amount = $_REQUEST['tr_amount'];
				$cc_id = $_REQUEST['cc_id'];
				$from = $to = false;

				if (!$cc_id) {
					$smarty->assign('msg',"You must select a cc system.");
				} else {
					if (!$amount > 0) {
						$smarty->assign('msg',"Amount must be positive.");
					} else {
						if ($from_user == $to_user) {
								$smarty->assign('msg',"Both accounts are the same.");
						} else {
							if (!$cclib->user_exists($from_user)) {
								$smarty->assign('msg',"User $from_user not found");
							} else {
								if (!$cclib->is_registered($from_user,$cc_id)) {
									$smarty->assign('msg',"User $from_user not registered in $cc_id.");
								} else {
									$from = true;
								}
							}
							if ($from) {
								if ($cclib->user_exists($to_user)) {
									if ($cclib->is_registered($to_user,$cc_id)) {
										$to = true;
									} else {
										$smarty->assign('msg',"User $to_user not registered in $cc_id.");
									}
								} else {
									$smarty->assign('msg',"User $to_user not found.");
								}
							}
						}
					}
				}
				if ($from and $to) {
					$cclib->record_transaction($cc_id,$type,$from_user,$to_user,$_REQUEST['tr_amount'],$_REQUEST['tr_item']);
					$_GET = array();
				} else {
					$_REQUEST['new'] = true;
				}
			}
		}
		if (isset($_REQUEST['new'])) {
			if (isset($_REQUEST['currency'])) {
				$currency = $_REQUEST['currency'];
				if (!$cclib->is_currency($currency)) {
					$smarty->assign('msg',tra("Sorry, the currency $currency is not available."));
					$currency = false;
				} elseif (!$cclib->is_registered($user,$currency,'y')) {
					$smarty->assign('msg',tra("Sorry, you are not registered into currency $currency."));
					$currency = false;
				}
			} else {
				$currency = false;
			}
			$currencies = $cclib->get_registered_cc($user,'y');
			$smarty->assign('currencies',$currencies);
			$smarty->assign('currency',$currency);
			$view = 'new';
			$mid = 'cc/transactions_form.tpl';
		} elseif (isset($_SESSION['viewrange']) and $_SESSION['viewrange'] == 'all' and $tiki_p_cc_admin == 'y') {
			$thelist = $cclib->get_transactions(0,-1,$sort_mode);
			$smarty->assign('thelist',$thelist['data']);
			$mid = "cc/transactions.tpl";
		} else {
			$thelist = $cclib->get_transactions(0,-1,$sort_mode,'',$user);
			$smarty->assign('thelist',$thelist['data']);
			$mid = "cc/transactions.tpl";
		}
	
	// ---------------- CURRENCIES ----------------------------------------------
	} elseif ($page == 'currencies' or $page == 'my_cc') {
		if (!isset($_REQUEST["sort_mode"])) {
			$sort_mode = 'cc_name_asc';
			$_REQUEST["sort_mode"] = $sort_mode;
		} else {
			$sort_mode = $_REQUEST["sort_mode"];
		}	
		if (isset($_REQUEST['cc_id'])) {
			$info = $cclib->get_currency($_REQUEST['cc_id']);
			if (isset($_REQUEST['view'])) {
				if ($tiki_p_cc_admin == 'y' or $info['owner_id'] == $user or (!isset($info['owner_id']))) {
					if (isset($_REQUEST['app']) and isset($_REQUEST['who'])) {
						$cclib->moderate_cc($_REQUEST['cc_id'],$_REQUEST['who'],$_REQUEST['app']);
					}
					$population = $cclib->get_ledgers(0,-1,'acct_id_desc',false,$_REQUEST['cc_id']);
					$smarty->assign('population', $population['data']);
				}
				$smarty->assign('info', $info);
				$mid = "cc/currency_admin.tpl";
			} elseif ($tiki_p_cc_admin == 'y' or $info['owner_id'] == $user or (!isset($info['owner_id']) and $tiki_p_cc_create == 'y')) {
				if (isset($_REQUEST['cc_name'])) {
					if (!isset($_REQUEST['cc_description'])) $_REQUEST['cc_description'] = '';
					if (isset($_REQUEST['owner']) and $tiki_p_cc_admin == 'y') {
						$owner = $_REQUEST['owner'];
					} else {
						$owner = $user;
					}
					if (isset($info['seq'])) {
						$seq = $info['seq'];
					} else {
						$seq = false;
					}
					if (!$cclib->replace_currency($owner,$_REQUEST['cc_id'],$_REQUEST['cc_name'],$_REQUEST['cc_description'],$_REQUEST['requires_approval'],$_REQUEST['listed'],$seq)) {
						$smarty->assign('msg',$cclib->msg);
					} else {
						if ($seq) {
							$smarty->assign('msg',"Currency ". $_REQUEST['cc_id'] ." modified.");
						} else {
							if (isset($_REQUEST['register_owner']) and $_REQUEST['register_owner'] == 'y') {
								$cclib->register_cc($_REQUEST['cc_id'],$owner);
							}
							$smarty->assign('msg',"Currency ". $_REQUEST['cc_id'] ." created.");
							$ccuser = $cclib->user_infos($user);
						}
						$thelist = $cclib->get_currencies(true,0,-1,$sort_mode,'',$owner,'y');
						$smarty->assign('thelist', $thelist['data']);
						$mid = "cc/currencies.tpl";
						$view = 'my';
					}
				} else {
					$smarty->assign('info', $info);
					$mid = "cc/currencies_form.tpl";
				}
			} else {
				$smarty->assign('msg',"no perm");
				$mid = "cc/currencies_form.tpl";
			}
		} elseif (isset($_REQUEST['new']) and ($tiki_p_cc_create == 'y' or $tiki_p_cc_admin == 'y')) {
			$mid = "cc/currencies_form.tpl";
		} elseif (isset($_REQUEST['reg']) or $_SESSION['viewrange'] == 'reg') {
			$thelist = $cclib->get_currencies(true,0,-1,$sort_mode,false,false,false,$user);
			$smarty->assign('thelist',$thelist['data']);
			$mid = "cc/currencies.tpl";
			$view = 'reg';
		} else {
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
			if (isset($_REQUEST['my']) or $_SESSION['viewrange'] == 'my') { 
				$thelist = $cclib->get_currencies(true,0,-1,$sort_mode,'',$user);
				$view = 'my';
			} else {
				if ($tiki_p_cc_admin == 'y' and $_SESSION['viewrange'] == 'all') {
					$thelist = $cclib->get_currencies(true,0,-1,$sort_mode,'',false,false,false,"%");
				} else {
					$thelist = $cclib->get_currencies(false,0,-1,$sort_mode);
				}
				$view = '';
			}
			$smarty->assign('thelist', $thelist['data']);
			$mid = "cc/currencies.tpl";
		}
	}
}

$smarty->assign('view', $view);
$smarty->assign('page', $page);
$smarty->assign('mid', $mid);
$smarty->display('tiki.tpl');
?>
