<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// TODO: product_id is meant for storing some information about the product
// purchased that led to the credits being added.

class CreditsLib extends TikiLib
{
	
	function getRawCredits( $userId ) // {{{
	{
		$result = $this->query( "SELECT `creditId`, `credit_type`, `creation_date`, `expiration_date`, `total_amount`, `used_amount` FROM `tiki_credits` WHERE `userId` = ? ORDER BY `credit_type`, `creation_date`, `expiration_date`", array( $userId ) );

		$credits = array();
		
		while( $row = $result->fetchRow() ) {
			$credits[ $row['creditId'] ] = $row;
		}
		
		return $credits;
	} // }}}

	function getRawCreditsByType( $userId, $credit_type ) // {{{
	{
		$result = $this->query( "SELECT `creditId`, `creation_date`, `expiration_date`, `total_amount`, `used_amount` FROM `tiki_credits` WHERE `userId` = ? AND `credit_type` = ? ORDER BY `credit_type`, `creation_date`, `expiration_date`", array( $userId, $credit_type ) );

		$credits = array();
		
		while( $row = $result->fetchRow() ) {
			$credits[ $row['creditId'] ] = $row;
		}
		
		return $credits;
	} // }}}

	function updateCreditType($credit_type, $display_text, $unit_text, $is_static_level = 'n', $scaling_divisor = 1) // {{{
	{
		$bindvars = array($credit_type, $display_text, $unit_text, $is_static_level, $scaling_divisor);
		$result = $this->query( "REPLACE INTO `tiki_credits_types` (`credit_type`, `display_text`, `unit_text`, `is_static_level`, `scaling_divisor`) VALUES (?, ?, ?, ?, ?)", $bindvars);
		return $result;			
	}
	
	function getCreditTypes($staticonly = false) // {{{
	{
		$result = $this->query( "SELECT `credit_type`, `display_text`, `unit_text`, `is_static_level`, `scaling_divisor` FROM `tiki_credits_types`");
		$creditTypes = array();
		
		while( $row = $result->fetchRow() ) {
			if ($staticonly && $row['is_static_level'] == 'y') {
				$creditTypes[ $row['credit_type'] ] = $row;
			} elseif (!$staticonly) {
				$creditTypes[ $row['credit_type'] ] = $row;
			}
		}
		
		return $creditTypes;
	} // }}}
	
