<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class CartLib
{
	function add_product( $code, $quantity, $info, $parentCode ) {
		$this->init_cart();

		$this->init_product( $code, $info, $parentCode );
		
		if (isset($parentCode)) return false;
		
		$current = $this->get_quantity( $code );
		$current += $quantity;
		
		$this->add_bundle( $code, $quantity, $info );
		
		$this->update_quantity( $code, $current, $info, $fromBundle );
	}
	
	function get_product_info( $code ) {
		$productId = $this->get_tracker_item_id_custom( "Products", "Product ID", $code );
		$array = $this->get_tracker_values_custom( "Products", $productId);
		$info = array();
		
		while($result = current($array)) {
			$key = str_replace(" ", "", strtolower(key($array)));
			switch ($key) {
				case "productname": $key = "description"; break;	
			}
			$info[$key] = $result;
			next($array);
		}
		
		return $info;
	}
	
	function add_bundle( $code, $quantity, $info ) {
		$moreInfo = $this->get_product_info( $code );
		if ($moreInfo['productsinbundle']) {
			$products = explode(",", $moreInfo['productsinbundle']);
			$price = 0;
			foreach($products as $product) {
				if (is_numeric($product)) {
					$infoProduct = $this->get_product_info($product);
					$infoProduct['price'] = 0;
					$this->add_product( $product, $quantity, $infoProduct, $code );
				}
			}
		}
	}

	function get_tracker_item_id_custom( $trackerName, $fieldName, $value ) {
		global $tikilib;

		$itemId = $tikilib->getOne("
			SELECT tiki_tracker_item_fields.itemId
			FROM tiki_tracker_item_fields
			LEFT JOIN tiki_tracker_fields ON tiki_tracker_fields.fieldId = tiki_tracker_item_fields.fieldId
			LEFT JOIN tiki_trackers ON tiki_trackers.trackerId = tiki_tracker_fields.trackerId
			LEFT JOIN tiki_tracker_items ON tiki_tracker_items.itemId = tiki_tracker_item_fields.itemId
			WHERE tiki_trackers.name = ? AND
			tiki_tracker_fields.name = ? AND
			tiki_tracker_item_fields.value = ?
		", array($trackerName, $fieldName, $value));
		
		return $itemId;
	}
	
	function get_tracker_value_custom( $trackerName, $fieldName, $itemId ) {
		global $tikilib;

		$value = $tikilib->getOne("
			SELECT tiki_tracker_item_fields.value
			FROM tiki_tracker_item_fields
			LEFT JOIN tiki_tracker_fields ON tiki_tracker_fields.fieldId = tiki_tracker_item_fields.fieldId
			LEFT JOIN tiki_trackers ON tiki_trackers.trackerId = tiki_tracker_fields.trackerId
			LEFT JOIN tiki_tracker_items ON tiki_tracker_items.itemId = tiki_tracker_item_fields.itemId
			WHERE tiki_trackers.name = ? AND
			tiki_tracker_fields.name = ? AND
			tiki_tracker_item_fields.itemId = ?
		", array($trackerName, $fieldName, $itemId));
		
		return $value;
	}
	
	function get_tracker_values_custom( $trackerName, $itemId ) {
		global $tikilib;
		
		$result = $tikilib->fetchAll("
			SELECT tiki_tracker_fields.name, tiki_tracker_item_fields.value
			FROM tiki_tracker_item_fields
			LEFT JOIN tiki_tracker_fields ON tiki_tracker_fields.fieldId = tiki_tracker_item_fields.fieldId
			LEFT JOIN tiki_trackers ON tiki_trackers.trackerId = tiki_tracker_fields.trackerId
			LEFT JOIN tiki_tracker_items ON tiki_tracker_items.itemId = tiki_tracker_item_fields.itemId
			WHERE tiki_trackers.name = ? AND
			tiki_tracker_item_fields.itemId = ? AND
			tiki_tracker_item_fields.value <> ''
		", array( $trackerName, $itemId ));
		
		$item = array();
		
		foreach($result as $row) {
			$item[$row['name']] = $row['value'];
		}
		return $item;
	}
	
	function set_tracker_value_custom( $trackerName, $fieldName, $itemId, $value ) {
		global $tikilib;

		$value = $tikilib->query("
			UPDATE tiki_tracker_item_fields 
			LEFT JOIN tiki_tracker_fields ON tiki_tracker_fields.fieldId = tiki_tracker_item_fields.fieldId
			LEFT JOIN tiki_trackers ON tiki_trackers.trackerId = tiki_tracker_fields.trackerId
			LEFT JOIN tiki_tracker_items ON tiki_tracker_items.itemId = tiki_tracker_item_fields.itemId
			
			SET tiki_tracker_item_fields.value = ?
			
			WHERE tiki_trackers.name = ? AND
			tiki_tracker_fields.name = ? AND
			tiki_tracker_item_fields.itemId = ?
		", array($value, $trackerName, $fieldName, $itemId));
	}
	
	function update_gift_certificate( $invoice ) {
		//if total is more than 0 the gift card is less than the order total, otherwise the giftcard is as much as the order total
		$this->get_gift_certificate();
		
		if (!$this->gift_certificate_code) return false;
		
		$balanceCurrent = $this->gift_certificate_amount - $this->gift_certificate_discount;
		
		if ($this->gift_certificate_amount_original == 0) { //if original balance isn't set, go ahead and set it, it is just for reference
			$this->set_tracker_value_custom( "Gift Certificates", "Original Balance or Percentage", $this->gift_certificate_id, $this->gift_certificate_amount );
		}
		
		if ($this->gift_certificate_mode == "Percentage" || $this->gift_certificate_mode == "Coupon Percentage" || $this->gift_certificate_mode == "Coupon") {
			$balanceCurrent = 0;	
		}
		
		$this->set_tracker_value_custom( "Gift Certificates", "Current Balance or Percentage", $this->gift_certificate_id, $balanceCurrent );

		if (!$invoice) return false;
		$invoiceId = $this->get_tracker_item_id_custom( "Orders", "Tiki Payment ID", $invoice );
		$this->set_tracker_value_custom( "Orders", "Gift Certificate ID", $invoiceId, $this->gift_certificate_id );
		$this->set_tracker_value_custom( "Orders", "Gift Certificate Amount Applied", $invoiceId, $this->gift_certificate_discount );
		// set refunding in the event of cancellation
		global $paymentlib; require_once 'lib/payment/paymentlib.php';
		$paymentlib->register_behavior( $invoice, 'cancel', 'cart_gift_certificate_refund', array( $this->gift_certificate_id, $this->gift_certificate_mode, $this->gift_certificate_amount, $this->gift_certificate_discount) );
	}
	
	function get_gift_certificate_code($code) {
		$code = ( $code ? $code : $_SESSION["cart"]["tiki-gc"]["code"] ); //TODO: needs to be a little less dirty
		return $code;
	}
	
	function get_gift_certificate( $code ) {
		$this->gift_certificate_code = $code = ( $code ? $code : $this->get_gift_certificate_code() );
		if (!$code) return false;
		
		$this->gift_certificate_id = $this->get_tracker_item_id_custom( "Gift Certificates", "Redeem Code", $code );
		
		$this->gift_certificate_amount = floatval( 
			$this->get_tracker_value_custom( "Gift Certificates", "Current Balance or Percentage", $this->gift_certificate_id)
		);
		
		$this->gift_certificate_amount_original = floatval(
			$this->get_tracker_value_custom( "Gift Certificates", "Original Balance or Percentage", $this->gift_certificate_id )
		);
		
		$this->gift_certificate_type = $this->get_tracker_value_custom( "Gift Certificates", "Type", $this->gift_certificate_id );
		$this->gift_certificate_type_reference = $this->get_tracker_value_custom( "Gift Certificates", "Type Reference", $this->gift_certificate_id );
		$this->gift_certificate_name = $this->get_tracker_value_custom( "Gift Certificates", "Name", $this->gift_certificate_id );
		$this->gift_certificate_mode = $this->get_tracker_value_custom( "Gift Certificates", "Mode", $this->gift_certificate_id );
		
		switch ( $this->gift_certificate_mode ) {
			case "Percentage":
			case "Coupon Percentage":
				$this->gift_certificate_mode_symbol_after = '%';
				break;
		}
		
		return ( $this->gift_certificate_amount > 0 ? true : false );
	}
	
	function remove_gift_certificate() {
		$_SESSION['cart']['tiki-gc'] = '';
	}
	
	function add_gift_certificate( $code ) {	
		$this->get_gift_certificate( $code );
		
		if ( $this->gift_certificate_amount > 0 ) {
			if ( ! isset($_SESSION['cart']) ) return false;
			$_SESSION['cart']['tiki-gc'] = array();
			$_SESSION['cart']['tiki-gc']['is_gift_certificate'] = true;
			$_SESSION['cart']['tiki-gc']['code'] = $code;
			return true;
		} else {
			return false;
		}
	}
	
	function has_gift_certificate() {
		global $trklib;
		require_once('lib/trackers/trackerlib.php');
		return ( $trklib->get_tracker_by_name( "Gift Certificates" ) ? true : false );
	}
	
	function discount_from_total( $total ) { //ensures that the discount being had isn't less that the total resulting in a negative value
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
	
	function product_reference_gift_certificate($total, $reference ) {
		$productTotal = 0;
		$giftCertificateApplies = false;
		$products = array();
		
		foreach( $_SESSION['cart'] as $info ) {
			if ( $info[$reference] == $this->gift_certificate_type_reference ) {
				$products[] = $info;
				$productTotal += floatval( $info['quantity'] ) * floatval( $info['price'] );
				$giftCertificateApplies = true;
			}
		}
		
		if ($giftCertificateApplies) {
			if ($this->gift_certificate_mode == "Coupon Percentage" || $this->gift_certificate_mode == "Coupon") {
				$cheapestPrice = 0;
				foreach( $products as $product ) {
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
			$productTotal = $this->discount_from_total( $productTotal );
			$total += $productTotal;
		} else {
			$this->remove_gift_certificate();
			global $access;
			$access->redirect( $_SERVER['REQUEST_URI'], tra('Gift card is not valid for products in cart') );
		}
		
		return $total;
	}
	
	function get_total() {
		$this->init_cart();
		$this->get_gift_certificate();

		$total = 0;

		foreach( $_SESSION['cart'] as $info ) {
			$total += floatval( $info['quantity'] ) * floatval( $info['price'] );
		}
		
		$this->total_no_discount = $total;

		switch ($this->gift_certificate_type) {
			case "Product Bundle":
				$total = $this->product_reference_gift_certificate( $total, 'productbundle' );
				break;
			case "Bundle Class":
				$total = $this->product_reference_gift_certificate( $total, 'bundleclass' );
				break;
			case "Product Class": //product class can only be used for a single Product Class
				$total = $this->product_reference_gift_certificate( $total, 'productclass' );
				break;
			case "Product": //product can only be used with a single product
				$total = $this->product_reference_gift_certificate( $total, 'code' );
				break;
			case "Cash": //cash can be used with any cart items
			default:
				$total = $this->discount_from_total( $total );
		}
		
		$this->gift_certificate_discount = $this->total_no_discount - $total;
		
		return number_format( $total, 2, '.', '' );
	}

	function get_quantity( $code ) {
		$this->init_cart();

		if( isset( $_SESSION['cart'][ $code ] ) ) {
			return $_SESSION['cart'][ $code ]['quantity'];
		} else {
			return 0;
		}
	}

	function get_hash( $code ) {
		$this->init_cart();

		if( isset( $_SESSION['cart'][ $code ] ) ) {
			return $_SESSION['cart'][ $code ]['hash'];
		} else {
			return '';
		}
	}

	function get_description() {
		$id_label = tra('ID');
		$product_label = tra('Product');
		$quantity_label = tra('Quantity');
		$price_label = tra('Unit Price');
		$gift_certificate_label = tra("Gift Certificate: ");
		$gift_certificate_amount_used_label = tra("Gift Certificate Amount Used: ");
		
		$wiki = "||__{$id_label}__|__{$product_label}__|__{$quantity_label}__|__{$price_label}__\n";

		foreach( $this->get_content() as $item ) {
			if ( !$item['is_gift_certificate'] ) {
				if( $item['href'] ) {
					$label = "[{$item['href']}|{$item['description']}]";
				} else {
					$label = $item['description'];
				}
				if ( !empty($item['onbehalf']) ) {
					//Custom2
					$label .= " " . tra('for') . " " . $item['onbehalf'];
				}
				
				$wiki .= "{$item['code']}|{$label}|{$item['quantity']}|{$item['price']}\n";
			}
		}

		$wiki .= "||\n";
		
		if ( isset($this->gift_certificate_code) && isset($this->gift_certificate_discount) ) {
			$wiki .= $gift_certificate_label . $this->gift_certificate_code . " " . $this->gift_certificate_name . "\n";
			$wiki .= $gift_certificate_amount_used_label . $this->gift_certificate_discount;
		}
		
		return $wiki;
	}

	function update_quantity( $code, $quantity, $info = array('exchangetoproductid' => 0, 'exchangeorderamount' => 0), $skipRedirect = false ) {		
		$currentQuantity = $this->get_quantity( $code );
		// Prevent going below 0 inventory TODO check feature
		$currentInventory = $this->get_inventory($code);
		if ($quantity - $currentQuantity > $currentInventory) {
			if ($currentQuantity == 0) {
				unset( $_SESSION['cart'][ $code ] );
			}
			global $access;
			
			if (!$skipRedirect)
				$access->redirect( $_SERVER['REQUEST_URI'], tra('There is not enough inventory left for your request') );
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
			// TODO check feature
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
		$this->init_cart();

		if( isset( $_SESSION['cart'][ $code ] ) && $quantity != 0  ) {
			$_SESSION['cart'][ $code ]['quantity'] = abs($quantity);
		} else {
			unset( $_SESSION['cart'][ $code ] );
		}
	}

	function request_payment() {
		global $prefs, $user, $tikilib;
		global $paymentlib; require_once 'lib/payment/paymentlib.php';

		$total = $this->get_total();

		if( $total > 0 || $this->total_no_discount ) {
			// if anonymous shopping to set pref as to which shopperinfo to show in description
			if (empty($user)) {
				$shopperinfo_descvar = 'email'; // this needs to be a pref
				if (!empty($_SESSION['shopperinfo'][$shopperinfo_descvar])) {
					$shopperinfo_desc = $_SESSION['shopperinfo'][$shopperinfo_descvar];
					$description = tra('Registration Check-Out') . " ($shopperinfo_desc)";
				} else {
					$description = tra('Registration Check-Out');
				}
			} else {
				$description = tra('Registration Check-Out') . " ($user)";
			}	
			$invoice = $paymentlib->request_payment( $description, $total, 1440 * $prefs['payment_default_delay'], $this->get_description() );
			foreach( $this->get_behaviors() as $behavior ) {
				$paymentlib->register_behavior( $invoice, $behavior['event'], $behavior['behavior'], $behavior['arguments'] );
			}
		} else {
			$invoice = 0;
			foreach( $this->get_behaviors() as $behavior ) {
				if ($behavior['event'] == 'complete') {
					$name = $behavior['behavior'];
					$file = dirname(__FILE__) . "/behavior/$name.php";
					$function = 'payment_behavior_' . $name;
					require_once $file;
					call_user_func_array($function, $behavior['arguments']);
				}
			} 
		}
		// Custom++ TODO: check cart feature existence   Record order TODO: should have some error checking on missing profiles
		require_once 'lib/profilelib/installlib.php';
		require_once 'lib/profilelib/profilelib.php';
		// Handle anonymous user (not logged in) shopping that require only email 
		if (!$user || isset($_SESSION['forceanon']) && $_SESSION['forceanon'] == 'y') {
			if (!empty($_SESSION['shopperinfo'])) { // should also check for pref that this anonymous shopping feature is on
				// First create shopper info in shopper tracker
				global $record_profile_items_created;
				$record_profile_items_created = array();
				$shopperprofile = Tiki_Profile::fromDb( 'shopper_prf' );
				$profileinstaller = new Tiki_Profile_Installer();
				$profileinstaller->forget ($shopperprofile ); // profile can be installed multiple times
				$profileinstaller->setUserData( $_SESSION['shopperinfo'] );
				$profileinstaller->install( $shopperprofile );
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
			'user' => $user,
			'time' => $tikilib->now,
			'total' => $total,
			'invoice' => $invoice, 
		);
		$orderprofile = Tiki_Profile::fromDb( 'order_prf' );
		$orderitemprofile = Tiki_Profile::fromDb( 'orderitem_prf' );
		$profileinstaller = new Tiki_Profile_Installer();
		$profileinstaller->forget( $orderprofile ); // profile can be installed multiple times
		$profileinstaller->forget( $orderitemprofile );
		$profileinstaller->setUserData( $userInput );
		
		global $record_profile_items_created;
		$record_profile_items_created = array();

		$profileinstaller->install( $orderprofile );
		
		$content = $this->get_content();
		foreach( $content as $info ) {
			if ($info['productclass']) {
				$product_classes[] = $info['productclass'];
			}
			if (!empty($info['onbehalf'])) {
				$itemuser = $info['onbehalf'];
			} elseif (!$user || isset($_SESSION['forceanon']) && $_SESSION['forceanon'] == 'y') {
				$itemuser = $cartuser;
			} else {
				$itemuser = $user;
			}
			// TODO get discounted price	
			$userInput = array(
				'user' => $itemuser,
				'quantity' => $info['quantity'],
				'price' => $info['price'],
				'product' => $info['code'],
				'eventcode' => $info['eventcode'],
				'eventstart' => $this->get_tracker_value_custom('Events','Event Date',$info['eventcode']),
				'eventend' => $this->get_tracker_value_custom('Events','Event End Date',$info['eventcode']),
			);
			$profileinstaller->setUserData( $userInput );	
			$profileinstaller->forget( $orderitemprofile );
			$profileinstaller->install( $orderitemprofile );
			$this->change_inventory($info['code'], -1 * $info['quantity'], false);
			if ($info['exchangetoproductid'] && $info['exchangeorderamount']) {	
				$this->change_inventory($info['exchangetoproductid'], -1 * $info['exchangeorderamount'], false);
			}
			if ($total > 0) {
				$paymentlib->register_behavior( $invoice, 'cancel', 'replace_inventory', array( $info['code'], $info['quantity'] ) );
				if ($info['exchangetoproductid'] && $info['exchangeorderamount']) {
					$paymentlib->register_behavior( $invoice, 'cancel', 'replace_inventory', array( $info['exchangetoproductid'], $info['exchangeorderamount'] ) );
				}
			}
		}
		$email_template_ids = array();
		$product_classes = array_unique($product_classes);
		foreach ($product_classes as $pc) {
			if ($email_template_id = $this->get_tracker_value_custom( 'Product Classes', 'Email Template ID', $pc)) {
				$email_template_ids[] = $email_template_id;
			} 
		}
		if (!empty($record_profile_items_created)) {
			if ($total > 0) {
				$paymentlib->register_behavior( $invoice, 'complete', 'record_cart_order', array( $record_profile_items_created ) );
				$paymentlib->register_behavior( $invoice, 'cancel', 'cancel_cart_order', array( $record_profile_items_created ) );
				if ($user) {
					$paymentlib->register_behavior( $invoice, 'complete', 'cart_send_confirm_email', array( $user, $email_template_ids ) );
				}
			} else {
				require_once('lib/payment/behavior/record_cart_order.php');
				payment_behavior_record_cart_order( $record_profile_items_created );
				if ($user) {
					require_once('lib/payment/behavior/cart_send_confirm_email.php');
					payment_behavior_cart_send_confirm_email( $user, $email_template_ids );
				}
			}
		}
		// end Custom++
		// Additional feature, needs to be optional, setting a page (which should be configurable) as a token access page for the anonymous user, this feature depends on token feature to be activated
		if (!$user || isset($_SESSION['forceanon']) && $_SESSION['forceanon'] == 'y') {
			$shopperurl = 'tiki-index.php?page=My+Ticket&shopper=' . intval( $cartuser );
			global $tikiroot, $prefs;
			$shopperurl = $tikilib->httpPrefix( true ) . $tikiroot . $shopperurl;
			require_once 'lib/auth/tokens.php';
			$tokenlib = AuthTokens::build( $prefs );
			$shopperurl = $tokenlib->includeToken( $shopperurl, array('Temporary Shopper','Anonymous') );
			// Need a pref for send email feature
			if ( !empty($_SESSION['shopperinfo']['email']) ) {
				require_once('lib/webmail/tikimaillib.php');
				global $smarty;
				$smarty->assign('shopperurl', $shopperurl);
				$mail_subject = $smarty->fetch('mail/cart_order_received_anon_subject.tpl');
				$mail_data = $smarty->fetch('mail/cart_order_received_anon.tpl');
				$mail = new TikiMail();
				$mail->setSubject($mail_subject);
				$mail->setText($mail_data);
				$mail->setHeader("From", $prefs['sender_email']);
				$mail->send($_SESSION['shopperinfo']['email']); // the field to use probably needs to be configurable as well 
			} 
		}
		$this->update_gift_certificate( $invoice );
		// end Additional feature
		$this->empty_cart(); 
		return $invoice;
	}

	function empty_cart() {
		$this->clear_onhold_list();
		$_SESSION['cart'] = array(); 
	}

	private function get_behaviors() {
		$behaviors = array();

		foreach( $this->get_content() as $item ) {
			if( isset( $item['behaviors'] ) ) {
				foreach( $item['behaviors'] as $behavior ) {
					for( $i = 0; $item['quantity'] > $i; ++$i ) {
						$behaviors[] = $behavior;
					}
				}
			}
		}

		return $behaviors;
	}

	private function init_cart() {
		if( ! isset( $_SESSION['cart'] ) ) {
			$_SESSION['cart'] = array(); 
		}
	}

	private function init_product( $code, $info, $parentCode ) {
	
		if( ! isset( $_SESSION['cart'][ $code ] ) ||  ! isset( $_SESSION['cart'][ $parentCode ][ 'bundledproducts' ][ $code ] ) ) {
			$info['hash'] = md5($code.time());
			$info['code'] = $code;
			$info['quantity'] = 0;
			$info['price'] = number_format( abs($info['price']), 2, '.', '' );

			if( ! isset( $info['href'] ) ) {
				$info['href'] = null;
			}

			if( ! isset( $parentCode ) ) {
				$_SESSION['cart'][ $code ] = $info;
			} else {
				 $_SESSION['cart'][ $parentCode ][ 'bundledproducts' ][ $code ] = $info;
			}
		}
	}

	function get_content() {
		$this->init_cart();

		return $_SESSION['cart'];
	}

	// TODO Inventory functions need to check for feature before doing anything

	function get_inventory_type( $productId ) {
		// TODO get trackerId etc. of products tracker from profile tokens in db, should be object properties
		$productTrackerId = 3;
		$inventoryTypeFieldId = 11;
		global $trklib;
		require_once('lib/trackers/trackerlib.php');
		return $trklib->get_item_value($productTrackerId, $productId, $inventoryTypeFieldId); 
	}

	function get_inventory( $productId, $less_hold = true ) {
		$inventoryType = $this->get_inventory_type( $productId );
		if ($inventoryType == 'none') {
			return 999999999;
		}
		if ($inventoryType == 'shared') {
			// TODO
			return 0;
		}
		// TODO get trackerId etc. of products tracker from profile tokens in db, should be object properties
		$productTrackerId = 3;
		$inventoryTotalFieldId = 7;
		$inventoryLessHoldFieldId = 8;
		if ($less_hold) {
			$this->expire_onhold_list( $productId );
			$inventoryFieldId = $inventoryLessHoldFieldId;
		} else {
			$inventoryFieldId = $inventoryTotalFieldId;
		}
		global $trklib;
		require_once('lib/trackers/trackerlib.php');
		return $trklib->get_item_value($productTrackerId, $productId, $inventoryFieldId); 
	}

	function change_inventory( $productId, $amount = 1, $changeLessHold = true) {
		$inventoryType = $this->get_inventory_type( $productId );
		if ($inventoryType == 'none') {
			return false;
		}
		$currentTotal = $this->get_inventory($productId, false);
		$newTotal = max(0, $currentTotal + $amount);
		$this->set_inventory( $productId, $newTotal, false);
		if ($changeLessHold) {
			$currentLessHold = $this->get_inventory($productId);
			$newLessHold = max(0, $currentLessHold + $amount);
			$this->set_inventory( $productId, $newLessHold, true);
		}
		return true; 
	}

	function hold_inventory( $productId, $amount = 1) {	
		$inventoryType = $this->get_inventory_type( $productId );
		if ($inventoryType == 'none') {
			return false;
		}
		$currentLessHold = $this->get_inventory($productId);
		$newLessHold = max(0, $currentLessHold - $amount);
		$this->set_inventory( $productId, $newLessHold, true);	
		return true;
	}

	function unhold_inventory( $productId, $amount = 1) {
		$inventoryType = $this->get_inventory_type( $productId );
		if ($inventoryType == 'none') {
			return false;
		}
		$currentLessHold = $this->get_inventory($productId);
		$currentTotal = $this->get_inventory($productId, false);
		$newLessHold = min($currentTotal, $currentLessHold + $amount);
		$this->set_inventory( $productId, $newLessHold, true);
		return true;
	}

	private function set_inventory( $productId, $amount, $less_hold = true ) {
		$inventoryType = $this->get_inventory_type( $productId );
		if ($inventoryType == 'none') {
			return false;
		}
		if ($inventoryType == 'shared') {
			// TODO
			return false;
		}
		// TODO get trackerId etc. of products tracker from profile tokens in db, should be object properties
                $productTrackerId = 3;
                $inventoryTotalFieldId = 7;
                $inventoryLessHoldFieldId = 8;

                if ($less_hold) {
                        $inventoryFieldId = $inventoryLessHoldFieldId; 
                } else {
                        $inventoryFieldId = $inventoryTotalFieldId;
                }
		$trackerFields = array();
		$trackerFields[] = array('fieldId' => $inventoryFieldId, 'value' => $amount);
               	$this->modify_tracker_item( $productTrackerId, $productId, $trackerFields ); 
		return true;
	}

	private function modify_tracker_item( $trackerId, $itemId, $trackerFields ) {
		global $trklib;
		require_once('lib/trackers/trackerlib.php');
		$tracker_fields_info = $trklib->list_tracker_fields($trackerId);
		$fieldTypes = array();
		foreach($tracker_fields_info['data'] as $t) {
			$fieldTypes[$t['fieldId']] = $t['type'];
			$fieldOptionsArray[$t['fieldId']] = $t['options_array'];
		}
		foreach($trackerFields as &$h) {
			$h['type'] = $fieldTypes[$h['fieldId']];
			$h['options_array'] = $fieldOptionsArray[$h['fieldId']];
		}
		foreach ($trackerFields as $v) {
			$ins_fields["data"][] = array('options_array' => $v['options_array'], 'type' => $v['type'], 'fieldId' => $v['fieldId'], 'value' => $v['value']);
		}
		$trklib->replace_item($trackerId, $itemId, $ins_fields);
		return true;
	}

	private function clear_onhold_list()  {
		global $tikilib;
		$hashes = array();
		foreach( $this->get_content() as $item ) {
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

	private function expire_onhold_list( $productId ) {
		global $tikilib;
		// TODO: set pref for expiry time
		$expiry = 15 * 60; // 15 minutes
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

	function extend_onhold_list() {
		global $tikilib;
		// TODO: set pref for time after last setting to extend 
		$extend = 5 * 60; // 5 minutes
		$hashes = array();
		foreach( $this->get_content() as $item ) {
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
		
	private function remove_from_onhold_list( $code ) {
		global $tikilib;
		$hash = $this->get_hash($code); 
		$query = "delete from `tiki_cart_inventory_hold` where `hash` = ?";
		$tikilib->query($query, $hash);
		return true;
	}

	private function add_to_onhold_list( $code, $quantity ) {
		global $tikilib;
		$hash = $this->get_hash($code);
		$query = "insert into `tiki_cart_inventory_hold` (`productId`, `quantity`, `timeHeld`, `hash`) values (?,?,?,?)";
		$bindvars = array($code, $quantity, $tikilib->now, $hash);
		$tikilib->query($query, $bindvars);
		return true;	
	}

	function get_missing_user_information_fields( $product_class_id, $type = 'required' ) {
		global $user;
		global $trklib;
		require_once('lib/trackers/trackerlib.php');
		if ($type == 'required') {
			$fields_str = $this->get_tracker_value_custom( 'Product Classes', 'Required Field IDs', $product_class_id);
		} else if ($type == 'postpurchase') {
			$fields_str = $this->get_tracker_value_custom( 'Product Classes', 'Postpurchase Field IDs', $product_class_id);
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
			if ($fieldId = $trklib->get_field_id_from_type($trackerId, 'u', '1%')) { // user creator field
				$item = $trklib->get_item($trackerId,$fieldId,$user);
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

	function get_missing_user_information_form( $product_class_id, $type = 'required' ) {
		if ($type == 'required') {
			return $this->get_tracker_value_custom( 'Product Classes', 'Associated Required Form', $product_class_id );
		} else {
			return $this->get_tracker_value_custom( 'Product Classes', 'Associated Postpurchase Form', $product_class_id );
		}
	}

	function skip_user_information_form_if_not_missing( $product_class_id ) {
		if ($this->get_tracker_value_custom( 'Product Classes', 'Skip Required Form if Filled', $product_class_id ) == 'Yes') {
			return true;
		} else {
			return false;
		}
	} 

}

global $cartlib;
$cartlib = new CartLib;

