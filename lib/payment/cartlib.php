<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class CartLib
{
	function add_product( $code, $quantity, $info ) {
		$this->init_cart();

		$this->init_product( $code, $info );

		$current = $this->get_quantity( $code );
		$current += $quantity;

		$this->update_quantity( $code, $current );
	}

	function get_total() {
		$this->init_cart();

		$total = 0;

		foreach( $_SESSION['cart'] as $info ) {
			$total += floatval( $info['quantity'] ) * floatval( $info['price'] );
		}

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
			return 0;
		}
	}

	function get_description() {
		$id_label = tra('ID');
		$product_label = tra('Product');
		$quantity_label = tra('Quantity');
		$price_label = tra('Unit Price');

		$wiki = "||__{$id_label}__|__{$product_label}__|__{$quantity_label}__|__{$price_label}__\n";

		foreach( $this->get_content() as $item ) {
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

		$wiki .= "||\n";

		return $wiki;
	}

	function update_quantity( $code, $quantity ) {
		$currentQuantity = $this->get_quantity( $code );
		if ($currentQuantity > 0) {
			$this->unhold_inventory($code, $currentQuantity);
			$this->remove_from_onhold_list($code);
		}
		if ($quantity > 0) {
			// TODO check feature
                	$currentInventory = $this->get_inventory($code);
                	if ($quantity > $currentInventory) {
                        	$quantity = $currentInventory;
			}
			$this->hold_inventory($code, $quantity);
			$this->add_to_onhold_list($code, $quantity);
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

		if( $total > 0 ) {
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
			$invoice = $paymentlib->request_payment( $description, $total, $prefs['payment_default_delay'], $this->get_description() );

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
			if (!empty($info['onbehalf'])) {
				$itemuser = $info['onbehalf'];
			} elseif (!$user || isset($_SESSION['forceanon']) && $_SESSION['forceanon'] == 'y') {
				$itemuser = $cartuser;
			} else {
				$itemuser = '';
			}
			// TODO get discounted price	
			$userInput = array(
				'user' => $itemuser,
				'quantity' => $info['quantity'],
				'price' => $info['price'],
				'product' => $info['code'],
				'eventcode' => $info['eventcode'],
			);
			$profileinstaller->setUserData( $userInput );	
			$profileinstaller->forget( $orderitemprofile );
			$profileinstaller->install( $orderitemprofile );
			$this->change_inventory($info['code'], -1 * $info['quantity'], false);
			if ($total > 0) {
				$paymentlib->register_behavior( $invoice, 'cancel', 'replace_inventory', array( $info['code'], $info['quantity'] ) );
			}
		}
		if (!empty($record_profile_items_created)) {
			if ($total > 0) {
				$paymentlib->register_behavior( $invoice, 'complete', 'record_cart_order', array( $record_profile_items_created ) );
			} else {
				require_once('lib/payment/behavior/record_cart_order.php');
				payment_behavior_record_cart_order( $record_profile_items_created );
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

	private function init_product( $code, $info ) {
		if( ! isset( $_SESSION['cart'][ $code ] ) ) {
			$info['hash'] = md5($code.time());
			$info['code'] = $code;
			$info['quantity'] = 0;
			$info['price'] = number_format( abs($info['price']), 2, '.', '' );

			if( ! isset( $info['href'] ) ) {
				$info['href'] = null;
			}

			$_SESSION['cart'][ $code ] = $info;
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
			return -1;
		}
		if ($inventoryType == 'shared') {
			// TODO
			return -1;
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
			return -1;
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
			return -1;
		}
		$currentLessHold = $this->get_inventory($productId);
		$newLessHold = max(0, $currentLessHold - $amount);
		$this->set_inventory( $productId, $newLessHold, true);	
		return true;
	}

	function unhold_inventory( $productId, $amount = 1) {
		$inventoryType = $this->get_inventory_type( $productId );
		if ($inventoryType == 'none') {
			return -1;
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
			return -1;
		}
		if ($inventoryType == 'shared') {
			// TODO
			return -1;
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
		}
		foreach($trackerFields as &$h) {
			$h['type'] = $fieldTypes[$h['fieldId']];
		}
		foreach ($trackerFields as $v) {
			$ins_fields["data"][] = array('type' => $v['type'], 'fieldId' => $v['fieldId'], 'value' => $v['value']);
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

}

global $cartlib;
$cartlib = new CartLib;

