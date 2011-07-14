<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: sample.php 25244 2010-02-16 06:26:12Z changi67 $

function payment_behavior_cart_exchange_product( $exchangeorderitemid = 0, $exchangetoproductid = 0 ) {
	
	global $prefs;
	
	if (!($exchangeorderitemid) || !($exchangetoproductid) || $prefs['payment_cart_exchange'] != 'y') {
		return false;
	}

	global $trklib;
	include_once ('lib/trackers/trackerlib.php');
	
	$orderitemsTrackerId = $prefs['payment_cart_orderitems_tracker'];
	
	$exchangefromrecordFieldId = $trklib->get_field_id($orderitemsTrackerId,'Exchange - Current Order ID');
	$exchangetorecordFieldId = $trklib->get_field_id($orderitemsTrackerId,'Exchange - Desired Product ID');
	$realEventFieldId = $trklib->get_field_id($orderitemsTrackerId,'Real Associated Event ID');
	$eventFieldId = $trklib->get_field_id($orderitemsTrackerId,'Associated Event ID');
	$associatedproductFieldId = $trklib->get_field_id($orderitemsTrackerId,'Product ID');
	$amountBoughtFieldId = $trklib->get_field_id($orderitemsTrackerId,'Amount bought');
	$itemeventStartFieldId = $trklib->get_field_id($orderitemsTrackerId,'Associated Event Start Date');
	$itemeventEndFieldId = $trklib->get_field_id($orderitemsTrackerId,'Associated Event End Date');

	$productsTrackerId = $prefs['payment_cart_product_tracker'];
	$productclassFieldId = $trklib->get_field_id($productsTrackerId, $prefs['payment_cart_product_classid_fieldname']);
	if ($prefs['payment_cart_associatedevent'] == 'y') {
		$producteventFieldId = $trklib->get_field_id($productsTrackerId, $prefs['payment_cart_associated_event_fieldname']);
		$eventsTrackerId = $prefs['payment_cart_event_tracker'];
		$eventStartFieldId = $trklib->get_field_id($eventsTrackerId, $prefs['payment_cart_eventstart_fieldname']);
		$eventEndFieldId = $trklib->get_field_id($eventsTrackerId, $prefs['payment_cart_eventend_fieldname']);
	}

	// Get order item
	$orderItemId = $exchangeorderitemid;
	$fromproductId = $trklib->get_item_value($orderitemsTrackerId, $orderItemId, $associatedproductFieldId);
	$amountBought = $trklib->get_item_value($orderitemsTrackerId, $orderItemId, $amountBoughtFieldId);
	// Get product class
	$productclass = $trklib->get_item_value($productsTrackerId, $fromproductId, $productclassFieldId);
	// Check target product class
	$targetProductclass = $trklib->get_item_value($productsTrackerId, $exchangetoproductid, $productclassFieldId);

	if ($targetProductclass != $productclass) {
		return false;
	}

	// Perform exchange
	if ($producteventFieldId && $realEventFieldId) {
		// Update real associated event id if necessary
		$eventId = $trklib->get_item_value($productsTrackerId, $exchangetoproductid, $producteventFieldId); 
		if ($eventId) {
			$ins_fields["data"][] = array('type' => 't', 'fieldId' => $realEventFieldId, 'value' => $eventId);
			$ins_fields["data"][] = array('type' => 'r', 'fieldId' => $eventFieldId, 'value' => $eventId);
			$eventStartDate = $trklib->get_item_value($eventsTrackerId, $eventId, $eventStartFieldId);
			$eventEndDate = $trklib->get_item_value($eventsTrackerId, $eventId, $eventEndFieldId);
			$ins_fields["data"][] = array('type' => 'f', 'fieldId' => $itemeventStartFieldId, 'value' => $eventStartDate);
			$ins_fields["data"][] = array('type' => 'f', 'fieldId' => $itemeventEndFieldId, 'value' => $eventEndFieldDate);
			
		}
	}
	$ins_fields["data"][] = array('type' => 'r', 'fieldId' => $associatedproductFieldId, 'value' => $exchangetoproductid);
	$trklib->replace_item($orderitemsTrackerId, $orderItemId, $ins_fields);

	// Replace inventory for original product
	global $cartlib; require_once 'lib/payment/cartlib.php';
	$cartlib->change_inventory( $fromproductId, $amountBought );

	return true;	
}
