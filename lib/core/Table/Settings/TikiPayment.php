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
	protected $ts = [
		'ajax' => [
			'url' => [
				'file' => 'tiki-payment.php',
			],
		],
		'columns' => [
			'#id' => [
				'sort' => [
					'type' => 'digit',
					'dir' => 'asc',
					'ajax' => 'paymentRequestId',
				],
				'filter' => [
					'type' => 'text',
					'ajax' => 'filter_paymentRequestId',
				],
				'priority' => 3,
			],
			'#description' => [
				'sort' => [
					'type' => 'text',
					'ajax' => 'description',
				],
				'filter' => [
					'type' => 'text',
					'ajax' => 'filter_description',
				],
				'priority' => 2,
			],
			'#amount' => [
				'sort' => [
					'type' => 'digit',
					'ajax' => 'amount',
				],
				'filter' => [
					'type' => 'text',
					'ajax' => 'filter_amount',
				],
				'priority' => 'critical',
			],
			'#req_date' => [
				'sort' => [
					'type' => 'dateFormat-yyyy-mm-dd',
					'ajax' => 'request_date',
				],
				'filter' => [
					'type' => 'date',
					'ajax' => 'filter_request_date',
				],
				'priority' => 5,
			],
			'#user' => [
				'sort' => [
					'type' => 'text',
					'ajax' => 'login',
				],
				'filter' => [
					'type' => 'text',
					'ajax' => 'filter_login',
				],
				'priority' => 5,
			],
			'#payer' => [
				'sort' => [
					'type' => 'text',
					'ajax' => 'login',
				],
				'filter' => [
					'type' => 'text',
					'ajax' => 'filter_login',
				],
				'priority' => 6,
			],
			'#actions' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 'critical',
			],
		],
	];

	protected function getTableSettings()
	{
		$this->ts['ajax']['offset'] .= '_' . $this->ts['ajax']['requiredparams']['list_type'];
		return $this->ts;
	}
}
