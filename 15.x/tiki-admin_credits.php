<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'tiki-setup.php';
$creditslib = TikiLib::lib('credits');
//get_strings tra('Admin credits')

if ($tiki_p_admin_users != 'y') {
	$smarty->assign('msg', tra('You do not have permission to use this feature'));
	$smarty->display('error.tpl');
	die;
}

if ( isset( $_REQUEST['use_credit'] ) && $use_credit_userid = $tikilib->get_user_id($_POST['userfilter']) ) {
	$creditslib->useCredits(
		$use_credit_userid,
		$_POST['use_credit_type'],
		$_POST['use_credit_amount']
	);

	header('Location: tiki-admin_credits.php?userfilter=' . urlencode($_REQUEST['userfilter']));
	exit;
}

if ( isset( $_REQUEST['restore_credit'] ) && $restore_credit_userid = $tikilib->get_user_id($_POST['userfilter']) ) {
	$creditslib->restoreCredits(
		$restore_credit_userid,
		$_POST['restore_credit_type'],
		$_POST['restore_credit_amount']
	);

	header('Location: tiki-admin_credits.php?userfilter=' . urlencode($_REQUEST['userfilter']));
	exit;
}

if ( isset( $_REQUEST['purge_credits'] ) ) {
	$creditslib->purgeCredits();
	header('Location: tiki-admin_credits.php');
	exit;
}

if ( isset( $_REQUEST['update_types'] ) ) {
	foreach ( $_POST['credit_types'] as $key => $values ) {
		$creditslib->updateCreditType(
			$values['credit_type'],
			$values['display_text'],
			$values['unit_text'],
			$values['is_static_level'],
			$values['scaling_divisor']
		);
	}

	if ( !empty($_POST['new_credit_type']) ) {
		$creditslib->updateCreditType(
			$_POST['new_credit_type'],
			$_POST['display_text'],
			$_POST['unit_text'],
			$_POST['is_static_level'],
			$_POST['scaling_divisor']
		);
	}
}

$creditTypes = $creditslib->getCreditTypes();
$staticCreditTypes = $creditslib->getCreditTypes(true);
$smarty->assign('credit_types', $creditTypes);
$smarty->assign('static_credit_types', $staticCreditTypes);

if ( isset($_REQUEST['userfilter']) ) {
	$smarty->assign('userfilter', $_REQUEST['userfilter']);

	$editing = $userlib->get_user_info($_REQUEST['userfilter']);

	if ( $editing ) {
		$userPlans = array();
		foreach ($creditTypes as $ct => $v) {
			$userPlans[$ct]['nextbegin'] = $creditslib->getNextPlanBegin($editing['userId'], $ct);
			$userPlans[$ct]['currentbegin'] = $creditslib->getLatestPlanBegin($editing['userId'], $ct);
			$userPlans[$ct]['expiry'] = $creditslib->getPlanExpiry($editing['userId'], $ct);
		}
		$smarty->assign('userPlans', $userPlans);

		$credits = $creditslib->getRawCredits($editing['userId']);
		$smarty->assign('credits', $credits);
		$smarty->assign('editing', $editing);

		// Get usage information

		// date values
		if (isset($_REQUEST['startDate_Year']) || isset($_REQUEST['endDate_Year'])) {
			$smarty->assign(
				'startDate',
				$tikilib->make_time(0, 0, 0, $_REQUEST['startDate_Month'], $_REQUEST['startDate_Day'], $_REQUEST['startDate_Year'])
			);

			$smarty->assign(
				'endDate',
				$tikilib->make_time(23, 59, 59, $_REQUEST['endDate_Month'], $_REQUEST['endDate_Day'], $_REQUEST['endDate_Year'])
			);

			$start_date = $_REQUEST['startDate_Year'] . '-' . $_REQUEST['startDate_Month'] . '-' . $_REQUEST['startDate_Day'];
			$end_date = $_REQUEST['endDate_Year'] . '-' . $_REQUEST['endDate_Month'] . '-' . $_REQUEST['endDate_Day'] . ' 23:59:59';
		} else {
			$start_date = $tikilib->now - 3600 * 24 * 30;
			$smarty->assign('startDate', $start_date);
			$end_date = date('Y-m-d 23:59:59');
		}

		$req_type = $_REQUEST['action_type'];
		$smarty->assign('act_type', $req_type);

		$consumption_data = $creditslib->getCreditsUsage($editing['userId'], $req_type, $start_date, $end_date);
		$smarty->assign('consumption_data', $consumption_data);

		if ( isset( $_POST['save'], $_POST['credits'] ) ) {
			foreach ( $_POST['credits'] as $key => $values ) {
				if ( ! isset( $credits[$key] ) )
					die('Mismatch');

				$same = true;
				$current = $credits[$key];
				foreach ( $current as $field => $value )
					if ( $field != 'creditId' && $value != $values[$field] ) {
						$same = false;
						break;
					}

				if ( ! $same ) {
					$creditslib->replaceCredit(
						$key,
						$values['credit_type'],
						$values['used_amount'],
						$values['total_amount'],
						$values['creation_date'],
						$values['expiration_date']
					);
				}
			}

			if ( !empty($_POST['credit_type'])
						&& !empty( $_POST['total_amount'])
						&& in_array($_POST['credit_type'], array_keys($creditTypes))
			) {
				$creditslib->addCredits(
					$editing['userId'],
					$_POST['credit_type'],
					$_POST['total_amount'],
					$_POST['expiration_date'],
					$_POST['creation_date']
				);
			}

			header('Location: tiki-admin_credits.php?userfilter=' . urlencode($_REQUEST['userfilter']));
			exit;
		}

		if ( !empty($_POST['credit_type']) && !empty($_POST['total_amount']) ) {
			$creditslib->addCredits(
				$editing['userId'],
				$_POST['credit_type'],
				$_POST['total_amount'],
				$_POST['expiration_date'],
				$_POST['creation_date']
			);

			header('Location: tiki-admin_credits.php?userfilter=' . urlencode($_REQUEST['userfilter']));
			exit;
		}

		if ( isset($_POST['confirm'], $_POST['delete']) ) {
			foreach ( $_POST['delete'] as $creditId )
				if ( isset($credits[$creditId]) )
					$creditslib->removeCreditBlock($creditId);

			header('Location: tiki-admin_credits.php?userfilter=' . urlencode($_REQUEST['userfilter']));
			exit;
		}
	}
}

$smarty->assign('mid', 'tiki-admin_credits.tpl');
$smarty->display('tiki.tpl');
