<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Controller.php 46965 2013-08-02 19:05:59Z jonnybradley $

class Services_Payment_Controller
{

	function setUp()
	{
		Services_Exception_Disabled::check('payment_feature', 'wikiplugin_addtocart');
	}

	function action_addtocart($input)
	{
		$cartlib = TikiLib::lib('cart');

		$params = $input->params->asArray();

		return $cartlib->add_to_cart($params, $input);
	}

	function action_addalltocart($input)
	{
		$cartlib = TikiLib::lib('cart');

		$items = $input->items->asArray();
		$ret = array();

		foreach ($items as $item) {
			$ret[] = $cartlib->add_to_cart($item['params'], new jitFilter($item));
		}

		return $ret;
	}

	function action_capture($input)
	{
		$perms = Perms::get();
		if (! $perms->payment_admin) {
			throw new Services_Exception_Denied(tr('Reserved for payment administrators'));
		}

		$paymentlib = TikiLib::lib('payment');
		$paymentlib->capture_payment($input->paymentId->int());

		$access = TikiLib::lib('access');
		$access->redirect($input->next->url());
	}
}
