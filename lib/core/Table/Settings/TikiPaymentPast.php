<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Table_Settings_TikiPaymentPast
 *
 * Tablesorter settings for the table listings of paid payments at tiki-payments.php
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Settings_Standard
 */
class Table_Settings_TikiPaymentPast extends Table_Settings_TikiPayment
{

	protected function getTableSettings()
	{
		unset ($this->ts['columns']['#req_date']);
		$this->ts['columns']['#detail'] = [
			'sort' => [
				'type' => 'text',
				'ajax' => 'detail',
			],
			'filter' => [
				'type' => 'text',
				'ajax' => 'filter_detail',
			],
			'priority' => 6,
			'hidden' => true,
		];
		$this->ts['columns']['#pmt_date'] = [
			'sort' => [
				'type' => 'dateFormat-yyyy-mm-dd',
				'ajax' =>'payment_date',
			],
			'filter' => [
				'type' => 'date',
				'ajax' =>'filter_payment_date',
			],
			'priority' => 5,
		];
		$this->ts['columns']['#pmt_type'] = [
			'sort' => [
				'type' => 'text',
				'ajax' =>'type',
			],
			'filter' => [
				'type' => 'text',
				'ajax' =>'filter_type',
			],
			'priority' => 5,
		];
		$this->ts['columns']['#payer'] = [
			'sort' => array(
				'type' => 'text',
				'ajax' =>'payer',
			),
			'filter' => array(
				'type' => 'text',
				'ajax' =>'filter_payer',
			),
			'priority' => 6,
		];
		$this->ts['ajax']['offset'] .= '_' . $this->ts['ajax']['requiredparams']['list_type'];
		return $this->ts;
	}
}