	function getCredits( $userId ) // {{{
	{
		$result = $this->query( "
			SELECT `credit_type`, SUM(`total_amount`) total_amount, SUM(`used_amount`) used_amount
			FROM `tiki_credits`
			WHERE 
				`userId` = ?
				AND (`expiration_date` IS NULL OR `expiration_date` > NOW())
				AND `creation_date` <= NOW()
			GROUP BY `credit_type`
			", array( $userId ) );

		$credits = array();
		
		while( $row = $result->fetchRow() ) {
			$credits[ $row['credit_type'] ] = array(
				'remain' => $row['total_amount'] - $row['used_amount'],
				'used' => $row['used_amount'],
				'total' => $row['total_amount'],
			);
		}
		// Handle level-type credits in a different manner
		// Level of used amount stored in user preferences
		// Total used (flow) from credits table
		global $tikilib, $userlib;
		$info = $userlib->get_userid_info( $userId );

		$creditTypes = $this->getCreditTypes();
		
		foreach( $credits as $type => $crVal) {
			if( $creditTypes[$type]['is_static_level'] == 'y' )
			{
				$prefName = "credits_level_" . $type;
				$credits[$type]['used'] = (float) $tikilib->get_user_preference( $info['login'], $prefName );
				$credits[$type]['remain'] = $credits[$type]['total'] - $credits['used'];
			}
		}
		
		// set zero for creditTypes that user does not have
		foreach($creditTypes as $k => $c) {
			if (!array_key_exists($k, $credits)) {
				$credits[$k]['used'] = 0;
				$credits[$k]['remain'] = 0;
				$credits[$k]['total'] = 0;
			}			
		}
		return $credits;
	} // }}}

	function getScaledCredits( $userId ) // {{{
	{
		$creditTypes = $this->getCreditTypes();
		$credits = $this->getCredits( $userId );

		foreach( $credits as $type => &$data )
		{
			$factor = 1;
			if( isset($creditTypes[$type]) && $creditTypes[$type]['scaling_divisor'] ) {
				$factor = $creditTypes[$type]['scaling_divisor'];
			}
			if( isset($creditTypes[$type]) && $display_text = $creditTypes[$type]['display_text'] ) {
				$data['display_text'] = $display_text;
			} else {
				$data['display_text'] = $type;
			}
			if( isset($creditTypes[$type]) && $unit_text = $creditTypes[$type]['unit_text'] ) {
				$data['unit_text'] = $unit_text;
			} else {
				$data['unit_text'] = '';
			}
			
			$data['discreet_total'] = $this->scale( $data['total'], $factor );
			$data['discreet_used'] = $data['used'] / ($data['total'] / $data['discreet_total']);
			$data['discreet_remain'] = $data['discreet_total'] - $data['discreet_used'];
			$data['empty'] = $data['remain'] <= 0;
			$data['low'] = $data['remain'] <= $data['total']*.15;
		}

		return $credits;
	} // }}}

	private function scale( $value, $factor = 1 ) // {{{
	{
		// 1.5 log( (x/fac)^2 + 1 )
		return floor( 1.5 * log( ($value/$factor)*($value/$factor) + 1 ) );
	} // }}}

	function removeCreditBlock( $creditId ) // {{{
	{
		$this->query( "DELETE FROM `tiki_credits` WHERE `creditId` = ?", array( $creditId ) );
	} // }}}

	function replaceCredit( $creditId, $type, $used, $total, $validFrom, $expirationDate ) // {{{
	{
		if( !empty( $expirationDate ) )
			$expirationDate = date( 'Y-m-d H:i:s', $time = strtotime( $expirationDate ) );

		if( $time === false )
			return false;

		$validFrom = date( 'Y-m-d H:i:s', $time = strtotime( $validFrom ) );

		if( $time === false )
			return false;

		$this->query( "
			UPDATE `tiki_credits` 
			SET `credit_type` = ?, `used_amount` = ?, `total_amount` = ?, `expiration_date` = IF(?='',NULL,?), `creation_date` = ?
			WHERE `creditId` = ?",
			array( $type, $used, $total, $expirationDate, $expirationDate, $validFrom, $creditId ) );
	} // }}}

	/**
	 * Adds a new credits entry for the user.
	 */
	function addCredits( $userId, $creditType, $amount, $expirationDate = null, $validFrom = null ) // {{{
	{
		if( !empty( $expirationDate ) )
			$expirationDate = date( 'Y-m-d H:i:s', $time = strtotime( $expirationDate ) );

		if( $time === false )
			return false;

		if( !empty( $validFrom ) )
			$validFrom = date( 'Y-m-d H:i:s', $time = strtotime( $validFrom ) );

		if( $time === false )
			return false;

		$this->query( "
			INSERT INTO `tiki_credits` 
				(`userId`, `credit_type`, `total_amount`, `expiration_date`, `creation_date`) 
				VALUES(?,?,?,IF(? = '', NULL, ?),IF(?='', NULL, ?))",
			array( $userId, $creditType, $amount, $expirationDate, $expirationDate, $validFrom, $validFrom) );

		return true;
	} // }}}

	/**
	 * Use the user's credits of a certain type. If the user does not have
	 * enough credits, the function will return false. Credits may be used from
	 * different entries. Entries expiring soon will be used first.
	 */
	function useCredits( $userId, $creditType, $amount, $product_id = null ) // {{{
	{
		if( $amount == 0 ) {
			return true;
		}

		// Level-type credits
		$creditTypes = $this->getCreditTypes();
		if( $creditTypes[$creditType]['is_static_level'] == 'y' )
		{
			$credits = $this->getCredits( $userId );
			if( ! array_key_exists( $creditType, $credits ) ) {
				return false;
			}

			if( $credits[$creditType]['remain'] > 0 )
			{
				global $tikilib, $userlib;
				$info = $userlib->get_userid_info( $userId );

				// Expense all credits if not enough
				$toUse = min( $credits[$creditType]['used'] + $amount, $credits[$creditType]['remain'] );
				$prefName = "credits_level_" . $creditType;
				
				$tikilib->set_user_preference( 
					$info['login'], 
					$prefName, 
					$toUse );

				if ($amount > 0) {
					$this->_recCredits( $userId, $creditType, $amount, $product_id );
				}

				return true;
			}

			return false;
		}

		// Expendable credits
		$result = $this->query( "
			SELECT `creditId`, `product_id`, `total_amount` - `used_amount` as available
			FROM `tiki_credits`
			WHERE
				( `expiration_date` > NOW() OR `expiration_date` IS NULL )
				AND `creation_date` <= NOW()
				AND `userId` = ?
				AND `credit_type` = ?
			ORDER BY IF(`expiration_date` IS NULL, 1000000, DATEDIFF(`expiration_date`, NOW())) ASC",
			array( $userId, $creditType ) );

		$total = 0;
		$list = array();
		while( $row = $result->fetchRow() ) {
			$total += $row['available'];
			$list[] = $row;
		}

		if( $total == 0 ) {
			return false;
		}
		
		if( $amount > $total ) {
			$amount = $total;
		}
		
		if ($amount > 0) {
			$this->_recCredits( $userId, $creditType, $amount, $product_id );
		}
		
		foreach( $list as $row ) {
			$amount = $this->_useCredits( $row['creditId'], $row['available'], $amount );
			if( $amount <= 0 ) {
				return true;
			}
		}

		die( "The verification failed in using credits." );
	} // }}}

	function restoreCredits( $userId, $creditType, $amount, $product_id = null ) // {{{
	{
		// Only valid for level-type credits
		if( ! array_key_exists( $creditType, $this->getCreditTypes(true) ) ) {
			return false;
		}
		
		global $tikilib, $userlib;
		$info = $userlib->get_userid_info( $userId );

		$prefName = "credits_level_" . $creditType;

		$used = (float) $tikilib->get_user_preference( $info['login'], $prefName );
		if( $used === 0 ) {
			return false;
		}

		$used -= $amount;
		if( $used < 0 ) {
			$used = 0;
		}

		$tikilib->set_user_preference( $info['login'], $prefName, $used );

		if ($amount > 0) {
			$this->_recCredits( $userId, $creditType, -1 * $amount, $product_id );
		}

		return true;
	} // }}}

	/**
	 * Uses up a credit id and returns the amount of credits remaining to use.
	 */
	function _useCredits( $creditId, $available, $amount ) // {{{
	{
		if( $available >= $amount ) {
			$this->query( "UPDATE `tiki_credits` SET `used_amount` = `used_amount` + ? WHERE `creditId`= ?", 
				array( $amount, $creditId ) );

			return 0;
		} else {
			$this->query( "UPDATE `tiki_credits` SET `used_amount` = `total_amount` WHERE `creditId `= ?", 
				array( $creditId ) );

			return $amount - $available;
		}
	} // }}}


	/**
	 * Uses up a credit id and returns the amount of credits remaining to use.
	 */
	function _recCredits( $userId, $creditType, $amount, $product_id = null ) // {{{
	{
		return $this->query( "INSERT INTO `tiki_credits_usage` (`userId`, `usage_date`, `credit_type`, `used_amount`, `product_id`) VALUES (?, NOW(), ?, ?, ?)", array( $userId, $creditType, $amount, $product_id ) );
	} // }}}


	/**
	 * Delete expired credit entries and those completely used up.
	 */
	function purgeCredits() // {{{
	{
		$this->query( "DELETE FROM `tiki_credits` WHERE `expiration_date` IS NOT NULL AND `expiration_date` < NOW()" );
		$this->query( "DELETE FROM `tiki_credits` WHERE `total_amount` = `used_amount`" );
	} // }}}


	function getPlanExpiry( $userId, $creditType ) // {{{
	{
		$result = $this->getOne( "SELECT MAX(`expiration_date`) FROM `tiki_credits` WHERE `userId` = ? AND `expiration_date` IS NOT NULL AND `credit_type` = ?", array( $userId, $creditType ));
		if ( $result )  {
			return $result;
		} else {	
			return '';
		}
	} // }}}

	function getLatestPlanBegin( $userId, $creditType ) // {{{
	{
		$result = $this->getOne( "SELECT MAX(`creation_date`) FROM `tiki_credits` WHERE `userId` = ? AND `expiration_date` IS NOT NULL AND `expiration_date` > NOW() AND `credit_type` = ? AND `creation_date` < NOW()", array( $userId, $creditType ));
		if ( $result ) {
			return $result;
		} else {	
			return '';
		}
	} // }}}

	function getNextPlanBegin( $userId, $creditType ) // {{{
	{
		$result = $this->getOne( "SELECT MIN(`creation_date`) FROM `tiki_credits` WHERE `userId` = ? AND `expiration_date` IS NOT NULL AND `credit_type` = ? AND `creation_date` > NOW()", array( $userId, $creditType ));
		if ( $result ) {
			return $result;
		} else {	
			return '';
		}
	} // }}}

	function getCreditsUsage( $target_user_id, $req_type, $start_date, $end_date ) // {{{
	{
		if ($req_type) {
			$results = $this->query("SELECT * FROM tiki_credits_usage WHERE userId = ? AND credit_type = ? AND usage_date > ? AND usage_date <= ? ORDER BY `usage_date` desc", array($target_user_id, $req_type, $start_date, $end_date));
		} else {
			$results = $this->query("SELECT * FROM tiki_credits_usage WHERE userId = ? AND usage_date > ? AND usage_date <= ? ORDER BY `usage_date` desc", array($target_user_id, $start_date, $end_date));
		}
		$consumption_data = array();
		while( $row = $results->fetchRow() )
		{
			$consumption_data[] = $row;
		}
		return $consumption_data;
	} // }}}
	
}
$creditslib = new CreditsLib;
