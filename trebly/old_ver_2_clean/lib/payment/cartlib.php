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

			$wiki .= "{$item['code']}|{$label}|{$item['quantity']}|{$item['price']}\n";
		}

		$wiki .= "||\n";

		return $wiki;
	}

	function update_quantity( $code, $quantity ) {
		$this->init_cart();

		if( isset( $_SESSION['cart'][ $code ] ) && $quantity != 0  ) {
			$_SESSION['cart'][ $code ]['quantity'] = abs($quantity);
		} else {
			unset( $_SESSION['cart'][ $code ] );
		}
	}

	function request_payment() {
		global $prefs, $user;
		global $paymentlib; require_once 'lib/payment/paymentlib.php';

		$total = $this->get_total();

		if( $total > 0 ) {
			$description = tra('Cart Check-Out') . (empty($user) ? '' : " ($user)" );
			$invoice = $paymentlib->request_payment( $description, $total, $prefs['payment_default_delay'], $this->get_description() );

			foreach( $this->get_behaviors() as $behavior ) {
				$paymentlib->register_behavior( $invoice, $behavior['event'], $behavior['behavior'], $behavior['arguments'] );
			}

			$this->empty_cart();

			return $invoice;
		}

		return 0;
	}

	function empty_cart() {
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
}

global $cartlib;
$cartlib = new CartLib;

