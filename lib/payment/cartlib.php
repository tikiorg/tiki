<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class CartLib
{
	/**
	 * Called when addtocart button clicked. Adds a quantity of a specified product to the cart
	 *
	 * @param array $product_info		wikiplugin_addtocart params
	 * @param jitFilter $input			request input (POST)
	 * @return bool						success
	 */
	function add_to_cart($product_info, $input)
	{
		global $prefs, $user, $globalperms;

		$userlib = TikiLib::lib('user');
		$cartlib = TikiLib::lib('cart');

		$quantity = $input->quantity->int();

		if ( $input->code->text() !== $product_info['code'] ) {
			$this->handle_error(tra('Cart: Product code mismatch.'));
			return false;
		}

		if (!empty($params['exchangeorderitemid']) && !empty($params['exchangetoproductid'])) {
			if ( $input->exchangeorderitemid->int() !== $params['exchangeorderitemid'] ||
					$input->exchangetoproductid->int() !== $params['exchangetoproductid'] ) {

				$this->handle_error(tra('Cart: Product exchange mismatch.'));
				return false;
			}
		}

		if ($input->gift_certificate->text() && !empty($product_info['giftcertificate']) && $product_info['giftcertificate'] === 'y') {
			if (!$cartlib->add_gift_certificate($input->gift_certificate->text())) {
				$this->handle_error(tra('Invalid gift certificate: %0', $input->gift_certificate->text()));
				return false;
			}
		}

		if ($prefs['payment_cart_anonymous'] === 'y' && (!$user || $product_info['forceanon'] == 'y') && empty($_SESSION['shopperinfo'])) {
			// There needs to be a shopperinfo plugin on the page
			$this->handle_error(tr('Please enter your shopper information first'));
			return false;
		}

		if ($globalperms->payment_admin && $input->buyonbehalf->text() && $userlib->user_exists($input->buyonbehalf->text())) {
			$product_info['onbehalf'] = $input->buyonbehalf->text();
		} else {
			$product_info['onbehalf'] = '';
		}

		// Generate behavior for exchanges
		if (!empty($product_info['exchangeorderitemid']) && !empty($product_info['exchangetoproductid'])) {
			$product_info['behaviors'][] = array(
				'event' => 'complete',
				'behavior' => 'cart_exchange_product',
				'arguments' => array($product_info["exchangeorderitemid"], $product_info["exchangetoproductid"])
			);
			if (!isset($product_info['exchangeorderamount']) || !$product_info['exchangeorderamount']) {
				$product_info['exchangeorderamount'] = 1;
			}
		}
		// Generate behavior for gift certificate purchase
		if (strtolower($product_info['producttype']) == 'gift certificate') {
			if ($product_info['onbehalf']) {
				$giftcert_email = $userlib->get_user_email($product_info['onbehalf']);
			} elseif (!$user && !empty($_SESSION['shopperinfo']['email'])) {
				$giftcert_email = $_SESSION['shopperinfo']['email'];
			} elseif ($user) {
				$giftcert_email = $userlib->get_user_email($user);
			} else {
				$this->handle_error(tra('No email found for gift certificate recipient'));
				return false;
			}
			$product_info['behaviors'][] = array(
				'event' => 'complete',
				'behavior' => 'cart_gift_certificate_purchase',
				'arguments' => array($product_info['code'], $giftcert_email)
			);
		}
		// Now add product to cart
		return $cartlib->add_product($product_info['code'], $quantity, $product_info);
	}

	//Used for putting new items in the cart, to modify an already existing item in the cart, use update_quantity
	function add_product( $code, $quantity, $info, $parentCode = 0, $childInputedPrice = 0 )
	{
		global $prefs;
		$this->init_cart();

		$current = $this->get_quantity($code);

		if ($parentCode) {
			$this->init_product($code, $info, $parentCode, $quantity, $childInputedPrice);
			if ($prefs['payment_cart_inventory'] == 'y') {
				$currentInventory = $this->get_inventory($code);
				if ($currentInventory < $quantity) {
					// Abort entire bundle if one of the child products is out of stock
					$this->update_quantity($parentCode, 0, $info);
				}
			}
			return false;
		} elseif (!$current) {
			$this->init_product($code, $info, $parentCode);
		}

		$current += $quantity;

		$this->add_bundle($code, $quantity, $info);

		$this->update_quantity($code, $current, $info);
		return true;
	}

	function get_product_info( $code )
	{
		// This function is used by several advanced cart features (e.g. bundled products, associated events)
		global $prefs;
		if (empty($prefs['payment_cart_product_tracker_name'])) {
			return array();
		}

		$array = $this->get_tracker_values_custom($prefs['payment_cart_product_tracker_name'], $code);

		$info = array();
		$info['code'] = $code;
		while ($result = current($array)) {
			$key = key($array);
			switch ($key) {
				case $prefs['payment_cart_product_name_fieldname']: $key = "description";
					break;
				case $prefs['payment_cart_associated_event_fieldname']: $key = "eventcode";
					break;
				case $prefs['payment_cart_product_classid_fieldname']: $key = "productclass";
					break;
				case $prefs['payment_cart_products_inbundle_fieldname']: $key = "productsinbundle";
					break;
				case $prefs['payment_cart_product_price_fieldname']: $key = "price";
					break;
			}
			$info[$key] = $result;
			next($array);
		}
		return $info;
	}

	function add_bundle( $code, $quantity, $info )
	{
		global $prefs;
		if ($prefs['payment_cart_bundles'] != 'y') {
			return false;
		}
		$moreInfo = $this->get_product_info($code);
		if (!empty($moreInfo['productsinbundle'])) {
			$products = explode(",", $moreInfo['productsinbundle']);
			foreach ($products as $product) {
				$p = explode(":", $product);
				if (count($p) == 1) {
					$p[1] = 1; // quantity
					$p[2] = ''; // inputted price
				} elseif (count($p) == 2) {
					$p[2] = ''; // inputted price
				}
				list($productId, $productQuantity, $childInputedPrice) = $p;
				if (is_numeric($productId)) {
					$infoProduct = $this->get_product_info($productId);
					if ($childInputedPrice == '') {
						$childInputedPrice = $info['price'] / count($products) / $productQuantity;
						// default evenly split between products in the bundle (regardless of individual quantities)
					}
					$infoProduct['price'] = 0;
					if (!empty($info['onbehalf'])) {
						$infoProduct['onbehalf'] = $info['onbehalf'];
					}
					$this->add_product($productId, $productQuantity, $infoProduct, $code, $childInputedPrice);
				}
			}
		}
	}

	function get_tracker_item_ids_custom( $trackerName, $fieldName, $value )
	{
		global $tikilib;

		$itemId = $tikilib->fetchAll(
			"SELECT tiki_tracker_item_fields.itemId
			FROM tiki_tracker_item_fields
			LEFT JOIN tiki_tracker_fields ON tiki_tracker_fields.fieldId = tiki_tracker_item_fields.fieldId
			LEFT JOIN tiki_trackers ON tiki_trackers.trackerId = tiki_tracker_fields.trackerId
			LEFT JOIN tiki_tracker_items ON tiki_tracker_items.itemId = tiki_tracker_item_fields.itemId
			WHERE tiki_trackers.name = ? AND
			tiki_tracker_fields.name = ? AND
			tiki_tracker_item_fields.value = ?",
			array($trackerName, $fieldName, $value)
		);

		return $itemId;
	}

	function get_tracker_item_id_custom( $trackerName, $fieldName, $value )
	{
		global $tikilib;

		$itemId = $tikilib->getOne(
			"SELECT tiki_tracker_item_fields.itemId
			FROM tiki_tracker_item_fields
			LEFT JOIN tiki_tracker_fields ON tiki_tracker_fields.fieldId = tiki_tracker_item_fields.fieldId
			LEFT JOIN tiki_trackers ON tiki_trackers.trackerId = tiki_tracker_fields.trackerId
			LEFT JOIN tiki_tracker_items ON tiki_tracker_items.itemId = tiki_tracker_item_fields.itemId
			WHERE tiki_trackers.name = ? AND
			tiki_tracker_fields.name = ? AND
			tiki_tracker_item_fields.value = ?",
			array($trackerName, $fieldName, $value)
		);

		return $itemId;
	}

	function get_tracker_value_custom( $trackerName, $fieldName, $itemId )
	{
		global $tikilib;

		$value = $tikilib->getOne(
			"SELECT tiki_tracker_item_fields.value
			FROM tiki_tracker_item_fields
			LEFT JOIN tiki_tracker_fields ON tiki_tracker_fields.fieldId = tiki_tracker_item_fields.fieldId
			LEFT JOIN tiki_trackers ON tiki_trackers.trackerId = tiki_tracker_fields.trackerId
			LEFT JOIN tiki_tracker_items ON tiki_tracker_items.itemId = tiki_tracker_item_fields.itemId
			WHERE tiki_trackers.name = ? AND
			tiki_tracker_fields.name = ? AND
			tiki_tracker_item_fields.itemId = ?",
			array($trackerName, $fieldName, $itemId)
		);

		return $value;
	}

	function get_tracker_values_custom( $trackerName, $itemId )
	{
		global $tikilib;

		$result = $tikilib->fetchAll(
			"SELECT tiki_tracker_fields.name, tiki_tracker_item_fields.value
			FROM tiki_tracker_item_fields
			LEFT JOIN tiki_tracker_fields ON tiki_tracker_fields.fieldId = tiki_tracker_item_fields.fieldId
			LEFT JOIN tiki_trackers ON tiki_trackers.trackerId = tiki_tracker_fields.trackerId
			LEFT JOIN tiki_tracker_items ON tiki_tracker_items.itemId = tiki_tracker_item_fields.itemId
			WHERE tiki_trackers.name = ? AND
			tiki_tracker_item_fields.itemId = ? AND
			tiki_tracker_item_fields.value <> ''",
			array( $trackerName, $itemId )
		);

		$item = array();

		foreach ($result as $row) {
			$item[$row['name']] = $row['value'];
		}
		return $item;
	}

	function set_tracker_value_custom( $trackerName, $fieldName, $itemId, $value )
	{
		global $tikilib;

		$result = $tikilib->query(
			"UPDATE tiki_tracker_item_fields
			LEFT JOIN tiki_tracker_fields ON tiki_tracker_fields.fieldId = tiki_tracker_item_fields.fieldId
			LEFT JOIN tiki_trackers ON tiki_trackers.trackerId = tiki_tracker_fields.trackerId
			LEFT JOIN tiki_tracker_items ON tiki_tracker_items.itemId = tiki_tracker_item_fields.itemId

			SET tiki_tracker_item_fields.value = ?

			WHERE tiki_trackers.name = ? AND
			tiki_tracker_fields.name = ? AND
			tiki_tracker_item_fields.itemId = ?",
			array($value, $trackerName, $fieldName, $itemId)
		);
	}

	function update_gift_certificate( $invoice )
	{
		global $prefs;
		//if total is more than 0 the gift card is less than the order total, otherwise the giftcard is as much as the order total
		$this->get_gift_certificate();

		if (!$this->gift_certificate_code) return false;

		$balanceCurrent = $this->gift_certificate_amount - $this->gift_certificate_discount;

		if ($this->gift_certificate_amount_original == 0) { //if original balance isn't set, go ahead and set it, it is just for reference
			$this->set_tracker_value_custom($prefs['payment_cart_giftcert_tracker_name'], "Original Balance or Percentage", $this->gift_certificate_id, $this->gift_certificate_amount);
		}

		if ($this->gift_certificate_mode == "Percentage" || $this->gift_certificate_mode == "Coupon Percentage" || $this->gift_certificate_mode == "Coupon") {
			$balanceCurrent = 0;
		}

		$this->set_tracker_value_custom($prefs['payment_cart_giftcert_tracker_name'], "Current Balance or Percentage", $this->gift_certificate_id, $balanceCurrent);

		if (!$invoice) return false;
		//makes the order display a discount, and ensures it is linked correctly
		$orderId = $this->get_tracker_item_id_custom($prefs['payment_cart_orders_tracker_name'], "Tiki Payment ID", $invoice);
		$this->set_tracker_value_custom($prefs['payment_cart_orders_tracker_name'], "Gift Certificate ID", $orderId, $this->gift_certificate_id);
		$this->set_tracker_value_custom($prefs['payment_cart_orders_tracker_name'], "Gift Certificate Amount Applied", $orderId, $this->gift_certificate_discount);

		//now we link the products
		$productTotal = 0;
		$giftCertificateApplies = false;
		$giftCertificateTypeLink = $this->gift_certificate_type_link();
		$products = array();
		$actual_prices_paid = array();

		foreach ( $_SESSION['cart'] as $info ) {
			if ( $info[$giftCertificateTypeLink] == $this->gift_certificate_type_reference || $giftCertificateTypeLink == '' && (!isset($info['is_gift_certificate']) || !$info['is_gift_certificate'])) {
				$products[] = $info;
				$productTotal += floatval($info['quantity']) * floatval($info['price']);
				$giftCertificateApplies = true;
			}
		}

		//We also need to record the item with the price
		if ($giftCertificateApplies == true) {
			if ($this->gift_certificate_mode == "Coupon Percentage" || $this->gift_certificate_mode == "Coupon") {
				$cheapestPrice = 0;
				$cheapestProduct = '';
				foreach ( $products as $product ) {
					if ($cheapestPrice == 0 || $cheapestPrice > $product['price']) {
						$cheapestPrice = $product['price'];
						$cheapestProduct = $product;
					}
				}

				//if it applies, use the cheapest price
				if ($cheapestPrice > 0) {
					$productTotal = $cheapestPrice;
				}
			}

			$this->discount_from_total($productTotal);

			$orderitems = $this->get_orderitems_of_order($orderId);
			foreach ($orderitems as $o) {
				if ($o['parentCode']) {
					$t_code = $o['parentCode'] . '-' . $o['productId'];
				} else {
					$t_code = $o['productId'];
				}
				$order_product_itemIds[$t_code] = $o['itemId'];
			}

			if (isset($cheapestPrice) && $cheapestPrice > 0) {
				// Coupon used
				$totalInputedBundle = 0;
				if (!empty($cheapestProduct['bundledproducts'])) {
					foreach ($cheapestProduct['bundledproducts'] as $q) {
						$totalInputedBundle += $q['inputedprice'];
					}
				}
				$actual_prices_paid[$cheapestProduct['code']] = $cheapestPrice - $this->gift_certificate_discount / $cheapestProduct['quantity'];
				if ($this->gift_certificate_mode == "Coupon Percentage" || $this->gift_certificate_mode == "Percentage") {
					$inputed_giftcert_prices[$cheapestProduct['code']] = $this->get_gift_certificate_cost() / $cheapestProduct['quantity'];
				} else {
					$inputed_giftcert_prices[$cheapestProduct['code']] = $this->get_gift_certificate_cost() * $this->gift_certificate_discount / $this->gift_certificate_amount_original / $cheapestProduct['quantity'];
				}
				if ($totalInputedBundle) {
					foreach ($cheapestProduct['bundledproducts'] as $q) {
						$t_code = $cheapestProduct['code'] . '-' . $q['code'];
						$inputed_giftcert_prices[$t_code] = $q['inputedprice']/$totalInputedBundle * $inputed_giftcert_prices[$cheapestProduct['code']];
					}
				}
			} else {
				foreach ($products as $p) {
					$totalInputedBundle = 0;
					if (!empty($p['bundledproducts'])) {
						foreach ($p['bundledproducts'] as $q) {
							$totalInputedBundle += $q['inputedprice'] * $q['quantity'];
						}
					}
					$actual_prices_paid[$p['code']] = $p['price'] - $p['price'] / $productTotal * $this->gift_certificate_discount;
					if ($this->gift_certificate_mode == "Coupon Percentage" || $this->gift_certificate_mode == "Percentage") {
						$inputed_giftcert_prices[$p['code']] = $this->get_gift_certificate_cost() * $p['price'] / $productTotal;
					} else {
						$inputed_giftcert_prices[$p['code']] = $this->get_gift_certificate_cost() * $this->gift_certificate_discount / $this->gift_certificate_amount_original * $p['price'] / $productTotal;
					}
					if ($totalInputedBundle) {
						foreach ($p['bundledproducts'] as $q) {
							$t_code = $p['code'] . '-' . $q['code'];
							$inputed_giftcert_prices[$t_code] = $q['inputedprice'] / $totalInputedBundle * $inputed_giftcert_prices[$p['code']];
							$inputed_bundle_prices[$t_code] = $q['inputedprice'] / $totalInputedBundle * $actual_prices_paid[$p['code']];

						}
					}
				}
			}

			// Now save Price Paid
			foreach ($actual_prices_paid as $productId => $amountPaid) {
				$this->set_tracker_value_custom($prefs['payment_cart_orderitems_tracker_name'], "Price paid", $order_product_itemIds[$productId], $amountPaid);
			}
			foreach ($inputed_giftcert_prices as $productId => $amountInputed) {
				$this->set_tracker_value_custom($prefs['payment_cart_orderitems_tracker_name'], "Inputed Price From Gift Cert", $order_product_itemIds[$productId], $amountInputed);
			}
			foreach ($inputed_bundle_prices as $productId => $amountInputed) {
				$this->set_tracker_value_custom($prefs['payment_cart_orderitems_tracker_name'], "Inputed Price From Bundle", $order_product_itemIds[$productId], $amountInputed);
			}
		}
		// set refunding in the event of cancellation
		$paymentlib = TikiLib::lib('payment');
		$paymentlib->register_behavior($invoice, 'cancel', 'cart_gift_certificate_refund', array( $this->gift_certificate_id, $this->gift_certificate_mode, $this->gift_certificate_amount, $this->gift_certificate_discount));
	}

	function get_orderitems_of_order( $orderId )
	{
		global $prefs;
		$result = array();
		$productItemIds = $this->get_tracker_item_ids_custom($prefs['payment_cart_orderitems_tracker_name'], "Order ID", $orderId);

		foreach ($productItemIds as $productItemId) {
			$result[] = array(
				"itemId" => $productItemId["itemId"],
				"productId" => $this->get_tracker_value_custom($prefs['payment_cart_orderitems_tracker_name'], "Product ID", $productItemId["itemId"]),
				"parentCode" => $this->get_tracker_value_custom($prefs['payment_cart_orderitems_tracker_name'], "Parent Code", $productItemId["itemId"])
			);
		}

		return $result;
	}

	function get_gift_certificate_code( $code = null )
	{
		$code = ( $code ? $code : isset($_SESSION['cart']['tiki-gc']['code']) ? $_SESSION['cart']['tiki-gc']['code'] : null ); //TODO: needs to be a little less dirty
		return $code;
	}

	function get_gift_certificate( $code = null )
	{
		global $prefs;
		$this->gift_certificate_code = $code = ( $code ? $code : $this->get_gift_certificate_code() );
		if (!$code) return false;

		$this->gift_certificate_id = $this->get_tracker_item_id_custom($prefs['payment_cart_giftcert_tracker_name'], "Redeem Code", $code);

		$this->gift_certificate_amount = floatval(
			$this->get_tracker_value_custom(
				$prefs['payment_cart_giftcert_tracker_name'],
				"Current Balance or Percentage",
				$this->gift_certificate_id
			)
		);

		$this->gift_certificate_amount_original = floatval(
			$this->get_tracker_value_custom(
				$prefs['payment_cart_giftcert_tracker_name'],
				"Original Balance or Percentage",
				$this->gift_certificate_id
			)
		);

		$this->gift_certificate_type = $this->get_tracker_value_custom($prefs['payment_cart_giftcert_tracker_name'], "Type", $this->gift_certificate_id);
		$this->gift_certificate_type_reference = $this->get_tracker_value_custom($prefs['payment_cart_giftcert_tracker_name'], "Type Reference", $this->gift_certificate_id);
		$this->gift_certificate_name = $this->get_tracker_value_custom($prefs['payment_cart_giftcert_tracker_name'], "Name", $this->gift_certificate_id);
		$this->gift_certificate_mode = $this->get_tracker_value_custom($prefs['payment_cart_giftcert_tracker_name'], "Mode", $this->gift_certificate_id);

		switch ( $this->gift_certificate_mode ) {
			case "Percentage":
			case "Coupon Percentage":
				$this->gift_certificate_mode_symbol_after = '%';
				break;
		}

		return ( $this->gift_certificate_amount > 0 ? true : false );
	}

	function remove_gift_certificate()
	{
		unset ($_SESSION['cart']['tiki-gc']);
	}

	function add_gift_certificate( $code = null )
	{
		$this->get_gift_certificate($code);

		if ( $this->gift_certificate_amount > 0 ) {
			if ( ! isset($_SESSION['cart']) ) return false;
			$_SESSION['cart']['tiki-gc'] = array();
			$_SESSION['cart']['tiki-gc']['is_gift_certificate'] = true;
			$_SESSION['cart']['tiki-gc']['code'] = $code;
			return true;
		} else {
			$this->remove_gift_certificate();
			return false;
		}
	}

	function has_gift_certificate()
	{
		global $prefs;
		$trklib = TikiLib::lib('trk');
		return ($trklib->get_tracker_by_name($prefs['payment_cart_giftcert_tracker_name']) ? true : false );
	}

	function discount_from_total( $total )
	{ //ensures that the discount being had isn't less that the total resulting in a negative value
		switch ( $this->gift_certificate_mode ) {
			case "Percentage":
			case "Coupon Percentage":
				$total = $total - (($total / 100) * $this->gift_certificate_amount);
				break;
			default:
				if ( $this->gift_certificate_amount <= $total ) { //total is more or equal to cert
					$total -= $this->gift_certificate_amount;
				} else { //cert is valued for more than the order total
					$total = 0;
				}
		}

		return $total;
	}

	function product_reference_gift_certificate($total, $reference )
	{
		$productTotal = 0;
		$giftCertificateApplies = false;
		$products = array();

		foreach ( $_SESSION['cart'] as $info ) {
			if ( $info[$reference] == $this->gift_certificate_type_reference ) {
				$products[] = $info;
				$productTotal += floatval($info['quantity']) * floatval($info['price']);
				$giftCertificateApplies = true;
			}
		}

		if ($giftCertificateApplies) {
			if ($this->gift_certificate_mode == "Coupon Percentage" || $this->gift_certificate_mode == "Coupon") {
				$cheapestPrice = 0;
				foreach ( $products as $product ) {
					if ($cheapestPrice == 0 || $cheapestPrice > $product['price']) {
						$cheapestPrice = $product['price'];
					}
				}

				//if it applies, use the cheapest price
				if ($cheapestPrice > 0) {
					$productTotal = $cheapestPrice;
				}
			}

			$total -= $productTotal;
			$productTotal = $this->discount_from_total($productTotal);
			$total += $productTotal;
		} else {
			$this->remove_gift_certificate();
			$this->handle_error(tra('Gift card is not valid for products in cart'));
		}

		return $total;
	}

	function gift_certificate_type_link()
	{
		if ($this->gift_certificate_code) {
				switch ($this->gift_certificate_type) {
					case "Product Bundle":
						return 'productbundle';
			      break;
					case "Bundle Class":
						return 'bundleclass';
			      break;
					case "Product Class": //product class can only be used for a single Product Class
						return 'productclass';
			      break;
					case "Product": //product can only be used with a single product
						return 'code';
			      break;
					case "Cash": //cash can be used with any cart items
					default:
						return;
				}
		}
	}

	function get_total()
	{
		$this->init_cart();
		$this->get_gift_certificate();

		$total = 0;

		foreach ( $_SESSION['cart'] as $info ) {
			$total += floatval($info['quantity']) * floatval($info['price']);
		}

		$this->total_no_discount = $total;

		if ($this->gift_certificate_code) {
			$giftCertificateTypeLink = $this->gift_certificate_type_link();
			if (strlen($giftCertificateTypeLink) > 0) {
				$total = $this->product_reference_gift_certificate($total, $giftCertificateTypeLink);
			} else {
				$total = $this->discount_from_total($total);
			}
		}

		$this->gift_certificate_discount = $this->total_no_discount - $total;

		// CUSTOM feature not complete for group discount
		if ($groupDiscount = $this->get_group_discount()) {
			$total = (1 - $groupDiscount) * $total;
		}

		return number_format($total, 2, '.', '');
	}

	function get_quantity( $code )
	{
		$this->init_cart();

		if ( isset( $_SESSION['cart'][ $code ] ) ) {
			return $_SESSION['cart'][ $code ]['quantity'];
		} else {
			return 0;
		}
	}

	function get_hash( $code )
	{
		$this->init_cart();

		if ( isset( $_SESSION['cart'][ $code ] ) ) {
			return $_SESSION['cart'][ $code ]['hash'];
		} else {
			return '';
		}
	}

	function generate_item_description( $item, $parentCode = 0 )
	{
		$wiki = '';

		if ( $item['href'] ) {
			$label = "[{$item['href']}|{$item['description']}]";
		} else {
			$label = $item['description'];
		}
		if ( !empty($item['onbehalf']) ) {
			$label .= " " . tra('for') . " " . $item['onbehalf'];
		}
		if ($parentCode) {
			$label = tra('Bundled Product') . ' - ' . $label;
			if ($item['quantity'] > 1) {
				$label .= ' (x' . $item['quantity'] . ')';
			}
			$item['quantity'] = ' ';
			$item['price'] = ' ';
		}
		$wiki .= "{$item['code']}|{$label}|{$item['quantity']}|{$item['price']}\n";
		return $wiki;
	}

	function get_description()
	{
		$id_label = tra('ID');
		$product_label = tra('Product');
		$quantity_label = tra('Quantity');
		$price_label = tra('Unit Price');
		$gift_certificate_label = tra("Gift Certificate: ");
		$gift_certificate_amount_used_label = tra("Gift Certificate Amount Used: ");

		$wiki = "||__{$id_label}__|__{$product_label}__|__{$quantity_label}__|__{$price_label}__\n";

		foreach ( $this->get_content() as $item ) {
			if ( !isset($item['is_gift_certificate']) || !$item['is_gift_certificate'] ) {
				$wiki .= $this->generate_item_description($item);
				if ($bundledProducts = $this->get_bundled_products($item['code'])) {
					foreach ($bundledProducts as $b) {
						$wiki .= $this->generate_item_description($b, $item['code']);
					}
				}
			}
		}

		$wiki .= "||\n";

		if ( isset($this->gift_certificate_code) && isset($this->gift_certificate_discount) ) {
			$wiki .= $gift_certificate_label . $this->gift_certificate_code . " " . $this->gift_certificate_name . "\n";
			$wiki .= $gift_certificate_amount_used_label . $this->gift_certificate_discount;
		}

		if ( $groupDiscount = $this->get_group_discount() ) {
			$wiki .= "\nSpecial Group Discount: " . $groupDiscount * 100 . "%";
		}

		return $wiki;
	}

	function get_total_weight()
	{
		$this->init_cart();

		$total = 0;

		foreach ( $_SESSION['cart'] as $info ) {
			if (!empty($info['weight'])) {
				$total += intval($info['quantity']) * floatval($info['weight']);
			}
		}

		return $total;
	}

	function get_count()
	{
		$this->init_cart();

		$total = 0;

		foreach ( $_SESSION['cart'] as $info ) {
			if (!empty($info['quantity'])) {
				$total += intval($info['quantity']);
			}
		}

		return $total;
	}

	function product_in_cart( $code )
	{
		return isset( $_SESSION['cart'][$code] );
	}

 	//Used for adjusting already added items in the cart
	function update_quantity( $code, $quantity, $info = array('exchangetoproductid' => 0, 'exchangeorderamount' => 0) )
	{
		global $prefs;
		$currentQuantity = $this->get_quantity($code);
		if ($prefs['payment_cart_inventory'] == 'y') {
			// Prevent going below 0 inventory
			$currentInventory = $this->get_inventory($code);
			if ($quantity - $currentQuantity > $currentInventory) {
				if ($currentQuantity == 0) {
					unset( $_SESSION['cart'][ $code ] );
				}

				$this->handle_error(tra('There is insufficient inventory to meet your request'));
			}

			if ($currentQuantity > 0) {
				if ($this->unhold_inventory($code, $currentQuantity)) {
					$this->remove_from_onhold_list($code);
				}
				if ($info['exchangetoproductid'] && $info['exchangeorderamount']) {
					if ($this->unhold_inventory($info['exchangetoproductid'], $info['exchangeorderamount'])) {
						$this->remove_from_onhold_list('XC' . $info['exchangetoproductid']);
					}
				}
			}
			if ($quantity > 0) {
				$currentInventory = $this->get_inventory($code);
				if ($quantity > $currentInventory) {
					$quantity = $currentInventory;
				}
				if ($this->hold_inventory($code, $quantity)) {
					$this->add_to_onhold_list($code, $quantity);
				}

				if ($info['exchangetoproductid'] && $info['exchangeorderamount']) {
					if ($this->hold_inventory($info['exchangetoproductid'], $info['exchangeorderamount'])) {
	                                	$this->add_to_onhold_list('XC' . $info['exchangetoproductid'], $info['exchangeorderamount']);
					}
				}
			}
		}
		$this->init_cart();

		if ( isset( $_SESSION['cart'][ $code ] ) && $quantity != 0  ) {
			$_SESSION['cart'][ $code ]['quantity'] = abs($quantity);
		} else {
			unset( $_SESSION['cart'][ $code ] );
		}
	}

	function request_payment()
	{
		global $prefs, $user;
		$tikilib = TikiLib::lib('tiki');
		$paymentlib = TikiLib::lib('payment');

		$total = $this->get_total();

		if ( $total > 0 || $this->total_no_discount ) {
			// if anonymous shopping to set pref as to which shopperinfo to show in description
			if (empty($user) && $prefs['payment_cart_anonymous'] === 'y') {
				$shopperinfo_descvar = 'email'; // TODO: make this a pref
				if (!empty($_SESSION['shopperinfo'][$shopperinfo_descvar])) {
					$shopperinfo_desc = $_SESSION['shopperinfo'][$shopperinfo_descvar];
					$description = tra($prefs['payment_cart_heading']) . " ($shopperinfo_desc)";
				} else {
					$description = tra($prefs['payment_cart_heading']);
				}
			} else {
				$description = tra($prefs['payment_cart_heading']) . " ($user)";
			}
			$invoice = $paymentlib->request_payment($description, $total, $prefs['payment_default_delay'], $this->get_description());
			foreach ( $this->get_behaviors() as $behavior ) {
				$paymentlib->register_behavior($invoice, $behavior['event'], $behavior['behavior'], $behavior['arguments']);
			}
		} else {
			$invoice = 0;
			foreach ( $this->get_behaviors() as $behavior ) {
				if ($behavior['event'] == 'complete') {
					$name = $behavior['behavior'];
					$file = dirname(__FILE__) . "/behavior/$name.php";
					$function = 'payment_behavior_' . $name;
					require_once $file;
					call_user_func_array($function, $behavior['arguments']);
				}
			}
		}
		// Handle anonymous user (not logged in) shopping that require only email
		if (!$user || isset($_SESSION['forceanon']) && $_SESSION['forceanon'] == 'y') {
			if (!empty($_SESSION['shopperinfo'])) { // should also check for pref that this anonymous shopping feature is on
				// First create shopper info in shopper tracker
				global $record_profile_items_created;
				$record_profile_items_created = array();
				if (!empty($_SESSION['shopperinfoprofile'])) {
					$shopper_profile_name = $_SESSION['shopperinfoprofile'];
				} else {
					$shopper_profile_name = $prefs['payment_cart_anonshopper_profile'];
				}
				$shopperprofile = Tiki_Profile::fromDb($shopper_profile_name);
				$profileinstaller = new Tiki_Profile_Installer();
				$profileinstaller->forget($shopperprofile); // profile can be installed multiple times
				$profileinstaller->setUserData($_SESSION['shopperinfo']);
				$profileinstaller->install($shopperprofile);
				// Then set user to shopper ID
				$cartuser = $record_profile_items_created[0];
				$record_profile_items_created = array();
			} else {
				$this->empty_cart();
				return $invoice;
			}
		} else {
			$cartuser = $user;
		}

		$userInput = array(
			'user' => $cartuser,
			'time' => $tikilib->now,
			'total' => $total,
			'invoice' => $invoice,
			'weight' => $this->get_total_weight(),
		);
		if (!$user || isset($_SESSION['forceanon']) && $_SESSION['forceanon'] == 'y') {
			$orderprofile = Tiki_Profile::fromDb($prefs['payment_cart_anonorders_profile']);
			$orderitemprofile = Tiki_Profile::fromDb($prefs['payment_cart_anonorderitems_profile']);
		} else {
			$orderprofile = Tiki_Profile::fromDb($prefs['payment_cart_orders_profile']);
			$orderitemprofile = Tiki_Profile::fromDb($prefs['payment_cart_orderitems_profile']);
		}
		if ($user && $prefs['payment_cart_orders'] == 'y' || !$user && $prefs['payment_cart_anonymous'] == 'y') {
			if (! $orderprofile) {
				TikiLib::lib('errorreport')->report(tra('Advanced Shopping Cart setup error: Orders profile missing.'));
				return false;
			}
			$profileinstaller = new Tiki_Profile_Installer();
			$profileinstaller->forget($orderprofile); // profile can be installed multiple times
			$profileinstaller->setUserData($userInput);
		} else {
			$profileinstaller = '';
		}
		global $record_profile_items_created;
		$record_profile_items_created = array();

		if ($user && $prefs['payment_cart_orders'] == 'y' || !$user && $prefs['payment_cart_anonymous'] == 'y') {
			$profileinstaller->install($orderprofile, 'none');
		}

		$content = $this->get_content();

		foreach ( $content as $info ) {
			if (!isset($info['is_gift_certificate']) || !$info['is_gift_certificate']) {
				$process_info = $this->process_item($invoice, $total, $info, $userInput, $cartuser, $profileinstaller, $orderitemprofile);
			}
		}
		$email_template_ids = array();

		if (isset($process_info['product_classes']) && is_array($process_info['product_classes'])) {
			$product_classes = array_unique($process_info['product_classes']);
		} else {
			$product_classes = array();
		}

		foreach ($product_classes as $pc) {
			if ($email_template_id = $this->get_tracker_value_custom($prefs['payment_cart_productclasses_tracker_name'], 'Email Template ID', $pc)) {
				$email_template_ids[] = $email_template_id;
			}
		}
		if (!empty($record_profile_items_created)) {
			if ($total > 0) {
				$paymentlib->register_behavior($invoice, 'complete', 'record_cart_order', array( $record_profile_items_created ));
				$paymentlib->register_behavior($invoice, 'cancel', 'cancel_cart_order', array( $record_profile_items_created ));
				if ($user) {
					$paymentlib->register_behavior($invoice, 'complete', 'cart_send_confirm_email', array( $user, $email_template_ids ));
				}
			} else {
				require_once('lib/payment/behavior/record_cart_order.php');
				payment_behavior_record_cart_order($record_profile_items_created);
				if ($user) {
					require_once('lib/payment/behavior/cart_send_confirm_email.php');
					payment_behavior_cart_send_confirm_email($user, $email_template_ids);
				}
			}
		}

		if (!$user || isset($_SESSION['forceanon']) && $_SESSION['forceanon'] == 'y') {
			$shopperurl = 'tiki-index.php?page=' . $prefs['payment_cart_anon_reviewpage'] . '&shopper=' . intval($cartuser);
			global $tikiroot, $prefs;
			$shopperurl = $tikilib->httpPrefix(true) . $tikiroot . $shopperurl;
			require_once 'lib/auth/tokens.php';
			$tokenlib = AuthTokens::build($prefs);
			$shopperurl = $tokenlib->includeToken($shopperurl, array($prefs['payment_cart_anon_group'], 'Anonymous'));

			if ( !empty($_SESSION['shopperinfo']['email']) ) {
				require_once('lib/webmail/tikimaillib.php');
				$smarty = TikiLib::lib('smarty');
				$smarty->assign('shopperurl', $shopperurl);
				$smarty->assign('email_template_ids', $email_template_ids);
				$mail_subject = $smarty->fetch('mail/cart_order_received_anon_subject.tpl');
				$mail_data = $smarty->fetch('mail/cart_order_received_anon.tpl');
				$mail = new TikiMail();
				$mail->setSubject($mail_subject);
				if ($mail_data == strip_tags($mail_data)) {
					$mail->setText($mail_data);
				} else {
					$mail->setHtml($mail_data);
				}
				$mail->send($_SESSION['shopperinfo']['email']); // the field to use probably needs to be configurable as well
			}
		}
		$this->update_gift_certificate($invoice);
		$this->update_group_discount($invoice);

		$this->empty_cart();
		return $invoice;
	}

	function process_item($invoice, $total, $info, $userInput, $cartuser, $profileinstaller, $orderitemprofile, $parentQuantity = 0, $parentCode = 0 )
	{
		global $user, $prefs, $record_profile_items_created;
		$userlib = TikiLib::lib('user');
		$paymentlib = TikiLib::lib('payment');
		if ($bundledProducts = $this->get_bundled_products($info['code'])) {
			foreach ($bundledProducts as $i) {
				$this->process_item($invoice, $total, $i, $userInput, $cartuser, $profileinstaller, $orderitemprofile, $info['quantity'], $info['code']);
			}
		}
		if ($parentQuantity) {
			$info['quantity'] = $info['quantity'] * $parentQuantity;
		}
		$product_classes = array();
		if (isset($info['productclass']) && $info['productclass']) {
			$product_classes[] = $info['productclass'];
		}
		if (!empty($info['onbehalf'])) {
			$itemuser = $info['onbehalf'];
		} elseif (!$user || isset($_SESSION['forceanon']) && $_SESSION['forceanon'] == 'y') {
			$itemuser = $cartuser;
		} else {
			$itemuser = $user;
		}
		$userInput = array(
			'user' => $itemuser,
			'quantity' => $info['quantity'],
			'price' => $info['price'],
			'product' => $info['code'],
			'inputedprice' => $info['inputedprice'],
			'eventcode' => $info['eventcode'],
			'parentcode' => $parentCode,
			'eventstart' => $this->get_tracker_value_custom($prefs['payment_cart_event_tracker_name'], $prefs['payment_cart_eventstart_fieldname'], $info['eventcode']),
			'eventend' => $this->get_tracker_value_custom($prefs['payment_cart_event_tracker_name'], $prefs['payment_cart_eventend_fieldname'], $info['eventcode']),
		);
		if ($user && $prefs['payment_cart_orders'] == 'y' || !$user && $prefs['payment_cart_anonymous'] == 'y') {
			$profileinstaller->setUserData($userInput);
			$profileinstaller->forget($orderitemprofile);
			$profileinstaller->install($orderitemprofile, 'none');
		}

		$this->change_inventory($info['code'], -1 * $info['quantity'], false);
		if ((isset($info['exchangetoproductid']) && $info['exchangetoproductid'])
			&& (isset($info['exchangeorderamount']) && $info['exchangeorderamount'])) {
			$this->change_inventory($info['exchangetoproductid'], -1 * $info['exchangeorderamount'], false);
		}
		if ($total > 0) {
			$paymentlib->register_behavior($invoice, 'cancel', 'replace_inventory', array( $info['code'], $info['quantity'] ));
			if ((isset($info['exchangetoproductid']) && $info['exchangetoproductid'])
				&& (isset($info['exchangeorderamount']) && $info['exchangeorderamount'])) {
				$paymentlib->register_behavior($invoice, 'cancel', 'replace_inventory', array( $info['exchangetoproductid'], $info['exchangeorderamount'] ));
			}
		}
		// Generate behavior for gift certificate purchase
		if ($info['producttype'] == 'gift certificate') {
			if (!$user && !empty($_SESSION['shopperinfo']['email'])) {
				$giftcert_email = $_SESSION['shopperinfo']['email'];
			} else {
				$giftcert_email = $userlib->get_user_email($itemuser);
			}
			if (!empty($giftcert_email) && $total > 0) {
				$paymentlib->register_behavior($invoice, 'complete', 'cart_gift_certificate_purchase', array($info['code'], $giftcert_email, $info['quantity'], $record_profile_items_created[0], end($record_profile_items_created)));
			} elseif (!empty($giftcert_email)) {
				require_once('lib/payment/behavior/cart_gift_certificate_purchase.php');
				payment_behavior_cart_gift_certificate_purchase($info['code'], $giftcert_email, $info['quantity'], $record_profile_items_created[0], end($record_profile_items_created));
			}
		}
		$ret = array('product_classes' => $product_classes);
		return $ret;
	}

	function empty_cart()
	{
		$this->clear_onhold_list();
		$_SESSION['cart'] = array();
	}

	private function get_behaviors()
	{
		$behaviors = array();

		foreach ( $this->get_content() as $item ) {
			if ( isset( $item['behaviors'] ) ) {
				foreach ( $item['behaviors'] as $behavior ) {
					for ( $i = 0; $item['quantity'] > $i; ++$i ) {
						$behaviors[] = $behavior;
					}
				}
			}
		}

		return $behaviors;
	}

	private function init_cart()
	{
		if ( ! isset( $_SESSION['cart'] ) ) {
			$_SESSION['cart'] = array();
		}
	}

	private function init_product( $code, $info, $parentCode = 0, $quantity = 0, $childInputedPrice = 0 )
	{

		if ( ! isset( $_SESSION['cart'][ $code ] ) ||  ! isset( $_SESSION['cart'][ $parentCode ][ 'bundledproducts' ][ $code ] ) ) {
			$info['hash'] = md5($code.time());
			$info['code'] = $code;
			$info['quantity'] = $quantity;
			$info['price'] = number_format(abs($info['price']), 2, '.', '');
			$info['inputedprice'] = number_format(abs($childInputedPrice), 2, '.', '');

			if ( ! isset( $info['href'] ) ) {
				$info['href'] = null;
			}

			if ( !$parentCode ) {
				$_SESSION['cart'][ $code ] = $info;
			} else {
				 $_SESSION['cart'][ $parentCode ][ 'bundledproducts' ][ $code ] = $info;
			}
		}
	}

	function get_bundled_products( $parentCode )
	{
		$cart = $this->get_content();
		if (isset($cart[$parentCode]['bundledproducts'])) {
			return $cart[$parentCode]['bundledproducts'];
		} else {
			return false;
		}
	}

	function get_content()
	{
		$this->init_cart();

		return $_SESSION['cart'];
	}

	function get_inventory_type( $productId )
	{
		global $prefs;
		$productTrackerId = $prefs['payment_cart_product_tracker'];
		$inventoryTypeFieldId = $prefs['payment_cart_inventory_type_field'];
		$trklib = TikiLib::lib('trk');
		return $trklib->get_item_value($productTrackerId, $productId, $inventoryTypeFieldId);
	}

	function get_inventory( $productId, $less_hold = true )
	{
		global $prefs;
		$inventoryType = $this->get_inventory_type($productId);
		if ($inventoryType == 'none') {
			return 999999999;
		}
		if ($inventoryType == 'shared') {
			// TODO: shared inventory feature not yet exist
			return 0;
		}
		$productTrackerId = $prefs['payment_cart_product_tracker'];
		$inventoryTotalFieldId = $prefs['payment_cart_inventory_total_field'];
		$inventoryLessHoldFieldId = $prefs['payment_cart_inventory_lesshold_field'];
		if ($less_hold) {
			$this->expire_onhold_list($productId);
			$inventoryFieldId = $inventoryLessHoldFieldId;
		} else {
			$inventoryFieldId = $inventoryTotalFieldId;
		}
		$trklib = TikiLib::lib('trk');
		return $trklib->get_item_value($productTrackerId, $productId, $inventoryFieldId);
	}

	function change_inventory( $productId, $amount = 1, $changeLessHold = true)
	{
		global $prefs;
		if ($prefs['payment_cart_inventory'] != 'y') {
			return false;
		}
		$inventoryType = $this->get_inventory_type($productId);
		if ($inventoryType == 'none') {
			return false;
		}
		$currentTotal = $this->get_inventory($productId, false);
		$newTotal = max(0, $currentTotal + $amount);
		$this->set_inventory($productId, $newTotal, false);
		if ($changeLessHold) {
			$currentLessHold = $this->get_inventory($productId);
			$newLessHold = max(0, $currentLessHold + $amount);
			$this->set_inventory($productId, $newLessHold, true);
		}
		return true;
	}

	function hold_inventory( $productId, $amount = 1)
	{
		if ($bundledProducts = $this->get_bundled_products($productId) ) {
			foreach ($bundledProducts as $b) {
				$this->hold_inventory($b['code'], $amount * $b['quantity']);
			}
		}
		$inventoryType = $this->get_inventory_type($productId);
		if ($inventoryType == 'none') {
			return false;
		}
		$currentLessHold = $this->get_inventory($productId);
		$newLessHold = max(0, $currentLessHold - $amount);
		$this->set_inventory($productId, $newLessHold, true);
		return true;
	}

	function unhold_inventory( $productId, $amount = 1)
	{
		if ($bundledProducts = $this->get_bundled_products($productId) ) {
			foreach ($bundledProducts as $b) {
				$this->unhold_inventory($b['code'], $amount * $b['quantity']);
			}
		}
		$inventoryType = $this->get_inventory_type($productId);
		if ($inventoryType == 'none') {
			return false;
		}
		$currentLessHold = $this->get_inventory($productId);
		$currentTotal = $this->get_inventory($productId, false);
		$newLessHold = min($currentTotal, $currentLessHold + $amount);
		$this->set_inventory($productId, $newLessHold, true);
		return true;
	}

	private function set_inventory( $productId, $amount, $less_hold = true )
	{
		global $prefs;
		$inventoryType = $this->get_inventory_type($productId);
		if ($inventoryType == 'none') {
			return false;
		}
		if ($inventoryType == 'shared') {
			// TODO: shared inventory feature not existing yet
			return false;
		}
		$productTrackerId = $prefs['payment_cart_product_tracker'];
		$inventoryTotalFieldId = $prefs['payment_cart_inventory_total_field'];
		$inventoryLessHoldFieldId = $prefs['payment_cart_inventory_lesshold_field'];
		if ($less_hold) {
			$inventoryFieldId = $inventoryLessHoldFieldId;
		} else {
			$inventoryFieldId = $inventoryTotalFieldId;
		}
		$trackerFields = array();
		$trackerFields[] = array('fieldId' => $inventoryFieldId, 'value' => $amount);
               	$this->modify_tracker_item($productTrackerId, $productId, $trackerFields);
		return true;
	}

	private function modify_tracker_item( $trackerId, $itemId, $trackerFields )
	{
		$trklib = TikiLib::lib('trk');
		$tracker_fields_info = $trklib->list_tracker_fields($trackerId);
		$fieldTypes = array();
		foreach ($tracker_fields_info['data'] as $t) {
			$fieldTypes[$t['fieldId']] = $t['type'];
			$fieldOptionsArray[$t['fieldId']] = $t['options_array'];
		}
		foreach ($trackerFields as &$h) {
			$h['type'] = $fieldTypes[$h['fieldId']];
			$h['options_array'] = $fieldOptionsArray[$h['fieldId']];
		}
		foreach ($trackerFields as $v) {
			$ins_fields["data"][] = array('options_array' => $v['options_array'], 'type' => $v['type'], 'fieldId' => $v['fieldId'], 'value' => $v['value']);
		}
		$trklib->replace_item($trackerId, $itemId, $ins_fields);
		return true;
	}

	private function clear_onhold_list()
	{
		global $tikilib;
		$hashes = array();
		foreach ( $this->get_content() as $item ) {
			$hashes[] = $item['hash'];
		}
		if (empty($hashes)) {
			return false;
		}
		$mid = implode(',', array_fill(0, count($hashes), '?'));
		$query = "delete from `tiki_cart_inventory_hold` where `hash` in ($mid)";
		$tikilib->query($query, $hashes);
		return true;
	}

	private function expire_onhold_list( $productId )
	{
		global $tikilib, $prefs;
		$expiry = $prefs['payment_cart_inventoryhold_expiry'] * 60;
		$hash = $this->get_hash($productId);
		$query = "select sum(`quantity`) from `tiki_cart_inventory_hold` where `productId` = ? and `timeHeld` < ?";
		$bindvars = array($productId, $tikilib->now - $expiry);
		if ($hash) {
			$query .= " and `hash` != ?";
			$bindvars[] = $hash;
		}
		$quantity = $tikilib->getOne($query, $bindvars);
		$query = "delete from `tiki_cart_inventory_hold` where `productId` = ? and `timeHeld` < ?";
		if ($hash) {
			$query .= " and `hash` != ?";
		}
		$tikilib->query($query, $bindvars);
		if ($quantity > 0) {
			$this->unhold_inventory($productId, $quantity);
		}
		return true;
	}

	function extend_onhold_list()
	{
		global $tikilib, $prefs;
		$extend = $prefs['payment_cart_inventoryhold_expiry'] * 60;
		$hashes = array();
		foreach ( $this->get_content() as $item ) {
			$hashes[] = $item['hash'];
		}
		if (empty($hashes)) {
			return false;
		}
		$mid = implode(',', array_fill(0, count($hashes), '?'));
		$query = "select min(`timeHeld`) from `tiki_cart_inventory_hold` where `hash` in ($mid)";
		$earliest = $tikilib->getOne($query, $hashes);
		if ($earliest > $tikilib->now - $extend) {
			return false;
		}
		$query = "update `tiki_cart_inventory_hold` set `timeHeld` = ? where `hash` in ($mid)";
		$bindvars = array_merge(array($tikilib->now), $hashes);
		$tikilib->query($query, $bindvars);
		return true;
	}

	private function remove_from_onhold_list( $code )
	{
		global $tikilib;
		$hash = $this->get_hash($code);
		$query = "delete from `tiki_cart_inventory_hold` where `hash` = ?";
		$tikilib->query($query, $hash);
		return true;
	}

	private function add_to_onhold_list( $code, $quantity )
	{
		global $tikilib;
		$hash = $this->get_hash($code);
		$query = "insert into `tiki_cart_inventory_hold` (`productId`, `quantity`, `timeHeld`, `hash`) values (?,?,?,?)";
		$bindvars = array($code, $quantity, $tikilib->now, $hash);
		$tikilib->query($query, $bindvars);
		return true;
	}

	function get_missing_user_information_fields( $product_class_id, $type = 'required' )
	{
		global $user, $prefs;
		$trklib = TikiLib::lib('trk');
		if ($type == 'required') {
			$fields_str = $this->get_tracker_value_custom($prefs['payment_cart_productclasses_tracker_name'], 'Required Field IDs', $product_class_id);
		} else if ($type == 'postpurchase') {
			$fields_str = $this->get_tracker_value_custom($prefs['payment_cart_productclasses_tracker_name'], 'Postpurchase Field IDs', $product_class_id);
		}
		$fields = explode(',', str_replace(' ', '', $fields_str));
		$tocheck = array();
		$missing = array();
		foreach ($fields as $f) {
			if (empty($f)) continue;
			$trackerId = $trklib->getOne('select `trackerId` from `tiki_tracker_fields` where `fieldId` = ?', $f);
			$tocheck[$trackerId][] = $f;
		}
		foreach ($tocheck as $trackerId => $flds) {
			$definition = Tracker_Definition::get($trackerId);
			if ($fieldId = $definition->getUserField()) {
				$item = $trklib->get_item($trackerId, $fieldId, $user);
				foreach ($flds as $f) {
					if (!isset($item[$f]) || !$item[$f]) {
						$missing[$trackerId][] = $f;
					}
				}
			} else {
				$missing = $tocheck;
			}
		}
		return $missing;
	}

	function get_missing_user_information_form( $product_class_id, $type = 'required' )
	{
		global $prefs;
		if ($type == 'required') {
			return $this->get_tracker_value_custom($prefs['payment_cart_productclasses_tracker_name'], 'Associated Required Form', $product_class_id);
		} else {
			return $this->get_tracker_value_custom($prefs['payment_cart_productclasses_tracker_name'], 'Associated Postpurchase Form', $product_class_id);
		}
	}

	function skip_user_information_form_if_not_missing( $product_class_id )
	{
		global $prefs;
		if ($this->get_tracker_value_custom($prefs['payment_cart_productclasses_tracker_name'], 'Skip Required Form if Filled', $product_class_id) == 'Yes') {
			return true;
		} else {
			return false;
		}
	}

	function get_group_discount()
	{
		// TOTALLY CUSTOM until proper feature is ready
		global $user;
		$userlib = TikiLib::lib('user');
		if (!$user) return 0;
		$userGroups = $userlib->get_user_groups($user);
		if (in_array('Shop Free', $userGroups)) {
			return 1;
		}
		return 0;
	}

	function update_group_discount( $invoice )
	{
		global $prefs;
		// Now to take into account group discount as well
		if (!$invoice) return false;
		if ($groupDiscount = $this->get_group_discount()) {
			$orderId = $this->get_tracker_item_id_custom($prefs['payment_cart_orders_tracker_name'], "Tiki Payment ID", $invoice);
			$orderitems = $this->get_orderitems_of_order($orderId);
			foreach ($orderitems as $o) {
				$order_product_itemIds[$o['productId']] = $o['itemId'];
				$orig_prices_paid[$o['productId']] = $this->get_tracker_value_custom($prefs['payment_cart_orderitems_tracker_name'], "Price paid", $o['itemId']);
			}
			// Now save Price Paid
			foreach ($orig_prices_paid as $productId => $origPrice) {
				$amountPaid = (1 - $groupDiscount) * $origPrice;
				$this->set_tracker_value_custom($prefs['payment_cart_orderitems_tracker_name'], "Price paid", $order_product_itemIds[$productId], $amountPaid);
			}
		}
	}

	function get_gift_certificate_cost( $id = 0 )
	{
		global $prefs;
		if (!$id) {
			$id = $this->gift_certificate_id;
		}
		$inputedPrice = $this->get_tracker_value_custom($prefs['payment_cart_giftcert_tracker_name'], 'Gift Certificate Inputed Price', $id);
		if ($inputedPrice > 0) {
			return $inputedPrice;
		}
		$orderItemId = $this->get_tracker_value_custom($prefs['payment_cart_giftcert_tracker_name'], 'Order Item ID', $id);
		if (!$orderItemId) {
			return 0;
		}
		if ($parentCode = $this->get_tracker_value_custom($prefs['payment_cart_orderitems_tracker_name'], 'Parent Code', $orderItemId)) {
			$cost = $this->get_tracker_value_custom($prefs['payment_cart_orderitems_tracker_name'], 'Inputed Price From Bundle', $orderItemId);
		} else {
			$cost = $this->get_tracker_value_custom($prefs['payment_cart_orderitems_tracker_name'], 'Price paid', $orderItemId);
		}
		return $cost;
	}

	function handle_error($msg) {
		$access = TikiLib::lib('access');

		if ($access->is_xml_http_request()) {
			throw new Services_Exception($msg);
		} else {
			$access->redirect($_SERVER['REQUEST_URI'], $msg);
		}
	}
}

