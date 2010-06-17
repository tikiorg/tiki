<?php

require_once 'lib/payment/cartlib.php';

class Payment_CartTest extends TikiTestCase
{
	function setUp() {
		parent::setUp();

		unset( $_SESSION['cart'] );
	}

	function testEmptyCart() {
		$lib = new CartLib;

		$this->assertEquals( 0.0, $lib->get_total() );
	}

	function testAddToCart() {
		$lib = new CartLib;

		$lib->add_product( 'T-123', 3, array(
			'price' => '100.43',
			'description' => 'Hello',
		) );

		$this->assertEquals( 301.29, $lib->get_total() );
	}

	function testUpdateQuantity() {
		$lib = new CartLib;

		$lib->add_product( 'T-123', 3, array(
			'price' => '100.43',
			'description' => 'Hello',
		) );

		$lib->update_quantity( 'T-123', 1 );

		$this->assertEquals( 100.43, $lib->get_total() );
	}

	function testMultipleProducts() {
		$lib = new CartLib;

		$lib->add_product( 'T-123', 2, array(
			'price' => '100.43',
			'description' => 'Hello',
		) );
		$lib->add_product( 'T-456', 1, array(
			'price' => '100.43',
			'description' => 'World',
		) );

		$this->assertEquals( 301.29, $lib->get_total() );
	}

	function testProductWithConflictingInformation() {
		$lib = new CartLib;

		$lib->add_product( 'T-123', 2, array(
			'price' => '100.43',
			'description' => 'Hello',
		) );
		$lib->add_product( 'T-123', 1, array(
			'price' => '1000.00',
			'description' => 'World',
		) );

		$this->assertEquals( 301.29, $lib->get_total() );
	}

	function testUpdateMissingProduct() {
		$lib = new CartLib;
		$lib->update_quantity( '1234', 3 );

		$this->assertEquals( 0, $lib->get_quantity( '1234' ) );
	}

	function testPrecision() {
		$lib = new CartLib;

		$lib->add_product( 'T-456', 1, array(
			'price' => '1.012',
			'description' => 'World',
		) );

		$this->assertEquals( 1.01, $lib->get_total() );
	}

	function testNegativeQuantity() {
		$lib = new CartLib;

		$lib->add_product( 'T-456', -1, array(
			'price' => '1.01',
			'description' => 'World',
		) );

		$this->assertEquals( 1.01, $lib->get_total() );
	}

	function testNegativePrice() {
		$lib = new CartLib;

		$lib->add_product( 'T-456', 1, array(
			'price' => '-1.01',
			'description' => 'World',
		) );

		$this->assertEquals( 1.01, $lib->get_total() );
	}

	function testZeroQuantityRemovedLine() {
		$lib = new CartLib;

		$lib->add_product( 'T-123', 2, array(
			'price' => '100.43',
			'description' => 'Hello',
		) );

		$lib->update_quantity( 'T-123', 0 );

		$this->assertEquals( array(), $lib->get_content() );
	}

	function testPricePadded() {
		$lib = new CartLib;

		$lib->add_product( 'T-123', 2, array(
			'price' => '100.4',
			'description' => 'Hello',
		) );

		$content = $lib->get_content();
		$this->assertSame( '100.40', $content['T-123']['price'] );
	}

	function testTotalPadded() {
		$lib = new CartLib;

		$lib->add_product( 'T-123', 2, array(
			'price' => '100.4',
			'description' => 'Hello',
		) );

		$this->assertSame( '200.80', $lib->get_total() );
	}

	function testRequestPaymentClearsCart() {
		$lib = new CartLib;

		$lib->add_product( 'T-123', 2, array(
			'price' => '100.4',
			'description' => 'Hello',
		) );

		$lib->request_payment();

		$this->assertEquals( array(), $lib->get_content() );
	}

	function testEmptyCartRequestsNothing() {
		$lib = new CartLib;

		$this->assertEquals( 0, $lib->request_payment() );
	}

	function testCollectDescription() {
		$lib = new CartLib;

		$lib->add_product( 'T-123', 2, array(
			'description' => 'Hello World',
			'href' => 'product123',
			'price' => 12.50,
		) );
		$lib->add_product( 'T-456', 1, array(
			'description' => 'Foobar',
			'price' => 120.50,
		) );

		$this->assertEquals( "||__ID__|__Product__|__Quantity__|__Unit Price__
T-123|[product123|Hello World]|2|12.50
T-456|Foobar|1|120.50
||
", $lib->get_description() );
	}

	function testWithItemsRegistersPayment() {
		global $paymentlib; require_once 'lib/payment/paymentlib.php';
		$lib = new CartLib;
		$lib->add_product( '123', 2, array(
			'price' => 123,
			'description' => 'test',
		) );

		$id = $lib->request_payment();

		$this->assertNotEquals( 0, $id );

		$payment = $paymentlib->get_payment( $id );

		TikiDb::get()->query( 'DELETE FROM tiki_payment_requests WHERE paymentRequestId = ?', array( $id ) );

		$this->assertEquals( 246, $payment['amount_original'] );
		$this->assertContains( '123|test|2|123', $payment['detail'] );
	}

	function testRegisteredBehaviorsOnItems() {
		global $paymentlib; require_once 'lib/payment/paymentlib.php';
		$lib = new CartLib;
		$lib->add_product( '123', 2, array(
			'price' => 123,
			'description' => 'test',
			'behaviors' => array(
				array( 'event' => 'complete', 'behavior' => 'sample', 'arguments' => array( 'Done 123!' ) ),
				array( 'event' => 'cancel', 'behavior' => 'sample', 'arguments' => array( 'No 123!' ) ),
			),
		) );
		$lib->add_product( '456', 1, array(
			'price' => 456,
			'description' => 'test',
			'behaviors' => array(
				array( 'event' => 'complete', 'behavior' => 'sample', 'arguments' => array( 'Done 456!' ) ),
				array( 'event' => 'cancel', 'behavior' => 'sample', 'arguments' => array( 'No 456!' ) ),
			),
		) );

		$id = $lib->request_payment();

		$this->assertNotEquals( 0, $id );

		$payment = $paymentlib->get_payment( $id );

		TikiDb::get()->query( 'DELETE FROM tiki_payment_requests WHERE paymentRequestId = ?', array( $id ) );

		$this->assertEquals( array(
			array( 'behavior' => 'sample', 'arguments' => array( 'Done 123!' ) ),
			array( 'behavior' => 'sample', 'arguments' => array( 'Done 123!' ) ),
			array( 'behavior' => 'sample', 'arguments' => array( 'Done 456!' ) ),
		), $payment['actions']['complete'] );
		$this->assertEquals( array(
			array( 'behavior' => 'sample', 'arguments' => array( 'No 123!' ) ),
			array( 'behavior' => 'sample', 'arguments' => array( 'No 123!' ) ),
			array( 'behavior' => 'sample', 'arguments' => array( 'No 456!' ) ),
		), $payment['actions']['cancel'] );
	}
}


