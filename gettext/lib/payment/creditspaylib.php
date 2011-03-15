<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class UserPayCredits extends CreditsLib
{
	function __construct() {
		global $user, $prefs;
		$valid_credits = unserialize($prefs['payment_tikicredits_types']);
		$credits_xcrates = unserialize($prefs['payment_tikicredits_xcrates']);
		
		$userId = $this->get_user_id($user);
		$uc = $this->getScaledCredits($userId);

		$ret = array();
		for ($i = 0, $cvalid_credits = count($valid_credits); $i < $cvalid_credits ; $i++) {
			$one = array();
			$k = $valid_credits[$i];
			if (!empty($credits_xcrates[$i])) {
				$one['xcrate'] = $credits_xcrates[$i];	
			} else {
				$one['xcrate'] = 1;
			}
			if (isset($uc[$k])) {
				$one['remain'] = $uc[$k]['remain'];
				$one['unit_text'] = $uc[$k]['unit_text'];
				$one['display_text'] = $uc[$k]['display_text'];
			}
			$ret[$k] = $one;
		}
		
		$this->credits = $ret;
	}
	
	function setPrice($price) {
		$credits = $this->credits;
		foreach ($credits as $k => $uc) {
			$credits[$k]['price'] = $price * $credits[$k]['xcrate'];
			if ($credits[$k]['price'] > $credits[$k]['remain']) {
				$credits[$k]['enough'] = 0;
			} else {
				$credits[$k]['enough'] = 1;
			}
		}
		$this->credits = $credits;
	}
	
	function payAmount($creditType, $amount, $invoice) {
		global $user, $tikilib, $paymentlib;
		require_once 'lib/payment/paymentlib.php';
		$userId = $this->get_user_id($user);
		$uc = $this->getCredits($userId);
		if ($amount > $uc[$creditType]['remain']) {
			return false;
		}
		$credits_amount = $amount * $this->credits[$creditType]['xcrate'];
		if ($this->useCredits($userId, $creditType, $credits_amount)) {
			$msg = tr("Tiki credits payment done on %0 for $amount (using $creditType)", $tikilib->get_short_datetime($tikilib->now));
			$paymentlib->enter_payment( $invoice, $amount, 'tikicredits', array('info' => $msg, 'username' => $user, 'creditType' => $creditType, 'creditAmount' => $credits_amount));
			return true;
		} else {
			return false;
		}			
	}
}
