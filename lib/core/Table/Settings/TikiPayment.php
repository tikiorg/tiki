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
 * Class Table_Settings_TikiPayment
 *
 * Tablesorter settings for the table listings of payments at tiki-payments.php
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Settings_Standard
 */
class Table_Settings_TikiPayment extends Table_Settings_Standard
{
	protected $ts = array(
		'ajax' => array(
			'url' => array(
				'file' => 'tiki-payment.php',
			),
		),
		'columns' => array(
			'#id' => array(
				'sort' => array(
					'type' => 'digit',
					'dir' => 'asc',
					'ajax' =>'paymentRequestId',
				),
				'filter' => array(
					'type' => 'text',
					'ajax' =>'filter_paymentRequestId',
				),
				'priority' => 3,
			),
			'#description' => array(
				'sort' => array(
					'type' => 'text',
					'ajax' =>'description',
				),
				'filter' => array(
					'type' => 'text',
					'ajax' =>'filter_description',
				),
				'priority' => 2,
			),
			'#amount' => array(
				'sort' => array(
					'type' => 'digit',
					'ajax' =>'amount',
				),
				'filter' => array(
					'type' => 'text',
					'ajax' =>'filter_amount',
				),
				'priority' => 'critical',
			),
			'#req_date' => array(
				'sort' => array(
					'type' => 'dateFormat-yyyy-mm-dd',
					'ajax' =>'request_date',
				),
				'filter' => array(
					'type' => 'date',
					'ajax' =>'filter_request_date',
				),
				'priority' => 5,
			),
			'#user' => array(
				'sort' => array(
					'type' => 'text',
					'ajax' =>'login',
				),
				'filter' => array(
					'type' => 'text',
					'ajax' =>'filter_login',
				),
				'priority' => 5,
			),
			'#payer' => array(
				'sort' => array(
					'type' => 'text',
					'ajax' =>'login',
				),
				'filter' => array(
					'type' => 'text',
					'ajax' =>'filter_login',
				),
				'priority' => 6,
			),
			'#actions' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 'critical',
			),
		),
	);

	protected function getTableSettings()
	{
		$this->ts['ajax']['offset'] .= '_' . $this->ts['ajax']['requiredparams']['list_type'];
		return $this->ts;
	}

}